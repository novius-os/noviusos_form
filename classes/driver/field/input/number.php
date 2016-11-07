<?php

namespace Nos\Form;

class Driver_Field_Input_Number extends Driver_Field_Input
{
    /**
     * Checks the validation state
     *
     * @param array|null $formData
     * @return bool
     * @throws Exception_Driver_Field_Validation
     */
    public function checkValidation($formData = null)
    {
        if (!filter_var($this->getValue(), FILTER_VALIDATE_INT)) {
            throw new Exception_Driver_Field_Validation(__('{{label}}: ‘{{value}}’ is not a valid number.'));
        }
        return true;
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

    /**
     * Sanitizes the value
     *
     * @param $value
     * @return int
     */
    protected function sanitizeValue($value)
    {
        return (int) $value;
    }
}
