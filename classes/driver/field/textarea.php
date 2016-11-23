<?php

namespace Nos\Form;

class Driver_Field_Textarea extends Driver_Field_Abstract implements Interface_Driver_Field_Placeholder
{
    use Trait_Driver_Field_Html_Attributes {
        // Renames the getHtmlAttributes method for overriding
        getHtmlAttributes as getDefaultHtmlAttributes;
    }

    /**
     * Gets the HTML content
     *
     * @param mixed|null $inputValue
     *
     * @return mixed
     */
    public function getHtml($inputValue = null, $formData = array())
    {
        $name       = $this->getVirtualName();
        $value      = $this->sanitizeValue($inputValue);
        $attributes = $this->getHtmlAttributes();

        return array(
            'callback' => array('Form', 'textarea'),
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
        return html_tag('textarea', array(
            'rows' => $this->field->field_height !== '' ? $this->field->field_height : null,
            'placeholder'  => ($this instanceof Interface_Driver_Field_Placeholder ) ? $this->getPlaceholderValue() : '',
        ), $this->getFieldDefaultValue());
    }

    /**
     * Renders the answer as HTML
     *
     * @param Model_Answer_Field $answerField
     *
     * @return mixed|string
     */
    public function renderAnswerHtml(Model_Answer_Field $answerField)
    {
        $html = \Str::textToHtml($answerField->value);
        return $html;
    }

    /**
     * Gets the HTML attributes
     *
     * @return array
     */
    protected function getHtmlAttributes()
    {
        $attributes = $this->getDefaultHtmlAttributes();

        if (!empty($this->field->field_height)) {
            $html_attrs['rows'] = $this->field->field_height;
        }

        // Sets the placeholder
        if (!empty($this->field->field_placeholder)) {
            $attributes['placeholder'] = $this->field->field_placeholder;
        }

        return $attributes;
    }

    public function getPlaceholderValue()
    {
        return !empty($this->field->field_placeholder) ? $this->field->field_placeholder : '';
    }

}
