<?php

namespace Nos\Form;

class Field_Checkbox extends Field_Abstract
{
    /**
     * Gets the HTML content
     *
     * @return array
     */
    public function getHtml()
    {
        $attributes = $this->getHtmlAttributes();

        // Builds a field for each choice
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
        $default_choices = $this->getDefaultValue();

        // Builds a field for each choice
        $html = array();
        foreach ($this->getChoices() as $i => $choice) {
            $html[] = html_tag('p', array(),
                html_tag('label', array(),
                    html_tag('input', array(
                        'type' => 'checkbox',
                        'value' => $i,
                        'checked' => in_array($i, $default_choices),
                    )).' '.$choice
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
     * Gets the default value
     *
     * @param null $defaultValue
     * @return array|mixed|null
     */
    public function getDefaultValue($defaultValue = null)
    {
        $defaultValue = parent::getDefaultValue($defaultValue);
        return $this->sanitizeValue($defaultValue);
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
            $value = str_replace(",", "\n", $value);
            $value = explode("\n", (string) $value);
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

    public function getHtmlVirtualName()
    {
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
