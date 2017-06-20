<?php

namespace Nos\Form;

class Driver_Field_Input extends Driver_Field_Abstract implements Interface_Driver_Field_Placeholder
{
    use Trait_Driver_Field_Html_Attributes {
        // Renames the getHtmlAttributes method for overriding
        getHtmlAttributes as getDefaultHtmlAttributes;
    }
    use Trait_Driver_Field_Placeholder;

    /**
     * Gets the HTML content
     *
     * @param mixed|null $inputValue
     *
     * @return mixed
     */
    public function getHtml($inputValue = null, $formData = array())
    {
        $name       = $this->getInputVirtualName();
        $value      = $this->sanitizeValue($inputValue);
        $attributes = $this->getHtmlAttributes();

        return array(
            'callback' => array('Form', 'input'),
            'args'     => array($name, $value, $attributes),
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
            'type'        => $this->getInputType(),
            'value'       => $this->getFieldDefaultValue(),
            'size'        => !empty($this->field->field_width) ? $this->field->field_width : null,
            'placeholder' => ($this instanceof Interface_Driver_Field_Placeholder) ? $this->getPlaceholderValue() : '',
        ));
    }

    /**
     * Gets the HTML attributes
     *
     * @return array
     */
    protected function getHtmlAttributes()
    {
        $attributes = $this->getDefaultHtmlAttributes();

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

        // Sets the placeholder
        if (!empty($this->field->field_placeholder)) {
            $attributes['placeholder'] = $this->field->field_placeholder;
        }

        return $attributes;
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

    /**
     * Gets the virtual name for the HTML input
     *
     * @return string
     */
    protected function getInputVirtualName()
    {
        return $this->getVirtualName();
    }
}
