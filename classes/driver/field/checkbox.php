<?php

namespace Nos\Form;

use Fuel\Core\Form;

class Driver_Field_Checkbox extends Driver_Field_Abstract
{
    use Trait_Driver_Field_Html_Attributes;
    use Trait_Driver_Field_Choices_Multiple;

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
                        $this->getHtmlVirtualName(),
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
     * Gets the instructions
     *
     * @return string
     */
    public function getInstructions()
    {
        return '';
    }

    /**
     * Gets the virtual name
     *
     * @return string
     */
    protected function getHtmlVirtualName()
    {
        // Adds brackets for handling multiple selected values
        return $this->getVirtualName().'[]';
    }
}
