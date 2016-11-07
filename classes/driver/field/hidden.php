<?php

namespace Nos\Form;

class Driver_Field_Hidden extends Driver_Field_Abstract
{
    /**
     * Gets the HTML content
     *
     * @param array $options
     * @return array
     */
    public function getHtml($options = array())
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
        return '';
    }
}
