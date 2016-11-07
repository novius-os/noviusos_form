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

        $form = $this->item->form;

        $view_params = $this->view_params();

        // Gets the answer fields
        $answerFields = array();
        foreach ($this->item->fields as $answer_field) {
            $answerFields[$answer_field->anfi_field_id] = $answer_field;
        }

        // Builds the layout
        $layout = explode("\n", $form->form_layout);
        array_walk($layout, function (&$v) {
            $v = explode(',', $v);
        });
        // Cleanup empty layout values
        foreach ($layout as $a => $rows) {
            $layout[$a] = array_filter($rows);
            if (empty($layout[$a])) {
                unset($layout[$a]);
                continue;
            }
        }

        // Builds answer fields
        $view_params['fields'] = array();
        $view_params['has_page_break'] = false;
        $page = 1;
        foreach ($layout as $rows) {
            foreach ($rows as $row) {
                list($field_id) = explode('=', $row);

                // Captcha
                if ($field_id == 'captcha') {
                    continue;
                }

                // Page break
                elseif ($field_id == 'page_break') {
                    $view_params['has_page_break'] = true;
                    $page++;
                }

                // Field
                else {

                    // Gets the field
                    $field = \Arr::get($form->fields, $field_id);
                    if (empty($field)) {
                        continue;
                    }

                    // Gets the answer
                    $answerField = \Arr::get($answerFields, $field_id);

                    // Builds the fields label and value
                    $fieldDriver = $field->getDriver();
                    if (!empty($fieldDriver)) {
                        if (!empty($answerField)) {
                            $html = $fieldDriver->renderAnswerHtml($answerField);
                        } else {
                            $html = __('There is no answer for this field');
                        }
                        $view_params['fields'][$page][] = array(
                            'label' => $field->field_label,
                            'value' => $html,
                        );
                    }
                }
            }
        }

        $view_params['view_params'] = &$view_params;

        return \View::forge('noviusos_form::admin/answer/layout', $view_params, false);
    }
}
