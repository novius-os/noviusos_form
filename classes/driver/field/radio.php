<?php

namespace Nos\Form;

use Fuel\Core\Form;

class Driver_Field_Radio extends Driver_Field_Abstract
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

        // Builds the HTML choices
        $html = array();
        foreach ($this->getChoicesList() as $value => $label) {
            $attributes_choice = $attributes;
            $attributes_choice['id'] .= \Inflector::friendly_title($value);
            $html[] = array(
                'field' => array(
                    'callback' => array('Form', 'radio'),
                    'args' => array(
                        $this->getVirtualName().'[]',
                        $value,
                        $value === $this->getValue(),
                        $attributes_choice
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
        $default_choice = $this->getFieldDefaultValue();
        $default_choice = $this->convertChoiceValueToHash($default_choice);

        // Builds a radio field for each choice
        $html = array();
        foreach ($this->getChoicesList() as $value => $label) {
            $html[] = html_tag('p', array(),
                html_tag('label', array(),
                    Form::radio('', $value, $value == $default_choice).' '.$label
                )
            );
        }

        return implode("\n", $html);
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
        return $value;
    }
}
