<?php

namespace Nos\Form;

class Driver_Field_Input_Email extends Driver_Field_Input implements Interface_Driver_Field_Email
{
    /**
     * Checks the validation state
     *
     * @param $inputValue
     * @param array $formData
     * @return bool
     * @throws Exception_Driver_Field_Validation
     */
    public function checkValidation($inputValue, $formData = array())
    {
        if (!empty($inputValue) && !filter_var($inputValue, FILTER_VALIDATE_EMAIL)) {
            throw new Exception_Driver_Field_Validation(__('{{label}}: ‘{{value}}’ is not a valid email.'));
        } else {
            return true;
        }
    }

    /**
     * Gets the email address
     *
     * @param $inputValue
     * @param null $formData
     * @return mixed
     */
    public function getEmail($inputValue, $formData = null)
    {
        if (!filter_var($inputValue, FILTER_VALIDATE_EMAIL)) {
            return null;
        }
        return $this->sanitizeValue($inputValue);
    }

    /**
     * Sanitizes the value
     *
     * @param $value
     * @return mixed|null
     */
    public function sanitizeValue($value)
    {
        $value =  parent::sanitizeValue($value);
        return $value;
    }

    /**
     * Gets the input type
     *
     * @return string
     */
    protected function getInputType()
    {
        return 'email';
    }
}
