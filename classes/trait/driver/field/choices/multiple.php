<?php

namespace Nos\Form;

/**
 * Implements choices (list of values or keys/values)
 *
 * @package Nos\Form
 */
trait Trait_Driver_Field_Choices_Multiple
{
    use Trait_Driver_Field_Choices_Single;

    /**
     * Renders the value as html for a string error message
     *
     * @param $value
     * @return string
     */
    public function renderErrorValueHtml($value)
    {
        $value = $this->sanitizeValue($value);
        $value = implode(', ', $value);
        return $value;
    }

    /**
     * Renders the answer as a string for export
     *
     * @param Model_Answer_Field $answerField
     * @return string
     */
    public function renderExportValue(Model_Answer_Field $answerField)
    {
        // Gets the answer values
        $values = $this->sanitizeValue($answerField->value);

        // Converts to choices
        $values = $this->getValuesChoiceLabel($values);

        return implode(' / ', $values);
    }

    /**
     * Renders the answer as HTML
     *
     * @param Model_Answer_Field $answerField
     * @return mixed|string
     */
    public function renderAnswerHtml(Model_Answer_Field $answerField)
    {
        // Gets the answer values
        $values = $this->sanitizeValue($answerField->value);

        // Converts to choices
        $values = $this->getValuesChoiceLabel($values);

        // Linearizes the values
        $values = implode("\n", $values);
        $html = \Str::textToHtml($values);

        return $html;
    }

    /**
     * Gets the data for the mail sent at submission
     *
     * @param $inputValue
     * @return array
     */
    public function getEmailData($inputValue, Model_Answer $answer)
    {
        // Gets the answer values
        $values = $this->sanitizeValue($inputValue);

        // Converts to choices
        $values = $this->getValuesChoiceLabel($values);

        // Linearizes the values
        $html = implode("\n", $values);

        return array(
            'label' => $this->getField()->field_label,
            'value' => $html,
        );
    }

    /**
     * Sanitizes the value
     *
     * @param $value
     * @return array
     */
    public function sanitizeValue($value)
    {
        $value = $this->convertValueToArray($value);

        $value = array_filter($value, function($v) {
            return $v !== '';
        });

        return $value;
    }

    /**
     * Converts the value (possibly containing comma separated values) to an array
     *
     * @param $value
     * @return array|mixed
     */
    protected function convertValueToArray($value)
    {
        if (!is_array($value)) {
            // Replaces comma by newlines
            $value = str_replace(",", PHP_EOL, $value);
            $value = preg_split('`\r\n|\r|\n`', $value);
            $value = array_combine($value, $value);
        }
        return $value;
    }

    /**
     * Gets the choice (label) for the specified value
     *
     * @param array $values
     * @return mixed
     */
    protected function getValuesChoiceLabel($values)
    {
        // Gets the choices
        $choices = $this->getChoicesList();

        // Converts values to choice
        $values = array_map(function($value) use ($choices) {
            $hashValue = $this->convertChoiceValueToHash($value);
            $clearValue = $this->getChoiceValueByHash($value);

            // If the hash equals the choice value then it's an indexed list
            if ($hashValue === $clearValue) {
                // If the choice value is not a number then consider it as the choice label
                // (for compatibility with older versions of noviusos_form)
                if (!ctype_digit((string) $value)) {
                    // Searches choice value by label
                    $value = array_search($value, $choices);
                    if ($value === false) {
                        return $value;
                    } else {
                        return \Arr::get($choices, $value);
                    }
                }
            }

            return \Arr::get($choices, $hashValue);
        }, $values);

        return $values;
    }
}
