<?php

namespace Nos\Form;

class Driver_Field_Hidden extends Driver_Field_Abstract
{
    /**
     * Gets the HTML content
     *
     * @param mixed|null $inputValue
     * @return mixed
     */
    public function getHtml($inputValue = null)
    {
        return array(
            'callback' => array('Form', 'hidden'),
            'args' => array($this->getVirtualName(), e($this->getDefaultValue())),
        );
    }

    /**
     * Gets the HTML preview
     *
     * @return array
     */
    public function getPreviewHtml()
    {
        return '';
    }

    /**
     * Gets the label
     *
     * @return string
     */
    public function getLabel()
    {
        // No label
        return '';
    }
}
