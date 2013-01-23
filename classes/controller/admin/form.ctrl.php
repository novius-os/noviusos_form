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
    protected static $to_delete = array();

    public function prepare_i18n()
    {
        parent::prepare_i18n();
        \Nos\I18n::current_dictionary('noviusos_form::common');
    }

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
                $item->form_submit_email .= $email . "\n";
            } else {
                throw new \Exception('An email which receive answers is not a valid.');
            }
        }

        $field_names = array();
        foreach ($this->config['fields_config'] as $name => $field) {
            if (!empty($field['dont_save']) || (!empty($field['form']['type']) && $field['form']['type'] == 'submit')) {
                continue;
            }
            $name = str_replace(array('field[', '][]'), '', $name);
            $field_names[] = $name;
        }
        // null is for the first argument of array_map() to transpose the matrix
        $values = array(null);
        $fields = array();
        foreach ($field_names as &$name) {
            $values[] = \Input::post('field.'.$name);
            $name = 'field_'.$name;
        }
        // If there are values
        if (!empty($values[1])) {
            foreach (call_user_func_array('array_map', $values) as $value) {
                $fields[] = array_combine(array_values($field_names), $value);
            }
        }

        foreach ($fields as $index => $field) {

            // The default_value from POST is a comma-separated string of the indexes
            // We want to store textual values (separated by \n for the multiple values of checkboxes)
            if (in_array($field['field_type'], array('checkbox', 'select', 'radio'))) {
                $choices = explode("\n", $field['field_choices']);
                $default_value = explode(',', $field['field_default_value']);
                $default_value = array_combine($default_value, $default_value);
                $fields[$index]['field_default_value'] = implode("\n", array_intersect_key($choices, $default_value));
            }

            if ($field['field_type'] == 'checkbox' && empty($values[$name])) {
                $field_config = $this->config['fields_config']['field['.substr($name, 6).'][]']['form'];
                // Empty checkboxes should be populated with the 'empty' key of the configuration array
                // We need to do it manually here, since we're not using the Fieldset class
                if (isset($field_config['empty'])) {
                    $fields[$index][$name] = $field_config['empty'];
                }
            }
        }

        static::$to_delete = array_diff(
            array_keys($item->fields),
            static::array_pluck($fields, 'field_id')
        );

        foreach ($fields as $field) {
            $field_id = $field['field_id'];
            $model_field = Model_Field::find($field_id);
            unset($field['field_id']);
            $model_field->set($field);
            $item->fields[$field_id] = $model_field;
        }
    }

    public function save($item, $data)
    {
        $return = parent::save($item, $data);
        foreach ($item->fields as $field) {
            if (in_array($field->field_id, static::$to_delete)) {
                continue;
            }
            $field->field_form_id = $item->form_id;
            $field->save();
        }
        foreach (static::$to_delete as $field_id) {
            $item->fields[$field_id]->delete();
            unset($item->fields[$field_id]);
        }
        return $return;
    }

    public function action_form_field_meta($meta)
    {
        if ($meta == 'page_break') {
            return $this->page_break();
        }
        if ($meta == 'default') {
            $definition = $this->config['fields_meta']['default']['definition'];
        } else {
            $lookup = $this->config['fields_meta']['standard'] + $this->config['fields_meta']['special'];
            $definition = $lookup[$meta]['definition'];
        }
        $fields = array();
        $layout = $definition['layout'];
        foreach ($definition['fields_list'] as $type => $field_data) {
            $field = $this->create_field_db($field_data);
            $fields[] = $this->action_render_field($field);
            $layout = str_replace($type, $field->field_id, $layout);
        }
        \Response::json(array(
            'fields' => implode("\n", $fields),
            'layout' => $layout,
        ));
    }

    public function create_field_db($data = array())
    {

        $default_data = array(
            'field_form_id' => '0',
            'field_virtual_name' => uniqid(),
        );
        foreach ($this->config['fields_config'] as $name => $field) {
            if (!empty($field['dont_save']) || (!empty($field['form']['type']) && $field['form']['type'] == 'submit')) {
                continue;
            }
            $name = str_replace(array('field[', '][]'), '', $name);
            $default_data['field_'.$name] = \Arr::get($field, 'form.value', '');
        }
        unset($default_data['field_id']);
        $default_data['field_mandatory'] = 0;
        $model_field = Model_Field::forge(array_merge($default_data, $data), true);
        $model_field->save();
        return $model_field;
    }

    public function action_render_field($item, $view = null)
    {
        // This action is not available from the browser. Only internal requests are authorised.
        if (!empty($view) && !\Request::is_hmvc()) {
            exit();
        } else {
            $view = 'noviusos_form::admin/layout';
        }

        if ($item->field_type == 'page_break') {
            return $this->render_page_break($item);
        }

        $fieldset = \Fieldset::build_from_config($this->config['fields_config'], $item, array('save' => false));
        $fields_view_params = array(
            'layout' => $this->config['fields_layout'],
            'fieldset' => $fieldset,
        );
        $fields_view_params['view_params'] = &$fields_view_params;
        return \View::forge($view, $fields_view_params, false);
    }

    public function page_break()
    {
        $data = array(
            'field_form_id' => '0',
            'field_virtual_name' => uniqid(),
        );
        foreach ($this->config['fields_config'] as $name => $field) {
            if (!empty($field['dont_save']) || (!empty($field['form']['type']) && $field['form']['type'] == 'submit')) {
                continue;
            }
            $name = str_replace(array('field[', '][]'), '', $name);
            $data['field_'.$name] = '';
        }
        unset($data['field_id']);
        $data['field_type'] = 'page_break';
        $data['field_label'] = __('Page break');
        $item = Model_Field::forge($data, true);
        $item->save();

        \Response::json(array(
            'fields' => (string) $this->render_page_break($item),
            'layout' => $item->field_id.'=4',
        ));
    }

    public function render_page_break($item)
    {

        $fields_config = $this->config['fields_config'];
        $fields_config['field[type][]']['form']['options'] = array('page_break' => __('Page break'));
        $fieldset = \Fieldset::forge(uniqid(), array('auto_id' => false));
        $fieldset->add_renderers($fields_config);
        $fieldset->populate_with_instance($item);
        $fields_view_params = array(
            'layout' => $this->config['fields_layout'],
            'fieldset' => $fieldset,
        );
        $fields_view_params['view_params'] = &$fields_view_params;
        return \View::forge('noviusos_form::admin/page_break', $fields_view_params, false);
    }

    /**
     * Pluck an array of values from an array.
     *
     * @param  array   $array  collection of arrays to pluck from
     * @param  string  $key    key of the value to pluck
     * @param  string  $index  optional return array index key, true for original index
     * @return array   array of plucked values
     */
    public static function array_pluck($array, $key, $index = null)
    {
        $return = array();
        $get_deep = strpos($key, '.') !== false;

        if ( ! $index) {
            foreach ($array as $i => $a) {
                $return[] = (is_object($a) and ! ($a instanceof \ArrayAccess)) ? $a->{$key} :
                    ($get_deep ? static::get($a, $key) : $a[$key]);
            }
        } else {
            foreach ($array as $i => $a) {
                $index !== true and $i = (is_object($a) and ! ($a instanceof \ArrayAccess)) ? $a->{$index} : $a[$index];
                $return[$i] = (is_object($a) and ! ($a instanceof \ArrayAccess)) ? $a->{$key} :
                    ($get_deep ? static::get($a, $key) : $a[$key]);
            }
        }

        return $return;
    }

    public function action_export($id)
    {
        try {
            $this->item = $this->crud_item($id);
            if ($this->item->is_new()) {
                throw new \Exception($this->config['messages']['not found']);
            }

            $layout = explode("\n", $this->item->form_layout);
            array_walk($layout, function(&$v) {
                $v = explode(',', $v);
            });

            // Cleanup empty values
            foreach ($layout as $a => $rows) {
                $layout[$a] = array_filter($rows);
                if (empty($layout[$a])) {
                    unset($layout[$a]);
                    continue;
                }
            }

            $fields = array();
            $csv = array(
                'header' => array(),
            );

            foreach ($layout as $rows) {
                foreach ($rows as $row) {
                    list($field_id, $width) = explode('=', $row);

                    if ($field_id == 'captcha') {
                        continue;
                    }
                    $field = $this->item->fields[$field_id];
                    if (!in_array($field->field_type, array('text', 'textarea', 'select', 'email', 'number', 'date', 'checkbox', 'radio', 'hidden', 'variable', 'file'))) {
                        continue;
                    }

                    $fields[] = $field;
                    $csv['header'][] = $field->field_label;
                    if (in_array($field->field_type, array('select', 'checkbox', 'radio'))) {
                        if (empty($csv['choices'])) {
                            $csv['choices'] = array_fill(0, count($csv['header']) - 1, '');
                        }
                        $choices = explode("\n", $field->field_choices);
                        foreach ($choices as $choice) {
                            $csv['choices'][] = $choice;
                        }

                        $csv['header'] = array_pad($csv['header'], count($csv['header']) + count($choices) - 1, '');
                    } else if (!empty($csv['choices'])) {
                        $csv['choices'][] = '';
                    }
                }
            }


            foreach ($this->item->answers as $answer) {
                $values = array();
                foreach ($answer->fields as $answer_field) {
                    $values[$answer_field->anfi_field_id] = $answer_field;
                }

                $csv_row = array();
                foreach ($fields as $field) {
                    $value = !empty($values[$field->field_id]) ? $values[$field->field_id]->anfi_value : '';

                    if (in_array($field->field_type, array('select', 'checkbox', 'radio'))) {
                        $choices = explode("\n", $field->field_choices);
                        $selected = explode("\n", $value);
                        foreach ($choices as $choice) {
                            $csv_row[] = in_array($choice, $selected) ? 'x' : '';
                        }
                    } else if ($field->field_type === 'file') {
                        $attachment = $answer->getAttachment($field);
                        $csv_row[] = $attachment->filename();
                    } else {
                        $csv_row[] = $value;
                    }
                }
                $csv[] = $csv_row;
            }

            $csv = \Format::forge($csv)->to_csv();
            \Response::forge($csv, 200, array(
                    'Content-Type' => 'application/csv',
                    'Content-Disposition' => 'attachment; filename='.\Nos\Orm_Behaviour_Virtualname::friendly_slug($this->item->form_name).'.csv;',
                    'Content-Transfer-Encoding' => 'binary',
                    'Content-Length' => \Str::length($csv),
                ))->send(true);
            exit();
        } catch (\Exception $e) {
            $this->send_error($e);
        }
    }
}

