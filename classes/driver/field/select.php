<?php

namespace Nos\Form;

use Fuel\Core\Form;

class Driver_Field_Select extends Driver_Field_Abstract
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
            'callback' => array('Form', 'select'),
            'args' => array(
                $this->getVirtualName(),
                $this->getValue(),
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
     * Gets the value
     *
     * @return mixed|string
     */
    public function getValue()
    {
        $value = parent::getValue();
        $value = $this->convertChoiceValueToHash($value);
        return $value;
    }

    /**
     * Triggered before form submission
     *
     * @param Model_Form $form
     */
    public function beforeSubmission(Model_Form $form)
    {
        if ($this->field->field_technical_id === 'recipient-list') {
            // Adds value(s) to recipient list
            foreach ((array) $this->getValue() as $v) {
                if (preg_match("/" . preg_quote($v) . "$/m", $this->getChoices())) {
                    $form->form_submit_email .= $v."\n";
                }
            }
        }
    }

    /**
     * Gets the HTML attributes
     *
     * @return array
     */
    public function getHtmlAttributes()
    {
        $attributes = parent::getHtmlAttributes();

        // Sets the label as placeholder if option is specified
        if ($this->getOption('label_position') === 'placeholder') {
            $attributes['placeholder'] = $this->field->field_label;
        }

        // Sets the error state
        if ($this->hasErrors()) {
            if ($this->getOption('label_position') === 'placeholder') {
                $attributes['class'] .= ' user-error form-ui-invalid';
                $attributes['title'] = htmlspecialchars($this->getErrors());
            }
        }

        if (!empty($this->field->field_height)) {
            $html_attrs['rows'] = $this->field->field_height;
        }

        return $attributes;
    }
}
