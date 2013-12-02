<?php
/**
 * NOVIUS OS - Web OS for digital communication
 *
 * @copyright  2011 Novius
 * @license    GNU Affero General Public License v3 or (at your option) any later version
 *             http://www.gnu.org/licenses/agpl-3.0.html
 * @link http://www.novius-os.org
 */

Nos\I18n::current_dictionary('noviusos_form::common', 'nos::common');

return array(
    'model' => 'Nos\Form\Model_Answer',
    'inspectors' => array(
        'date',
        'preview' => array(
            'appdesk' => array(
                'vertical' => true,
                'reloadEvent' => 'Nos\\Form\\Model_Answer',
                'preview' => true,
                'options' => array(
                    'meta' => array(
                        'receipt_date_preview' => array(
                            'label' => __('Received on:'),
                        ),
                    ),
                    'actions' => array('Nos\Form\Model_Answer.visualise', 'Nos\Form\Model_Answer.delete'),
                    'texts' => array(
                        // Note to translator: 'Preview' here is a label, not an action
                        'headerDefault' => __('Preview'),
                        'selectItem' => __('Click on an answer to preview it.'),
                    ),
                ),
            )
        ),
    ),
    'i18n' => array(
        'item' => __('answer'),
        'items' => __('answers'),
        'NItems' => n__(
            '1 answer',
            '{{count}} answers'
        ),
        'showNbItems' => n__(
            'Showing 1 answer out of {{y}}',
            'Showing {{x}} answers out of {{y}}'
        ),
        'showNoItem' => __('No answers'),
        // Note to translator: This is the action that clears the 'Search' field
        'showAll' => __('Show all answers'),
    ),
    'search_text' => array(
        function ($value, $query) {
            $query->related('fields', array('where' => array(
                array('anfi_field_type', 'IN', array('text', 'textarea', 'checkbox', 'select', 'radio', 'email', 'number')),
                array('anfi_value', 'LIKE', '%'.$value.'%'),
            )));
            return $query;
        }
    ),
    'appdesk' => array (
        'tab' => array (
            'iconUrl' => 'static/apps/noviusos_form/img/icons/form-16.png',
        ),
    ),
    'inputs' => array(
        'form_id' =>
            function ($value, $query) {
                if ($value) {
                    $query->where(array('answer_form_id', '=', $value));
                }
                return $query;
            },
    ),
);
