<?php

namespace Nos\Form;

class Driver_Field_Input_File extends Driver_Field_Input implements Interface_Driver_Field_Attachment
{
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
        $attributes = $this->getHtmlAttributes();

        return array(
            'callback' => array('Form', 'input'),
            'args'     => array($name, null, $attributes),
        );
    }

    /**
     * Gets the input value
     *
     * @param string $defaultValue
     *
     * @return mixed
     */
    public function getInputValue($defaultValue = '')
    {
        $value = \Arr::get($_FILES, $this->getVirtualName());
        $value = $this->sanitizeValue($value);

        return $value;
    }

    /**
     * Checks the mandatory state
     *
     * @param array|null $formData
     *
     * @return bool Returns true if successfully checked
     */
    public function checkRequirement($inputValue, $formData = null)
    {
        if (!$this->isMandatory()) {
            return true;
        }

        $file     = $this->sanitizeValue($inputValue);
        $filePath = \Arr::get($file, 'tmp_name');

        return !empty($filePath);
    }

    /**
     * Checks the mandatory state
     *
     * @param      $inputValue
     * @param null $formData
     *
     * @return bool
     * @throws Exception_Driver_Field_Validation
     */
    public function checkValidation($inputValue, $formData = null)
    {
        $file     = $this->sanitizeValue($inputValue);
        $filePath = \Arr::get($file, 'tmp_name');
        if (!empty($file) && !empty($filePath)) {
            // Checks if file exists
            if (!is_file($filePath)) {
                throw new Exception_Driver_Field_Validation(__('Invalid file.'));
            }
            // @todo better checks ? (file size, format, etc...)
        }

        return true;
    }

    /**
     * Renders the value as html for a string error message
     *
     * @param $value
     *
     * @return string
     */
    public function renderErrorValueHtml($value)
    {
        return '';
    }

    /**
     * Renders the answer attachments
     *
     * @param Model_Answer_Field $answerField
     *
     * @return array|string
     */
    public function renderAnswerHtml(Model_Answer_Field $answerField)
    {
        $html = '';

        // Renders the attachment
        $attachment = $answerField->getAttachment();
        if (!empty($attachment)) {
            $url = $attachment->url(false);
            if ($url !== false) {
                $html = $attachment->htmlAnchor(array(
                    'data-attachment' => $url,
                    'target'          => '_blank'
                ));
            }
        }

        if (empty($html)) {
            $html = __('No file attached.');
        }

        return $html;
    }

    /**
     * Gets the data for the mail sent at submission
     *
     * @param $inputValue
     *
     * @return array
     */
    public function getEmailData($inputValue, Model_Answer $answer)
    {
        $value = __('No file attached.');
        if (!empty($this->getAttachments($answer))) {
            $value = __('File attached.');
        }

        return array(
            'label' => $this->getField()->field_label,
            'value' => $value,
        );
    }

    /**
     * Renders the answer as a string for export
     *
     * @param Model_Answer_Field $answerField
     *
     * @return string|array
     */
    public function renderExportValue(Model_Answer_Field $answerField)
    {
        // Renders the attachment
        $attachment = $answerField->getAttachment();
        if (empty($attachment)) {
            return null;
        }

        return $attachment->url(true);
    }

    /**
     * Saves the attachments on the specified $answer
     *
     * @param Model_Answer $answer
     * @param mixed|null   $inputValue
     * @param mixed|null   $formData
     *
     * @throws Exception_Driver_Field_Attachment
     */
    public function saveAttachments(Model_Answer $answer, $inputValue = null, $formData = null)
    {
        $file = $this->sanitizeValue($inputValue);

        // Sets the file as attachment
        if (!empty($file)) {

            // Gets the file path
            $filePath = \Arr::get($file, 'tmp_name');
            if (!empty($filePath) && is_file($filePath)) {

                // Gets the file name
                $fileName = \Arr::get($file, 'name', 'default');

                // Saves as attachment
                $attachment = $answer->getFieldAttachment($this->field);
                $attachment->set($filePath, $fileName);
                $attachment->save();
            }
        }
    }

    /**
     * Gets the attachment from the specified $answer
     *
     * @param Model_Answer $answer
     *
     * @return array
     */
    public function getAttachments(Model_Answer $answer)
    {
        $attachments = array();

        $attachment = $answer->getFieldAttachment($this->field);
        if (!empty($attachment) && $attachment->path()) {
            $attachments[] = $attachment;
        }

        return $attachments;
    }

    /**
     * Sanitizes the value
     *
     * @param $value
     *
     * @return array
     */
    public function sanitizeValue($value)
    {
        return is_array($value) ? $value : array();
    }

    /**
     * Gets the input type
     *
     * @return string
     */
    protected function getInputType()
    {
        return 'file';
    }
}
