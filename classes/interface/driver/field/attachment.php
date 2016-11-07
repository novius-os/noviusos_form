<?php

namespace Nos\Form;

interface Interface_Driver_Field_Attachment
{
    /**
     * Saves the attachments on the specified $answer
     *
     * @param $answer
     */
    public function saveAttachments(Model_Answer $answer);

    /**
     * Gets the attachment from the specified $answer
     *
     * @param Model_Answer $answer
     * @return array
     */
    public function getAttachments(Model_Answer $answer);
}
