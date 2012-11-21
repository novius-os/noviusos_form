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

class Controller_Admin_Answer extends \Nos\Controller_Admin_Crud
{
    protected function crud_item($id)
    {
        if (empty($id)) {
            return null;
        }
        $item = parent::crud_item($id);
        return $item;
    }

    protected function get_tab_params()
    {
        $tab = parent::get_tab_params();
        $tab['label'] = \Str::tr(__('Answer of ":form"'), array(':form' => $this->item->form->form_name));
        $tab['url'] = $this->config['controller_url'].'/visualize/'.$this->item->id;

        return $tab;
    }

    public function action_visualize($id)
    {
        $this->item = $this->crud_item($id);

        if (empty($this->item)) {
            return $this->send_error(new \Exception($this->config['messages']['item deleted']));
        }

        $this->check_permission('visualize');

        $view_params = $this->view_params();
        $view_params['fields'] = $this->form_layout_fields();
        $view_params['view_params'] = &$view_params;

        return \View::forge('noviusos_form::admin/answer', $view_params, false);
    }

    public function form_layout_fields()
    {
        $form = $this->item->form;
        $values = array();
        foreach ($this->item->fields as $answer_field) {
            $values[$answer_field->anfi_field_id] = $answer_field;
        }

        $layout = explode("\n", $form->form_layout);
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

        // Loop through rows...
        foreach ($layout as $rows) {
            $first_col = true;
            $col_width = 0;
            // ...and cols
            foreach ($rows as $row) {
                list($field_id, $width) = explode('=', $row);

                $available_width = $width * 3;
                $col_width += $available_width;

                if ($field_id == 'captcha') {
                    $field = null;
                } else {
                    $field = $form->fields[$field_id];
                }

                $value = !empty($values[$field_id]) ? $values[$field_id]->anfi_value : '';

                $html = '';

                $label = !empty($field) ? $field->field_label : '';

                if (!empty($field) && in_array($field->field_type, array('text', 'textarea', 'select', 'email', 'number', 'date', 'checkbox', 'radio', 'hidden', 'variable'))) {

                    if (in_array($field->field_type, array('textarea', 'checkbox'))) {
                        $html = \Str::textToHtml($value);
                    } else {
                        $html = $value;
                    }
                }

                $fields[] = array(
                    'label' => $label,
                    'value' => $html,
                    'new_row' => $first_col,
                    'width' => $width,
                );
                $first_col = false;
            }
        }

        return $fields;
    }
}
