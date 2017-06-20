<?php
/**
 * NOVIUS OS - Web OS for digital communication
 *
 * @copyright  2017 Novius
 * @license    GNU Affero General Public License v3 or (at your option) any later version
 *             http://www.gnu.org/licenses/agpl-3.0.html
 * @link http://www.novius-os.org
 */

Nos\I18n::current_dictionary('noviusos_form::common');

$config = \Config::load('noviusos_form::config', true);

// Gets the available fields drivers
$available_fields_drivers = \Arr::get($config, 'available_fields_drivers', array());

// Builds the drivers options list
$drivers_options = array();
foreach ($available_fields_drivers as $driverCLass) {
    $drivers_options[$driverCLass] = $driverCLass::getName();
}

// Gets the default fields layout
$default_fields_layout = \Arr::get($config, 'default_fields_layout', array());

// Gets the available fields layouts
$available_fields_layouts = \Arr::get($config, 'available_fields_layouts', array());

return array(
    'controller_url' => 'admin/noviusos_form/form',
    'model' => 'Nos\Form\Model_Form',
    'tab' => array(
        'labels' => array(
            'insert' => __('Add a form'),
        ),
    ),
    'require_js' => array(
        'static/apps/noviusos_form/dist/js/admin/insert_update.min.js?update=20161102',
    ),
    'views' => array(
        'delete' => 'noviusos_form::admin/form/popup_delete',
    ),
    'layout' => array(
        'standard' => array(
            'view' => 'nos::form/layout_standard',
            'params' => array(
                'title' => 'form_name',
                'subtitle' => array('form_publish_warning'),
                'medias' => array(),
                'large' => true,
                'content' => array(
                    'content' => array(
                        'view' => 'nos::form/expander',
                        'params' => array(
                            'title' => __('Properties'),
                            'nomargin' => true,
                            'options' => array(
                                'allowExpand' => false,
                            ),
                            'content' => array(
                                'view' => 'nos::form/fields',
                                'params' => array(
                                    'fields' => array(
                                        'form_virtual_name',
                                        'form_submit_email',
                                        'form_submit_email_warning',
                                    ),
                                ),
                            ),
                        ),
                    ),
                    'fields' => array(
                        'view' => 'nos::form/expander',
                        'params' => array(
                            'title' => __('Fields'),
                            'nomargin' => true,
                            'options' => array(
                                'allowExpand' => false,
                            ),
                            'content' => array(
                                'view' => 'noviusos_form::admin/form/fields',
                                'params' => array(),
                            ),
                        ),
                    ),
                ),
                'menu' => array(),
            ),
        ),
    ),
    'fields' => array(
        'form_name' => array(
            'label' => __('Title'),
            'form' => array(
                'type' => 'text',
            ),
            'validation' => array(
                'required',
                'min_length' => array(2),
            ),
        ),
        'form_virtual_name' => array(
            'label' => __('Virtual name:'),
            'form' => array(
                'type' => 'text',
                'size' => 30,
            ),
            'expert' => true,
        ),
        'form_layout' => array(
            'form' => array(
                'type' => 'hidden',
            ),
        ),
        'form_captcha' => array(
            'label' => __('Captcha protected'),
            'form' => array(
                'type' => 'checkbox',
                'value' => '1',
                'empty' => '0',
            ),
        ),
        'form_submit_label' => array(
            'label' => __('Submit button’s label:'),
            'form' => array(
                'type' => 'text',
                'value' => __('I’m the submit button, click to edit me'),
            ),
        ),
        'form_submit_email' => array(
            'label' => __('For every new answer, an email notification is sent to:'),
            'form' => array(
                'description' => __('One email per line'),
                'type' => 'textarea',
                'placeholder' => __('One email per line'),
                'cols' => 50,
            ),
        ),
        'form_submit_email_warning' => array(
            'label' => '',
            'renderer' => \Nos\Renderer_Text::class,
            'form' => array(
                'value' => '
                    <div class="ui-state-error" style="padding:0.5em;">
                        '.__(
                            'You have a problem here: Your Novius OS is not set up to send emails. '.
                            'You’ll have to ask your developer to set it up for you.'
                        ).'
                    </div>
                '
            ),
            'show_when' => function () {
                return !\Email::hasDefaultFrom();
            }
        ),

        'form_publish_warning' => array(
            'label' => '',
            'renderer' => \Nos\Renderer_Text::class,
            'form' => array(
                'value' => \View::forge('noviusos_form::admin/form/warning_not_published', array(), false)->render()
            ),
            'show_when' => function ($item) {
                if ($item->is_new()) {
                    return false;
                }
                $count = \Nos\Model_Wysiwyg::count(array(
                    'where' => array(
                        array('wysiwyg_text', 'LIKE', '%&quot;form_id&quot;:&quot;'.$item->form_id.'%'),
                    ),
                ));

                return $count == 0;
            },
            'template' => '<td colspan="2">{field}</td>',
        ),

    ),
    'fields_config' => array(
        'layout' => array(
            'standard' => array(
                'view' => 'nos::form/accordion',
                'params' => array(
                    //'classes' => 'notransform',
                    'accordions' => array(
                        'main' => array(
                            'title' => __('Properties'),
                            'fields' => array(
                                'field_driver',
                                'field_label',
                                //'field_choices',
                                //'field_style',
                                //'field_message',
                            ),
                        ),
                        'optional' => array(
                            'title' => __('Further options'),
                            'fields' => array(
                                //'field_mandatory',
                                //'field_default_value',
                                //'field_origin',
                                //'field_origin_var',
                                //'field_details',
                                //'field_width',
                                //'field_height',
                                //'field_limited_to',
                            ),
                        ),
                        'technical' => array(
                            'title' => __('Technical options'),
                            'fields' => array(
                                'field_virtual_name',
                                'field_technical_id',
                                'field_technical_css',
                            ),
                        ),
                        'condition' => array(
                            'title' => __('Conditions'),
                            'fields' => array(
                                'field_conditional',
                                'field_conditional_form',
                                'field_conditional_value',
                            ),
                        ),
                    ),
                ),
            ),
        ),
        'fields' => array(
            'field_id' => array(
                'form' => array(
                    'type' => 'hidden',
                    'value' => '0',
                ),
            ),
            'field_driver' => array(
                'label' => __('Type:'),
                'form' => array(
                    'type' => 'select',
                    'options' => $drivers_options,
                    'value' => 'text',
                ),
                'validation' => array(
                    'required',
                ),
            ),
            'field_label' => array(
                'label' => __('Label:'),
                'form' => array(
                    'type' => 'text',
                    'value' => __('I’m the label, click to edit me:'),
                ),
                'validation' => array(
                    'required',
                ),
            ),
            'field_message' => array(
                'label' => __('Message:'),
                'form' => array(
                    'type' => 'textarea',
                    'rows' => '3',
                    'value' => __('I’m a message, click to edit me.'),
                ),
                'validation' => array(
                    'required',
                ),
            ),
            'field_style' => array(
                'label' => __('Style:'),
                'form' => array(
                    'type' => 'select',
                    'options' => array(
                        'p' => __('Standard'),
                        'h1' => __('Heading 1'),
                        'h2' => __('Heading 2'),
                        'h3' => __('Heading 3'),
                    ),
                    'value' => 'p',
                ),
            ),
            'field_choices' => array(
                'label' => __('Answers:'),
                'form' => array(
                    'type' => 'hidden',
                    'value' => '',
                ),
                'populate' => function ($item) {
                    return is_array($item->choices) ? implode(PHP_EOL, $item->choices) : $item->choices;
                },
            ),
            'field_mandatory' => array(
                'label' => __('Mandatory'),
                'form' => array(
                    'type' => 'checkbox',
                    'value' => '1',
                    'empty' => '0',
                ),
            ),
            'field_default_value' => array(
                'label' => __('Default value:'),
                'form' => array(
                    'type' => 'text',
                ),
                'populate' => function ($item) {
                    $defaultValue = $item->hasDriver() ? $item->getDriver()->getDefaultValue() : $item->field_default_value;
                    if (is_array($defaultValue)) {
                        $defaultValue = implode(',', $defaultValue);
                    }

                    return $defaultValue;
                },
            ),
            'field_details' => array(
                'label' => __('Instructions for the user:'),
                'form' => array(
                    'type' => 'textarea',
                    'rows' => '3',
                ),
            ),
            'field_width' => array(
                'label' => __('Width:'),
                'template' => str_replace('{{count}}', '{field} {required}', __('Width: {{count}} characters')),
                'form' => array(
                    'type' => 'number',
                    'value' => '',
                    'size' => '3',
                ),
                'populate' => function ($item) {
                    return empty($item->field_width) ? '' : $item->field_width;
                },
            ),
            'field_height' => array(
                'label' => '',
                'template' => str_replace('{{count}}', '{field} {required}', __('Height: {{count}} lines')),
                'form' => array(
                    'type' => 'number',
                    'size' => '3',
                    'value' => '3',
                ),
                'populate' => function ($item) {
                    return empty($item->field_height) ? '' : $item->field_height;
                },
            ),
            'field_limited_to' => array(
                'label' => '',
                'template' => str_replace('{{count}}', '{field} {required}', __('Limited to {{count}} characters')),
                'form' => array(
                    'type' => 'number',
                    'size' => '3',
                ),
                'populate' => function ($item) {
                    return empty($item->field_limited_to) ? '' : $item->field_limited_to;
                },
            ),
            'field_origin' => array(
                'label' => __('Origin:'),
                'form' => array(
                    'type' => 'select',
                    'options' => array(
                        'get' => 'Get',
                        'post' => 'Post',
                        'request' => 'Request',
                        'global' => 'Global',
                        'session' => 'Session',
                    ),
                ),
            ),
            'field_origin_var' => array(
                'label' => __('Variable name:'),
                'form' => array(
                    'type' => 'text',
                    'value' => '',
                ),
            ),
            'field_virtual_name' => array(
                'label' => __('Virtual field name:'),
                'form' => array(
                    'type' => 'text',
                    'value' => '',
                ),
                'expert' => true,
            ),
            'field_technical_id' => array(
                'label' => __('ID:'),
                'form' => array(
                    'type' => 'text',
                ),
                'expert' => true,
            ),
            'field_technical_css' => array(
                'label' => __('CSS classes:'),
                'form' => array(
                    'type' => 'text',
                ),
                'expert' => true,
            ),
            'field_conditional' => array(
                'label' => __('Conditional'),
                'form' => array(
                    'type' => 'checkbox',
                    'value' => '1',
                    'empty' => '0',
                ),
                'expert' => true,
            ),
            'field_conditional_form' => array(
                'label' => __('Form name to Check:'),
                'form' => array(
                    // Textarea is needed to preserve \n to store multiple default values (checkboxes)
                    'type' => 'text',
                ),
                'expert' => true,
            ),
            'field_conditional_value' => array(
                'label' => __('Show when the value is:'),
                'form' => array(
                    // Textarea is needed to preserve \n to store multiple default values (checkboxes)
                    'type' => 'textarea',
                ),
                'expert' => true,
            ),
        ),
    ),
);
