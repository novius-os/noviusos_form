<?php

namespace Nos\Form;

class Service_Field
{
    protected $field;

    public function __construct($field)
    {
        $this->field = $field;
    }

    public static function forge($field)
    {
        return new static($field);
    }

    public function getPreviewHtml($options = array())
    {
        //$this->enhancer_args
        if (!method_exists($this->field, 'getDriver')) {
            return '';
        }

        // Gets field driver
        $fieldDriver = $this->field->getDriver($options);
        if (empty($fieldDriver)) {
            return __('No preview.');
        }

        // Gets the preview html content
        $html = $fieldDriver->getPreviewHtml();

        if (is_array($html)) {
            $html = implode("\n", $html);
        }

        return $html;
    }
    /**
     * Checks if a field is mandatory or not.
     * Take in count the fact that a field may be conditionnal
     *
     * @param array|null $formData Values sent in the form
     * @return bool
     */
    public function isMandatory($formData = null)
    {
        $driver = $this->field->getDriver();
        if (empty($driver)) {
            return false;
        }

        return $driver->checkDisplayable($formData) && $driver->isMandatory($formData);
    }
}
