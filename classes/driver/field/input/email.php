<?php

namespace Nos\Form;

class Driver_Field_Input_Email extends Driver_Field_Input
{
    /**
     * Checks the validation state
     *
     * @param array $formData
     * @return bool
     * @throws Exception_Driver_Field_Validation
     */
    public function checkValidation($formData = array())
    {
        if (!filter_var($this->getValue(), FILTER_VALIDATE_EMAIL)) {
            throw new Exception_Driver_Field_Validation(__('{{label}}: ‘{{value}}’ is not a valid email.'));
        } else {
            return true;
        }
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
