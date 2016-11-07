<?php

namespace Nos\Form;

class Driver_Field_Input extends Driver_Field_Abstract
{
    /**
     * Gets the HTML content
     *
     * @param array $options
     * @return array
     */
    public function getHtml($options = array())
    {
        $name = $this->getVirtualName();
        $value = $this->getValue();
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
     * Gets the label
     *
     * @return string
     */
    public function getLabel()
    {
        // No label if set as placeholder
        if ($this->getOption('label_position') === 'placeholder') {
            return '';
        } else {
            return parent::getLabel();
        }
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
     * Gets the input type
     *
     * @return string
     */
    protected function getInputType()
    {
        return 'text';
    }
}
