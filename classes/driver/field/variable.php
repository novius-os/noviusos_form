<?php

namespace Nos\Form;

class Driver_Field_Variable extends Driver_Field_Abstract
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
            'callback' => 'html_tag',
            'args' => array('p', array(), e($this->getDefaultValue())),
        );
    }

    /**
     * Gets the HTML preview
     *
     * @return array
     */
    public function getPreviewHtml()
    {
        return html_tag('p', array(), e($this->getDefaultValue()));
    }
}
