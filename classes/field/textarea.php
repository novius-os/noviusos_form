<?php

namespace Nos\Form;

class Field_Textarea extends Field_Abstract
{
    /**
     * Gets the HTML content
     *
     * @return array
     */
    public function getHtml()
    {
        $name = $this->getVirtualName();
        $value = $this->getValue();
        $attributes = $this->getHtmlAttributes();

        return array(
            'callback' => array('Form', 'textarea'),
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
        return html_tag('textarea', array(
            'rows' => $this->field->field_height !== '' ? $this->field->field_height : null,
        ), $this->getFieldDefaultValue());
    }

    /**
     * Gets the HTML attributes
     *
     * @return array
     */
    protected function getHtmlAttributes()
    {
        $attributes = parent::getHtmlAttributes();

        // Sets the label as placeholder if option is specified
        if ($this->getOption('label_position') === 'placeholder') {
            $attributes['placeholder'] = $this->field->field_label;
        }

        // Sets the error state
        if ($this->hasErrors()) {
            if ($this->getOption('label_position') === 'placeholder') {
                $attributes['class'] .= ' user-error form-ui-invalid';
                $attributes['title'] = htmlspecialchars($this->getErrors());
            }
        }

        if (!empty($this->field->field_height)) {
            $html_attrs['rows'] = $this->field->field_height;
        }

        return $attributes;
    }
}
