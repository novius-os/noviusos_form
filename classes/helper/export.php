<?php
/**
 * NOVIUS OS - Web OS for digital communication
 *
 * @copyright  2011 Novius
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
    protected $fields;
    protected $item;
    protected $offset;


    /**
     * Get the layout of the form as an array
     *
     * @param $item
     *
     * @return array
     */
    protected function getLayout($item)
    {
        $layout = explode("\n", $item->form_layout);
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
        return $layout;
    }

    /**
     * Get the array of headers and put them in the $header property
     * Elements of the array have the key 'label' with the label of the header and
     * optionnaly the key 'choices' with the list of selectable choices.
     *
     * @param $item
     * @param $layout
     */
    protected function getHeaders($item, $layout)
    {
        $this->fields = array();
        foreach ($layout as $rows) {
            foreach ($rows as $row) {
                list($field_id) = explode('=', $row);

                if ($field_id == 'captcha') {
                    continue;
                }
                $field = $item->fields[$field_id];
                if (!in_array($field->field_type,
                    array(
                        'text', 'textarea', 'select', 'email', 'number',
                        'date', 'checkbox', 'radio', 'hidden', 'variable', 'file'
                    ))
                ) {
                    continue;
                }

                $this->fields[] = $field;
                $header         = array('label' => $field->field_label);
                if (in_array($field->field_type, array('select', 'checkbox', 'radio'))) {
                    $choices           = explode("\n", $field->field_choices);
                    $header['choices'] = array();
                    foreach ($choices as $choice) {
                        $header['choices'][] = $choice;
                    }
                }
                $this->headers[] = $header;
            }
        }
        $this->headers[] = array('label' => __('Answer date'));
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

        $offset = 0;
        $limit  = 500;
        $answer_list = array();
        foreach ($answers as $answer) {
            $values = array();
            foreach ($answer->fields as $answer_field) {
                $values[$answer_field->anfi_field_id] = $answer_field;
            }

            $answer_row = array();
            foreach ($this->fields as $field) {
                $value = !empty($values[$field->field_id]) ? $values[$field->field_id]->anfi_value : '';

                if (in_array($field->field_type, array('select', 'checkbox', 'radio'))) {
                    $choices  = explode("\n", $field->field_choices);
                    $selected = explode("\n", $value);
                    $value    = array();
                    foreach ($choices as $choice) {
                        $value[] = in_array($choice, $selected) ? 'x' : '';
                    }
                } else if ($field->field_type === 'file') {
                    $attachment = $answer->getAttachment($field);
                    $value      = $attachment->filename();
                }
                $answer_row[] = $value;
            }
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
            $this->item    = Model_Form::find($id);
            $layout        = $this->getLayout($this->item);
            $this->headers = array();
            $this->getHeaders($this->item, $layout);
            $this->reset();
        } catch (\Exception $e) {
            $this->send_error($e);
        }
    }
}