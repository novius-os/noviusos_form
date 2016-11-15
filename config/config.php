<?php
/**
 * NOVIUS OS - Web OS for digital communication
 *
 * @copyright  2011 Novius
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
        \Nos\Form\Driver_Field_Input_File::class,
        \Nos\Form\Driver_Field_Input_Email::class,
        \Nos\Form\Driver_Field_Input_Number::class,
        \Nos\Form\Driver_Field_Input_Date::class,
        \Nos\Form\Driver_Field_Message::class,
        \Nos\Form\Driver_Field_Hidden::class,
        \Nos\Form\Driver_Field_Separator::class,
        \Nos\Form\Driver_Field_Variable::class,
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

    // The available fields layouts (displayed when clicking the "Add field" button in the form CRUD in backoffice)
    'available_fields_layouts' => array(
        'single_line_text' => array(
            'icon' => 'static/apps/noviusos_form/img/fields/text.png',
            'title' => __('Single line text'),
            'definition' => array(
                'layout' => 'text=4',
                'fields' => array(
                    'text' => array(
                        'driver' => \Nos\Form\Driver_Field_Input_Text::class,
                    ),
                ),
            ),
        ),
        'paragraph_text' => array(
            'icon' => 'static/apps/noviusos_form/img/fields/textarea.png',
            'title' => __('Paragraph text'),
            'definition' => array(
                'layout' => 'textarea=4',
                'fields' => array(
                    'textarea' => array(
                        'driver' => \Nos\Form\Driver_Field_Textarea::class,
                    ),
                ),
            ),
        ),
        'checkboxes' => array(
            'icon' => 'static/apps/noviusos_form/img/fields/checkbox.png',
            'title' => __('Multiple choice (checkboxes)'),
            'definition' => array(
                'layout' => 'checkbox=4',
                'fields' => array(
                    'checkbox' => array(
                        'driver' => \Nos\Form\Driver_Field_Checkbox::class,
                        'default_values' => array(
                            'field_choices' => __("First option\nSecond option"),
                        ),
                    ),
                ),
            ),
        ),
        'dropdown' => array(
            'icon' => 'static/apps/noviusos_form/img/fields/dropdown.png',
            'title' => __('Unique choice (drop-down list)'),
            'definition' => array(
                'layout' => 'select=4',
                'fields' => array(
                    'select' => array(
                        'driver' => \Nos\Form\Driver_Field_Select::class,
                        'default_values' => array(
                            'field_choices' => __("First option\nSecond option"),
                        ),
                    ),
                ),
            ),
        ),
        'unique_choice' => array(
            'icon' => 'static/apps/noviusos_form/img/fields/radio.png',
            'title' => __('Unique choice (radio buttons)'),
            'definition' => array(
                'layout' => 'radio=4',
                'fields' => array(
                    'radio' => array(
                        'driver' => \Nos\Form\Driver_Field_Radio::class,
                        'default_values' => array(
                            'field_choices' => __("First choice\nSecond choice"),
                        ),
                    ),
                ),
            ),
        ),
        'file' => array(
            'icon' => 'static/apps/noviusos_form/img/fields/file.png',
            'title' => __('File'),
            'definition' => array(
                'layout' => 'file=4',
                'fields' => array(
                    'file' => array(
                        'driver' => \Nos\Form\Driver_Field_Input_File::class,
                        'default_values' => array(
                            'field_label' => __('I’m the label of a file input, click to edit me:'),
                        ),
                    ),
                ),
            ),
        ),
        'email' => array(
            'icon' => 'static/apps/noviusos_form/img/fields/email.png',
            'title' => __('Email address'),
            'definition' => array(
                'layout' => 'email=4',
                'fields' => array(
                    'email' => array(
                        'driver' => \Nos\Form\Driver_Field_Input_Email::class,
                        'default_values' => array(
                            'field_label' => __('Your email address:'),
                        ),
                    ),
                ),
            ),
        ),
        'number' => array(
            'icon' => 'static/apps/noviusos_form/img/fields/number.png',
            'title' => __('Number'),
            'definition' => array(
                'layout' => 'number=4',
                'fields' => array(
                    'number' => array(
                        'driver' => \Nos\Form\Driver_Field_Input_Number::class,
                        'default_values' => array(
                            'field_label' => __('Enter a number:'),
                        ),
                    ),
                ),
            ),
        ),
        'date' => array(
            'icon' => 'static/apps/noviusos_form/img/fields/date.png',
            'title' => __('Date'),
            'definition' => array(
                'layout' => 'date=4',
                'fields' => array(
                    'date' => array(
                        'driver' => \Nos\Form\Driver_Field_Input_Date::class,
                        'default_values' => array(
                            'field_label' => __('Pick a date:'),
                        ),
                    ),
                ),
            ),
        ),
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
        'message' => array(
            'icon' => 'static/apps/noviusos_form/img/fields/message.png',
            'title' => __('Message'),
            'definition' => array(
                'layout' => 'message=4',
                'fields' => array(
                    'message' => array(
                        'driver' => \Nos\Form\Driver_Field_Message::class,
                        'default_values' => array(
                            'field_label' => __('Message:'),
                            'field_message' => 'Your message',
                        ),
                    ),
                ),
            ),
        ),
        'separator' => array(
            'icon' => 'static/apps/noviusos_form/img/fields/separator.png',
            'title' => __('Separator'),
            'definition' => array(
                'layout' => 'separator=4',
                'fields' => array(
                    'separator' => array(
                        'driver' => \Nos\Form\Driver_Field_Separator::class,
                        'default_values' => array(
                            'field_label' => __('Separator'),
                        ),
                    ),
                ),
            ),
        ),
        'recipients' => array(
            'icon' => 'static/apps/noviusos_form/img/fields/dropdown.png',
            'title' => __('Email Recipient List'),
            'definition' => array(
                'layout' => 'select=4',
                'fields' => array(
                    'select' => array(
                        'driver' => \Nos\Form\Driver_Field_Recipient_Select::class,
                        'default_values' => array(
                            'field_technical_id' => 'recipient-list',
                            'field_details' => __('A notification of the form answer will be sent to the selected email.'), //Separate name and email addresses with a "=" sign. (eg. Name=mail@domain.com)
                            'field_choices' => __("First option=mail@domain.com\nSecond option=othermail@domain.com"),
                        ),
                    ),
                ),
            ),
        ),
        'hidden' => array(
            'icon' => 'static/apps/noviusos_form/img/fields/hidden.png',
            'title' => __('Hidden'),
            'expert' => true,
            'definition' => array(
                'layout' => 'hidden=4',
                'fields' => array(
                    'hidden' => array(
                        'driver' => \Nos\Form\Driver_Field_Hidden::class,
                        'default_values' => array(
                            'field_label' => __('I’m the label for internal use only as I won’t be shown to users:'),
                        ),
                    ),
                ),
            ),
        ),
        'variable' => array(
            'icon' => 'static/apps/noviusos_form/img/fields/variable.png',
            'title' => __('Variable'),
            'expert' => true,
            'definition' => array(
                'layout' => 'variable=4',
                'fields' => array(
                    'variable' => array(
                        'driver' => \Nos\Form\Driver_Field_Variable::class,
                    ),
                ),
            ),
        ),
    ),
);
