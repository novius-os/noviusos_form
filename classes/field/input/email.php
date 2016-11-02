<?php

namespace Nos\Form;

class Field_Input_Email extends Field_Input
{
    /**
     * Checks the validation state
     *
     * @return bool
     * @throws Exception_Field_Validation
     */
    public function checkValidation()
    {
        if (!filter_var($this->getValue(), FILTER_VALIDATE_EMAIL)) {
            throw new Exception_Field_Validation(__('{{label}}: ‘{{value}}’ is not a valid email.'));
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
