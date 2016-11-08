<?php

namespace Nos\Form;

use Fuel\Core\Form;

class Driver_Field_Checkbox extends Driver_Field_Abstract
{
    /**
     * Gets the HTML content
     *
     * @param mixed|null $inputValue
     * @return mixed
     */
    public function getHtml($inputValue = null)
    {
        // Converts values to hash
        $values = $this->sanitizeValue($inputValue);
        $values = array_map(array($this, 'convertChoiceValueToHash'), $values);
        $values = $this->getValuesChoiceLabel($values);

        $attributes = $this->getHtmlAttributes();

        // Builds a field for each choice
        $html = array();
        foreach ($this->getChoicesList() as $value => $label) {
            $attributes_choice = $attributes;
            $attributes_choice['id'] .= \Inflector::friendly_title($value);
            $html[] = array(
                'field' => array(
                    'callback' => array('Form', 'checkbox'),
                    'args' => array(
                        $this->getVirtualName().'[]',
                        $value,
                        in_array($value, $values),
                        $attributes_choice,
                    ),
                ),
                'label' => array(
                    'callback' => array('Form', 'label'),
                    'args' => array(
                        $label,
                        $this->field->field_technical_id,
                        array(
                            'for' => $attributes_choice['id'],
                        ),
                    ),
                ),
                'template' => '{field} {label} <br />',
            );
        }

        return $html;
    }

    /**
     * Gets the HTML preview
     *
     * @return string
     */
    public function getPreviewHtml()
    {
        $defaultValues = $this->getDefaultValue();
        $defaultValues = array_map(array($this, 'convertChoiceValueToHash'), $defaultValues);
        $defaultValues = $this->getChoicesList($defaultValues);

        // Builds a field for each choice
        $html = array();
        foreach ($this->getChoicesList() as $value => $label) {
            $html[] = html_tag('p', array(),
                html_tag('label', array(),
                    Form::checkbox('', $value, isset($defaultValues[$value])).' '.$label
                )
            );
        }

        return $html;
    }

    /**
     * Renders the specified value as html for an error message
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
     * Gets the choice (label) for the specified value
     *
     * @param array $values
     * @return mixed
     */
    public function getValuesChoiceLabel($values)
    {
        // Gets the choices
        $choices = $this->getChoicesList();

        // Converts values to choice
        $values = array_map(function($value) use ($choices) {
            return \Arr::get($choices, $value);
        }, $values);

        return $values;
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
     * Formats the value
     *
     * @param $value
     * @return array
     */
    protected function sanitizeValue($value)
    {
        if (!is_array($value)) {
            $value = str_replace(",", "\n", (string) $value);
            $value = preg_split('`\r\n|\r|\n`', $value);
            $value = array_combine($value, $value);
        }

        $value = array_filter($value, function($v) {
            return $v !== '';
        });

        return $value;
    }

    /**
     * Gets the virtual name
     *
     * @return string
     */
    public function getHtmlVirtualName()
    {
        // Adds brackets for handling multiple selected values
        return $this->getVirtualName().'[]';
    }
}
