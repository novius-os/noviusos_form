<?php
/**
 * NOVIUS OS - Web OS for digital communication
 *
 * @copyright  2017 Novius
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

        // Builds answer fields
        $view_params['fields'] = array();
        $view_params['has_page_break'] = false;
        $page = 1;
        foreach ($form->getService()->getLayoutFieldsName() as $field_name) {

            // Increment page count if page break and continue
            if ($field_name == 'page_break') {
                $view_params['has_page_break'] = true;
                $page++;
                continue;
            }

            // Gets the field
            $field = \Arr::get($form->fields, $field_name);
            if (empty($field)) {
                continue;
            }

            // Checks if displayable
            if (\Arr::get($field->getDriver()->getConfig(), 'display_as_answer', true) === false) {
                continue;
            }

            // Gets the answer
            $answerField = \Arr::get($answerFields, $field_name);

            // Builds the fields label and value
            if (!empty($answerField)) {
                $html = $field->getDriver()->renderAnswerHtml($answerField);
            } else {
                $html = __('There is no answer for this field');
            }
            $view_params['fields'][$page][] = array(
                'label' => $field->field_label,
                'value' => $html,
            );
        }

        $view_params['view_params'] = &$view_params;

        return \View::forge('noviusos_form::admin/answer/layout', $view_params, false);
    }
}
