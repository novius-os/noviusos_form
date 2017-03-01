<?php

namespace Nos\Form;

use Fuel\Core\Form;

class Driver_Field_Select extends Driver_Field_Abstract
{
    use Trait_Driver_Field_Html_Attributes {
        // Renames the getHtmlAttributes method for overriding
        getHtmlAttributes as getDefaultHtmlAttributes;
    }
    use Trait_Driver_Field_Choices_Single;

    /**
     * Gets the HTML content
     *
     * @param mixed|null $inputValue
     * @return mixed
     */
    public function getHtml($inputValue = null, $formData = array())
    {
        $value = $this->sanitizeValue($inputValue);
        $value = $this->convertChoiceValueToHash($value);

        return array(
            'callback' => array('Form', 'select'),
            'args' => array(
                $this->getVirtualName(),
                $value,
                $this->getChoicesList(),
                $this->getHtmlAttributes()
            ),
        );
    }

    /**
     * Gets the HTML preview
     *
     * @return string
     */
    public function getPreviewHtml()
    {
        $defaultValue = $this->convertChoiceValueToHash($this->getDefaultValue());
        return Form::select('', $defaultValue, $this->getChoicesList());
    }

    /**
     * Gets the instructions
     *
     * @return string
     */
    public function getInstructions()
    {
        return '';
    }

    /**
     * Gets the HTML attributes
     *
     * @return array
     */
    protected function getHtmlAttributes()
    {
        $attributes = $this->getDefaultHtmlAttributes();

        if (!empty($this->field->field_height)) {
            $html_attrs['rows'] = $this->field->field_height;
        }

        return $attributes;
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
        $values = (array)$this->sanitizeValue($answerField->value);

        // Converts to choices
        $values = $this->getValuesChoiceLabel($values);

        return implode(' / ', $values);
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
