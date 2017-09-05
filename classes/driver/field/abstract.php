<?php

namespace Nos\Form;

use Fuel\Core\Form;

abstract class Driver_Field_Abstract
{
    /**
     * @var Model_Field
     */
    protected $field;

    protected $errors = array();
    protected $options = array();

    /**
     * The driver config
     *
     * @var array
     */
    protected static $config = array();

    /**
     * Constructor
     *
     * @param Model_Field $field
     * @param array $options
     */
    public function __construct(Model_Field $field, $options = array())
    {
        $this->field = $field;
        $this->options = $options;
    }

    /*
     * Forges a new instance
     *
     * @param Model_Field $field
     * @param array $options
     * @return static
     */
    public static function forge(Model_Field $field, $options = array())
    {
        return new static($field, $options);
    }

    /**
     * Gets the field html
     *
     * @param mixed|null $inputValue
     * @param array $formData
     * @return mixed
     */
    abstract public function getHtml($inputValue = null, $formData = array());

    /**
     * Gets the HTML preview
     *
     * @return string
     */
    abstract public function getPreviewHtml();

    /**
     * Gets the HTML identifier
     *
     * @return string
     */
    public function getHtmlId()
    {
        return $this->field->field_technical_id ?: $this->getVirtualName();
    }

    /**
     * Gets the field instructions
     *
     * @return array
     */
    public function getInstructions()
    {
        if (!empty($this->field->field_details)) {
            return array(
                'callback' => 'html_tag',
                'args' => array('p', array('class' => 'instructions'), $this->field->field_details),
            );
        } else {
            return '';
        }
    }

    /**
     * Gets the label
     *
     * @return mixed
     */
    public function getLabel()
    {
        $label_attrs = array(
            'for' => $this->getHtmlId(),
        );

        // Errors
        if ($this->hasErrors()) {
            $label_attrs['class'] = ' user-error form-ui-invalid';
            $label_attrs['title'] = nl2br(htmlspecialchars(implode("\n", $this->getErrors())));
        }

        $content = $this->field->field_label;

        // Adds an asterisk if mandatory
        if ($this->isMandatory()) {
            $content .= '<span class="required">*</span>';
        }

        $html = Form::label($content, $this->getHtmlId(), $label_attrs);

        return $html;
    }

    /**
     * Gets the field virtual name
     *
     * @return string
     */
    public function getVirtualName()
    {
        return $this->field->getInputName();
    }

    /**
     * Renders the value as html for a string error message
     *
     * @param $value
     * @return string
     */
    public function renderErrorValueHtml($value)
    {
        $value = $this->sanitizeValue($value);
        if (is_array($value)) {
            $value = implode(', ', $value);
        }

        return (string) $value;
    }

    /**
     * Gets the sanitized input value
     *
     * @param string|null $defaultValue
     * @return mixed
     */
    public function getInputValue($defaultValue = null)
    {
        $value = \Input::post($this->getVirtualName(), $defaultValue);
        $value = $this->sanitizeValue($value);

        return $value;
    }

    /**
     * Gets the field default value (no var origin)
     *
     * @return array|mixed|\Nos\Orm\Model|null
     */
    public function getFieldDefaultValue()
    {
        // Gets the default value
        $defaultValue = $this->field->field_default_value;

        // Gets the first value if it's an array (this case happens when switching between a driver
        // that handles multiple default values to a driver that handles only a single default value)
        if (is_array($defaultValue)) {
            $defaultValue = reset($defaultValue);
        }

        return $defaultValue;
    }

    /**
     * Gets the default value
     *
     * @param null $defaultValue
     * @return mixed
     */
    public function getDefaultValue($defaultValue = null)
    {
        // Gets the default value
        $defaultValue = is_null($defaultValue) ? $this->getFieldDefaultValue() : $defaultValue;

        // Sets the default value from the configured origin
        if (!empty($this->field->field_origin_var)) {
            switch ($this->field->field_origin) {
                case 'get':
                    $defaultValue = \Input::get($this->field->field_origin_var, $defaultValue);
                    break;

                case 'post':
                    $defaultValue = \Input::post($this->field->field_origin_var, $defaultValue);
                    break;

                case 'request':
                    $defaultValue = \Input::param($this->field->field_origin_var, $defaultValue);
                    break;

                case 'global':
                    $defaultValue = \Arr::get($GLOBALS, $this->field->field_origin_var, $defaultValue);
                    break;

                case 'session':
                    $defaultValue = \Session::get($this->field->field_origin_var, $defaultValue);
                    break;

                default:
            }
        }

        $defaultValue = $this->sanitizeValue($defaultValue);

        return $defaultValue;
    }

    /**
     * Checks if field is mandatory
     *
     * @return bool
     */
    public function isMandatory()
    {
        return !empty($this->field->field_mandatory);
    }

    /**
     * Checks if conditions are met to display this field with the specified form data
     *
     * @param array|null $formData
     * @return bool
     */
    public function checkDisplayable($formData = null)
    {
        // Checks if conditional field
        if (!is_null($formData)
            && !empty($this->field->field_conditional)
            && !empty($this->field->field_conditional_form)
            && !empty($this->field->field_conditional_value)) {

            // Retrieve the conditionnal field
            $conditional_field = $this->field->query()
                ->where('field_form_id', $this->field->field_form_id)
                ->where('field_virtual_name', $this->field->field_conditional_form)
                ->get_one();
            if (empty($conditional_field)) {
                return false;
            }

            // Gets the conditional field driver
            $conditionalFieldValue = \Arr::get($formData, $conditional_field->getDriver($this->getOptions())->getVirtualName());

            // Checks if the conditional field value match the expected value
            if ($conditionalFieldValue != $this->field->field_conditional_value) {
                return false;
            }
        }

        return true;
    }

    /**
     * Checks the requirement state or the specified form data
     *
     * @param $inputValue
     * @param array|null $formData
     * @return bool Returns true if successfully checked
     */
    public function checkRequirement($inputValue, $formData = null)
    {
        if (!$this->isMandatory()) {
            return true;
        }

        return is_string($inputValue) && $inputValue !== '';
    }

    /**
     * Checks the validation state for the specified form data
     *
     * @param $inputValue
     * @param array|null $formData
     * @return bool Returns true if successfully checked
     */
    public function checkValidation($inputValue, $formData = null)
    {
        return true;
    }

    /**
     * Sets the field errors
     *
     * @param $errors
     * @return $this
     */
    public function setErrors($errors)
    {
        $this->errors = (array) $errors;

        return $this;
    }

    /**
     * Sets the field errors
     *
     * @return mixed
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * Sets the field errors
     *
     * @return bool
     */
    public function hasErrors()
    {
        return !empty($this->errors);
    }

    /**
     * Triggered before form submission
     *
     * @param Model_Form $form
     * @param null $inputValue
     * @param null $formData
     */
    public function beforeFormSubmission(Model_Form $form, $inputValue = null, $formData = null)
    {
    }

    /**
     * Renders the answer as a string (eg. for displaying in backoffice)
     *
     * @param Model_Answer_Field $answerField
     * @return string
     */
    public function renderAnswerHtml(Model_Answer_Field $answerField)
    {
        $value = $this->sanitizeValue($answerField->value);
        if (is_array($value)) {
            $value = implode(', ', $value);
        }

        return e($value);
    }

    /**
     * Renders the answer as a string for export
     *
     * @param Model_Answer_Field $answerField
     * @return string|array
     */
    public function renderExportValue(Model_Answer_Field $answerField)
    {
        $value = $this->sanitizeValue($answerField->value);
        if (is_array($value)) {
            $value = implode(', ', $value);
        }

        return $value;
    }

    /**
     * Triggered before answer save
     *
     * @param Model_Answer $answer
     * @param null mixed|null $inputValue
     * @param null mixed|null $fomData
     */
    public function beforeAnswerSave(Model_Answer $answer, $inputValue = null, $fomData = null)
    {
    }

    /**
     * Triggered after answer save
     *
     * @param Model_Answer $answer
     * @param null mixed|null $inputValue
     * @param null mixed|null $fomData
     */
    public function afterAnswerSave(Model_Answer $answer, $inputValue = null, $fomData = null)
    {
    }

    /**
     * Gets the reply-to email address for the mail sent at submission
     */
    public function getEmailReplyTo()
    {
        return false;
    }

    /**
     * Gets the data for the mail sent at submission
     *
     * @param $inputValue
     * @return array
     */
    public function getEmailData($inputValue, Model_Answer $answer)
    {
        $value = $this->sanitizeValue($inputValue);

        return array(
            'label' => $this->field->field_label,
            'value' => $value,
        );
    }

    /**
     * Gets the driver config
     *
     * @return mixed
     */
    public static function getConfig()
    {
        $class = get_called_class();
        if (!isset(static::$config[$class])) {
            try {
                list($app, $file) = \Config::configFile($class);
                \Arr::set(static::$config, $class, \Config::load($app.'::'.$file, true));
            } catch (\Exception $e) {
                \Arr::set(static::$config, $class, array());
            }
        }

        return \Arr::get(static::$config, $class, array());
    }

    /**
     * Gets the admin fields meta config (merged with the default config)
     *
     * @return array
     */
    public function getAdminFieldsMetaConfig()
    {
        // Gets the default app fields meta config
        $appConfig = \Config::load('noviusos_form::controller/admin/form', true);
        $fieldsConfig = \Arr::get($appConfig, 'fields_config.fields', array());

        // Merges with the driver's config
        $fieldsConfig = \Arr::merge($fieldsConfig, \Arr::get($this->getConfig(), 'admin.fields', array()));

        return $fieldsConfig;
    }

    /**
     * Gets the admin layout meta config (merged with the default config)
     *
     * @return array
     */
    public function getAdminLayoutMetaConfig()
    {
        // Gets the default app layout meta config
        $appConfig = \Config::load('noviusos_form::controller/admin/form', true);
        $layoutConfig = \Arr::get($appConfig, 'fields_config.layout');

        // Merges with the field meta layout
        $metaLayout = \Arr::get($this->getConfig(), 'admin.layout', array());
        if (!empty($metaLayout)) {
            $layoutAccordions = \Arr::get($layoutConfig, 'standard.params.accordions', array());

            foreach ($metaLayout as $name => $params) {
                if (isset($layoutAccordions[$name])) {
                    // Merges the configs
                    $layoutAccordions[$name] = \Arr::merge($layoutAccordions[$name], $params);

                    // If fields are specified in the driver config they have to replace the default fields
                    if (isset($params['fields'])) {
                        $layoutAccordions[$name]['fields'] = $params['fields'];
                    }

                    // Deletes the field_driver if present (it will be pushed again later, see below)
                    if (isset($layoutAccordions[$name]['fields'])) {
                        $key = array_search('field_driver', $layoutAccordions[$name]['fields']);
                        if ($key !== false) {
                            unset($layoutAccordions[$name]['fields'][$key]);
                        }
                    }
                }
            }

            // Ensure the default panel is present with the driver as first field
            $layoutAccordions['main'] = \Arr::merge(array(
                'title' => __('Properties'),
                'fields' => array(
                    'field_driver'
                ),
            ), $layoutAccordions['main']);

            \Arr::set($layoutConfig, 'standard.params.accordions', $layoutAccordions);
        }

        return $layoutConfig;
    }

    /**
     * Gets the driver name
     *
     * @return mixed
     */
    public static function getName()
    {
        return \Arr::get(static::getConfig(), 'name', get_called_class());
    }

    /**
     * Gets the field model instance
     *
     * @return Model_Field
     */
    public function getField()
    {
        return $this->field;
    }

    /**
     * Gets the specified option
     *
     * @param $path
     * @param null $default
     * @return mixed
     */
    public function getOption($path, $default = null)
    {
        return \Arr::get($this->options, $path, $default);
    }

    /**
     * Gets the options
     *
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * Sets the options
     *
     * @param array $options
     * @return $this
     */
    public function setOptions(array $options)
    {
        $this->options = $options;

        return $this;
    }

    /**
     * Sanitizes the input value
     *
     * @param $value
     * @return mixed
     */
    public function sanitizeValue($value)
    {
        return (string) $value;
    }

    public function getAnswerExportHeader()
    {
        return array(
            'label' => $this->field->field_label
        );
    }
}
