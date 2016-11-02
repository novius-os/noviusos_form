<?php
/**
 * NOVIUS OS - Web OS for digital communication
 *
 * @copyright  2011 Novius
 * @license    GNU Affero General Public License v3 or (at your option) any later version
 *             http://www.gnu.org/licenses/agpl-3.0.html
 * @link http://www.novius-os.org
 */

namespace Nos\Form;

class Controller_Admin_Form extends \Nos\Controller_Admin_Crud
{
    protected $to_delete = array();
    protected $fields_fieldset = array();
    protected $fields_data = array();

    public function prepare_i18n()
    {
        parent::prepare_i18n();
        \Nos\I18n::current_dictionary('noviusos_form::common');
    }

    protected function init_item()
    {
        $this->item->form_captcha = 1;
        parent::init_item();
    }

    /**
     * Before saving the form
     *
     * @param $item
     * @param $data
     * @throws \Exception
     * @throws \FuelException
     */
    public function before_save($item, $data)
    {
        $emails = explode("\n", $item->form_submit_email);
        $item->form_submit_email = '';
        foreach ($emails as $email) {
            $email = trim($email);
            if (empty($email)) {
                continue;
            }
            if (filter_var(trim($email), FILTER_VALIDATE_EMAIL)) {
                $item->form_submit_email .= $email."\n";
            } else {
                throw new \Exception('An email which receive answers is not valid.');
            }
        }

        // Gets the fields data (the field data is json encoded, see Pull Request #15)
        $fields_post = \Input::post('fields', null);
        if (empty($fields_post)) {
            throw new \Exception(__('Error: Your form seems to have no field.'));
        }
        $fields_data = json_decode($fields_post, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            logger(\Fuel::L_ERROR, 'Error: invalid fields format. JSON Error: '.json_last_error_msg());
        }
        if (empty($fields_data)) {
            throw new \Exception(__('Your form must have at least one field.'));
        }

        // Formats fields data
        foreach ($fields_data as $index => $field) {

            // The default_value from POST is a comma-separated string of the indexes
            // We want to store textual values (separated by \n for the multiple values of checkboxes)
            if (isset($field['field_choices'])) {
                $choices = explode("\n", $field['field_choices']);
                // Check possible values in choices
                $choiceList = array();
                foreach ($choices as $choice) {
                    $choiceInfos = preg_split('~(?<!\\\)=~', $choice);
                    foreach ($choiceInfos as $key => $choiceValue) {
                        $choiceInfos[$key] = str_replace("\=", "=", $choiceValue);
                    }
                    $choiceList[] = \Arr::get($choiceInfos, 1, $choiceInfos[0]);
                }
                $choices = array_combine($choiceList, $choiceList);
                $default_value = explode(',', $field['field_default_value']);
                $default_value = array_combine($default_value, $default_value);
                $fields_data[$index]['field_default_value'] = implode("\n", array_intersect_key($choices, $default_value));
            }
        }

        // Deletes missing fields
        $this->to_delete = array_diff(
            array_keys($item->fields),
            \Arr::pluck($fields_data, 'field_id')
        );

        // Registers the fields
        foreach ($fields_data as $field_id => $field_data) {
            $this->fields_fieldset[$field_id] = \Fieldset::build_from_config(\Arr::get($this->config, 'fields_config.meta.fields'), array(
                'save' => false,
            ));
            $this->fields_data[$field_id] = $field_data;
            $item->fields[$field_id] = Model_Field::find($field_id);
        }
    }

    /**
     * Saves the form
     *
     * @param $item
     * @param $data
     * @return \Nos\Array
     */
    public function save($item, $data)
    {
        $return = parent::save($item, $data);

        // Save the form registered fields
        foreach ($this->fields_fieldset as $field_id => $fieldset) {
            if (in_array($field_id, $this->to_delete)) {
                continue;
            }
            $field = $item->fields[$field_id];
            $field->field_form_id = $item->form_id;
            $fieldset->validation()->run($this->fields_data[$field_id]);
            $fieldset->triggerComplete($field, $fieldset->validated());
        }

        // Delete fields
        foreach ($this->to_delete as $field_id) {
            $item->fields[$field_id]->delete();
            unset($item->fields[$field_id]);
        }
        $this->to_delete = array();

        return $return;
    }

    /**
     * Renders the specified layout
     *
     * @param $layoutName
     * @throws \Exception
     */
    public function action_render_layout($layoutName)
    {
        $fields = $previews = array();

        // Page break
        if ($layoutName == 'page_break') {
            $data = array(
                'field_form_id' => '0',
                'field_virtual_name' => uniqid(),
            );
            foreach (\Arr::get($this->config, 'fields_config.meta.fields') as $name => $field) {
                if (!empty($field['dont_save']) || (!empty($field['form']['type']) && $field['form']['type'] == 'submit')) {
                    continue;
                }
                $data[$name] = '';
            }
            unset($data['field_id']);
            $data['field_type'] = 'page_break';
            $data['field_label'] = __('Page break');
            $field = Model_Field::forge($data, true);
            $field->save();

            $fields[] = (string) $this->render_field_meta($field);
            $previews[] = (string) $this->render_field_preview($field);

            $layout = $field->field_id.'=4';
        }
        // Other fields
        else {

            // Gets the field definition
            if ($layoutName == 'default') {
                $definition = \Arr::get($this->config, 'fields_config.layout.default.definition', array());
            } else {
                $definition = \Arr::get($this->config, 'fields_config.layout.available.'.$layoutName.'.definition', array());
            }
            if (empty($definition)) {
                throw new \Exception('Field definition not found.');
            }

            // Creates the fields
            $layout = \Arr::get($definition, 'layout');
            foreach (\Arr::get($definition, 'fields', array()) as $field_identifier => $field_properties) {

                // Gets the driver
                $driverClass = \Arr::get($field_properties, 'driver');
                if (empty($driverClass)) {
                    throw new \Exception('The `driver` key is missing in the driver configuration.');
                }

                // Builds the field data
                $field_data = \Arr::get($field_properties, 'default_values');
                $field_data['field_driver'] = $driverClass;

                // Creates the field in database
                $field = $this->create_field_db($field_data);

                // Renders the field
                $fields[] = (string) $this->render_field_meta($field);
                $previews[] = $this->render_field_preview($field);

                // Replaces the field identifier by the primary key in the layout
                $layout = str_replace($field_identifier, $field->field_id, $layout);
            }
        }

        \Response::json(array(
            'fields' => $fields,
            'previews' => $previews,
            'layout' => $layout,
        ));
    }

    /**
     * @deprecated Please use action_render_layout() instead
     *
     * @param $meta
     * @throws \Exception
     */
    public function action_form_field_meta($meta)
    {
        $this->action_render_layout($meta);
    }

    /**
     * Renders the field
     *
     * @param $field_id
     * @throws \Exception
     * @throws \FuelException
     */
    public function action_render_field($field_id)
    {
        // Gets the field
        $field = Model_Field::find($field_id);
        if (empty($field)) {
            throw new \Exception('Field not found');
        }

        // Injects the specified field data
        $fieldData = $this->getInputFieldData();
        \Arr::delete($fieldData, 'field_id');
        if (!empty($fieldData)) {
            $field->set($fieldData);
        }

        // Renders the field
        $html = (string) $this->render_field_meta($field);

        \Response::json(array(
            'meta' => $html,
            'preview' => $this->render_field_preview($field),
        ));
    }

    /**
     * Handles the request to displays a new field in a JSON response
     *
     * @param $field_id
     * @throws \Exception
     */
    public function action_render_field_preview($field_id)
    {
        $field_id = intval($field_id);

        // Gets the field or forge a new one
        if (!empty($field_id)) {
            $field = Model_Field::find($field_id);
            if (empty($field)) {
                throw new \Exception(__('Field not found.'));
            }
        } else {
            $field = Model_Field::forge();
        }

        // Gets the field data
        $fieldData = $this->getInputFieldData();
        if (!empty($fieldData)) {
            \Arr::delete($fieldData, 'field_id');
            // Sets the field data
            $field->set($fieldData);
        }

        \Response::json(array(
            'preview' => $this->render_field_preview($field),
        ));
    }

    /**
     * Handles the request to render the specified field
     *
     * @param $field
     * @return string
     */
    public function action_render_field_meta($field)
    {
        return $this->render_field_meta($field);
    }

    /**
     * Exports the form answers
     *
     * @param $id
     * @throws
     */
    public function action_export($id)
    {
        set_time_limit(5 * 60);

        try {
            $helper = new Helper_Export();
            $helper->parseForm($id);
            $headers = $helper->headers;

            $csv = array(
                'header' => array(),
            );

            foreach ($headers as $header) {
                $csv['header'][] = $header['label'];
                if (!empty($header['choices'])) {
                    if (!isset($csv['choices'])) {
                        $csv['choices'] = array();
                    }
                    $fill = count($csv['header']) - 1;
                    $fill > 0 and $csv['choices'] = array_pad($csv['choices'], $fill, '');
                    foreach ($header['choices'] as $choice) {
                        $csv['choices'][] = $choice;
                    }
                    $csv['header'] = array_pad($csv['header'], count($csv['header']) + count($header['choices']) - 1, '');
                }
            }

            // Disable response buffering
            $level = ob_get_level();
            for ($i = 0; $i < $level; $i++) {
                ob_end_clean();
            }

            // Enable garbage collector
            gc_enable();

            // Send HTTP headers for inform the browser that it will receive a CSV file
            \Response::forge(\Format::forge($csv)->to_csv() . "\n", 200, array(
                'Content-Type'              => 'application/csv',
                'Content-Disposition'       => 'attachment; ' .
                    'filename=' . \Nos\Orm_Behaviour_Virtualname::friendly_slug($this->crud_item($id)->form_name) . '.csv;',
                'Content-Transfer-Encoding' => 'binary',
            ))->send(true);

            while (($values = $helper->getValues())) {
                $csv = array();
                foreach ($values as $value) {
                    $csvValue = array();
                    foreach ($value as $key => $valueContent) {
                        if (!is_array($valueContent)) {
                            $csvValue[] = $valueContent;
                        } else {
                            $csvValue += \Arr::merge($csvValue, $valueContent);
                        }
                    }
                    $csv[] = $csvValue;
                }
                \Response::forge(\Format::forge($csv)->to_csv() . "\n")->send();
            }
            exit();
        } catch (\Exception $e) {
            $this->send_error($e);
        }
    }

    /**
     * Renders the field preview
     *
     * @param Model_Field $field
     * @return string
     * @throws \Exception
     * @throws \FuelException
     */
    protected function render_field_preview(Model_Field $field)
    {
        // Field with a driver
        $fieldDriver = method_exists($field, 'getDriver') ? $field->getDriver($this->enhancer_args) : null;
        if (!empty($fieldDriver)) {
            $html = $fieldDriver->getPreviewHtml();
        } else {
            $html = 'No preview.';
        }

        if (is_array($html)) {
            $html = implode("\n", $html);
        }

        return $html;
    }

    /**
     * Renders the field meta
     *
     * @param $field
     * @return string
     * @throws \FuelException
     */
    protected function render_field_meta($field)
    {
        static $auto_id_increment = 1;

        // Gets the fields config
        $fields_config = \Arr::get($this->config, 'fields_config.meta.fields');
        if ($field->field_type == 'page_break') {
            $fields_config['field_type']['form']['options'] = array('page_break' => __('Page break'));
        }

        // Builds the fieldset
        $fieldset = \Fieldset::build_from_config($fields_config, $field, array('save' => false, 'auto_id' => false));

        // Override auto_id generation so it don't use the name (because we replace it below)
        $auto_id = uniqid('auto_id_');
        foreach ($fieldset->field() as $fieldsetField) {
            if ($fieldsetField->get_attribute('id') == '') {
                $fieldsetField->set_attribute('id', $auto_id.$auto_id_increment++);
            }
        }

        // Builds the layout
        $layout = \Arr::get($this->config, 'fields_config.meta.layout');
        $fieldDriver = method_exists($field, 'getDriver') ? $field->getDriver($this->enhancer_args) : null;
        if (!empty($fieldDriver)) {

            // Gets the field meta layout
            $fieldConfig = $fieldDriver::getConfig();
            $fieldMetaLayout = \Arr::get($fieldConfig, 'meta.layout', array());

            // Merges the field layout with the default layout
            if (!empty($fieldMetaLayout)) {

                $layoutAccordions = \Arr::get($layout, 'standard.params.accordions', array());

                foreach ($fieldMetaLayout as $name => $params) {
                    if (isset($layoutAccordions[$name])) {
                        // Merges the configs
                        $layoutAccordions[$name] = \Arr::merge($layoutAccordions[$name], $params);

                        // If fields are specified in the driver config they have to replace the default fields
                        if (isset($params['fields'])) {
                            $layoutAccordions[$name]['fields'] = $params['fields'];
                        }

                        // Deletes the field_driver if present (it will be pushed again later, see below)
                        if (isset($layoutAccordions[$name]['fields'])) {
                            $key = array_search('field_driver', $layoutAccordions[$name]['fields']);
                            if ($key !== false) {
                                unset($layoutAccordions[$name]['fields'][$key]);
                            }
                        }
                    }
                }

                // Ensure the default panel is present with the driver as first field
                $layoutAccordions['main'] = \Arr::merge(array(
                    'title' => __('Properties'),
                    'fields' => array(
                        'field_driver'
                    ),
                ), $layoutAccordions['main']);

                \Arr::set($layout, 'standard.params.accordions', $layoutAccordions);
            }
        }

        // Builds the view params
        $fields_view_params = array(
            'layout' => $layout,
            'fieldset' => $fieldset,
        );
        $fields_view_params['view_params'] = &$fields_view_params;

        // Renders the field content
        if ($field->field_type == 'page_break') {
            $content = (string)\View::forge('noviusos_form::admin/page_break', $fields_view_params, false);
        } else {
            $content = (string) \View::forge('noviusos_form::admin/layout', $fields_view_params, false)->render();
            $content .= $fieldset->build_append();
        }

        // Replace name="field[field_type][]" "with field[field_type][12345]" <- add field_ID here
        $replaces = array();
        foreach ($fields_config as $name => $field_config) {
            $replaces[$name] = "field[{$field->field_id}][$name]";
        }
        $content = strtr($content, $replaces);

        return $content;
    }

    /**
     * Creates a field in database
     *
     * @param array $data
     * @return static
     * @throws \Exception
     */
    protected function create_field_db($data = array())
    {
        $default_data = array(
            'field_form_id' => '0',
            'field_virtual_name' => uniqid(),
        );

        // Gets the default values
        foreach (\Arr::get($this->config, 'fields_config.meta.fields') as $name => $field) {
            if (!empty($field['dont_save']) || (!empty($field['form']['type']) && $field['form']['type'] == 'submit')) {
                continue;
            }
            $default_data[$name] = \Arr::get($field, 'form.value', '');
        }

        unset($default_data['field_id']);
        $default_data['field_mandatory'] = 0;
        $default_data['field_conditional'] = 0;

        // Creates and saves the field
        $model_field = Model_Field::forge(array_merge($default_data, $data), true);
        $model_field->save();

        return $model_field;
    }

    /**
     * Gets the input field data (JSON or array)
     *
     * @param string $name
     * @return array
     */
    protected function getInputFieldData($name = 'fieldData')
    {
        // Gets the field data
        $fieldData = \Input::post($name);
        if (empty($fieldData)) {
            return array();
        }

        // Json decodes if it's a string
        if (is_string($fieldData)) {
            $fieldData = (array) json_decode($fieldData);
        }

        if (!is_array($fieldData)) {
            return array();
        }

        // Flatten the array
        $fieldData = \Arr::pluck($fieldData, 'value', 'name');

        return $fieldData;
    }
}
