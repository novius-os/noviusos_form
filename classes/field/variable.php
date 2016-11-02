<?php

namespace Nos\Form;

class Field_Variable extends Field_Abstract
{
    /**
     * Gets the HTML content
     *
     * @return array
     */
    public function getHtml()
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
