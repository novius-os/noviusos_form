<?php

namespace Nos\Form;

class Driver_Field_Separator extends Driver_Field_Abstract
{
    /**
     * Gets the HTML content
     *
     * @param mixed|null $inputValue
     * @return mixed
     */
    public function getHtml($inputValue = null)
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
        // No label
        return '';
    }

    /**
     * Checks if field is mandatory
     *
     * @return bool
     */
    public function isMandatory()
    {
        // Never mandatory
        return false;
    }
}
