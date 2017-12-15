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
        $emails = array_map('trim', explode(',', $this->getValueChoiceLabel($value)));
        foreach ($emails as $email) {
            if (!empty($email) && filter_var($email, FILTER_VALIDATE_EMAIL)) {
                // Add the value to the recipient list
                $form->form_submit_email .= $email.PHP_EOL;
            }
        }
    }
}
