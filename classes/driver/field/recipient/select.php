<?php

namespace Nos\Form;

class Driver_Field_Recipient_Select extends Driver_Field_Select
{
    protected $useLineNumberForValues = true;

    /**
     * Triggered before form submission
     *
     * @param Model_Form $form
     * @param null $inputValue
     * @param null $formData
     */
    public function beforeFormSubmission(Model_Form $form, $inputValue = null, $formData = null)
    {
        // Gets the field value
        $value = $this->sanitizeValue($inputValue);
        $label = trim($this->getValueChoiceLabel($value));
        if (!empty($label) && filter_var($label, FILTER_VALIDATE_EMAIL)) {
            // Add the value to the recipient list
            $form->form_submit_email .= $label.PHP_EOL;
        }
    }
}
