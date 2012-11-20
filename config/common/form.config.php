<?php

return array(
    'query' => array(
        'model' => 'Nos\Form\Model_Form',
        'order_by' => array('form_name' => 'ASC'),
    ),
    'search_text' => 'form_name',
    'data_mapping' => array(
        'title' => array(
            'column'        => 'form_name',
            'headerText'    => __('Name'),
        ),
    ),
    'actions' => array(
        'Nos\Form\Model_Form.answers' => array(
            'label' => __('Answers'),
            'name' => 'answers',
            'icon' => 'mail-closed',
            'context' => array(
                'list' => true,
                'item' => true,
            ),
            'action' => array(
                'action' => 'nosTabs',
                'tab' => array(
                    'url' => 'admin/noviusos_form/answer/appdesk?form_id={{id}}',
                    'label' => __('Answers of "{{title}}"'),
                    'iconUrl' => 'static/apps/noviusos_form/img/icons/form-16.png',
                ),
            ),
        ),
        'Nos\Form\Model_Form.export' => array(
            'label' => __('Export'),
            'name' => 'export',
            'icon' => 'document',
            'context' => array(
                'list' => true,
                'item' => true,
            ),
            'action' => array(
                'action' => 'window.open',
                'url' => 'admin/noviusos_form/answer/appdesk/export?form_id={{id}}',
            ),
        ),
    ),
);