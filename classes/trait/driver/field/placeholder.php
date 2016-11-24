<?php

namespace Nos\Form;

trait Trait_Driver_Field_Placeholder
{

    public function getPlaceholderValue()
    {
        return !empty($this->field->field_placeholder) ? $this->field->field_placeholder : '';
    }

}
