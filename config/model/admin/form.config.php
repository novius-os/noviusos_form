<?php

return array(
    'query' => array(
        'model' => 'Nos\Form\Model_Form',
        'order_by' => array('form_name' => 'ASC'),
    ),
    'search_text' => 'form_name',
    'dataset' => array(
        'title' => array(
            'column'        => 'form_name',
            'headerText'    => __('Name'),
        ),
    ),
);