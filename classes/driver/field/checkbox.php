<?php

namespace Nos\Form;

use Fuel\Core\Form;

class Driver_Field_Checkbox extends Driver_Field_Abstract
{
    /**
     * Gets the HTML content
     *
     * @param array $options
     * @return array
     */
    public function getHtml($options = array())
    {
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
                        $value, in_array($value, $this->getValue()),
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
     * Gets the instructions
     *
     * @return string
     */
    public function getInstructions()
    {
        return '';
    }

    /**
     * Gets the value
     *
     * @return mixed|string
     */
    public function getValue()
    {
        $value = parent::getValue();
        $value = $this->convertChoiceValueToHash($value);
        $value = array_map(array($this, 'convertChoiceValueToHash'), $value);
        return $value;
    }

    /**
     * Renders the specified value
     *
     * @param $value
     * @return mixed
     */
    public function renderValue($value)
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
        $html = \Str::textToHtml($answerField->value);
        return $html;
    }

//    /**
//     * Gets the default value
//     *
//     * @param null $defaultValue
//     * @return array|mixed|null
//     */
//    public function getDefaultValue($defaultValue = null)
//    {
//        $defaultValues = $this->getDefaultValue();
//        $defaultValues = array_map(array($this, 'convertChoiceValueToHash'), $defaultValues);
//        $defaultValues = $this->getChoicesList($defaultValues);
//        return $this->sanitizeValue($defaultValues);
//    }

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
     * Gets the HTML content of the specified choice
     *
     * @param $choice
     * @param $attributes_choice
     * @return array
     */
    protected function getChoiceHtml($choice, $attributes_choice)
    {
        return array(
            'callback' => array('Form', 'checkbox'),
            'args' => array($this->getVirtualName().'[]', $choice, in_array($choice, $this->getValue()), $attributes_choice),
        );
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

    /**
     * Gets the label of the specified choice
     *
     * @param $choice
     * @param $attributes_choice
     * @return array
     */
    protected function getChoiceLabel($choice, $attributes_choice)
    {
        return array(
            'callback' => array('Form', 'label'),
            'args' => array(
                $choice,
                $this->field->field_technical_id,
                array(
                    'for' => $attributes_choice['id'],
                ),
            ),
        );
    }
}
