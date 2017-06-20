<?php
/**
 * NOVIUS OS - Web OS for digital communication
 *
 * @copyright  2017 Novius
 * @license    GNU Affero General Public License v3 or (at your option) any later version
 *             http://www.gnu.org/licenses/agpl-3.0.html
 * @link       http://www.novius-os.org
 */

namespace Nos\Form;

\Nos\I18n::current_dictionary('noviusos_form::common');

class Helper_Export
{
    public $headers;
    public $values;
    public $limit = 500;

    protected $offset;

    /**
     * @var Model_Field[]
     */
    protected $fields;

    /**
     * @var Model_Form
     */
    protected $item;

    /**
     * Get the array of headers and put them in the $header property
     * Elements of the array have the key 'label' with the label of the header and
     * optionnaly the key 'choices' with the list of selectable choices.
     *
     * @param $form
     */
    protected function getHeaders($form)
    {
        $this->fields = array();
        foreach ($this->item->getService()->getLayoutFieldsName() as $field_name) {

            // Gets the field
            $field = \Arr::get($form->fields, $field_name);
            if (empty($field)) {
                continue;
            }

            // Checks if exportable
            if (\Arr::get($field->getDriver()->getConfig(), 'exportable', true) === false) {
                continue;
            }

            // Adds to fields list
            $this->fields[] = $field;

            $this->headers[] = $field->getDriver()->getAnswerExportHeader();
        }
        $this->headers[] = array('label' => __('Answer date'));
    }

    protected function splitChoice($field, $choice) {
        if ($field->field_technical_id === 'recipient-list' && mb_strrpos($choice, '=')) {
            $choiceInfos = preg_split('~(?<!\\\)=~', $choice);
            foreach ($choiceInfos as $key => $choiceValue) {
                $choiceInfos[$key] = str_replace("\=", "=", $choiceValue);
            }
            return $choiceInfos;
        }
        return null;
    }


    /**
     * Get the next $limit values. Returns false when there is no value remaining.
     * If multiple choices are possible for a value, the value will be an array with 'x' as the selected value.
     *
     * @return bool|array
     */
    public function getValues()
    {
        $form_id = $this->item->form_id;
        $answers = Model_Answer::find('all', array(
            'related'    => array('fields'),
            'where'      => array(
                array('answer_form_id', $form_id),
            ),
            'order_by'   => array('answer_created_at'),
            'limit'      => $this->limit,
            'offset'     => $this->offset,
            'from_cache' => false,
        ));

        if (empty($answers)) {
            return false;
        }

        $this->offset += $this->limit;

        $answer_list = array();
        foreach ($answers as $answer) {

            // Gets the answer fields
            $answerFields = array();
            foreach ($answer->fields as $answerField) {
                $answerFields[$answerField->anfi_field_id] = $answerField;
            }

            // Renders the answer fields as exportable values
            $answer_row = array();
            foreach ($this->fields as $field) {
                // Gets the answer
                $answerField = \Arr::get($answerFields, $field->field_id);
                if (!empty($answerField)) {
                    $answer_row[] = $field->getDriver()->renderExportValue($answerField);
                }else{
                    $answer_row[] = '';
                }
            }

            // Appends the creation date
            $answer_row[] = $answer->answer_created_at;

            $answer_list[] = $answer_row;
        }



        return $answer_list;
    }

    /**
     * Reset the offset for the method getValues
     */
    public function reset()
    {
        $this->offset = 0;
    }

    /**
     * Parse the form $id and put headers in the $header property.
     * Reinitialize the offset for the method getValues
     *
     * @param $id
     */
    public function parseForm($id)
    {
        try {
            $this->item = Model_Form::find($id);
            $this->headers = array();
            $this->getHeaders($this->item);
            $this->reset();
        } catch (\Exception $e) {
            $this->send_error($e);
        }
    }
}