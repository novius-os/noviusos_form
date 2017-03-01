<?php

namespace Nos\Form;

use Fuel\Core\Form;

class Driver_Field_Select extends Driver_Field_Abstract
{
    use Trait_Driver_Field_Html_Attributes {
        // Renames the getHtmlAttributes method for overriding
        getHtmlAttributes as getDefaultHtmlAttributes;
    }
    use Trait_Driver_Field_Choices_Single;

    /**
     * Gets the HTML content
     *
     * @param mixed|null $inputValue
     * @return mixed
     */
    public function getHtml($inputValue = null, $formData = array())
    {
        $value = $this->sanitizeValue($inputValue);
        $value = $this->convertChoiceValueToHash($value);

        return array(
            'callback' => array('Form', 'select'),
            'args' => array(
                $this->getVirtualName(),
                $value,
                $this->getChoicesList(),
                $this->getHtmlAttributes()
            ),
        );
    }

    /**
     * Gets the HTML preview
     *
     * @return string
     */
    public function getPreviewHtml()
    {
        $defaultValue = $this->convertChoiceValueToHash($this->getDefaultValue());
        return Form::select('', $defaultValue, $this->getChoicesList());
    }

    /**
     * Gets the instructions
     *
     * @return string
     */
    public function getInstructions()
    {
        return '';
    }

    /**
     * Gets the HTML attributes
     *
     * @return array
     */
    protected function getHtmlAttributes()
    {
        $attributes = $this->getDefaultHtmlAttributes();

        if (!empty($this->field->field_height)) {
            $html_attrs['rows'] = $this->field->field_height;
        }

        return $attributes;
    }
}
