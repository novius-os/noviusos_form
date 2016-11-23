<?php

namespace Nos\Form;

class Driver_Field_Input_Date extends Driver_Field_Input
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
        if (!empty($inputValue) && !$this->isDateValid($inputValue)) {
            throw new Exception_Driver_Field_Validation(__('Please enter a valid date.'));
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
        $value = $this->sanitizeValue($answerField->value);
        if (!empty($value)) {
            try {
                return \Date::create_from_string($answerField->value, 'mysql_date')->format();
            } catch (\Exception $e) {}
        }
        return null;
    }

    /**
     * Checks if the specified value is a valid date
     *
     * @param $value
     * @return bool
     */
    protected function isDateValid($value)
    {
        if (is_string($value) && preg_match('`^(\d{4})-(\d{2})-(\d{2})$`', $value, $m)) {
            list(, $year, $month, $day) = $m;
            if (checkdate((int) $month, (int) $day, (int) $year)) {
                return true;
            }
        }
        return false;
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
