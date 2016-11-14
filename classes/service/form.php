<?php

namespace Nos\Form;

class Service_Form
{
    /**
     * @var Model_Form
     */
    protected $form;

    /**
     * Constructor
     *
     * @param Model_Form $form
     */
    public function __construct(Model_Form $form)
    {
        $this->form = $form;
    }

    /**
     * Forges a new instance
     *
     * @param Model_Form $form
     * @return static
     */
    public static function forge(Model_Form $form)
    {
        return new static($form);
    }

    /**
     * Gets the form fields layout
     *
     * @param array $errors
     * @param array $options
     * @return array
     */
    public function getFieldsLayout($errors = array(), $options = array())
    {
        // Gets the form layout
        $layout = $this->getLayout();

        // Adds the captcha to the layout (if enabled)
        if (!empty($this->form->form_captcha)) {
            $layout[][] = array(
                'field_name' => 'captcha',
                'field_width' => 4,
            );
        }

        // Builds the fields layout
        $fieldsLayout = array();
        $pageIndex = $rowIndex = $colIndex = 0;
        foreach ($layout as $rows) {
            foreach ($rows as $row) {
                $field_name = \Arr::get($row, 'field_name');
                $field_width = \Arr::get($row, 'field_width');

                // Page break
                if ($field_name == 'page_break') {
                    // Starts a new page
                    $pageIndex++;
                    $rowIndex = $colIndex = 0;
                    continue;
                }

                // Captcha
                elseif ($field_name == 'captcha') {
                    $field = Model_Field::forge(array(
                        'field_name' => 'form_captcha',
                        'field_label' => '',
                        'field_driver' => Driver_Field_Input_Captcha::class,
                        'field_mandatory' => '1',
                        'field_technical_id' => '',
                        'field_technical_css' => '',
                        'field_default_value' => '',
                        'field_virtual_name' => 'form_captcha',
                    ));
                }

                // Other fields
                else {
                    $field = $this->form->fields[$field_name];
                }

                // Gets the driver
                $fieldDriver = $field->getDriver($options);

                // Gets the field name
                $name = $fieldDriver->getVirtualName();

                // Sets the errors
                $fieldDriver->setErrors(\Arr::get($errors, $name, array()));

                // Gets the input value or the default value
                $value = $fieldDriver->getInputValue($fieldDriver->getDefaultValue());

                // Builds the field
                $fieldsLayout[$pageIndex][$rowIndex][$colIndex] = array(
                    'item' => $field,
                    'name' => $name,
                    'label' => $fieldDriver->getLabel(),
                    'field' => $fieldDriver->getHtml($value),
                    'instructions' => $fieldDriver->getInstructions(),
                    'width' => $field_width,
                    'view' => \Arr::get($fieldDriver->getConfig(), 'front.view'),
                );
                $colIndex++;
            }

            $rowIndex++;
        }

        return $fieldsLayout;
    }

    /**
     * Gets the form layout as an array
     *
     * @return array
     */
    public function getLayout()
    {
        // Gets the form layout
        $layout = explode("\n", $this->form->form_layout);

        // Extract rows
        $layout = array_map(function($rows) {
            $rows = explode(',', $rows);

            // Cleanup empty rows
            $rows = array_filter($rows);

            // Extract fields
            $rows = array_map(function($row) {
                $row = explode('=', $row);
                return array(
                    'field_name' => $row[0],
                    'field_width' => $row[1],
                );
            }, $rows);

            return $rows;
        }, $layout);

        return $layout;
    }

    /**
     * Gets the layout fields name in order of their appearance in the layout
     *
     * @return array
     */
    public function getLayoutFieldsName()
    {
        return \Arr::flatten(array_map(function($row) {
            return \Arr::pluck($row, 'field_name');
        }, $this->getLayout()));
    }

    /**
     * Gets the count of page break in the form layout
     *
     * @return int
     */
    public function getPageBreakCount()
    {
        return \Arr::get(array_count_values($this->getLayoutFieldsName()), 'page_break');
    }

    /**
     * Validates the form fields data
     *
     * @param array $fields
     * @param array $data
     * @return array
     */
    public function validateFieldsData(array $fields, array &$data)
    {
        $errors = array();

        // Fields validation
        foreach ($data as $name => $value) {
            $field = \Arr::get($fields, $name);

            // Gets the validation errors
            $errors = \Arr::merge($errors, Service_Field::forge($field)->getValidationErrors($value, $data));
        }

        // Custom validation
        foreach ((array) \Event::trigger_function('noviusos_form::data_validation', array(&$data, $fields, $this->form), 'array') as $array) {
            if ($array === null) {
                continue;
            }
            foreach ($array as $name => $error) {
                $errors[$name] = $error.(isset($errors[$name]) ? "\n".$errors[$name] : '');
            }
        }
        if (!empty($this->form->form_virtual_name)) {
            foreach ((array) \Event::trigger_function('noviusos_form::data_validation.'.$this->form->form_virtual_name, array(&$data, $fields, $this->form), 'array') as $array) {
                if ($array === null) {
                    continue;
                }
                foreach ($array as $name => $error) {
                    $errors[$name] = (isset($errors[$name]) ? $errors[$name]."\n" : '').$error;
                }
            }
        }

        // Captcha validation
        if ($this->form->form_captcha && \Session::get('captcha.'.$this->form->form_id) != \Input::post('form_captcha', 0)) {
            $errors['form_captcha'] = __('You have not passed the spam test. Please try again.');
        }

        return $errors;
    }
}