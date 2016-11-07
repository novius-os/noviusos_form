<?php

return array(
    'name' => __('Date'),

    // Fields default value
    'default_values' => array(
        'field_label' => __('Pick a date:'),
    ),

    'admin' => array(
        // Meta layout
        'layout' => array(
            'main' => array(
                'fields' => array(
                    'field_label',
                ),
            ),
            'optional' => array(
                'fields' => array(
                    'field_mandatory',
                    'field_default_value',
                    'field_origin',
                    'field_origin_var',
                    'field_details',
                    'field_width',
                ),
            ),
        ),
    ),

    // Sets true if answer can be displayed in a column of the answer appdesk
    'answer_appdesk_config' => array(
        'dataType' => 'datetime',
//        'value' => function($fieldDriver, $answerField) {
//            return $fieldDriver->myCustomMethod($answerField);
//        }
    ),
);
