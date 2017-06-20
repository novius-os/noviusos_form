<?php
/**
 * NOVIUS OS - Web OS for digital communication
 *
 * @copyright  2017 Novius
 * @license    GNU Affero General Public License v3 or (at your option) any later version
 *             http://www.gnu.org/licenses/agpl-3.0.html
 * @link http://www.novius-os.org
 */

return array(
    // The front layout (default, foundation, ...)
    'front_layout' => 'default', // Take a look at the available layouts in config/controller/front.config.php

    // Tries to automatically set the reply-to of the answer email notification using the first field that handles email
    'add_replyto_to_first_email' => true,

    // Maximum allowed size for mail attachments (if total size exceeds then no attachments will be set)
    'mail_attachments_max_size' => 8388608,

    // Fields drivers that will be available in the field type list in backoffice
    'available_fields_drivers' => array(
        \Nos\Form\Driver_Field_Input_Text::class,
        \Nos\Form\Driver_Field_Textarea::class,
        \Nos\Form\Driver_Field_Checkbox::class,
        \Nos\Form\Driver_Field_Select::class,
        \Nos\Form\Driver_Field_Radio::class,
        \Nos\Form\Driver_Field_Input_Email::class,
        \Nos\Form\Driver_Field_Input_Number::class,
        \Nos\Form\Driver_Field_Input_Date::class,
        \Nos\Form\Driver_Field_Input_File::class,
        \Nos\Form\Driver_Field_Separator::class,
        \Nos\Form\Driver_Field_Message::class,
        \Nos\Form\Driver_Field_Hidden::class,
        \Nos\Form\Driver_Field_Variable::class,
        \Nos\Form\Driver_Field_Recipient_Select::class,
    ),

    // The default fields layout when creating a new form in backoffice
    'default_fields_layout' => array(
        'definition' => array(
            'layout' => "firstname=2,lastname=2\nemail=4",
            'fields' => array(
                'firstname' => array(
                    'driver' => \Nos\Form\Driver_Field_Input_Text::class,
                    'default_values' => array(
                        'field_label' => __('Firstname:'),
                    ),
                ),
                'lastname' => array(
                    'driver' => \Nos\Form\Driver_Field_Input_Text::class,
                    'default_values' => array(
                        'field_label' => __('Lastname:'),
                    ),
                ),
                'email' => array(
                    'driver' => \Nos\Form\Driver_Field_Input_Email::class,
                    'default_values' => array(
                        'field_label' => __('Email address:'),
                    ),
                ),
            ),
        ),
    ),

    // The available fields layouts (displayed in the "Fields templates" column when clicking the "Add field" button in the form CRUD in backoffice)
    'available_fields_layouts' => array(
        'fullname' => array(
            'icon' => 'static/apps/noviusos_form/img/fields/fullname.png',
            'title' => __('Full name'),
            'definition' => array(
                'layout' => 'gender=1,firstname=1,name=2',
                'fields' => array(
                    'gender' => array(
                        'driver' => \Nos\Form\Driver_Field_Select::class,
                        'default_values' => array(
                            'field_label' => __('Title:'),
                            'field_choices' => __("Ms\nMr"),
                        ),
                    ),
                    'firstname' => array(
                        'driver' => \Nos\Form\Driver_Field_Input_Text::class,
                        'default_values' => array(
                            'field_label' => __('Firstname:'),
                        ),
                    ),
                    'name' => array(
                        'driver' => \Nos\Form\Driver_Field_Input_Text::class,
                        'default_values' => array(
                            'field_label' => __('Name:'),
                        ),
                    ),
                ),
            ),
        ),
        'address' => array(
            'icon' => 'static/apps/noviusos_form/img/fields/address.png',
            'title' => __('Address'),
            'definition' => array(
                'layout' => "line_1=4\nline_2=4\npostal=1,city=3",
                'fields' => array(
                    'line_1' => array(
                        'driver' => \Nos\Form\Driver_Field_Input_Text::class,
                        'default_values' => array(
                            'field_label' => __('First address line:'),
                        ),
                    ),
                    'line_2' => array(
                        'driver' => \Nos\Form\Driver_Field_Input_Text::class,
                        'default_values' => array(
                            'field_label' => __('Second address line:'),
                        ),
                    ),
                    'postal' => array(
                        'driver' => \Nos\Form\Driver_Field_Input_Text::class,
                        'default_values' => array(
                            'field_label' => __('Postal code:'),
                        ),
                    ),
                    'city' => array(
                        'driver' => \Nos\Form\Driver_Field_Input_Text::class,
                        'default_values' => array(
                            'field_label' => __('City:'),
                        ),
                    ),
                ),
            ),
        ),
    ),

    // The available drivers layouts (displayed in the "Standard fields" and "Special fields" columns
    // when clicking the "Add field" button in the form CRUD in backoffice)
    'available_drivers_layouts' => array(
        //// Example of how to override a default generated driver layout :
        //\Nos\Form\Driver_Field_Input_Text::class => array(
        //    'title' => __('My custom title'),
        //    'definition' => array(
        //        'layout' => "default=2",
        //    ),
        //),
    ),
);
