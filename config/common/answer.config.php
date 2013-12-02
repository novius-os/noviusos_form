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
        'model' => 'Nos\Form\Model_Answer',
        'order_by' => array('answer_created_at' => 'DESC'),
    ),
    'data_mapping' => array(
        'receipt_date' => array(
            'value' => function ($item) {
                return \Date::create_from_string($item->answer_created_at, 'mysql')->wijmoFormat();
            },
            'title'    => __('Received on'),
            'dataType' => 'datetime',
            'dataFormatString' => 'f',
        ),
        'receipt_date_preview' => array(
            'value' => function ($item) {
                return \Date::formatPattern($item->answer_created_at);
            },
        ),
        'form_title' => array(
            'value' => function ($answer) {
                return $answer->form->form_name;
            },
            'visible' => false,
        ),
    ),
    'i18n' => array(
        // Crud
        'notification item deleted' => __('The answer has been deleted.'),

        // General errors
        'notification item does not exist anymore' => __('This answer doesn’t exist any more. It has been deleted.'),
        'notification item not found' => __('We cannot find this answer.'),

        // Deletion popup
        'deleting item title' => __('Deleting the answer ‘{{title}}’'),

        # Delete action's labels
        'deleting button N items' => n__(
            'Yes, delete this answer',
            'Yes, delete these {{count}} answers'
        ),

        'N items' => n__(
            '1 answer',
            '{{count}} answers'
        ),
    ),
    'actions' => array(
        'list' => array(
            'edit' => false,
            'add' => false,
            'visualise' => array(
                'label' => __('Visualise'),
                'iconClasses' => 'nos-icon16 nos-icon16-eye',
                'primary' => true,
                'targets' => array(
                    'grid' => true,
                ),
                'action' => array(
                    'action' => 'nosTabs',
                    'tab' => array(
                        'url' => 'admin/noviusos_form/answer/visualise/{{_id}}',
                        'label' => str_replace('{{title}}', '{{form_title}}', __('Answer to ’{{title}}’')),
                        'iconUrl' => 'static/apps/noviusos_form/img/icons/form-16.png',
                    ),
                ),
                'visible' => true,
                'disabled' => false,
            ),
            'delete' => array(
                'disabled' => array(
                    'check_permission' => function ($item) {
                        return !\Nos\User\Permission::atLeast('noviusos_form::all', '2_write', 2);
                    }
                ),
            ),
        ),
        'order' => array(
            'Nos\Form\Model_Answer.visualise',
            'Nos\Form\Model_Answer.delete',
        ),
    ),
);
