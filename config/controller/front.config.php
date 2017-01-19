<?php

return array(

    // The available layouts (you can configure which one to use in config/config.php)
    'layouts' => array(
        // Default layout (works with no framework)
        'default' => array(
            // The view used to display the form
            'view' => 'noviusos_form::front/form',
            // The view params
            'view_params' => array(
                // The JS params injected before other scripts in "noviusos_form_params" JS global variable
                'js_params_injected_inline' => array(
                    'uri_base' => \Fuel\Core\Uri::base(),
                ),
                // The form script (for wizard, condition, etc...)
                'script_url' => 'static/apps/noviusos_form/dist/js/front/form.min.js',
                // The form stylesheet
                'stylesheet_url' => 'static/apps/noviusos_form/dist/css/front/form.min.css',
                // The view used to display the fields layout
                'view_fields' => 'noviusos_form::front/form/fields',
                // The view used to display the controls
                'view_controls' => 'noviusos_form::front/form/controls',
            ),
        ),

        // Bootstrap layout
        'foundation' => array(
            'view' => 'noviusos_form::front/form',
            'view_params' => array(
                'js_params_injected_inline' => array(
                    'uri_base' => \Fuel\Core\Uri::base(),
                ),
                'script_url' => 'static/apps/noviusos_form/dist/js/front/form.min.js',
                'stylesheet_url' => 'static/apps/noviusos_form/dist/css/front/form-foundation.min.css',
                'view_fields' => 'noviusos_form::front/form/fields-foundation',
                'view_controls' => 'noviusos_form::front/form/controls-foundation',
            ),
        ),

        // Bootstrap layout
        'bootstrap' => array(
            'view' => 'noviusos_form::front/form',
            'view_params' => array(
                'js_params_injected_inline' => array(
                    'uri_base' => \Fuel\Core\Uri::base(),
                ),
                'script_url' => 'static/apps/noviusos_form/dist/js/front/form.min.js',
                'stylesheet_url' => 'static/apps/noviusos_form/dist/css/front/form-bootstrap.min.css',
                'view_controls' => 'noviusos_form::front/form/controls-bootstrap',
                'view_fields' => 'noviusos_form::front/form/fields-bootstrap',
            ),
        ),
    ),
);
