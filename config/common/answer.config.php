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
        'model' => 'Nos\Form\Model_Answer',
        'order_by' => array('answer_created_at' => 'DESC'),
    ),
    'data_mapping' => array(
        'receipt_date' => array(
            'value' => function ($item) {
                return \Date::create_from_string($item->answer_created_at, 'mysql')->wijmoFormat();
            },
            'title'    => __('Receipt date'),
            'dataType' => 'datetime',
            'dataFormatString' => 'f',
        ),
        'form_title' => array(
            'value' => function($answer) {
                return $answer->form->form_name;
            },
            'visible' => false,
        ),
    ),
    'actions' => array(
        'list' => array(
            'Nos\Form\Model_Answer.edit' => false,
            'Nos\Form\Model_Answer.add' => false,
            'Nos\Form\Model_Answer.visualise' => array(
                'label' => __('Visualise'),
                'iconClasses' => 'nos-icon16 nos-icon16-eye',
                'primary' => true,
                'targets' => array(
                    'toolbar-edit' => false,
                ),
                'action' => array(
                    'action' => 'nosTabs',
                    'tab' => array(
                        'url' => 'admin/noviusos_form/answer/visualise/{{_id}}',
                        'label' => str_replace('{{title}}', '{{form_title}}', __('Answer to ’{{title}}’')),
                        'iconUrl' => 'static/apps/noviusos_form/img/icons/form-16.png',
                    ),
                ),
                'visible' => function() {
                    return true;
                },
                'disabled' => function() {
                    return false;
                },
            ),
        ),
        'order' => array(
            'Nos\Form\Model_Answer.visualise',
            'Nos\Form\Model_Answer.delete',
        ),
    ),
);