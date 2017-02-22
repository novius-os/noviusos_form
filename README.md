# Forms

Novius OS is an Open Source PHP Content Management System designed as an applications platform, hence the ‘OS’ suffix. Check out [Novius OS’ readme](http://github.com/novius-os/novius-os#readme) for more info.

The ‘Forms’ application for Novius OS allows you to create and publish forms to collect answers from your site visitors (e.g. contact form, order form, subscription).

## Requirements

* ‘Forms’ runs on Novius OS Elche.

## Installation

* [How to install a Novius OS application](http://community.novius-os.org/how-to-install-a-nos-app.html)

## Documentation


### Front-office integration

By default the forms are displayed in front-office using a custom grid layout with custom css.

Alternately you can use one of theses layouts :
 * Twitter Bootstrap
 * Zurb Foundation

Take a look at the layouts configuration in `config/controller/front.config.php`.

You can change the layout to be used via the `front_layout` key in `config/config.php` :

### Fields types

In a form you can have different types of fields (text, radio, select, file...). The fields are implemented via drivers.

Here is the list of the default available fields/drivers :
* `Driver_Field_Input_Text` Single line text
* `Driver_Field_Input_Date` Date
* `Driver_Field_Input_Email` Email
* `Driver_Field_Input_File` File upload
* `Driver_Field_Input_Number` Number
* `Driver_Field_Textarea` Multiple line text
* `Driver_Field_Select` Unique choice (drop-down list)
* `Driver_Field_Radio` Unique choice (radio buttons)
* `Driver_Field_Checkbox` Multiple choices (checkboxes)
* `Driver_Field_Hidden` Hidden
* `Driver_Field_Separator` Separator
* `Driver_Field_Variable` Variable
* `Driver_Field_Message` Text message
* `Driver_Field_Recipient_Select` Form recipient choice (drop-down list)

#### How to create a new type of field

First you have to **create the driver**, it consists of a class that extends `Driver_Field_Abstract`.

By default there are only two methods to implement :
* `public function getHtml($inputValue = null, $formData = array()) {}` which returns the HTML representation of the field (used to display the field in front office)
* `public function getPreviewHtml() {}` which returns the HTML representation of the field (used to display a preview of the field in backoffice)

Take a look at `Driver_Field_Abstract` to have a full overview of what you can implement.

Then you have to **create the configuration file** for the driver, with at least the field's name :

```php
array(
    'name' => __('Paragraph text'),
),
```

Take a look at the existing drivers configurations to have a full overview of what you can implement. 

Finally you have to **register your field** in the list of available fields drivers, take a look at the `available_fields_drivers` key in `config/config.php`.

### Fields layout

For a field to be available in a form, it have to be implemented as a field layout.

Take a look at the `fields_fields_drivers` key in `config/config.php` for some examples.

#### How to create a new field layout

Here is an example of a field layout to add a "Single line text" field on 4 columns (100% width) :

```php
array(
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
),
```

The interesting parts are the keys in the `definition` array :
* The `fields` key contains the fields configuration, each configuration consists of an array with an arbitrary identifier as key and containing at least the `driver` property.
* The `layout` key contains the fields layout, each field must be added in the form `XXX=Y` where `XXX` is the field's arbitrary identifier and `Y` is the number of columns. Each form row can contain up to 4 columns.

#### The default fields layout

When you create a new form, it will be populated with a default fields layout.

You can change this default layout via the `default_fields_layout` key in `config/config.php` :

```php
array(
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
),
```

See [How to create a new field layout](#how-to-create-a-new-field-layout) for more details on this configuration.

## Support

* You’ll find help in [the forum](http://forums.novius-os.org/en).

## Demo & further information

* Try ‘Forms’ at [demo.novius-os.org](http://demo.novius-os.org/admin).
* [Watch the screencast](http://www.youtube.com/watch?v=mptrVkmsw5g&list=PL49B38887F978ED5E) on YouTube.
* [An extension to support Japanese addresses](https://github.com/ounziw/jaaddress) has been developed by Fumito Mizuno.

## License

‘Forms’ is licensed under [GNU Affero General Public License v3](http://www.gnu.org/licenses/agpl-3.0.html) or (at your option) any later version.