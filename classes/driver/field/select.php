<?php

namespace Nos\Form;

use Fuel\Core\Form;

class Driver_Field_Select extends Driver_Field_Abstract implements Interface_Driver_Field_Placeholder
{
    /**
     * Gets the HTML content
     *
     * @param mixed|null $inputValue
     * @return mixed
     */
    public function getHtml($inputValue = null)
    {
        $value = $this->sanitizeValue($inputValue);
        $value = $this->convertChoiceValueToHash($value);

        return array(
            'callback' => array('Form', 'select'),
            'args' => array(
                $this->getVirtualName(),
                $value,
                $this->getChoicesList(),
                $this->getHtmlAttributes()
            ),
        );
    }

    /**
     * Gets the HTML preview
     *
     * @return string
     */
    public function getPreviewHtml()
    {
        $defaultValue = $this->convertChoiceValueToHash($this->getDefaultValue());
        return Form::select('', $defaultValue, $this->getChoicesList());
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
     * Renders the answer as HTML
     *
     * @param Model_Answer_Field $answerField
     * @return mixed|string
     */
    public function renderAnswerHtml(Model_Answer_Field $answerField)
    {
        // Gets the answer value
        $value = $this->sanitizeValue($answerField->value);

        // Converts to choice label
        $value = $this->getValueChoiceLabel($value);

        return e($value);
    }

    /**
     * Triggered before form submission
     *
     * @param Model_Form $form
     * @param null $inputValue
     * @param null $formData
     */
    public function beforeFormSubmission(Model_Form $form, $inputValue = null, $formData = null)
    {
        if ($this->field->field_technical_id === 'recipient-list') {
            $value = $this->sanitizeValue($inputValue);
            $label = $this->getValueChoiceLabel($value);
            if (!empty($label)) {
                // Add the value to the recipient list
                $form->form_submit_email .= $label . "\n";
            }
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

        if (!empty($this->field->field_height)) {
            $html_attrs['rows'] = $this->field->field_height;
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
}
