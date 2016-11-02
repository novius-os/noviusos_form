<?php

namespace Nos\Form;

class Field_Radio extends Field_Abstract
{
    /**
     * Gets the HTML content
     *
     * @return array
     */
    public function getHtml()
    {
        $attributes = $this->getHtmlAttributes();

        // Builds the HTML choices
        $html = array();
        foreach ($this->getChoices() as $i => $choice) {
            $attributes_choice = $attributes;
            $attributes_choice['id'] .= $i;
            $html[] = array(
                'field' => $this->getChoiceHtml($choice, $attributes_choice),
                'label' => $this->getChoiceLabel($choice, $attributes_choice),
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
        $default_choice = $this->getDefaultValue();

        // Builds a radio field for each choice
        $html = array();
        foreach ($this->getChoices() as $i => $choice) {
            $html[] = html_tag('p', array(),
                html_tag('label', array(),
                    html_tag('input', array(
                        'type' => 'radio',
                        'value' => $i,
                        'checked' => ($i === $default_choice),
                    ))
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
     * Gets the HTML content of the specified choice
     *
     * @param $choice
     * @param $attributes_choice
     * @return array
     */
    protected function getChoiceHtml($choice, $attributes_choice)
    {
        return array(
            'callback' => array('Form', 'radio'),
            'args' => array($this->getVirtualName().'[]', $choice, $choice == $this->getValue(), $attributes_choice),
        );
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
