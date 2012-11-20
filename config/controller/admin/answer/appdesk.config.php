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
    'model' => 'Nos\Form\Model_Answer',
    'inspectors' => array(
        'date',
        'preview' => array(
            'appdesk' => array(
                'vertical' => true,
                'reloadEvent' => 'Nos\\Form\\Model_Answer',
                'label' => __('Preview'),
                'preview' => true,
                'options' => array(
                    'meta' => array(
                        'receipt_date' => array(
                            'label' => __('Receipt date:'),
                        ),
                    ),
                    //'actions' => array('edit', 'delete', 'visualise'),
                ),
            )
        ),
    ),
    'search_text' => array(
        function ($value, $query)
        {
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
            function ($value, $query)
            {
                if ($value) {
                    $query->where(array('answer_form_id', '=', $value));
                }
                return $query;
            },
    ),
);
