<?php

namespace Nos\Form;

class Driver_Field_Input_Captcha extends Driver_Field_Input_Text
{
    /**
     * Checks if field is mandatory
     *
     * @return bool
     */
    public function isMandatory()
    {
        return true;
    }

    /**
     * Gets the default value
     *
     * @param null $defaultValue
     * @return string
     */
    public function getDefaultValue($defaultValue = null)
    {
        return '';
    }
}
