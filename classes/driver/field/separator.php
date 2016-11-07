<?php

namespace Nos\Form;

class Driver_Field_Separator extends Driver_Field_Abstract
{
    /**
     * Gets the HTML content
     *
     * @param array $options
     * @return array
     */
    public function getHtml($options = array())
    {
        return html_tag('hr');
    }

    /**
     * Gets the HTML preview
     *
     * @return string
     */
    public function getPreviewHtml()
    {
        return html_tag('hr');
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
