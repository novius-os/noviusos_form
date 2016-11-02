<?php

namespace Nos\Form;

class Field_Input_Date extends Field_Input
{
    /**
     * Checks the validation state
     *
     * @return bool
     * @throws Exception_Field_Validation
     */
    public function checkValidation()
    {
        $value = $this->getValue();
        if (!empty($value)) {
            if (preg_match('`^(\d{4})-(\d{2})-(\d{2})$`', $this->getValue(), $m)) {
                list(, $year, $month, $day) = $m;
                if (checkdate((int) $month, (int) $day, (int) $year)) {
                    return true;
                }
            }
            throw new Exception_Field_Validation(__('{{label}}: ‘{{value}}’ is not a valid date.'));
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
        return 'date';
    }
}
