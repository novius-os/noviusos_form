<?php

namespace Nos\Form;

interface Interface_Driver_Field_Email
{
    /**
     * Gets the email address
     *
     * @param $inputValue
     * @param null $formData
     * @return mixed
     */
    public function getEmail($inputValue, $formData = null);
}
