<?php

namespace Nos\Form;

class Field_Input_File extends Field_Input implements Interface_Field_Attachment
{
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
     * @return bool
     */
    public function checkMandatory()
    {
        if ($this->isMandatory() && empty(\Arr::get($this->getValue(), 'tmp_name'))) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * Saves the attachments on the specified $answer
     *
     * @param $answer
     */
    public function saveAttachments(Model_Answer $answer)
    {
        // Sets the file as attachment
        $file = $this->getValue();
        if (!empty($file)) {
            $attachment = $answer->getAttachment($this->getVirtualName());
            $attachment->set(\Arr::get($file, 'tmp_name'), \Arr::get($file, 'name'));
            $attachment->save();
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
        $file = $this->getValue();
        if (!empty($file)) {
            $attachments[] = $answer->getAttachment($this->getVirtualName());
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
        return (array) $value;
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
