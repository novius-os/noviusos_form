<?php
/**
 * NOVIUS OS - Web OS for digital communication
 *
 * @copyright  2011 Novius
 * @license    GNU Affero General Public License v3 or (at your option) any later version
 *             http://www.gnu.org/licenses/agpl-3.0.html
 * @link http://www.novius-os.org
 */

return array(
    'controller_url' => 'admin/noviusos_form/form',
    'model' => 'Nos\Form\Model_Form',
    'tab' => array(
        'labels' => array(
            'insert' => __('Add a form'),
        ),
    ),
    'layout' => array(
        'standard' => array(
            'view' => 'nos::form/layout_standard',
            'params' => array(
                'title' => 'form_name',
                'subtitle' => array('form_virtual_name'),
                'medias' => array(),
                'large' => true,
                'content' => array(
                    'fields' => array(
                        'view' => 'noviusos_form::admin/layout_fields',
                        'params' => array(),
                    ),//*/
                ),
                'menu' => array(),
                'save' => 'save',
            ),
        ),
    ),
    'fields' => array(
        'form_name' => array (
            'label' => __('Name'),
            'form' => array(
                'type' => 'text',
            ),
            'validation' => array(
                'required',
                'min_length' => array(2),
            ),
        ),
        'form_virtual_name' => array (
            'label' => __('Virtual name:'),
            'form' => array(
                'type' => 'text',
            ),
        ),
        'save' => array(
            'label' => '',
            'form' => array(
                'type' => 'submit',
                'tag' => 'button',
                'value' => __('Save'),
                'class' => 'primary',
                'data-icon' => 'check',
            ),
        ),
    ),
    'fields_layout' => array(
        'standard' => array(
            'view' => 'nos::form/accordion',
            'params' => array(
                //'classes' => 'notransform',
                'accordions' => array(
                    'main' => array(
                        'title' => __('Required informations for this field'),
                        'fields' => array('field[id][]', 'field[label][]', 'field[type][]', 'field[choices][]'),
                    ),
                    'optional' => array(
                        'title' => __('Optional parameters'),
                        'fields' => array(
                            'field[mandatory][]',
                            'field[default_value][]',
                            'field[details][]',
                            'field[size][]',
                            'field[layout][]',
                            'field[limited_to][]',
                        ),
                    ),
                    'technical' => array(
                        'title' => __('Technical parameters'),
                        'fields' => array(
                            'field[technical_id][]',
                            'field[technical_css][]',
                        ),
                    ),
                ),
            ),
        ),
    ),
    'fields_config' => array(
        'field[id][]' => array(
            'form' => array(
                'type' => 'hidden',
                'value' => '0',
            ),
            'populate' => function($item) {
                return $item->field_id;
            },
        ),
        'field[label][]' => array(
            'label' => __('Label:'),
            'form' => array(
                'value' => __('Your nice question here:'),
            ),
            'populate' => function($item) {
                return $item->field_label;
            },
        ),
        'field[type][]' => array(
            'label' => __('Type:'),
            'form' => array(
                'type' => 'select',
                'options' => array(
                    'text' => __('Single line'),
                    'textarea' => __('Paragraph'),
                    'radio' => __('Multiple choices'),
                    'checkbox' => __('Checboxes'),
                    'select' => __('Dropdown'),
                    'page_break' => __('Page break'),
                ),
                'value' => 'text',
            ),
            'populate' => function($item) {
                return $item->field_type;
            },
        ),
        'field[choices][]' => array(
            'label' => __('Answers:'),
            'form' => array(
                'type' => 'textarea',
                'rows' => '4',
                'value' => '',
            ),
            'populate' => function($item) {
                return $item->field_choices;
            },
        ),
        'field[virtual_name][]' => array(
            'label' => __('Virtual field name:'),
            'form' => array(
                'value' => '',
            ),
            'populate' => function($item) {
                return $item->field_virtual_name;
            },
        ),
        'field[mandatory][]' => array(
            'label' => __('Mandatory'),
            'form' => array(
                'type' => 'checkbox',
                'value' => '1',
                'empty' => '0',
            ),
        ),
        'field[default_value][]' => array(
            'label' => __('Default value:'),
        ),
        'field[details][]' => array(
            'label' => __('Field details:'),
            'form' => array(
                'type' => 'textarea',
                'rows' => '3',
             ),
        ),
        'field[size][]' => array(
            'label' => __('Field size:'),
            'form' => array(
                'type' => 'select',
                'options' => array(
                    'small' => __('Small'),
                    'medium' => __('Medium'),
                    'large' => __('Large'),
                ),
            ),
        ),
        'field[layout][]' => array(
            'label' => __('Field layout:'),
            'form' => array(
                'type' => 'select',
                'options' => array(
                    'one' => __('One column'),
                    'two' => __('Two column'),
                    'three' => __('Three column'),
                    'side_by_side' => __('Side by side'),
                ),
            ),
        ),
        'field[limited_to][]' => array(
            'label' => '',
            'template' => str_replace('{count}', '{field} {required}', __('Limited to {count} chars')),
            'form' => array(
                'size' => '3',
            ),
        ),
        'field[technical_id][]' => array(
            'label' => __('ID:'),
        ),
        'field[technical_css][]' => array(
            'label' => __('CSS classes:'),
        ),
    ),
);
