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
    public function action_main($enhancer_args = array())
    {
        $this->main_controller->addCss('static/apps/noviusos_form/css/front.css');
        //$this->main_controller->addJs('static/apps/noviusos_form/js/___.js');

        $form_id = $enhancer_args['form_id'];
        if (empty($form_id)) {
            return '';
        }
        $item = \Nos\Form\Model_Form::find($form_id);
        if (empty($item)) {
            return '';
        }

        if (\Input::method() == 'POST') {
            if ($this->post_answers($item)) {
                return __('You answer has been saved. Thank you.');
            }
        }

        return $this->render_form($item, $enhancer_args);
    }

    public function render_form($item, $enhancer_args) {

        $layout = explode("\n", $item->form_layout);
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

                $field = $item->fields[$field_id];
                $name = !empty($field->field_virtual_name) ? $field->field_virtual_name : 'field_'.$field->field_id;

                $html_attrs = array(
                    'id' => $field->field_technical_id ?: $name,
                    'class' => $field->field_technical_css,
                );

                if ($enhancer_args['label_position'] == 'placeholder') {
                    $html_attrs['placeholder'] = $field->field_label;
                }

                $label_attrs = array(
                    'for' => $html_attrs['id'],
                );

                $html = '';

                if ($enhancer_args['label_position'] == 'placeholder') {
                    $label = '';
                } else {
                    $label = array(
                        'callback' => array('Form', 'label'),
                        'args' => array($field->field_label, $field->field_technical_id, $label_attrs),
                    );
                }

                if (in_array($field->field_type, array('text', 'textarea', 'select', 'email', 'number', 'date'))) {

                    $value = \Input::post($name, $field->field_default_value);

                    if (in_array($field->field_type, array('text', 'email', 'number', 'date'))) {
                        $html_attrs['type'] = $field->field_type;
                        if (!empty($field->field_width)) {
                            $html_attrs['size'] = $field->field_width;
                        }
                        if (!empty($field->field_limited_to)) {
                            $html_attrs['maxlength'] = $field->field_limited_to;
                        }
                        $html = array(
                            'callback' => array('Form', 'input'),
                            'args' => array($name, $value, $html_attrs),
                        );
                    } else if ($field->field_type == 'textarea') {
                        if (!empty($field->field_height)) {
                            $html_attrs['rows'] = $field->field_height;
                        }
                        $html = array(
                            'callback' => array('Form', 'textarea'),
                            'args' => array($name, $value, $html_attrs),
                        );
                    } else if ($field->field_type == 'select') {
                        $label = array(
                            'callback' => 'html_tag',
                            'args' => array('span', $label_attrs, $field->field_label),
                        );
                        $choices = explode("\n", $field->field_choices);
                        $choices = array_combine($choices, $choices);
                        $html = array(
                            'callback' => array('Form', 'select'),
                            'args' => array($name, $value, $choices, $html_attrs),
                        );
                    }

                } else if (in_array($field->field_type, array('checkbox', 'radio'))) {

                    $label = array(
                        'callback' => 'html_tag',
                        'args' => array('span', $label_attrs, $field->field_label),
                    );

                    if (in_array($field->field_type, array('checkbox', 'radio'))) {
                        $html = array();
                        $default = \Input::post($name, explode("\n", $field->field_default_value));
                        $choices = explode("\n", $field->field_choices);
                        foreach ($choices as $i => $choice) {
                            $html_attrs_choice = $html_attrs;
                            $html_attrs_choice['id'] .= $i;
                            if ($field->field_type == 'checkbox') {
                                $item_html = array(
                                    'callback' => array('Form', 'checkbox'),
                                    'args' => array($name.'[]', $choice, in_array($choice, $default), $html_attrs_choice),
                                );
                            } else if ($field->field_type == 'radio') {
                                $item_html = array(
                                    'callback' => array('Form', 'radio'),
                                    'args' => array($name, $choice, $choice == $default, $html_attrs_choice),
                                );
                            }
                            $item_label = array(
                                'callback' => array('Form', 'label'),
                                'args' => array(
                                    $choice,
                                    $field->field_technical_id,
                                    array(
                                        'for' => $html_attrs_choice['id'],
                                    ),
                                ),
                            );
                            $html[] = array(
                                'field' => $item_html,
                                'label' => $item_label,
                                'template' => '{field} {label} <br />',
                            );
                        }
                    }
                } else if ($field->field_type == 'message') {
                    $label = '';
                    $type = in_array($field->field_style, array('p', 'h1', 'h2', 'h3')) ? $field->field_style : 'p';
                    $html = array(
                        'callback' => 'html_tag',
                        'args' => array($type, array('class' => 'label_text'), nl2br($field->field_message)),
                    );

                } else if ($field->field_type == 'separator') {
                    $label = '';
                    $html = html_tag('hr');
                } else if (in_array($field->field_type, array('hidden', 'variable'))) {
                    switch($field->field_origin) {
                        case 'get':
                            $value = \Input::get($field->field_origin_var, '');
                            break;

                        case 'post':
                            $value = \Input::post($field->field_origin_var, '');
                            break;

                        case 'request':
                            $value = \Input::param($field->field_origin_var, '');
                            break;

                        case 'global':
                            $value = \Arr::get($GLOBALS, $field->field_origin_var, '');
                            break;

                        case 'session':
                            $value = \Session::get($field->field_origin_var, '');
                            break;

                        default:
                    }
                    if ($field->field_type == 'hidden') {
                        $label = '';
                        $html = array(
                            'callback' => array('Form', 'hidden'),
                            'args' => array($name, e($value)),
                        );
                    } else if ($field->field_type == 'variable') {
                        $html = array(
                            'callback' => 'html_tag',
                            'args' => array('p', array(), e($value)),
                        );
                    }
                } else {
                    $label = '';
                }

                $fields[$name] = array(
                    'label' => $label,
                    'field' => $html,
                    'new_row' => $first_col,
                    'width' => $width,
                );
                $first_col = false;
            }
        }

        $layout = array(
            'layout' => 'noviusos_form::foundation',
            'args' => array(
                'item' => $item,
                'fields' => $fields,
                'enhancer_args' => $enhancer_args,
                'form_attrs' => array(
                    'method' => 'POST',
                    'enctype' => 'multipart/form-data',
                    'action' => '',
                ),
            ),
        );

        return \View::forge($layout['layout'], $layout['args'], false);
    }

    public function post_answers($form) {

        $answer = Model_Answer::forge(array(
            'answer_form_id' => $form->form_id,
            'answer_ip' => \Input::real_ip(),
        ), true);
        $answer->save();

        $data = array();
        $fields = array();

        foreach ($form->fields as $field) {
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

            $data[$name] = $value;
            $fields[$name] = $field;
        }

        // @todo send an event with the data
        // @todo pre-check method. (If it returns false, don't save the data, but rather display the form again)

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
        return true;
    }
}
