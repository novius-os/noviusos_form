<?php
/**
 * NOVIUS OS - Web OS for digital communication
 *
 * @copyright  2011 Novius
 * @license    GNU Affero General Public License v3 or (at your option) any later version
 *             http://www.gnu.org/licenses/agpl-3.0.html
 * @link http://www.novius-os.org
 */

Nos\I18n::current_dictionary('noviusos_form::common');

$id = uniqid('enhancer_popup_');

return array(
    'popup' => array(
        'layout' => array(
            'fields' => array(
                'view' => 'nos::form/fields',
                'params' => array(
                    'fields' => array(
                        'form_id',
                        'label_position',
                        'after_submit',
                        'confirmation_message',
                        'confirmation_page_id',
                    ),
                    'begin' => '<div id="'.$id.'">',
                    'end' => '</div>',
                ),
            ),
            'js' => array(
                'view' => 'noviusos_form::enhancer/js',
                'params' => array(
                    'id' => $id,
                ),
            ),
        ),
    ),
    'fields' => array(
        'form_id' => array(
            'label' => __('Pick a form:'),
            'renderer' => 'Nos\Renderer_Item_Picker',
            'renderer_options' => array(
                'model' => 'Nos\Form\Model_Form',
                'appdesk' => 'admin/noviusos_form/appdesk',
                'defaultThumbnail' => 'static/apps/noviusos_form/img/icons/form-64.png',
                'texts' => array(
                    'empty' => __('No form selected'),
                    'add' => __('Pick a form'),
                    'edit' => __('Pick another form'),
                    'delete' => __('Un-select this form'),
                ),
            ),
            'validation' => array(
                'required',
            ),
        ),
        'label_position' => array(
            'label' => __('Label position:'),
            'form' => array(
                'type' => 'select',
                'options' => array(
                    'top' => __('Top aligned'),
                    'left' => __('Left aligned'),
                    'right' => __('Right aligned'),
                    'placeholder' => __('In the field (placeholder), for text fields only'),
                ),
                'value' => 'top',
            ),
        ),
        'after_submit' => array(
            'label' => __('Once the user submitted the form:'),
            'form' => array(
                'type' => 'radio',
                'value' => 'message',
                'options' => array(
                    'message' => __('Display a message'),
                    'page_id' => __('Redirect to a page'),
                ),
                'class' => 'enhancer_after_submit',
            ),
        ),
        'confirmation_message' => array(
            'label' => '',
            'form' => array(
                'type' => 'textarea',
                'value' => __('Thank you. Your answer has been sent.'),
                'class' => 'enhancer_confirmation_message',
                'style' => 'width:100%;display:none;',
                'rows' => '4',
            ),
        ),
        'confirmation_page_id' => array(
            'label' => '',
            'renderer' => 'Nos\Page\Renderer_Selector',
            'renderer_options' => array(
                'context' => '{{_parent_context}}',
            ),
            'template' => '<div style="width:100%;display:none;" class="enhancer_confirmation_page_id">{field}</div>',
        ),
    ),
    'preview' => array(
        'params' => array(
            'title' => function ($enhancer_args) {
                if (!empty($enhancer_args['form_id'])) {
                    $form = \Nos\Form\Model_Form::find($enhancer_args['form_id']);
                }
                if (!empty($form)) {
                    return $form->form_name;
                } else {
                    return __('Form');
                }
            },
        ),
    ),
);
