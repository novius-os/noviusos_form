<?php

namespace Nos\Form;

use Fuel\Core\Form;
use Nos\Tools_Wysiwyg;

class Driver_Field_Consent_Checkbox extends Driver_Field_Abstract
{
    use Trait_Driver_Field_Html_Attributes {
        getHtmlAttributes as getHtmlAttributesNative;
    }

    /**
     * Gets the label
     *
     * @return mixed
     */
    public function getLabel()
    {
        return $this->getFieldContent();
    }

    /**
     * Gets the sanitized input value
     *
     * @param string|null $defaultValue
     * @return mixed
     */
    public function getInputValue($defaultValue = null)
    {
        $checked = \Input::post($this->getVirtualName(), $defaultValue);
        $checked = $this->sanitizeValue($checked);

        return $checked ? $this->getFieldContent() : null;
    }

    /**
     * Gets the HTML content
     *
     * @param mixed|null $inputValue
     * @return mixed
     */
    public function getHtml($inputValue = null, $formData = array())
    {
        $attributes = $this->getHtmlAttributes();

        // Builds a field for each choice
        return array(
            array(
                'field' => array(
                    'callback' => array('Form', 'checkbox'),
                    'args' => array(
                        $this->getVirtualName(),
                        1,
                        false,
                        $attributes,
                    ),
                ),
                'label' => array(
                    'callback' => array('Form', 'label'),
                    'args' => array(
                        $this->getFieldContent(),
                        $this->field->field_technical_id,
                        array(
                            'for' => \Arr::get($attributes, 'id'),
                        ),
                    ),
                ),
                'template' => '<div class="form_checkbox">{field} {label}</div>',
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
        return array(
            html_tag('div', array('class' => 'form_checkbox'),
                Form::checkbox('', 1).html_tag('label', array(), $this->getFieldContent())
            )
        );
    }

    /**
     * Renders the answer as a string (eg. for displaying in backoffice)
     *
     * @param Model_Answer_Field $answerField
     * @return string
     */
    public function renderAnswerHtml(Model_Answer_Field $answerField)
    {
        return $this->sanitizeValue($answerField->value);
    }

    /**
     * Renders the answer as a string for export
     *
     * @param Model_Answer_Field $answerField
     * @return string|array
     */
    public function renderExportValue(Model_Answer_Field $answerField)
    {
        $value = parent::renderExportValue($answerField);
        $value = \Security::strip_tags($value);
        $value = html_entity_decode($value, ENT_COMPAT | HTML_ENTITIES, 'UTF-8');

        return $value;
    }

    /**
     * Gets the HTML attributes
     *
     * @return array
     */
    protected function getHtmlAttributes()
    {
        $attributes = $this->getHtmlAttributesNative();
        $attributes['title'] = \Security::strip_tags($this->getFieldContent());

        return $attributes;
    }

    /**
     * Gets the field's content
     *
     * @return string
     */
    protected function getFieldContent()
    {
        if (!isset($this->field->field_content)) {
            return null;
        }

        $content = Tools_Wysiwyg::parse($this->field->field_content);

        return $content;
    }
}
