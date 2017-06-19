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

use Fuel\Core\Crypt;
use Fuel\Core\Form;
use Nos\Controller_Front_Application;

use View;

class Controller_Front extends Controller_Front_Application
{

    protected $enhancer_args = array();

    /**
     * @var array : An associative array to keep in memory the fields' recipients list
     *
     * Example of value :
     * [FIELD_ID] => [0 => email-1@example.com, 1 => email-2@example.com,]
     */
    protected static $fieldsRecipents = array();

    public function action_main($enhancer_args = array())
    {
        \Nos\I18n::current_dictionary('noviusos_form::front');

        $this->enhancer_args = $enhancer_args;

        $form_id = $enhancer_args['form_id'];
        if (empty($form_id)) {
            return '';
        }
        $item = \Nos\Form\Model_Form::find($form_id);
        if (empty($item)) {
            return '';
        }

        $errors = array();

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
        }

        // 'message' is used to show the user a message when the form is submitted
        $request_parameters_to_exclude_from_cache = array('message');

        foreach ($item->fields as $field) {
            if (!empty($field->field_origin_var) && in_array($field->field_origin, array('get', 'request'))) {
                $request_parameters_to_exclude_from_cache[] = $field->field_origin_var;

                // Don't store the page in cache when one field is pre-filled with a parameter
                // This allows to still use the cache for the "empty" version of the form
                $value = $this->getFieldDefaultValue($field);
                if (!empty($value) && $value != $field->field_default_value) {
                    \Nos\Nos::main_controller()->disableCaching();
                }
            }
        }

        \Nos\Nos::main_controller()->addCacheSuffixHandler(array(
            'type' => 'GET',
            'keys' => $request_parameters_to_exclude_from_cache,
        ));

        // Confirmation message
        if (\Input::get('message', 0) == $form_id) {
            return \View::forge('noviusos_form::message', array(
                'message' => \Arr::get($this->enhancer_args, 'confirmation_message', __('Thank you. Your answer has been sent.'))
            ), false);
        }

        return $this->render_form($item, $errors);
    }

    public function render_form($item, $errors)
    {
        $layout = explode("\n", $item->form_layout);
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

        $fields = array();

        if ($item->form_captcha) {
            $layout[] = array('captcha=4');
        }
        $layout[] = array('_form_id=4');

        $page_break_count = 0;
        foreach ($item->fields as $field_id => $field) {
            if ($field->field_type == 'page_break') {
                $page_break_count++;
            }
        }

        $new_page = $page_break_count > 0;

        // Loop through rows...
        foreach ($layout as $rows) {
            $first_col = true;
            $col_width = 0;
            // ...and cols
            foreach ($rows as $row) {
                list($field_id, $width) = explode('=', $row);

                $available_width = $width * 3;
                $col_width += $available_width;

                if ($field_id == 'captcha') {
                    $field = (object) array(
                        'field_name' => 'form_captcha',
                        'field_label' => '',
                        'field_type' => 'text',
                        'field_mandatory' => '1',
                        'field_technical_id' => '',
                        'field_technical_css' => '',
                        'field_default_value' => '',
                        'field_virtual_name' => 'form_captcha',
                    );
                    $name = 'form_captcha';
                } else if ($field_id == '_form_id') {
                    $field = (object) array(
                        'field_label' => '',
                        'field_type' => 'hidden',
                        'field_mandatory' => '1',
                        'field_technical_id' => '',
                        'field_technical_css' => '',
                        'field_default_value' => $item->form_id,
                        'field_origin' => '',
                    );
                    $name = '_form_id';
                } else {
                    $field = $item->fields[$field_id];
                    $name = !empty($field->field_virtual_name) ? $field->field_virtual_name : 'field_'.$field->field_id;
                }

                if ($field->field_type == 'page_break') {
                    $new_page = true;
                    continue;
                }

                $html_attrs = array(
                    'id' => $field->field_technical_id ?: $name,
                    'class' => $field->field_technical_css,
                    'title' => $field->field_label,
                );

                if ($this->enhancer_args['label_position'] == 'placeholder') {
                    $html_attrs['placeholder'] = $field->field_label;
                }

                if ($field->field_mandatory) {
                    $html_attrs['required'] = 'required';
                }

                $label_attrs = array(
                    'for' => $html_attrs['id'],
                );

                $value = \Input::post($name, $this->getFieldDefaultValue($field));

                if (!empty($errors[$name])) {
                    if ($name == 'form_captcha') {
                        $value = '';
                    }
                    if ($this->enhancer_args['label_position'] == 'placeholder') {
                        $html_attrs['class'] .= ' user-error form-ui-invalid';
                        $html_attrs['title'] = htmlspecialchars($errors[$name]);
                    }
                    $label_attrs['class'] = ' user-error form-ui-invalid';
                    $label_attrs['title'] = htmlspecialchars($errors[$name]);
                }

                $html = '';

                if ($this->enhancer_args['label_position'] == 'placeholder') {
                    $label = '';
                } else {
                    $label = array(
                        'callback' => array('Form', 'label'),
                        'args' => array($field->field_label, $field->field_technical_id, $label_attrs),
                    );
                }

                if (!in_array($field->field_type, array('radio', 'checkbox', 'select')) && !empty($field->field_details)) {
                    $instructions = array(
                        'callback' => 'html_tag',
                        'args' => array('p', array('class' => 'instructions'), $field->field_details),
                    );
                } else {
                    $instructions = '';
                }

                if (in_array($field->field_type, array('text', 'textarea', 'select', 'email', 'number', 'date', 'file'))) {

                    if (in_array($field->field_type, array('text', 'email', 'number', 'date', 'file'))) {
                        $html_attrs['type'] = $field->field_type;
                        if (!empty($field->field_width)) {
                            $html_attrs['size'] = $field->field_width;
                        }
                        if (!empty($field->field_limited_to)) {
                            $html_attrs['maxlength'] = $field->field_limited_to;
                        }
                        $html = array(
                            'callback' => array('Form', 'input'),
                            'args' => array($name, $value, $html_attrs),
                        );
                    } else if ($field->field_type == 'textarea') {
                        if (!empty($field->field_height)) {
                            $html_attrs['rows'] = $field->field_height;
                        }
                        $html = array(
                            'callback' => array('Form', 'textarea'),
                            'args' => array($name, $value, $html_attrs),
                        );
                    } else if ($field->field_type == 'select') {
                        $choices = $this->getSelectFieldChoices($field, $value);
                        $html = array(
                            'callback' => array('Form', 'select'),
                            'args' => array($name, $value, $choices, $html_attrs),
                        );
                    }

                } else if (in_array($field->field_type, array('checkbox', 'radio'))) {

                    $label = array(
                        'callback' => 'html_tag',
                        'args' => array('span', $label_attrs, $field->field_label),
                    );

                    if (in_array($field->field_type, array('checkbox', 'radio'))) {
                        $html = array();
                        $default = \Input::post($name, $field->field_type == 'checkbox' ? explode("\n", $field->field_default_value) : $field->field_default_value);
                        $choices = explode("\n", $field->field_choices);
                        foreach ($choices as $i => $choice) {
                            $html_attrs_choice = $html_attrs;
                            $html_attrs_choice['id'] .= $i;
                            if ($field->field_type == 'checkbox') {
                                $item_html = array(
                                    'callback' => array('Form', 'checkbox'),
                                    'args' => array($name.'[]', $choice, in_array($choice, $default), $html_attrs_choice),
                                );
                            } else if ($field->field_type == 'radio') {
                                $item_html = array(
                                    'callback' => array('Form', 'radio'),
                                    'args' => array($name, $choice, $choice == $default, $html_attrs_choice),
                                );
                            }
                            $item_label = array(
                                'callback' => array('Form', 'label'),
                                'args' => array(
                                    $choice,
                                    $field->field_technical_id,
                                    array(
                                        'for' => $html_attrs_choice['id'],
                                    ),
                                ),
                            );
                            $html[] = array(
                                'field' => $item_html,
                                'label' => $item_label,
                                'template' => '{field} {label} <br />',
                            );
                        }
                    }
                } else if ($field->field_type == 'message') {
                    $label = '';
                    $type = in_array($field->field_style, array('p', 'h1', 'h2', 'h3')) ? $field->field_style : 'p';
                    $html_attrs = array(
                        'id' => $field->field_technical_id,
                        'class' => 'label_text '.$field->field_technical_css,
                    );
                    $html = array(
                        'callback' => 'html_tag',
                        'args' => array($type, $html_attrs, nl2br($field->field_message)),
                    );

                } else if ($field->field_type == 'separator') {
                    $label = '';
                    $html = html_tag('hr');
                } else if (in_array($field->field_type, array('hidden', 'variable'))) {
                    $value = $this->getFieldDefaultValue($field);
                    if ($field->field_type == 'hidden') {
                        $label = '';
                        $html = array(
                            'callback' => array('Form', 'hidden'),
                            'args' => array($name, e($value)),
                        );
                    } else if ($field->field_type == 'variable') {
                        $html = array(
                            'callback' => 'html_tag',
                            'args' => array('p', array(), e($value)),
                        );
                    }
                } else {
                    $label = '';
                }

                $fields[$name] = array(
                    'label' => $label,
                    'field' => $html,
                    'instructions' => $instructions,
                    'new_row' => $first_col,
                    'new_page' => $new_page,
                    'width' => $width,
                    'item' => $field,
                );
                $first_col = false;
                $new_page = false;
            }
        }

        foreach ($fields as $condition_item) {
            if (is_a($condition_item['item'], 'Nos\Form\Model_Field') && filter_var($condition_item['item']->get('field_conditional'), FILTER_VALIDATE_BOOLEAN)) {


                // get condition source form type. text, radio, etc.
                $form_key = $condition_item['item']->get('field_conditional_form');
                $inputtype = $fields[$form_key]['item']->field_type;

                $array = array(
                    'inputtype' => $inputtype,
                    'inputname' => $condition_item['item']->get('field_conditional_form'),
                    'condition' => $condition_item['item']->get('field_virtual_name'),
                    'value' => $condition_item['item']->get('field_conditional_value')
                );
                $json = \Fuel\Core\Format::forge($array)->to_json();


                \Nos\Nos::main_controller()->addJavascriptInline("init_form_condition($json);");

            }
        }

        $layout = 'noviusos_form::foundation';
        $args = array(
            'fields' => &$fields,
            'layout' => &$layout,
            'enhancer_args' => $this->enhancer_args,
            'item' => $item,
        );
        \Event::trigger_function('noviusos_form::rendering', array(&$args));
        if (!empty($item->form_virtual_name)) {
            \Event::trigger_function('noviusos_form::rendering.' . $item->form_virtual_name, array(&$args));
        }

        $layout = array(
            'layout' => $layout,
            'args' => array(
                'item' => $item,
                'fields' => $fields,
                'enhancer_args' => $this->enhancer_args,
                'page_break_count' => $page_break_count,
                'errors' => $errors,
                'form_attrs' => array(
                    'method' => 'POST',
                    'enctype' => 'multipart/form-data',
                    'action' => '',
                ),
            ),
        );

        return \View::forge($layout['layout'], $layout['args'], false);
    }

    /**
     * @param Model_Field $field : The select field
     * @param $value
     * @return array : An associative array like : [$value-1 => $label-1, ...]
     */
    protected function getSelectFieldChoices(Model_Field $field, &$value)
    {
        if ($field->field_technical_id === 'recipient-list') {
            // The field is a recipent-list : the select values are line numbers so we have to keep emails in memory
            static::$fieldsRecipents[$field->id] = array();
        }

        $choices = explode("\n", $field->field_choices);
        $choiceList = array();
        $isCrypted = false;
        foreach ($choices as $keyChoice => $choice) {
            if (mb_strrpos($choice, '=')) {
                $choiceInfos = preg_split('~(?<!\\\)=~', $choice);
                foreach ($choiceInfos as $key => $choiceValue) {
                    $choiceInfos[$key] = str_replace("\=", "=", $choiceValue);
                }
                $choiceLabel       = $choiceInfos[0];
                $choiceValue = Crypt::encode(\Arr::get($choiceInfos, 1, $choiceLabel));
                if ($field->field_technical_id === 'recipient-list') {
                    // Keep in memory the recipent e-mail
                    static::$fieldsRecipents[$field->id][$keyChoice] = \Arr::get($choiceInfos, 1, $choiceLabel);
                    // Choice value for recipient-list is line number of the choice in $field->field_choices
                    $choiceValue = $keyChoice;
                }
                $isCrypted = true;
            } else {
                if ($field->field_technical_id === 'recipient-list') {
                    // Keep in memory the recipent e-mail
                    static::$fieldsRecipents[$field->id][$keyChoice] = $choice;
                    // Choice value for recipient-list is line number of the choice in $field->field_choices
                    $choiceValue = $keyChoice;
                    $choiceLabel = $choice;
                }else{
                    $choiceLabel = $choiceValue = $choice;
                }
            }
            $choiceList[$choiceValue] = $choiceLabel;
        }

        if ($isCrypted && $field->field_technical_id !== 'recipient-list') {
            $value = Crypt::encode($value);
        }

        return array('' => '') + $choiceList;
    }

    protected function getFieldDefaultValue($field)
    {
        if (!in_array($field->field_type, array('text', 'email', 'number', 'textarea', 'hidden', 'variable'))) {
            return $field->field_default_value;
        }

        $value = $field->field_default_value;

        // When the parameter name is not filled, there is nothing more to retrieve
        if (empty($field->field_origin_var)) {
            return $value;
        }

        switch($field->field_origin) {
            case 'get':
                $value = \Input::get($field->field_origin_var, $field->field_default_value);
                break;

            case 'post':
                $value = \Input::post($field->field_origin_var, $field->field_default_value);
                break;

            case 'request':
                $value = \Input::param($field->field_origin_var, $field->field_default_value);
                break;

            case 'global':
                $value = \Arr::get($GLOBALS, $field->field_origin_var, $field->field_default_value);
                break;

            case 'session':
                $value = \Session::get($field->field_origin_var, $field->field_default_value);
                break;

            default:
        }

        return $value;
    }

    public function post_answers($form)
    {
        $errors = array();
        $data = array();
        $fields = array();
        $files = array();


        $layout = $form->form_layout;
        $layout = explode("\n", $layout);
        array_walk($layout, function (&$v) {
            $v = explode(',', $v);
        });

        // Fetching the fields according to their layout position
        foreach ($layout as $cols) {
            foreach ($cols as $field_layout) {
                list($field_id, ) = explode('=', $field_layout);
                $field = $form->fields[$field_id];
                $type = $field->field_type;
                if (in_array($type, array('message', 'variable', 'separator', 'page_break'))) {
                    continue;
                }
                $name = !empty($field->field_virtual_name) ? $field->field_virtual_name : 'field_'.$field->field_id;
                $value = null;

                if ($type === 'file' && !empty($_FILES[$name])) {
                    if ($field->isMandatory($data) && empty($_FILES[$name]['tmp_name'])) {
                        $errors[$name] = __('{{label}}: Please select a file for this field.');
                    }
                    $files[$name] = $_FILES[$name];
                } else {
                    switch($type) {
                        case 'checkbox':
                            $value = implode("\n", \Input::post($name, array()));
                            break;

                        default:
                            $value = \Input::post($name, '');
                            if (in_array($type, array('select')) && mb_strpos($field->field_choices, '=')) {
                                if($field->field_technical_id === 'recipient-list' && $value !== '') {
                                    $tmp = '';
                                    $this->getSelectFieldChoices($field, $tmp); // Generate recipients list in static variable
                                    $value = \Arr::get(static::$fieldsRecipents, $field->id.'.'.((int) $value), '');
                                }else{
                                    $value = Crypt::decode($value);
                                }
                            }
                            break;
                    }

                    $data[$name] = $value;
                }
                $fields[$name] = $field;
            }
        }

        // Native validation
        foreach ($data as $name => $value) {
            $field = $fields[$name];
            $type = $field->field_type;

            if ($field->field_technical_id === 'recipient-list') {
                if (preg_match("/".preg_quote($value)."$/m", $field->field_choices) && !empty($value)) {
                    if (filter_var($value, FILTER_VALIDATE_EMAIL)) {
                        // Override form_submit_email only if selected option is an email
                        $form->form_submit_email = $value;
                    }
                }
            }

            // Mandatory (required)
            if ($field->isMandatory($data) && empty($value)) {
                $errors[$name] = __('{{label}}: Please enter a value for this field.');
            } else if (!empty($value)) {
                // Only if there is a value
                if ($type == 'email' && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                    $errors[$name] = __('{{label}}: ‘{{value}}’ is not a valid email.');
                }
                if ($type == 'number' && !filter_var($value, FILTER_VALIDATE_INT)) {
                    $errors[$name] = __('{{label}}: ‘{{value}}’ is not a valid number.');
                }
                if ($type == 'date') {
                    if ($checkdate = preg_match('`^(\d{4})-(\d{2})-(\d{2})$`', $value, $m)) {
                        list(, $year, $month, $day) = $m;
                        $checkdate = checkdate((int) $month, (int) $day, (int) $year);
                    }
                    if (!$checkdate) {
                        $errors[$name] = __('{{label}}: ‘{{value}}’ is not a valid date.');
                    }
                }
            }
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
            foreach ((array) \Event::trigger_function('noviusos_form::data_validation.' . $form->form_virtual_name, array(&$data, $fields, $form), 'array') as $array) {
                if ($array === null) {
                    continue;
                }
                foreach ($array as $name => $error) {
                    $errors[$name] = (isset($errors[$name]) ? $errors[$name]."\n" : '').$error;
                }
            }
        }

        if ($form->form_captcha && \Session::get('captcha.'.$form->form_id) != \Input::post('form_captcha', 0)) {
            $errors['form_captcha'] = __('You have not passed the spam test. Please try again.');
        }

        // Some validation errors occurred
        if (!empty($errors)) {
            foreach ($errors as $name => &$error) {
                if ($name == 'form_captcha') {
                    continue;
                }
                $error = strtr($error, array(
                    '{{label}}' => $fields[$name]->field_label,
                    '{{value}}' => $data[$name],
                ));
            }
            return $errors;
        }

        // data pre-processing
        $before_submission = (array) \Event::trigger_function('noviusos_form::before_submission', array(&$data, $form, $this->enhancer_args), 'array');
        if (!empty($form->form_virtual_name)) {
            $before_submission = array_merge((array) \Event::trigger_function('noviusos_form::before_submission.' . $form->form_virtual_name, array(&$data, $form, $this->enhancer_args), 'array'));
        }

        $before_submission = array_filter($before_submission, function ($val) {
            return $val === false;
        });

        // We only save the answer into the database if none before_submission callback returned 'false'
        if (count($before_submission) == 0) {
            $answer = Model_Answer::forge(array(
                'answer_form_id' => $form->form_id,
                'answer_ip' => \Input::real_ip(),
            ), true);
            $answer->save();

            $email_data = array();


            $config = \Config::load('noviusos_form::noviusos_form', true);
            $emails = array_filter(explode("\n", $form->form_submit_email), function ($var) {
                $var = trim($var);
                return !empty($var);
            });
            $sendMail = !empty($emails);
            $addAttachment = true;
            if ($sendMail) {
                $mail = \Email::forge();
                $attachmentMaxSize = \Arr::get($config, 'mail_attachments_max_size', 8388608);
                $size = 0;
                foreach ($files as $file) {
                    if (!empty($file['tmp_name'])) {
                        $size += filesize($file['tmp_name']);
                    }
                }
                if ($size > $attachmentMaxSize) {
                    $addAttachment = false;
                }
            }

            foreach ($files as $name => $file) {
                if (!empty($file['tmp_name'])) {
                    $attachment = $answer->getAttachment($fields[$name]);
                    $attachment->set($file['tmp_name'], $file['name']);
                    $attachment->save();
                    if ($sendMail && $addAttachment) {
                        $mail->attach($attachment->path());
                    }
                }
            }

            $reply_to_auto = \Arr::get($config, 'add_replyto_to_first_email', true);
            $reply_to = '';
            foreach ($data as $field_name => $value) {
                $field = $fields[$field_name];
                $email_data[] = array(
                    'label' => $field->field_label,
                    'value' => $value
                );
                if ($reply_to_auto && $field->field_type === 'email' && !empty($value) && empty($reply_to)) {
                    // save first non empty email as potential "reply_to"
                    $reply_to = $value;
                }
                $answer_field = Model_Answer_Field::forge(array(
                    'anfi_answer_id' => $answer->answer_id,
                    'anfi_field_id' => $field->field_id,
                    'anfi_field_type' => $field->field_type,
                    'anfi_value' => $value,
                ), true);
                $answer_field->save();
            }


            if ($sendMail) {
                $mail->bcc($emails);
                if (!empty($reply_to)) {
                    $mail->reply_to($reply_to);
                }
                $mail->html_body(\View::forge('noviusos_form::email', array('form' => $form, 'data' => $email_data), false));
                $mail->subject(strtr(__('{{form}}: New answer'), array(
                    '&nbsp;' => ' ',
                    '{{form}}' => $form->form_name,
                )));
                try {
                    $mail->send();
                } catch (\Exception $e) {
                    logger(\Fuel::L_ERROR, 'The Forms application cannot send emails - '.$e->getMessage());
                }
            }

            // after_submission
            \Event::trigger('noviusos_form::after_submission', array(
                'answer' => $answer,
                'enhancer_args' => $this->enhancer_args,
                0 => $answer, //For consistency. Deprecated. To remove when made BC
                1 => $this->enhancer_args, //For consistency. Deprecated. To remove when made BC
            ));
            if (!empty($form->form_virtual_name)) {
                \Event::trigger('noviusos_form::after_submission.' . $form->form_virtual_name, array(
                    'answer' => $answer,
                    'enhancer_args' => $this->enhancer_args,
                    0 => $answer, //For consistency. Deprecated. To remove when made BC
                    1 => $this->enhancer_args, //For consistency. Deprecated. To remove when made BC
                ));
            }
        }

        return $errors;
    }
}
