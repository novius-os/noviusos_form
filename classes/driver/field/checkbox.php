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
    public function getHtml($inputValue = null, $formData = array())
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
                        $this->getInputVirtualName(),
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
                'template' => '<div class="form_checkbox">{field} {label}</div>',
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
     * Gets the field default value (no var origin)
     *
     * @return array|mixed|\Nos\Orm\Model|null
     */
    public function getFieldDefaultValue()
    {
        $defaultValue = $this->field->field_default_value;

        // Converts to an array if not an array (this case happens when switching between a driver
        // that handles only a single default value to a driver that handles multiple default values)
        if (!is_array($defaultValue)) {
            $defaultValue = $this->convertValueToArray($defaultValue);
        }

        return $defaultValue;
    }

    /**
     * Gets the virtual name
     *
     * @return string
     */
    protected function getInputVirtualName()
    {
        // Adds brackets for handling multiple selected values
        return $this->getVirtualName().'[]';
    }

    /**
     * Checks the requirement state or the specified form data
     *
     * @param $inputValue
     * @param array|null $formData
     * @return bool Returns true if successfully checked
     */
    public function checkRequirement($inputValue, $formData = null)
    {
        if (!$this->isMandatory()) {
            return true;
        }
        return is_array($inputValue) && !empty($inputValue);
    }
}
