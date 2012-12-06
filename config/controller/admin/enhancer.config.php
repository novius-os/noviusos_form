<?php
return array(
    'popup' => array(
        'layout' => array(
            'view' => 'noviusos_form::enhancer/popup',
        ),
    ),
    'preview' => array(
        'params' => array(
            'icon' => '../static/apps/noviusos_form/img/icons/form-64.png',
            'title' => function($enhancer_args) {
                if (!empty($enhancer_args['form_id'])) {
                    $form = \Nos\Form\Model_Form::find($enhancer_args['form_id']);
                }
                if (!empty($form)) {
                    return $form->form_name;
                } else {
                    return __('Form');
                }
            },
        ),
    ),
);
