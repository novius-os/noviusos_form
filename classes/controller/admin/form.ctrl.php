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
    function before_save($item, $data)
    {
        $field_names = array();
        foreach (\Input::post('field') as $name => $whatever) {
            $field_names[] = $name;
        }
        $values = array(null);
        foreach ($field_names as &$name) {
            $values[] = \Input::post('field.'.$name);
            $name = 'field_'.$name;
        }
        $fields = array();
        foreach (call_user_func_array('array_map', $values) as $value) {
            $fields[] = array_combine(array_values($field_names), $value);
        }


        foreach ($fields as $field) {
            $is_new = empty($field['field_id']);
            $model_field = Model_Field::forge($field, $is_new);
            if ($is_new) {
                $item->fields[] = $model_field;
            } else {
                $item->fields[$model_field->field_id] = $model_field;
            }
        }
    }

    function save($item, $data)
    {
        $return = parent::save($item, $data);
        foreach ($item->fields as $field) {
            $field->field_form_id = $item->form_id;
            $field->save();
        }
        return $return;
    }

    function action_form_field($field) {

        $fieldset = \Fieldset::build_from_config($this->config['fields_config'], $field, array('save' => false));
        $fields_view_params = array(
            'layout' => $this->config['fields_layout'],
            'fieldset' => $fieldset,
        );
        $fields_view_params['view_params'] = &$fields_view_params;
        return \View::forge('noviusos_form::admin/layout', $fields_view_params, false);
    }

    function action_form_field_ajax($field_id = null) {
        if (empty($field_id)) {
            $model_field = Model_Field::forge(array(
                'field_form_id' => '0',
                'field_type' => 'text',
                'field_label' => '',
                'field_choices' => '',
                'field_virtual_name' => uniqid(),
            ), true);
            $model_field->save();
        } else {
            $model_field = Model_Field::find($field_id);
        }
        return $this->action_form_field($model_field);
    }
}
