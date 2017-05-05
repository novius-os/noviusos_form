<?php

namespace Nos\Form;

use Fuel\Core\Form;

class Driver_Field_Radio extends Driver_Field_Abstract
{
    use Trait_Driver_Field_Html_Attributes;
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

        $attributes = $this->getHtmlAttributes();

        // Builds the HTML choices
        $html = array();
        foreach ($this->getChoicesList() as $choiceValue => $label) {
            $attributes_choice = $attributes;
            $attributes_choice['id'] .= \Inflector::friendly_title($choiceValue);
            $html[] = array(
                'field' => array(
                    'callback' => array('Form', 'radio'),
                    'args' => array(
                        $this->getVirtualName(),
                        $choiceValue,
                        $choiceValue == $value,
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
                'template' => '<div class="form_radio">{field} {label}</div>',
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
}
