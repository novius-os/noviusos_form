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
    'query' => array(
        'model' => 'Nos\Form\Model_Form',
        'order_by' => array('form_name' => 'ASC'),
    ),
    'search_text' => 'form_name',
    'data_mapping' => array(
        'id' => array(
            'column'        => 'form_id',
        ),
        'title' => array(
            'column'        => 'form_name',
            'title'    => __('Title'),
        ),
        'answers_count' => array(
            'title' => __('Answers'),
            'cellFormatters' => array(
                'center' => array(
                    'type' => 'css',
                    'css' => array('text-align' => 'center'),
                ),
                'link' => array(
                    'type' => 'link',
                    'action' => 'Nos\Form\Model_Form.answers',
                ),
            ),
            'value' => function($item) {
                return $item->is_new() ? 0 : \Nos\Form\Model_Answer::count(array(
                        'where' => array(array('answer_form_id' => $item->form_id)),
                    ));
            },
            'width' => 100,
            'ensurePxWidth' => true,
            'allowSizing' => false,
        ),
    ),
    'i18n' => array(
        // Crud
        'successfully added' => __('Form successfully added.'),
        'successfully saved' => __('Form successfully saved.'),
        'successfully deleted' => __('The form has successfully been deleted!'),

        // General errors
        'item deleted' => __('This form has been deleted.'),
        'not found' => __('Form not found'),

        // Deletion popup
        'delete an item' => __('Delete a form'),
        'you are about to delete, confim' => __('You are about to delete the form <span style="font-weight: bold;">":title"</span>. Are you sure you want to continue?'),
        'you are about to delete' => __('You are about to delete the form <span style="font-weight: bold;">":title"</span>.'),
    ),
    'actions' => array(
        'Nos\Form\Model_Form.add' => array(
            'label' => __('Add a form'),
        ),
        'Nos\Form\Model_Form.answers' => array(
            'label' => __('Answers'),
            'icon' => 'mail-closed',
            'targets' => array(
                'grid' => true,
                'toolbar-edit' => true,
            ),
            'action' => array(
                'action' => 'nosTabs',
                'tab' => array(
                    'url' => 'admin/noviusos_form/answer/appdesk?form_id={{_id}}',
                    'label' => __('Answers to ‘{{title}}’'),
                    'iconUrl' => 'static/apps/noviusos_form/img/icons/form-16.png',
                ),
            ),
            'visible' => function($params) {
                return !isset($params['item']) || !$params['item']->is_new();
            },
            'disabled' =>
                function($item) {
                    return $item->is_new() || !\Nos\Form\Model_Answer::count(array(
                            'where' => array(array('answer_form_id' => $item->form_id)),
                        ));
                }
        ),
        'Nos\Form\Model_Form.export' => array(
            'label' => __('Export the answers (spreadsheet)'),
            'icon' => 'document',
            'targets' => array(
                'grid' => true,
                'toolbar-edit' => true,
            ),
            'action' => array(
                'action' => 'window.open',
                'url' => 'admin/noviusos_form/form/export/{{_id}}',
            ),
            'visible' => function($params) {
                return !isset($params['item']) || !$params['item']->is_new();
            },
            'disabled' =>
                function($item) {
                    return $item->is_new() || !\Nos\Form\Model_Answer::count(array(
                            'where' => array(array('answer_form_id' => $item->form_id)),
                        ));
                }
        ),
    ),
);