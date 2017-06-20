<?php

namespace Nos\Form;

trait Trait_Driver_Field_Placeholder
{
    public function getPlaceholderValue()
    {
        return !empty($this->getField()->field_placeholder) ? $this->getField()->field_placeholder : '';
    }
}
