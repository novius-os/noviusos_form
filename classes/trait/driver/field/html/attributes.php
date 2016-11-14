<?php

namespace Nos\Form;

/**
 * Implements html attributes
 *
 * @package Nos\Form
 */
trait Trait_Driver_Field_Html_Attributes
{
    /**
     * Gets the HTML attributes
     *
     * @return array
     */
    protected function getHtmlAttributes()
    {
        $attributes = array(
            'id' => $this->getHtmlId(),
            'class' => $this->getField()->field_technical_css,
            'title' => $this->getField()->field_label,
        );

        // Adds the required flag
        if ($this->isMandatory()) {
            $attributes['required'] = 'required';
        }

        // Sets the label as placeholder if option is specified and driver compatible
        if ($this instanceof Interface_Driver_Field_Placeholder && $this->getOption('label_position') === 'placeholder') {

            // Sets the placeholder value
            $attributes['placeholder'] = $this->getPlaceholderValue();
            if ($this->isMandatory()) {
                $attributes['placeholder'] .= '*';
            }

            // Sets the errors attributes
            if ($this->hasErrors()) {
                $attributes['class'] .= ' user-error form-ui-invalid';
                $attributes['title'] = nl2br(htmlspecialchars(implode("\n", $this->getErrors())));;
            }
        }

        if ($this->hasErrors()) {
            $attributes['class'] .= ' parsley-error';
        }

        return $attributes;
    }
}