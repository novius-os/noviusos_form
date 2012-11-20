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
);