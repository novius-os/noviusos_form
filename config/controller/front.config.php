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

                // The script that handles the form wizard, condition, etc...
                'script_url' => 'static/apps/noviusos_form/dist/js/front/form.min.js',

                // The form stylesheet
                'stylesheet_url' => 'static/apps/noviusos_form/dist/css/front/form.min.css',
            ),
        ),

        // Bootstrap layout
        'bootstrap' => array(

            // The view used to display the form
            'view' => 'noviusos_form::front/form',

            // The view params
            'view_params' => array(

                // The script that handles the form wizard, condition, etc...
                'script_url' => 'static/apps/noviusos_form/dist/js/front/form.min.js',

                // The form stylesheet
                'stylesheet_url' => 'static/apps/noviusos_form/dist/css/front/form-bootstrap.min.css',
            ),
        ),
    ),
);
