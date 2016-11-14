<?php

namespace Nos\Form;

class Driver_Field_Input_Number extends Driver_Field_Input implements Interface_Driver_Field_Placeholder
{
    /**
     * Checks the validation state
     *
     * @param array|null $formData
     * @return bool
     * @throws Exception_Driver_Field_Validation
     */
    public function checkValidation($inputValue, $formData = null)
    {
        if (!empty($inputValue) && !filter_var($inputValue, FILTER_VALIDATE_INT)) {
            throw new Exception_Driver_Field_Validation(__('Please enter a valid number.'));
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
    public function sanitizeValue($value)
    {
        return (int) $value;
    }
}
