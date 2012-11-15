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

use Nos\Controller_Front_Application;

use View;

class Controller_Front extends Controller_Front_Application
{
    public function action_main($args = array())
    {
        $this->main_controller->addCss('static/apps/noviusos_form/css/front.css');
        //$this->main_controller->addJs('static/apps/noviusos_form/js/___.js');

        $form_id = $args['form_id'];
        if (empty($form_id)) {
            return '';
        }
        $item = \Nos\Form\Model_Form::find($form_id);
        if (empty($item)) {
            return '';
        }

        if (\Input::method() == 'POST') {
            $answer = Model_Answer::forge(array(
                'answer_form_id' => $form_id,
                'answer_ip' => \Input::real_ip(),
            ), true);
            $answer->save();

            foreach ($item->fields as $field) {
                $type = $field->field_type;
                if (in_array($type, array('message', 'variable', 'separator', 'page_break'))) {
                    continue;
                }
                $name = !empty($field->field_virtual_name) ? $field->field_virtual_name : 'field_'.$field->field_id;
                switch($type) {
                    case 'checkbox':
                        $value = implode("\n", \Input::post($name, array()));
                        break;

                    default:
                        $value = \Input::post($name, '');
                }

                $data = array();
                $fields = array();

                $data[$name] = $value;
                $fields[$name] = $field;
            }

            // @todo send an event with the data

            foreach ($data as $field_name => $value) {
                $field = $fields[$field_name];
                $answer_field = Model_Answer_Field::forge(array(
                    'anfi_answer_id' => $answer->answer_id,
                    'anfi_field_id' => $field->field_id,
                    'anfi_field_type' => $field->field_type,
                    'anfi_value' => $value,
                ), true);
                $answer_field->save();
            }
            return __('You answer has been saved. Thank you.');
        }

        return \View::forge('noviusos_form::front', array(
            'item' => $item,
            'args' => $args,
        ), false);

    }
}
