<?php

namespace Nos\Form;

class Driver_Field_Input_Text extends Driver_Field_Input implements Interface_Driver_Field_Placeholder
{
    /**
     * Gets the input type
     *
     * @return string
     */
    protected function getInputType()
    {
        return 'text';
    }
}
