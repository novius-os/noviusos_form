<?php

namespace Nos\Form;

class Driver_Field_Input extends Driver_Field_Abstract implements Interface_Driver_Field_Placeholder
{
    /**
     * Gets the HTML content
     *
     * @param mixed|null $inputValue
     * @return mixed
     */
    public function getHtml($inputValue = null)
    {
        $name = $this->getVirtualName();
        $value = $this->sanitizeValue($inputValue);
        $attributes = $this->getHtmlAttributes();

        return array(
            'callback' => array('Form', 'input'),
            'args' => array($name, $value, $attributes),
        );
    }

    /**
     * Gets the HTML preview
     *
     * @return string
     */
    public function getPreviewHtml()
    {
        return html_tag('input', array(
            'type' => $this->getInputType(),
            'value' => $this->getFieldDefaultValue(),
            'size' => !empty($this->field->field_width) ? $this->field->field_width : null,
        ));
    }

    /**
     * Gets the HTML attributes
     *
     * @return array
     */
    public function getHtmlAttributes()
    {
        $attributes = parent::getHtmlAttributes();

        // Sets the input type
        $attributes['type'] = $this->getInputType();

        // Sets the width
        if (!empty($this->field->field_width)) {
            $attributes['size'] = $this->field->field_width;
        }

        // Sets the max length
        if (!empty($this->field->field_limited_to)) {
            $attributes['maxlength'] = $this->field->field_limited_to;
        }

        return $attributes;
    }

    /**
     * Gets the placeholder value
     *
     * @return string
     */
    public function getPlaceholderValue()
    {
        return $this->field->field_label;
    }

    /**
     * Gets the input type
     *
     * @return string
     */
    protected function getInputType()
    {
        return 'text';
    }
}
