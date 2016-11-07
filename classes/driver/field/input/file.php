<?php

namespace Nos\Form;

class Driver_Field_Input_File extends Driver_Field_Input implements Interface_Driver_Field_Attachment
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
        $attributes = $this->getHtmlAttributes();

        return array(
            'callback' => array('Form', 'input'),
            'args' => array($name, null, $attributes),
        );
    }

    /**
     * Renders the specified value
     *
     * @param $value
     * @return mixed
     */
    public function renderValue($value)
    {
        return '';
    }

    /**
     * Gets the input value
     *
     * @param string $defaultValue
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
     * @return bool
     */
    public function checkMandatory($formData = null)
    {
        if (!$this->isMandatory()) {
            return true;
        }

        // Checks if file exists
        // @todo better check
        $file = isset($this->value) ? $this->value : '';
        $file = $this->sanitizeValue($file);

        $filePath = \Arr::get($file, 'tmp_name');
        if (empty($filePath) || !is_file($filePath)) {
            return false;
        }

        return true;
    }

    /**
     * Renders the answer attachments
     *
     * @param Model_Answer_Field $answerField
     * @return array|string
     */
    public function renderAnswerHtml(Model_Answer_Field $answerField)
    {
        $html = '';

        $answer = $answerField->answer;

        // Renders the attachment
        $attachment = $answer->getAttachment($this->field);
        if (!empty($attachment)) {
            $url = $attachment->url(false);
            if ($url !== false) {
                $html = $attachment->htmlAnchor(array(
                    'data-attachment' => $url,
                    'target' => '_blank'
                ));
            }
        }

        if (empty($html)) {
            $html = __('No file attached.');
        }

        return $html;
    }

    /**
     * Saves the attachments on the specified $answer
     *
     * @param Model_Answer $answer
     * @throws Exception_Driver_Field_Attachment
     */
    public function saveAttachments(Model_Answer $answer)
    {
        // Sets the file as attachment
        $file = $this->getValue();
        if (!empty($file)) {

            // Gets the file path
            $filePath = \Arr::get($file, 'tmp_name');
            if (!empty($filePath) && is_file($filePath)) {

                // Gets the file name
                $fileName = \Arr::get($file, 'name', 'default');

                // Saves as attachment
                $attachment = $answer->getAttachment($this->field);
                $attachment->set($filePath, $fileName);
                $attachment->save();
            }
        }
    }

    /**
     * Gets the attachment from the specified $answer
     *
     * @param Model_Answer $answer
     * @return array
     */
    public function getAttachments(Model_Answer $answer)
    {
        $attachments = array();

        // Sets the file as attachment
//        $file = $this->getValue();
//        if (!empty($file)) {
//          $attachments[] = $answer->getAttachment($this->getVirtualName());
//        }

        $attachment = $answer->getAttachment($this->field);
        if (!empty($attachment) && $attachment->path()) {
            $attachments[] = $attachment;
        }

        return $attachments;
    }

    /**
     * Sanitizes the value
     *
     * @param $value
     * @return array
     */
    protected function sanitizeValue($value)
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
