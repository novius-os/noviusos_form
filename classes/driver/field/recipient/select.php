<?php

namespace Nos\Form;

class Driver_Field_Recipient_Select extends Driver_Field_Select
{
    /**
     * Triggered before form submission
     *
     * @param Model_Form $form
     * @param null $inputValue
     * @param null $formData
     */
    public function beforeFormSubmission(Model_Form $form, $inputValue = null, $formData = null)
    {
        // Checks if the field is a recipient list
        if ($this->field->field_technical_id === 'recipient-list') {

            // Gets the field value
            $value = $this->sanitizeValue($inputValue);
            $label = $this->getValueChoiceLabel($value);
            if (!empty($label)) {

                // Add the value to the recipient list
                $form->form_submit_email .= $label . "\n";
            }
        }
    }
}
