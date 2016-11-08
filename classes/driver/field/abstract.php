<?php

namespace Nos\Form;

abstract class Driver_Field_Abstract
{
    protected $field;
    protected $options;
    protected $errors;

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
     * @param mixed|null $inputValue
     * @return mixed
     */
    abstract public function getHtml($inputValue = null);

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

        // Sets the label as placeholder if option is specified and driver compatible
        if ($this instanceof Interface_Driver_Field_Placeholder && $this->getOption('label_position') === 'placeholder') {
            // Sets the placeholder value
            $attributes['placeholder'] = $this->getPlaceholderValue();

            // Sets the errors attributes
            if ($this->hasErrors()) {
                $attributes['class'] .= ' user-error form-ui-invalid';
                $attributes['title'] = htmlspecialchars($this->getErrors());
            }
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
        // No label if set as placeholder and driver is compatible
        if ($this instanceof Interface_Driver_Field_Placeholder && $this->getOption('label_position') === 'placeholder') {
            return '';
        }

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
        if (is_array($this->field->field_choices)) {
            return $this->field->field_choices;
        } else {
            return preg_split('`\r\n|\r|\n`', $this->field->field_choices);
        }
    }

    /**
     * Gets the choices list
     *
     * @param array $onlyValues
     * @return array
     */
    public function getChoicesList($onlyValues = array())
    {
        $choices = $this->getChoices();

        $choiceList = array();
        foreach ($choices as $index => $choice) {
            $choiceInfos = preg_split('`(?<!\\\)=`', $choice, 2);
            if (count($choiceInfos) === 2) {
                foreach ($choiceInfos as $key => $choiceValue) {
                    $choiceInfos[$key] = str_replace('\=', '=', $choiceValue);
                }
                $choiceLabel = (string) $choiceInfos[0];
                $choiceValue = $this->hashValue($choiceInfos[1] ?: $choiceLabel);
            } else {
                $choiceLabel = $choice;
                $choiceValue = (string) $index;
            }

            if (empty($onlyValues) || in_array($choiceValue, $onlyValues)) {
                $choiceList[$choiceValue] = $choiceLabel;
            }
        }

        // Prepends with the default value
        if (empty($choiceList) && empty($onlyValues)) {
            $choiceList = array('' => '');
        }

        return $choiceList;
    }

    /**
     * Gets a choice value by hash
     *
     * @param $hash
     * @return int|mixed|string
     */
    protected function getChoiceValueByHash($hash)
    {
        // Searches the specified choice in the available choices
        $choices = $this->getChoices();
        foreach ($choices as $choice) {

            // Split parts
            $choiceParts = preg_split('`(?<!\\\)=`', $choice, 2);
            if (count($choiceParts) === 2) {

                // Unescapes escaped equal signs
                $choiceParts = array_map(function($choice) {
                    return str_replace('\=', '=', $choice);
                }, $choiceParts);

                // Checks if hash match value hash
                $choiceValue = $choiceParts[1];
                if (!empty($choiceValue) && $hash === $this->hashValue($choiceValue)) {
                    return $choiceValue;
                }
            }
        }

        return $hash;
    }

    /**
     * Hashes the given option value if needed
     *
     * @param $value
     * @return mixed|string
     */
    protected function convertChoiceValueToHash($value)
    {
        // Searches the specified choice in the available choices
        $choices = $this->getChoices();
        foreach ($choices as $index => $choice) {

            // Split parts
            $choiceParts = preg_split('`(?<!\\\)=`', $choice, 2);
            if (count($choiceParts) === 2) {

                // Unescapes escaped equal signs
                $choiceParts = array_map(function($choice) {
                    return str_replace('\=', '=', $choice);
                }, $choiceParts);

                // Checks if value match
                $choiceValue = $choiceParts[1];
                if (!empty($choiceValue) && $value === $choiceValue) {
                    $value = $this->hashValue($choiceValue);
                    break;
                }
            }
        }

        return $value;
    }

    /**
     * Gets the choice label for the specified value
     *
     * @param $value
     * @return mixed
     */
    public function getValueChoiceLabel($value)
    {
        // Gets the choices
        $choices = $this->getChoicesList();

        return \Arr::get($choices, $value, $value);
    }

    /**
     * Renders the specified value as html for an error message
     *
     * @param $value
     * @return string
     */
    public function renderErrorValueHtml($value)
    {
        $value = $this->sanitizeValue($value);
        return (string) $value;
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
        return (string) $this->field->field_default_value;
    }

    /**
     * Hash the specified value
     *
     * @param $value
     * @return string
     */
    protected function hashValue($value)
    {
        return hash('sha256', $value);
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
            $conditional_field = self::query()
                ->where('field_form_id', $this->field->field_form_id)
                ->where('field_virtual_name', $this->field->field_conditional_form)
                ->get_one();
            if (empty($conditional_field)) {
                return false;
            }

            $conditionalFieldDriver = $conditional_field->getDriver();
            if (!empty($conditionalFieldDriver)) {
                // Checks if the conditional field value match the expected value
                if (\Arr::get($formData, $conditionalFieldDriver->getVirtualName()) != $this->field->field_conditional_value) {
                    return false;
                }
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
        return is_string($inputValue) && !empty($inputValue);
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
        $this->errors = $errors;
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
     * Renders the answer as HTML
     *
     * @param Model_Answer_Field $answerField
     * @return mixed|string
     */
    public function renderAnswerHtml(Model_Answer_Field $answerField)
    {
        return e($answerField->value);
    }

    /**
     * Triggered before answer save
     *
     * @param Model_Answer $answer
     * @param null mixed $inputValue
     * @param null mixed|null $fomData
     */
    public function beforeAnswerSave(Model_Answer $answer, $inputValue = null, $fomData = null)
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
     * @param $inputValue
     * @return array
     */
    public function getEmailData($inputValue)
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
     * @return mixed
     */
    protected function sanitizeValue($value)
    {
        return (string) $value;
    }
}
