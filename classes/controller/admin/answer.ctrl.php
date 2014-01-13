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
    public function prepare_i18n()
    {
        parent::prepare_i18n();
        \Nos\I18n::current_dictionary('noviusos_form::common');
    }

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
        $tab['label'] = strtr(__('Answer to ‘{{title}}’'), array('{{title}}' => $this->item->form->form_name));
        $tab['url'] = $this->config['controller_url'].'/visualise/'.$this->item->id;

        return $tab;
    }

    public function action_visualise($id)
    {
        $this->item = $this->crud_item($id);

        if (empty($this->item)) {
            return $this->send_error(new \Exception($this->config['messages']['item deleted']));
        }

        $this->checkPermission('visualise');

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

        // Loop through rows...
        foreach ($layout as $rows) {
            foreach ($rows as $row) {
                list($field_id) = explode('=', $row);

                if ($field_id == 'captcha') {
                    $field = null;
                } else {
                    $field = $form->fields[$field_id];
                }

                $value = !empty($values[$field_id]) ? $values[$field_id]->anfi_value : '';

                $html = '';

                $label = !empty($field) ? $field->field_label : '';

                if (!empty($field) && in_array($field->field_type, array('text', 'textarea', 'select', 'email', 'number', 'date', 'checkbox', 'radio', 'hidden', 'variable', 'file'))) {

                    if (in_array($field->field_type, array('textarea', 'checkbox'))) {
                        $html = \Str::textToHtml($value);
                    } else if ($field->field_type === 'file') {
                        $attachment = $this->item->getAttachment($field);
                        $url = $attachment->url(false);
                        if ($url !== false) {
                            $html = $attachment->htmlAnchor(array(
                                'data-attachment' => $url,
                                'target' => '_blank'
                            ));
                        } else {
                            $html = __('No file attached.');
                        }
                    } else {
                        $html = $value;
                    }
                }

                $fields[] = array(
                    'type' => $field->type,
                    'label' => $label,
                    'value' => $html,
                );
            }
        }

        return $fields;
    }
}
