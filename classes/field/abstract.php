<?php

namespace Nos\Form;

abstract class Field_Abstract
{
    protected $field;
    protected $options;
    protected $errors;
    protected $value;

    protected static $config = array();

    /**
     * Constructor
     *
     * @param Model_Field $field
     * @param array|null $options
     */
    public function __construct(Model_Field $field, $options = array())
    {
        $this->field = $field;
        $this->options = (array) $options;
    }

    /*
     * Forges a new instance
     *
     * @return static
     */
    public static function forge(Model_Field $field, $options = array())
    {
        return new static($field, $options);
    }

    /**
     * Gets the field html
     *
     * @return mixed
     */
    abstract public function getHtml();

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
     * Gets the HTML attributes
     *
     * @return array
     */
    public function getHtmlAttributes()
    {
        $attributes = array(
            'id' => $this->getHtmlId(),
            'class' => $this->field->field_technical_css,
            'title' => $this->field->field_label,
        );

        // Adds the required flag
        if ($this->isMandatory()) {
            $attributes['required'] = 'required';
        }

        return $attributes;
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
            $label_attrs['title'] = htmlspecialchars($this->getErrors());
        }

        return array(
            'callback' => array('Form', 'label'),
            'args' => array($this->field->field_label, $this->getHtmlId(), $label_attrs),
        );
    }

    /**
     * Gets the field virtual name
     *
     * @return string
     */
    public function getVirtualName()
    {
        return !empty($this->field->field_virtual_name) ? $this->field->field_virtual_name : 'field_'.$this->field->field_id;
    }

    /**
     * Gets the choices
     *
     * @return array
     */
    public function getChoices()
    {
        return explode("\n", $this->field->field_choices);
    }

    /**
     * Sets the value
     *
     * @param $value
     */
    public function setValue($value)
    {
        $this->value = $this->sanitizeValue($value);
    }

    /**
     * Gets the value
     *
     * @return mixed
     */
    public function getValue()
    {
        return isset($this->value) ? $this->value : $this->getDefaultValue();
    }

    /**
     * Gets the sanitized input value
     *
     * @param string $defaultValue
     * @return mixed
     */
    public function getInputValue($defaultValue = '')
    {
        $value = \Input::post($this->getVirtualName(), $defaultValue);
        $value = $this->sanitizeValue($value);
        return $value;
    }

    /**
     * Gets the field default value (no var origin)
     */
    public function getFieldDefaultValue()
    {
        // Gets the default value
        return $this->field->field_default_value;
    }

    /**
     * Gets the default value
     *
     * @param null $defaultValue
     * @return mixed
     */
    public function getDefaultValue($defaultValue = null)
    {
        // @todo why only for these fields ?
//        if (!in_array($this->field->field_type, array('text', 'email', 'number', 'textarea', 'hidden', 'variable'))) {
//            return $this->field->field_default_value;
//        }

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

        return $defaultValue;
    }

    /**
     * Checks if field is mandatory/required
     *
     * @return bool
     */
    public function isMandatory()
    {
        return !empty($this->field->field_mandatory);
    }

    /**
     * Checks the mandatory state
     *
     * @return bool
     */
    public function checkMandatory()
    {
        if ($this->isMandatory() && empty($this->getValue())) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * Checks the mandatory state
     *
     * @return bool
     */
    public function checkValidation()
    {
        return true;
    }

    /**
     * Sets the field errors
     *
     * @param $errors
     */
    public function setErrors($errors)
    {
        $this->errors = $errors;
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
     */
    public function beforeSubmission(Model_Form $form)
    {
    }

    /**
     * Triggered before answer save
     *
     * @param Model_Answer $answer
     */
    public function beforeAnswerSave(Model_Answer $answer)
    {
    }

    /**
     * Triggered after answer save
     *
     * @param Model_Answer $answer
     */
    public function afterAnswerSave(Model_Answer $answer)
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
     * @return array
     */
    public function getEmailData()
    {
        return array(
            'label' => $this->field->field_label,
            'value' => $this->getValue(),
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
                list($app, $file) = \Config::configFile(get_called_class());
                \Arr::set(static::$config, $class, \Config::load($app . '::' . $file, true));
            } catch (\Exception $e) {
                \Arr::set(static::$config, $class, array());
            }
        }
        return \Arr::get(static::$config, $class, array());
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
     * Gets the specified option
     *
     * @param $path
     * @param null $default
     * @return mixed
     */
    protected function getOption($path, $default = null)
    {
        return \Arr::get($this->options, $path, $default);
    }

    /**
     * Gets the options
     *
     * @return array
     */
    protected function getOptions()
    {
        return $this->options;
    }

    /**
     * Sanitizes the value
     *
     * @param $value
     * @return array
     */
    protected function sanitizeValue($value)
    {
        if (!is_array($value)) {
            $value = explode("\n", (string) $value);
        }
        return $value;
    }
}
