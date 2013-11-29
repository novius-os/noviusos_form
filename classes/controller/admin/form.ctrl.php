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

        $fields_data = \Input::post('field', array());

        foreach ($fields_data as $index => $field) {
            // The default_value from POST is a comma-separated string of the indexes
            // We want to store textual values (separated by \n for the multiple values of checkboxes)
            if (in_array($field['field_type'], array('checkbox', 'select', 'radio'))) {
                $choices = explode("\n", $field['field_choices']);
                $default_value = explode(',', $field['field_default_value']);
                $default_value = array_combine($default_value, $default_value);
                $fields_data[$index]['field_default_value'] = implode("\n", array_intersect_key($choices, $default_value));
            }
        }

        $this->to_delete = array_diff(
            array_keys($item->fields),
            \Arr::pluck($fields_data, 'field_id')
        );

        foreach ($fields_data as $field_id => $field_data) {
            $this->fields_fieldset[$field_id] = \Fieldset::build_from_config($this->config['fields_config'], array(
                'save' => false,
            ));
            $this->fields_data[$field_id] = $field_data;
            $item->fields[$field_id] = Model_Field::find($field_id);
        }
    }

    public function save($item, $data)
    {
        $return = parent::save($item, $data);
        // Save form fields
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
            $default_data[$name] = \Arr::get($field, 'form.value', '');
        }
        unset($default_data['field_id']);
        $default_data['field_mandatory'] = 0;
        $model_field = Model_Field::forge(array_merge($default_data, $data), true);
        $model_field->save();
        return $model_field;
    }

    public function action_render_field($item)
    {
        if ($item->field_type == 'page_break') {
            return $this->render_page_break($item);
        }

        static $auto_id_increment = 1;

        $fieldset = \Fieldset::build_from_config($this->config['fields_config'], $item, array('save' => false, 'auto_id' => false));
        // Override auto_id generation so it don't use the name (because we replace it below)
        $auto_id = uniqid('auto_id_');
        foreach ($fieldset->field() as $field) {
            if ($field->get_attribute('id') == '') {
                $field->set_attribute('id', $auto_id.$auto_id_increment++);
            }
        }

        $fields_view_params = array(
            'layout' => $this->config['fields_layout'],
            'fieldset' => $fieldset,
        );
        $fields_view_params['view_params'] = &$fields_view_params;

        // Replace name="field[field_type][]" "with field[field_type][12345]" <- add field_ID here
        $replaces = array();
        foreach ($this->config['fields_config'] as $name => $field_config) {
            $replaces[$name] = "field[{$item->field_id}][$name]";
        }
        $return = (string) \View::forge('noviusos_form::admin/layout', $fields_view_params, false)->render().$fieldset->build_append();

        return strtr($return, $replaces);
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
            $data[$name] = '';
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
        static $auto_id_increment = 1;

        $fields_config = $this->config['fields_config'];
        $fields_config['field_type']['form']['options'] = array('page_break' => __('Page break'));
        $fieldset = \Fieldset::build_from_config($fields_config, $item, array('save' => false, 'auto_id' => false));

        // Override auto_id generation so it don't use the name (because we replace it below)
        $auto_id = uniqid('auto_id_');
        foreach ($fieldset->field() as $field) {
            if ($field->get_attribute('id') == '') {
                $field->set_attribute('id', $auto_id.$auto_id_increment++);
            }
        }

        $fields_view_params = array(
            'layout' => $this->config['fields_layout'],
            'fieldset' => $fieldset,
        );
        $fields_view_params['view_params'] = &$fields_view_params;

        // Replace name="field[field_type][]" "with field[field_type][12345]" <- add field_ID here
        $replaces = array();
        foreach ($this->config['fields_config'] as $name => $field_config) {
            $replaces[$name] = "field[{$item->field_id}][$name]";
        }
        $return = (string) \View::forge('noviusos_form::admin/page_break', $fields_view_params, false);

        return strtr($return, $replaces);
    }

    public function action_export($id)
    {
        set_time_limit(5 * 60);
        try {
            $this->item = $this->crud_item($id);
            if ($this->item->is_new()) {
                throw new \Exception($this->config['messages']['not found']);
            }

            $layout = explode("\n", $this->item->form_layout);
            array_walk($layout, function (&$v) {
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
                    list($field_id) = explode('=', $row);

                    if ($field_id == 'captcha') {
                        continue;
                    }
                    $field = $this->item->fields[$field_id];
                    if (!in_array($field->field_type, array('text', 'textarea', 'select', 'email', 'number', 'date',
                        'checkbox', 'radio', 'hidden', 'variable', 'file'))) {
                        continue;
                    }

                    $fields[] = $field;
                    $csv['header'][] = $field->field_label;
                    if (in_array($field->field_type, array('select', 'checkbox', 'radio'))) {
                        if (empty($csv['choices'])) {
                            $fill = count($csv['header']) - 1;
                            $fill > 0 and $csv['choices'] = array_fill(0, $fill, '');
                        }
                        $choices = explode("\n", $field->field_choices);
                        foreach ($choices as $choice) {
                            $csv['choices'][] = $choice;
                        }

                        $csv['header'] = array_pad($csv['header'], count($csv['header']) + count($choices) - 1, '');
                    } elseif (!empty($csv['choices'])) {
                        $csv['choices'][] = '';
                    }
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
            \Response::forge(\Format::forge($csv)->to_csv()."\n", 200, array(
                'Content-Type' => 'application/csv',
                'Content-Disposition' => 'attachment; '.
                    'filename='.\Nos\Orm_Behaviour_Virtualname::friendly_slug($this->item->form_name).'.csv;',
                'Content-Transfer-Encoding' => 'binary',
            ))->send(true);

            $offset = 0;
            $limit = 500;
            while ($limit) {
                $csv = array();
                $form_id = $this->item->form_id;
                $answers = Model_Answer::find('all', array(
                    'related' => array('fields'),
                    'where' => array(
                        array('answer_form_id', $form_id),
                    ),
                    'order_by' => array('answer_created_at'),
                    'limit' => $limit,
                    'offset' => $offset,
                    'from_cache' => false,
                ));
                foreach ($answers as $answer) {
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
                \Response::forge(\Format::forge($csv)->to_csv()."\n")->send();

                if (count($answers) < $limit) {
                    break;
                }
                $offset = $offset + $limit;
            }

            exit();
        } catch (\Exception $e) {
            $this->send_error($e);
        }
    }
}
