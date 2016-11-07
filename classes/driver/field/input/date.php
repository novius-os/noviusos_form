<?php

namespace Nos\Form;

class Driver_Field_Input_Date extends Driver_Field_Input
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
        $value = $this->getValue();
        if (!empty($value)) {
            if (preg_match('`^(\d{4})-(\d{2})-(\d{2})$`', $this->getValue(), $m)) {
                list(, $year, $month, $day) = $m;
                if (checkdate((int) $month, (int) $day, (int) $year)) {
                    return true;
                }
            }
            throw new Exception_Driver_Field_Validation(__('{{label}}: ‘{{value}}’ is not a valid date.'));
        }
        return true;
    }

    /**
     * Renders the field answer value as a date
     *
     * @param Model_Answer_Field $answerField
     * @return null
     */
    public function renderAnswerHtml(Model_Answer_Field $answerField)
    {
        try {
            if (!empty($answerField->value)) {
                return \Date::create_from_string($answerField->value, 'mysql_date')->wijmoFormat();
            }
        } catch (\Exception $e) {}
        return null;
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
