<?php
/**
 * NOVIUS OS - Web OS for digital communication
 *
 * @copyright  2011 Novius
 * @license    GNU Affero General Public License v3 or (at your option) any later version
 *             http://www.gnu.org/licenses/agpl-3.0.html
 * @link http://www.novius-os.org
 */

namespace Nos\Form;

use Nos\Controller_Front_Application;
use View;

class Controller_Front extends Controller_Front_Application
{
    protected $enhancer_args = array();

    /**
     * Displays the form and handles it submission
     *
     * @param array $enhancer_args
     * @return \Fuel\Core\View|string
     */
    public function action_main($enhancer_args = array())
    {
        \Nos\I18n::current_dictionary('noviusos_form::front');

        $this->enhancer_args = $enhancer_args;

        // Gets the form
        $form_id = $enhancer_args['form_id'];
        if (empty($form_id)) {
            return '';
        }
        $item = \Nos\Form\Model_Form::find($form_id);
        if (empty($item)) {
            return '';
        }

        // Post handler with redirect
        if (\Input::method() == 'POST' && \Input::post('_form_id') == $form_id) {
            $errors = $this->post_answers($item);
            if (empty($errors)) {
                $after_submit = \Arr::get($this->enhancer_args, 'after_submit', 'message');
                if ($after_submit === 'page_id') {
                    $page_id = \Arr::get($this->enhancer_args, 'confirmation_page_id', null);
                    if (!empty($page_id)) {
                        $page = \Nos\Page\Model_Page::find($page_id);
                        if (!empty($page)) {
                            \Response::redirect(\Nos\Tools_Url::encodePath($page->url()));
                        }
                    }
                }

                \Response::redirect(\Nos\Tools_Url::encodePath(\Nos\Nos::main_controller()->getUrl()).'?message='.$form_id);
            }
        } else {
            $errors = array();
        }

        // 'message' is used to show the user a message when the form is submitted
        $request_parameters_to_exclude_from_cache = array('message');

        // Handles cache
        foreach ($item->fields as $field) {
            if (!empty($field->field_origin_var) && in_array($field->field_origin, array('get', 'request'))) {
                $request_parameters_to_exclude_from_cache[] = $field->field_origin_var;

                // Don't store the page in cache when one field is pre-filled with a parameter
                // This allows to still use the cache for the "empty" version of the form
                $fieldDriver = $field->getDriver($this->enhancer_args);
                if (!empty($fieldDriver)) {
                    $value = $fieldDriver->getDefaultValue();
                    if (!empty($value) && $value != $field->field_default_value) {
                        \Nos\Nos::main_controller()->disableCaching();
                    }
                }
            }
        }
        \Nos\Nos::main_controller()->addCacheSuffixHandler(array(
            'type' => 'GET',
            'keys' => $request_parameters_to_exclude_from_cache,
        ));

        // Displays the confirmation message
        if (\Input::get('message', 0) == $form_id) {
            return \View::forge('noviusos_form::front/form/message', array(
                'message' => \Arr::get($this->enhancer_args, 'confirmation_message', __('Thank you. Your answer has been sent.'))
            ), false);
        }
        // Displays the form
        else {
            return $this->render_form($item, $errors);
        }
    }

    /**
     * Renders the form
     *
     * @param $form
     * @param $errors
     * @return \Fuel\Core\View
     */
    public function render_form($form, $errors)
    {
        // Gets the form fields
        $fields = $this->getFormFields($form, $errors);

        // Initializes conditional fields
        foreach ($fields as $field) {
            if (is_a($field['item'], 'Nos\Form\Model_Field') && filter_var($field['item']->get('field_conditional'), FILTER_VALIDATE_BOOLEAN)) {
                $json = \Fuel\Core\Format::forge(array(
                    'inputname' => $field['item']->get('field_conditional_form'),
                    'condition' => $field['item']->get('field_virtual_name'),
                    'value' => $field['item']->get('field_conditional_value')
                ))->to_json();
                \Nos\Nos::main_controller()->addJavascriptInline("init_form_condition($json);");
            }
        }

        // Gets the default layout to use
        $config = \Config::load('noviusos_form::noviusos_form', true);
        $layout = \Arr::get($config, 'layout', 'noviusos_form::front/form/foundation');

        // Triggers an event to allow fields and layout manipulation
        $args = array(
            'layout' => &$layout,
            'fields' => &$fields,
            'enhancer_args' => $this->enhancer_args,
            'item' => $form,
        );
        \Event::trigger_function('noviusos_form::rendering', array(&$args));
        if (!empty($form->form_virtual_name)) {
            \Event::trigger_function('noviusos_form::rendering.'.$form->form_virtual_name, array(&$args));
        }

        return \View::forge($layout, array(
            'item' => $form,
            'fields' => $fields,
            'enhancer_args' => $this->enhancer_args,
            'page_break_count' => $this->getFormPageBreakCount($form),
            'errors' => $errors,
            'form_attrs' => array(
                'method' => 'POST',
                'enctype' => 'multipart/form-data',
                'action' => '',
            ),
        ), false);
    }

    /**
     * Handles form answer post
     *
     * @param $form
     * @return array
     * @throws \Exception
     */
    public function post_answers($form)
    {
        // Gets the form layout
        $layout = $this->getFormLayout($form);

        // Gets the fields and their values in order of their layout position
        $data = $fields = array();
        foreach ($layout as $cols) {
            foreach ($cols as $field_layout) {
                list($field_id, ) = explode('=', $field_layout);

                // Gets the field
                $field = \Arr::get($form->fields, $field_id);
                if (!empty($field)) {

                    // Gets the field driver
                    $fieldDriver = $field->getDriver($this->enhancer_args);
                    if (!empty($fieldDriver)) {

                        // Gets field name and value
                        $value = $fieldDriver->getInputValue();
                        $name = $fieldDriver->getVirtualName();

                        $data[$name] = $value;
                        $fields[$name] = $field;
                    }
                }
            }
        }

        // Validates the form fields
        $errors = $this->validateFormFieldsData($form, $fields, $data);
        if (!empty($errors)) {
            foreach ($errors as $name => &$error) {
                if (empty($fields[$name]) || $name == 'form_captcha') {
                    continue;
                }

                $value = \Arr::get($data, $name);

                $fieldDriver = $fields[$name]->getDriver();
                if (!empty($fieldDriver)) {
                    $value = $fieldDriver->renderErrorValueHtml($value);
                }

                $error = strtr($error, array(
                    '{{label}}' => $fields[$name]->field_label,
                    '{{value}}' => $value,
                ));
            }
            return $errors;
        }

        // Submits the form
        if ($this->beforeSubmission($form, $fields, $data)) {

            // Forges the new answer
            $answer = Model_Answer::forge(array(
                'answer_ip' => \Input::real_ip(),
            ), true);

            $answer->form = $form;

            // Fields pre processing on answer
            foreach ($fields as $name => $field) {
                $fieldDriver = $field->getDriver($this->enhancer_args);
                if (!empty($fieldDriver)) {
                    // Handles fields pre processing on answer
                    $fieldDriver->beforeAnswerSave($answer, \Arr::get($data, $name), $data);
                }
            }

            // Saves the answer
            $answer->save();

            // Fields post processing on answer
            foreach ($fields as $name => $field) {
                $fieldDriver = method_exists($field, 'getDriver') ? $field->getDriver($this->enhancer_args) : null;
                if (!empty($fieldDriver)) {

                    // Handles fields post processing on answer
                    $fieldDriver->afterAnswerSave($answer, \Arr::get($data, $name), $data);

                    // Handles fields attachments on answer
                    if ($fieldDriver instanceof Interface_Driver_Field_Attachment) {
                        $fieldDriver->saveAttachments($answer);
                    }
                }
            }

            // Creates the answer fields
            foreach ($fields as $name => $field) {
                 Model_Answer_Field::forge(array(
                    'anfi_answer_id' => $answer->answer_id,
                    'anfi_field_id' => $field->field_id,
                    'anfi_field_driver' => $field->field_driver,
                    'anfi_value' => \Arr::get($data, $name),
                ), true)->save();
            }

            // Sends the answer mail
            $this->sendAnswerMail($answer, $fields, $data);

            // after_submission
            \Event::trigger('noviusos_form::after_submission', array(
                'answer' => $answer,
                'enhancer_args' => $this->enhancer_args,
                0 => $answer, //For consistency. Deprecated. To remove when made BC
                1 => $this->enhancer_args, //For consistency. Deprecated. To remove when made BC
            ));
            if (!empty($form->form_virtual_name)) {
                \Event::trigger('noviusos_form::after_submission.'.$form->form_virtual_name, array(
                    'answer' => $answer,
                    'enhancer_args' => $this->enhancer_args,
                    0 => $answer, //For consistency. Deprecated. To remove when made BC
                    1 => $this->enhancer_args, //For consistency. Deprecated. To remove when made BC
                ));
            }
        }

        return $errors;
    }

    /**
     * Gets the form layout
     *
     * @param Model_Form $form
     * @return array
     */
    protected function getFormLayout(Model_Form $form)
    {
        // Gets the form layout
        $layout = explode("\n", $form->form_layout);
        array_walk($layout, function (&$v) {
            $v = explode(',', $v);
        });

        // Cleanup empty values
        foreach ($layout as $a => $rows) {
            $layout[$a] = array_filter($rows);
            if (empty($layout[$a])) {
                unset($layout[$a]);
                continue;
            }
        }

        return $layout;
    }

    /**
     * Gets the form fields
     *
     * @param Model_Form $form
     * @param $errors
     * @return array
     */
    protected function getFormFields(Model_Form $form, $errors)
    {
        // Gets the form layout
        $layout = $this->getFormLayout($form);

        // Adds the captcha to layout if enabled
        if ($form->form_captcha) {
            $layout[] = array('captcha=4');
        }

        // Adds the hidden form id to layout
        $layout[] = array('_form_id=4');

        // Builds the fields list from the layout
        $fields = array();
        $new_page = $this->getFormPageBreakCount($form) > 0;
        foreach ($layout as $rows) {
            $first_col = true;
            $col_width = 0;
            // ...and cols
            foreach ($rows as $row) {
                list($field_id, $width) = explode('=', $row);

                $available_width = $width * 3;
                $col_width += $available_width;

                // Page break
                if ($field_id == 'page_break') {
                    $new_page = true;
                    continue;
                }

                // Captcha
                elseif ($field_id == 'captcha') {
                    $field = Model_Field::forge(array(
                        'field_name' => 'form_captcha',
                        'field_label' => '',
                        'field_driver' => Driver_Field_Input_Text::class,
                        'field_mandatory' => '1',
                        'field_technical_id' => '',
                        'field_technical_css' => '',
                        'field_default_value' => '',
                        'field_virtual_name' => 'form_captcha',
                    ));
                }

                // Hidden form id
                else if ($field_id == '_form_id') {
                    $field = Model_Field::forge(array(
                        'field_label' => '',
                        'field_driver' => Driver_Field_Hidden::class,
                        'field_mandatory' => '1',
                        'field_technical_id' => '',
                        'field_technical_css' => '',
                        'field_default_value' => $form->form_id,
                        'field_origin' => '',
                        'field_virtual_name' => $field_id,
                    ));
                }

                // Other fields
                else {
                    $field = $form->fields[$field_id];
                }

                // Gets the driver
                $fieldDriver = $field->getDriver($this->enhancer_args);
                if (!empty($fieldDriver)) {

                    // Gets the field name
                    $name = $fieldDriver->getVirtualName();

                    // Sets the errors
                    $fieldDriver->setErrors(\Arr::get($errors, $name, array()));

                    // Gets the input value or the default value
                    $value = $fieldDriver->getInputValue($fieldDriver->getDefaultValue());

                    // Builds the field
                    $fields[$name] = array(
                        'label' => $fieldDriver->getLabel(),
                        'field' => $fieldDriver->getHtml($value),
                        'instructions' => $fieldDriver->getInstructions(),
                        'new_row' => $first_col,
                        'new_page' => $new_page,
                        'width' => $width,
                        'item' => $field,
                    );

                    $first_col = false;
                    $new_page = false;
                }
            }
        }

        return $fields;
    }

    /**
     * Validates the form fields data
     *
     * @param Model_Form $form
     * @param array $fields
     * @param array $data
     * @return array
     */
    protected function validateFormFieldsData(Model_Form $form, array $fields, array &$data)
    {
        $errors = array();

        // Fields validation
        foreach ($data as $name => $value) {
            $field = \Arr::get($fields, $name);

            // Gets the validation errors
            $errors = \Arr::merge($errors, Service_Field::forge($field)->getValidationErrors($value, $data));
        }

        // Custom validation
        foreach ((array) \Event::trigger_function('noviusos_form::data_validation', array(&$data, $fields, $form), 'array') as $array) {
            if ($array === null) {
                continue;
            }
            foreach ($array as $name => $error) {
                $errors[$name] = $error.(isset($errors[$name]) ? "\n".$errors[$name] : '');
            }
        }
        if (!empty($form->form_virtual_name)) {
            foreach ((array) \Event::trigger_function('noviusos_form::data_validation.'.$form->form_virtual_name, array(&$data, $fields, $form), 'array') as $array) {
                if ($array === null) {
                    continue;
                }
                foreach ($array as $name => $error) {
                    $errors[$name] = (isset($errors[$name]) ? $errors[$name]."\n" : '').$error;
                }
            }
        }

        // Captcha validation
        if ($form->form_captcha && \Session::get('captcha.'.$form->form_id) != \Input::post('form_captcha', 0)) {
            $errors['form_captcha'] = __('You have not passed the spam test. Please try again.');
        }

        return $errors;
    }

    /**
     * Triggers some callbacks before submission to know if we should continue
     *
     * @param Model_Form $form
     * @param array $fields
     * @param array $data
     * @return bool
     */
    protected function beforeSubmission(Model_Form $form, array $fields, array &$data)
    {
        // Triggers the fields method
        foreach ($fields as $name => $field) {
            $fieldDriver = method_exists($field, 'getDriver') ? $field->getDriver($this->enhancer_args) : null;
            if (!empty($fieldDriver)) {
                $fieldDriver->beforeFormSubmission($form, \Arr::get($data, $name), $data);
            }
        }

        // Triggers the global event
        $before_submission = (array) \Event::trigger_function('noviusos_form::before_submission', array(&$data, $form, $this->enhancer_args), 'array');

        // Triggers the form event
        if (!empty($form->form_virtual_name)) {
            $before_submission = array_merge((array) \Event::trigger_function('noviusos_form::before_submission.'.$form->form_virtual_name, array(&$data, $form, $this->enhancer_args), 'array'));
        }

        // We only save the answer into the database if none before_submission callback returned 'false'
        $before_submission = array_filter($before_submission, function ($val) {
            return $val === false;
        });
        return count($before_submission) == 0;
    }

    /**
     * Sends a mail for the specified answer
     *
     * @param Model_Answer $answer
     * @param array $fields
     * @param array $data
     * @return bool
     * @throws \EmailValidationFailedException
     * @throws \FuelException
     * @throws \InvalidAttachmentsException
     */
    protected function sendAnswerMail(Model_Answer $answer, array $fields, array $data)
    {
        $form = $answer->form;
        if (empty($form)) {
            return false;
        }

        // Gets the recipient list
        $config = \Config::load('noviusos_form::noviusos_form', true);
        $recipients = array_filter(explode("\n", $form->form_submit_email), function ($var) {
            $var = trim($var);
            return !empty($var);
        });

        // Sends an email if there is at least one recipient in list
        if (empty($recipients)) {
            return false;
        }

        // Creates the mail
        $mail = \Email::forge();

        $email_data = array();
        $reply_to_auto = \Arr::get($config, 'add_replyto_to_first_email', true);
        $reply_to = '';
        $attachments = array();
        foreach ($data as $name => $value) {
            $field = \Arr::get($fields, $name);

            // Builds the field email data
            $email_data[] = array(
                'label' => $field->field_label,
                'value' => $value
            );

            $fieldDriver = method_exists($field, 'getDriver') ? $field->getDriver($this->enhancer_args) : null;
            if (!empty($fieldDriver)) {

                // Gets the first non empty email as potential "reply_to"
                if ($reply_to_auto && empty($reply_to) && $fieldDriver instanceof Interface_Driver_Field_Email) {
                    $reply_to = $fieldDriver->getEmail();
                }

                // Gets the field attachments
                if ($fieldDriver instanceof Interface_Driver_Field_Attachment) {
                    $attachments = \Arr::merge($attachments, $fieldDriver->getAttachments($answer));
                }

                // Builds the field email data
                $email_data[] = $fieldDriver->getEmailData();
            }
        }

        // Adds attachments to the mail
        if (!empty($attachments)) {
            // Calculates total size of attachments
            $totalSize = array_sum(array_map(function($attachment) {
                return filesize($attachment->path());
            }, $attachments));
            // Checks if total size of attachments does not exceed the limit
            if ($totalSize <= \Arr::get($config, 'mail_attachments_max_size', 8388608)) {
                // Adds each attachment to the mail
                foreach ($attachments as $attachment) {
                    $mail->attach($attachment->path());
                }
            }
        }

        // Sets recipient list as BCC
        $mail->bcc($recipients);

        if (!empty($reply_to)) {
            $mail->reply_to($reply_to);
        }

        // Sets subject
        $mail->subject(strtr(__('{{form}}: New answer'), array(
            '&nbsp;' => ' ',
            '{{form}}' => $form->form_name,
        )));

        // Sets body
        $mail->html_body(\View::forge('noviusos_form::front/form/email', array(
            'form' => $form,
            'data' => $email_data,
        ), false));

        // Sends the mail
        try {
            $mail->send();
        } catch (\Exception $e) {
            logger(\Fuel::L_ERROR, 'The Forms application cannot send emails - '.$e->getMessage());
            return false;
        }

        return true;
    }

    /**
     * Gets the form page break count
     *
     * @param Model_Form $form
     * @return int
     */
    protected function getFormPageBreakCount(Model_Form $form)
    {
        // Gets the form layout
        $layout = $this->getFormLayout($form);

        // Counts the page breaks in layout
        $count = 0;
        foreach ($layout as $rows) {
            foreach ($rows as $row) {
                list($field_id, ) = explode('=', $row);
                if ($field_id == 'page_break') {
                    $count++;
                }
            }
        }

        return $count;
    }
}
