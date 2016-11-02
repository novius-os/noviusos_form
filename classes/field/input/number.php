<?php

namespace Nos\Form;

class Field_Input_Number extends Field_Input
{
    /**
     * Checks the validation state
     *
     * @return bool
     * @throws Exception_Field_Validation
     */
    public function checkValidation()
    {
        if (!filter_var($this->getValue(), FILTER_VALIDATE_INT)) {
            throw new Exception_Field_Validation(__('{{label}}: ‘{{value}}’ is not a valid number.'));
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
        return 'number';
    }
}
