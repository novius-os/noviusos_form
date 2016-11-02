<?php

namespace Nos\Form;

class Field_Separator extends Field_Abstract
{
    /**
     * Gets the HTML content
     *
     * @return string
     */
    public function getHtml()
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
