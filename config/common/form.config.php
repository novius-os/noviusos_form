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
            'value' => function ($item) {
                return $item->is_new() ? 0 : \Nos\Form\Model_Answer::count(array(
                        'where' => array(array('answer_form_id' => $item->form_id)),
                    ));
            },
            'sorting_callback' => function (&$query, $sortDirection) {
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
        'deleting button N items' => n__(
            'Yes, delete this form',
            'Yes, delete these {{count}} forms'
        ),

        'deleting wrong confirmation' => __('We cannot delete this form as the number of answers you’ve entered is wrong. Please amend it.'),

        'N items' => n__(
            '1 form',
            '{{count}} forms'
        ),
    ),
    'actions' => array(
        'add' => array(
            'label' => __('Add a form'),
            // 'visible' is needed to hide the button from the toolbar...
            'visible' => array(
                'check_permission' => function () {
                    return \Nos\User\Permission::atLeast('noviusos_form::all', '2_write', 2);
                },
            ),
            // ... and 'disabled' is needed deny adding a new item using direct access (used by Controller_Crud)
            'disabled' => array(
                'check_permission' => function () {
                    return !\Nos\User\Permission::atLeast('noviusos_form::all', '2_write', 2);
                },
            ),
        ),
        'edit' => array(
            'disabled' => array(
                'check_permission' => function ($item) {
                    return !\Nos\User\Permission::atLeast('noviusos_form::all', '2_write', 2);
                }
            ),
        ),
        'delete' => array(
            'disabled' => array(
                'check_permission' => function ($item) {
                    return !\Nos\User\Permission::atLeast('noviusos_form::all', '2_write', 2);
                }
            ),
        ),
        'answers' => array(
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
            'visible' => array(
                'check_is_new' => function ($params) {
                    return !isset($params['item']) || !$params['item']->is_new();
                },
            ),
            'disabled' => array(
                'check_empty' => function ($item) {
                    if ($item->is_new() || !\Nos\Form\Model_Answer::count(array(
                        'where' => array(array('answer_form_id' => $item->form_id)),
                    ))) {
                        return __('There is no answers yet.');
                    }
                }
            ),
        ),
        'export' => array(
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
            'visible' => function ($params) {
                return !isset($params['item']) || !$params['item']->is_new();
            },
            'disabled' => array(
                'check_empty' => function ($item) {
                    if ($item->is_new() || !\Nos\Form\Model_Answer::count(array(
                        'where' => array(array('answer_form_id' => $item->form_id)),
                    ))) {
                        return __('There is no answers yet.');
                    }
                },
            ),
        ),
    ),
);
