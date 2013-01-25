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
            'sorting_callback' => function(&$query, $sortDirection) {
                $query->_join_relation('answers', $join);
                $query->group_by($join['alias_from'].'.form_id');
                $query->order_by(\Db::expr('COUNT(*)'), $sortDirection);
            },
            'width' => 100,
            'ensurePxWidth' => true,
            'allowSizing' => false,
        ),
        'context' => true,
    ),
    'i18n' => array(
        // Crud
        'notification item added' => __('All good! Your new form has been added.'),
        'notification item deleted' => __('The form has been deleted.'),

        // General errors
        'notification item does not exist anymore' => __('This form doesn’t exist any more. It has been deleted.'),
        'notification item not found' => __('We cannot find this form.'),

        // Deletion popup
        'deleting item title' => __('Deleting the form ‘{{title}}’'),

        # Delete action's labels
        'deleting button 1 item' => __('Yes, delete this form'),

        '1 item' => __('1 form'),
        'N items' => __('{{count}} forms'),
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
            'icon' => 'extlink',
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