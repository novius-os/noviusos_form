<?php

namespace Nos\Form;

class Field_Hidden extends Field_Abstract
{
    /**
     * Gets the HTML content
     *
     * @return array
     */
    public function getHtml()
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
