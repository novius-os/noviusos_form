<?php

return array(
    'query' => array(
        'model' => 'Nos\Form\Model_Answer',
        'order_by' => array('answer_created_at' => 'DESC'),
    ),
    'data_mapping' => array(
        'receipt_date' => array(
            'column'        => 'answer_created_at',
            'headerText'    => __('Receipt date'),
            'dataType' => 'datetime',
        ),
    ),
    'actions' => array(
        'Nos\Form\Model_Answer.edit' => false,
        'Nos\Form\Model_Answer.add' => false,
        'Nos\Form\Model_Answer.visualize' => array(
            'label' => __('Visualize'),
            'name' => 'visualize',
            'iconClasses' => 'nos-icon16 nos-icon16-eye',
            'primary' => true,
            'context' => array(
                'list' => true,
            ),
            'action' => array(
                'action' => 'nosTabs',
                'tab' => array(
                    'url' => 'admin/noviusos_form/answer/visualize/{{id}}',
                    'label' => __('Answer'),
                    'iconUrl' => 'static/apps/noviusos_form/img/icons/form-16.png',
                ),
            ),
        ),
    ),
);