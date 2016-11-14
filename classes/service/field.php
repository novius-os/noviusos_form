<?php

namespace Nos\Form;

class Service_Field
{
    /**
     * @var Model_Field
     */
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
     * Gets the validation errors
     *
     * @param $value
     * @param null $formData
     * @return array
     */
    public function getValidationErrors($value, $formData = null)
    {
        $errors = array();

        // Gets the driver
        $fieldDriver = $this->field->getDriver();
        if (!empty($fieldDriver)) {

            $fieldName = $fieldDriver->getVirtualName();

            // Validates the field
            try {

                // Checks if displayable
                if ($fieldDriver->checkDisplayable($formData)) {

                    // Checks if mandatory
                    if (!$fieldDriver->checkRequirement($value, $formData)) {
                        $errors[$fieldName] = __('Please enter a value for this field.');
                    }
                    // Checks if validated
                    elseif (!$fieldDriver->checkValidation($value, $formData)) {
                        $errors[$fieldName] = __('Please enter a valid value for this field.');
                    }
                }
            }
            // Catch validation exceptions
            catch (Exception_Driver_Field_Validation $e) {
                $errors[$fieldName] = $e->getMessage();
            }
        }

        return $errors;
    }
}
