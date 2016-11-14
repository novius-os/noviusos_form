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

class Controller_Admin_Answer_Appdesk extends \Nos\Controller_Admin_Appdesk
{
    public function prepare_i18n()
    {
        parent::prepare_i18n();
        \Nos\I18n::current_dictionary('noviusos_form::common');
    }

    public function load_config()
    {
        parent::load_config();

        $form_id = \Input::get('form_id', null);
        if (!empty($form_id)) {
            $this->config['form_id'] = $form_id;

            $form = Model_Form::find($form_id);
            $this->config['appdesk']['tab']['label'] = strtr(__('Answers to ‘{{title}}’'), array('{{title}}' => $form->form_name));
            $this->config['appdesk']['tab']['iconSize'] = 16;
            $this->config['appdesk']['tab']['labelDisplay'] = true;
            $this->config['i18n']['gridTitle'] = $this->config['appdesk']['tab']['label'];
            $this->config['appdesk']['appdesk']['buttons'] = \Arr::merge($this->config['appdesk']['appdesk']['buttons'], array(
                'Nos\Form\Model_Form.export' => array(
                    'label' => __('Export (spreadsheet)'),
                    'icon' => 'extlink',
                    'primary' => true,
                    'action' => array(
                        'action' => 'window.open',
                        'url' => 'admin/noviusos_form/form/export/'.$form->form_id,
                    ),
                ),
            ));
            $this->config['appdesk']['appdesk']['values']['form_id'] = $form_id;
            $this->config['appdesk']['appdesk']['grid']['urlJson'] = $this->config['appdesk']['appdesk']['grid']['urlJson'].'?form_id='.$form->form_id;
            $this->config['hideContexts'] = true;

            $columns = array();
            $dataset = array();
            $meta = array();
            foreach ($form->getService()->getLayoutFieldsName() as $fieldName) {

                // We don't care about the page break
                if ($fieldName === 'page_break') {
                    continue;
                }

                // Gets the field
                $field = \Arr::get($form->fields, $fieldName);
                if (empty($field)) {
                    continue;
                }

                $id = 'field_'.$field->field_id;

                // Gets the field appdesk config
                $fieldAppdeskConfig = \Arr::get($field->getDriver()->getConfig(), 'answer_appdesk_config');

                // Default field config
                if ($fieldAppdeskConfig === true) {
                    $fieldAppdeskConfig = array();
                }

                // Skip if no config available
                elseif (!is_array($fieldAppdeskConfig)) {
                    continue;
                }

                $value = \Arr::get($fieldAppdeskConfig, 'value');

                // Gets data type
                $dataType = \Arr::get($fieldAppdeskConfig, 'dataType', 'string');
                $dataType = is_callable($dataType) ? $dataType($field) : $dataType;

                // Gets header text
                $headerText = \Arr::get($fieldAppdeskConfig, 'headerText', preg_replace('/\:\s*$/', ' ', $field->field_label));
                $headerText = is_callable($headerText) ? $dataType($field) : $headerText;

                // Gets title label (inspector)
                $label = \Arr::get($fieldAppdeskConfig, 'label', $field->field_label);
                if (is_callable($label)) {
                    $label = $label($field);
                }

                $column = array (
                    'headerText' => $headerText,
                    'dataType' => $dataType,
                    'dataKey' => $id,
                );
                $meta[$id] = array (
                    'label' => $label,
                    'dataType' => $dataType,
                );

                // Adds the column if less than 3 are already displayed
                if (count($columns) < 3) {
                    $columns[$id] = $column;
                }

                // Creates the dataset
                $dataset[$id] = array_merge($column, array(
                    'value' => function ($item) use ($field, $value) {

                        // Gets the answer field
                        $answerField = Model_Answer_Field::find('first', array(
                            'where' => array(
                                array('anfi_answer_id', $item->answer_id),
                                array('anfi_field_id', $field->field_id),
                            )
                        ));
                        if (empty($answerField)) {
                            return null;
                        }

                        // Gets the field driver
                        if (is_callable($value)) {
                            // Custom value callback
                            return $value($field->getDriver(), $answerField);
                        } else {
                            // Default driver render
                            return $field->getDriver()->renderAnswerHtml($answerField);
                        }
                    }
                ));

                // Stops after 6 columns
                if (count($columns) === 6) {
                    break;
                }
            }

            $actions = $this->config['appdesk']['appdesk']['grid']['columns']['actions'];
            unset($this->config['appdesk']['appdesk']['grid']['columns']['actions']);
            $this->config['appdesk']['appdesk']['grid']['columns'] = array_merge($this->config['appdesk']['appdesk']['grid']['columns'], $columns, array('actions' => $actions));
            $this->config['dataset'] = array_merge($this->config['dataset'], $dataset);
            $this->config['appdesk']['appdesk']['inspectors']['preview']['options']['meta'] = array_merge($this->config['appdesk']['appdesk']['inspectors']['preview']['options']['meta'], $meta);
        }

        return $this->config;
    }
}
