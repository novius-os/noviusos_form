<?php

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
            'cellFormatter' => array(
                array(
                    'type' => 'link',
                    'action' => 'Nos\Form\Model_Form.edit',
                ),
            ),
            'headerText'    => __('Name'),
        ),
        'answers_count' => array(
            'headerText' => __('Answers'),
            'cellFormatter' => array(
                array(
                    'type' => 'css',
                    'css' => array('text-align' => 'center'),
                ),
                array(
                    'type' => 'link',
                    'action' => 'Nos\Form\Model_Form.answers',
                ),
            ),
            'value' => function($item) {
                return $item->is_new() ? 0 : \Nos\Form\Model_Answer::count(array(
                        'where' => array(array('answer_form_id' => $item->form_id)),
                    ));
            },
            'width' => 100,
            'ensurePxWidth' => true,
            'allowSizing' => false,
        ),
        'title' => array(
            'column'        => 'form_name',
            'cellFormatter' => array(
                array(
                    'type' => 'link',
                    'action' => 'Nos\Form\Model_Form.edit',
                ),
            ),
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
            'enabled' =>
                function($item) {
                    return !$item->is_new() && \Nos\Form\Model_Answer::count(array(
                            'where' => array(array('answer_form_id' => $item->form_id)),
                        ));
                }
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
                'url' => 'admin/noviusos_form/form/export/{{id}}',
            ),
            'enabled' =>
                function($item) {
                    return !$item->is_new() && \Nos\Form\Model_Answer::count(array(
                            'where' => array(array('answer_form_id' => $item->form_id)),
                        ));
                }
        ),
    ),
);