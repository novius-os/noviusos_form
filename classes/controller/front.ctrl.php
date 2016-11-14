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

class Controller_Front extends \Nos\Controller_Front_Application
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

        // Gets the form ID
        $form_id = $enhancer_args['form_id'];
        if (empty($form_id)) {
            return '';
        }

        // Finds the form
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
            // Don't store the page in cache when one field is pre-filled with a parameter
            // This allows to still use the cache for the "empty" version of the form
            if (!empty($field->field_origin_var) && in_array($field->field_origin, array('get', 'request'))) {
                $request_parameters_to_exclude_from_cache[] = $field->field_origin_var;
                $defaultValue = $field->getDriver($this->enhancer_args)->getDefaultValue();
                if (!empty($defaultValue) && $defaultValue != $field->field_default_value) {
                    \Nos\Nos::main_controller()->disableCaching();
                }
            }
        }
        \Nos\Nos::main_controller()->addCacheSuffixHandler(array(
            'type' => 'GET',
            'keys' => $request_parameters_to_exclude_from_cache,
        ));

        // Displays the confirmation message
        if (\Input::get('message', 0) == $form_id) {
            return $this->render_confirmation($item);
        }
        // Displays the form
        else {
            return $this->render_form($item, $errors);
        }
    }

    /**
     * Renders the confirmation message
     *
     * @param Model_Form $form
     * @return \Fuel\Core\View
     */
    public function render_confirmation(Model_Form $form)
    {
        return \View::forge('noviusos_form::front/form/message', array(
            'form' => $form,
            'message' => \Arr::get($this->enhancer_args, 'confirmation_message', __('Thank you. Your answer has been sent.'))
        ), false);
    }

    /**
     * Renders the form
     *
     * @param Model_Form $form
     * @param $errors
     * @return \Fuel\Core\View
     */
    protected function render_form(Model_Form $form, $errors)
    {
        // Gets the form fields layout
        $fieldsLayout = $form->getService()->getFieldsLayout($errors, $this->enhancer_args);

        // Initializes conditional fields
        $fields = array();
        foreach ($fieldsLayout as $rows) {
            foreach ($rows as $cols) {
                foreach ($cols as $field) {
                    if (is_a($field['item'], 'Nos\Form\Model_Field') && filter_var($field['item']->get('field_conditional'), FILTER_VALIDATE_BOOLEAN)) {
                        $fieldDriver = $field['item']->getDriver($this->enhancer_args);
                        $json = \Fuel\Core\Format::forge(array(
                            'inputname' => $field['item']->get('field_conditional_form'),
                            'condition' => $fieldDriver->getVirtualName(),
                            'value' => $field['item']->get('field_conditional_value')
                        ))->to_json();
                        \Nos\Nos::main_controller()->addJavascriptInline("init_form_condition($json);");
                    }
                    $fields[] = $field;
                }
            }
        }

        // Gets the front form layout
        $formLayout = $this->getFormLayoutConfig();

        // Triggers an event to allow fields and layout manipulation
        $args = array(
            'item' => $form,
            'formLayout' => &$formLayout,
            'layout' => &$fieldsLayout,
            'enhancer_args' => $this->enhancer_args,
        );
        \Event::trigger_function('noviusos_form::rendering', array(&$args));
        if (!empty($form->form_virtual_name)) {
            \Event::trigger_function('noviusos_form::rendering.'.$form->form_virtual_name, array(&$args));
        }

        // Gets the minimum label width for each page
        $labelWidthPerPage = array();
        foreach ($fieldsLayout as $page => $rows) {
            foreach ($rows as $cols) {
                foreach ($cols as &$field) {
                    // Label width will be set according to the smallest columns
                    $labelWidthPerPage[$page] = isset($labelWidthPerPage[$page]) ? min($labelWidthPerPage[$page], $field['width']) : $field['width'];
                }
            }
        }

        // Gets the view and params
        $view = \Arr::get($formLayout, 'view');
        $view_params = \Arr::get($formLayout, 'view_params', array());

        return \View::forge($view, \Arr::merge($view_params, array(
            'form' => $form,
            'fields' => \Arr::pluck($fields, 'item', 'name'),
            'fieldsLayout' => $fieldsLayout,
            'labelWidthPerPage' => $labelWidthPerPage,
            'errors' => $errors,
            'enhancer_args' => $this->enhancer_args,
            'stylesheetUrl' => \Arr::get($this->config, 'stylesheet_url'),
            'scriptUrl' => \Arr::get($this->config, 'script_url'),
            'form_attrs' => array(
                'method' => 'POST',
                'enctype' => 'multipart/form-data',
                'action' => '',
                'data-locale' => \Nos\Form\Helper_Front_Form::getParsleyLocale(\Nos\Nos::main_controller()->getContext()),
            ),
        )), false);
    }

    /**
     * Handles form answer post
     *
     * @param Model_Form $form
     * @return array
     * @throws \Exception
     */
    protected function post_answers(Model_Form $form)
    {
        // Gets the fields and their values in order of their layout position
        $data = $fields = array();
        foreach ($form->getService()->getLayoutFieldsName() as $field_name) {
            // Gets the field
            $field = \Arr::get($form->fields, $field_name);
            if (!empty($field)) {

                // Gets the field driver
                $fieldDriver = $field->getDriver($this->enhancer_args);

                // Gets field name and value
                $value = $fieldDriver->getInputValue();
                $name = $fieldDriver->getVirtualName();

                $data[$name] = $value;
                $fields[$name] = $field;
            }
        }

        // Validates the form fields
        $errors = $form->getService()->validateFieldsData($fields, $data);
        if (!empty($errors)) {

            // Replaces errors placeholders
            foreach ($errors as $name => &$error) {
                $field = \Arr::get($fields, $name);
                if (empty($field) || $name == 'form_captcha') {
                    continue;
                }

                // Renders the value for the error string
                $value = \Arr::get($data, $name);
                $value = $field->getDriver($this->enhancer_args)->renderErrorValueHtml($value);

                $error = strtr($error, array(
                    '{{label}}' => $fields[$name]->field_label,
                    '{{value}}' => $value,
                ));
            }

            return $errors;
        }

        // Forges a new answer
        $answer = Service_Answer::forgeAnswer($form);

        // Does some checks and actions before submission
        if ($this->beforeSubmission($form, $answer, $fields, $data)) {

            // Saves the answer
            $answer->getService()->saveAnswer($fields, $data);

            // Sends a notification by mail to the form recipients
            $answer->getService()->sendNotificationByMail($fields, $data);

            // Does some actions after submission
            $this->afterSubmission($form, $answer, $fields, $data);
        }

        return $errors;
    }

    /**
     * Triggers some callbacks after submission
     *
     * @param Model_Form $form
     * @param Model_Answer $answer
     * @param array $fields
     * @param array $data
     */
    protected function afterSubmission(Model_Form $form, Model_Answer $answer, array $fields, array &$data)
    {
        // After_submission
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

    /**
     * Triggers some callbacks before submission to know if we should continue
     *
     * @param Model_Form $form
     * @param Model_Answer $answer
     * @param array $fields
     * @param array $data
     * @return bool
     */
    protected function beforeSubmission(Model_Form $form, Model_Answer $answer, array $fields, array &$data)
    {
        // Triggers the fields method
        foreach ($fields as $name => $field) {
            $field->getDriver($this->enhancer_args)->beforeFormSubmission($form, \Arr::get($data, $name), $data);
        }

        // Triggers the global event
        $before_submission = (array) \Event::trigger_function('noviusos_form::before_submission', array(
            &$data,
            $form,
            $this->enhancer_args,
            $answer,
        ), 'array');

        // Triggers the form event
        if (!empty($form->form_virtual_name)) {
            $before_submission = array_merge(
                (array) \Event::trigger_function('noviusos_form::before_submission.'.$form->form_virtual_name, array(
                    &$data,
                    $form,
                    $this->enhancer_args,
                    $answer
                ), 'array'));
        }

        // We only save the answer into the database if none before_submission callback returned 'false'
        $before_submission = array_filter($before_submission, function ($val) {
            return $val === false;
        });
        return count($before_submission) == 0;
    }

    /**
     * Gets the layout config
     *
     * @return mixed
     */
    protected function getFormLayoutConfig()
    {
        // Gets the layout name to use
        $appConfig = \Config::load('noviusos_form::config', true);
        $layoutName = \Arr::get($appConfig, 'front_layout', 'default');

        // Finds the layout config
        $layouts = \Arr::get($this->config, 'layouts', array());
        $layoutConfig = \Arr::get($layouts, $layoutName, array());

        return $layoutConfig;
    }
}
