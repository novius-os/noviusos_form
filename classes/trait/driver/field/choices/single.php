<?php

namespace Nos\Form;

/**
 * Implements choices (list of values or keys/values)
 *
 * @package Nos\Form
 */
trait Trait_Driver_Field_Choices_Single
{
    /**
     * Renders the answer as HTML
     *
     * @param Model_Answer_Field $answerField
     * @return mixed|string
     */
    public function renderAnswerHtml(Model_Answer_Field $answerField)
    {
        // Gets the answer value
        $value = $this->sanitizeValue($answerField->value);

        $value = $this->getValueChoiceLabel($value);

        return e($value);
    }

    /**
     * Renders the answer as a string for export
     *
     * @param Model_Answer_Field $answerField
     * @return string|array
     */
    public function renderExportValue(Model_Answer_Field $answerField)
    {
        // Gets the answer value
        $selectedValue = $this->sanitizeValue($answerField->value);

        $export = $this->getValueChoiceLabel($selectedValue);

        return $export;
    }

    /**
     * Renders the header for the answer export
     *
     * @return array
     */
    public function renderExportHeader()
    {
        $choicesList = $this->getChoicesList();

        // Builds the choices rows
        $choices = array_map(function($label, $value) {
            return $label.' ('.$value.')';
        }, $choicesList, array_keys($choicesList));

        return array(
            'label' => $this->getField()->field_label,
            'choices' => $choices,
        );
    }

    /**
     * Gets the data for the mail sent at submission
     *
     * @param $inputValue
     * @return array
     */
    public function getEmailData($inputValue, Model_Answer $answer)
    {
        // Gets the answer value
        $selectedValue = $this->sanitizeValue($inputValue);

        $html = $this->getValueChoiceLabel($selectedValue);

        return array(
            'label' => $this->getField()->field_label,
            'value' => e($html),
        );
    }

    /**
     * Gets the choice label for the specified value
     *
     * @param $value
     * @return mixed
     */
    protected function getValueChoiceLabel($value)
    {
        // Gets the choices
        $choices = $this->getChoicesList();

        $selectedValue = '';

        $hashValue = $this->convertChoiceValueToHash($value);
        if (array_key_exists($hashValue, $choices)) {
            $selectedValue = \Arr::get($choices, $hashValue);
        } else {
            $selectedValue = \Arr::get($choices, $value, $value);
        }

        return $selectedValue;
    }
    
    /**
     * Gets the choices
     *
     * @return array
     */
    protected function getChoices()
    {
        if (is_array($this->getField()->field_choices)) {
            return $this->getField()->field_choices;
        } else {
            return preg_split('`\r\n|\r|\n`', $this->getField()->field_choices);
        }
    }

    /**
     * Gets the choices list
     *
     * @param array|null $onlyValues
     * @return array
     */
    protected function getChoicesList($onlyValues = null)
    {
        $choices = $this->getChoices();

        $choiceList = array();
        foreach ($choices as $index => $choice) {
            $choiceInfos = preg_split('`(?<!\\\)=`', $choice, 2);
            if (count($choiceInfos) === 2) {
                foreach ($choiceInfos as $key => $choiceValue) {
                    $choiceInfos[$key] = str_replace('\=', '=', $choiceValue);
                }
                $choiceLabel = (string) $choiceInfos[0];
                $choiceValue = $this->hashChoiceValue($choiceInfos[1] ?: $choiceLabel);
            } else {
                $choiceLabel = $choice;
                $choiceValue = (string) $index;
            }

            if (is_null($onlyValues) || (is_array($onlyValues) && in_array($choiceValue, $onlyValues))) {
                $choiceList[$choiceValue] = $choiceLabel;
            }
        }

        // Prepends with the default value
        if (empty($choiceList) && is_null($onlyValues)) {
            $choiceList = array('' => '');
        }


        return $choiceList;
    }

    /**
     * Gets a choice value by hash
     *
     * @param $hash
     * @return int|mixed|string
     */
    protected function getChoiceValueByHash($hash)
    {
        // Searches the specified choice in the available choices
        $choices = $this->getChoices();
        foreach ($choices as $choice) {

            // Split parts
            $choiceParts = preg_split('`(?<!\\\)=`', $choice, 2);
            if (count($choiceParts) === 2) {

                // Unescapes escaped equal signs
                $choiceParts = array_map(function($choice) {
                    return str_replace('\=', '=', $choice);
                }, $choiceParts);

                // Checks if hash match value hash
                $choiceValue = $choiceParts[1];
                if (!empty($choiceValue) && $hash === $this->hashChoiceValue($choiceValue)) {
                    return $choiceValue;
                }
            }
        }

        return $hash;
    }

    /**
     * Hashes the given option value if needed
     *
     * @param $value
     * @return mixed|string
     */
    protected function convertChoiceValueToHash($value)
    {
        // Searches the specified choice in the available choices
        $choices = $this->getChoices();
        foreach ($choices as $index => $choice) {

            // Split parts
            $choiceParts = preg_split('`(?<!\\\)=`', $choice, 2);
            if (count($choiceParts) === 2) {

                // Unescapes escaped equal signs
                $choiceParts = array_map(function($choice) {
                    return str_replace('\=', '=', $choice);
                }, $choiceParts);

                // Checks if value match
                $choiceValue = $choiceParts[1];
                if (!empty($choiceValue) && $value === $choiceValue) {
                    $value = $this->hashChoiceValue($choiceValue);
                    break;
                }
            }
        }

        return $value;
    }

    /**
     * Hash the specified value
     *
     * @param $value
     * @return string
     */
    protected function hashChoiceValue($value)
    {
        return hash('sha256', $value);
    }
}
