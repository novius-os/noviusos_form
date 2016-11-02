<?php

namespace Nos\Form;

use Fuel\Core\Crypt;
use Fuel\Core\Form;

class Field_Select extends Field_Abstract
{
    /**
     * Gets the HTML content
     *
     * @return array
     */
    public function getHtml()
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
        return Form::select('', $this->getDefaultValue(), $this->getChoicesList());
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

        // Crypts the value if at least one choice is crypted
        $choices = explode("\n", $this->field->field_choices);
        foreach ($choices as $choice) {
            if (mb_strrpos($choice, '=')) {
                // Crypts the value
                $value = Crypt::encode($value);
                break;
            }
        }

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
                if (preg_match("/" . preg_quote($v) . "$/m", $this->field->field_choices)) {
                    $form->form_submit_email .= $v."\n";
                }
            }
        }
    }

    /**
     * Gets the choices list
     *
     * @return array
     */
    protected function getChoicesList()
    {
        $choices = $this->getChoices();
        $choiceList = array();
        foreach ($choices as $choice) {
            if (mb_strrpos($choice, '=')) {
                $choiceInfos = preg_split('~(?<!\\\)=~', $choice);
                foreach ($choiceInfos as $key => $choiceValue) {
                    $choiceInfos[$key] = str_replace("\=", "=", $choiceValue);
                }
                $choiceLabel = $choiceInfos[0];
                $choiceValue = Crypt::encode(\Arr::get($choiceInfos, 1, $choiceLabel));
            } else {
                $choiceLabel = $choiceValue = $choice;
            }
            $choiceList[$choiceValue] = $choiceLabel;
        }

        // Prepends with the default value
        $choiceList = array('' => '') + $choiceList;

        return $choiceList;
    }

    /**
     * Gets the HTML attributes
     *
     * @return array
     */
    protected function getHtmlAttributes()
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
