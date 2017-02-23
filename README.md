# Forms

Novius OS is an Open Source PHP Content Management System designed as an applications platform, hence the ‘OS’ suffix. Check out [Novius OS’ readme](http://github.com/novius-os/novius-os#readme) for more info.

The ‘Forms’ application for Novius OS allows you to create and publish forms to collect answers from your site visitors (e.g. contact form, order form, subscription).

## Requirements

* ‘Forms’ runs on Novius OS Elche.

## Installation

* [How to install a Novius OS application](http://community.novius-os.org/how-to-install-a-nos-app.html)

## Documentation

### Form layout

Forms are rendered in front-office using a layout. The default layout uses specific embedded styles to display the grid and the fields.

Here is the list of the default available layouts :

| Name                  | `front_layout` key value  |
|:--------------------- |:------------------------- |
| Default               | `default`                 |
| Twitter Bootstrap     | `foundation`              |
| Zurb Foundation       | `bootstrap`               |

You can **change which layout to use** via the `front_layout` key in `config/config.php` :

Take a look at the layouts configuration in `config/controller/front.config.php` if you want to customize the views, CSS or Javascript.

### Field types

In a form you can have different types of fields (text, radio, select, file...). The fields are implemented via drivers..

Here is the list of the default available field types/drivers :

| Description                               | Driver                                | HTML tag                      |
|:----------------------------------------- |:------------------------------------- |------------------------------ |
| Single line text                          | `Driver_Field_Input_Text`             | `<input type="text">`         |
| Date                                      | `Driver_Field_Input_Date`             | `<input type="date">`         |
| Email                                     | `Driver_Field_Input_Email`            | `<input type="email">`        |
| File upload                               | `Driver_Field_Input_File`             | `<input type="file">`         |
| Number                                    | `Driver_Field_Input_Number`           | `<input type="number">`       |
| Multiple line text                        | `Driver_Field_Textarea`               | `<textarea>`                  |
| Unique choice (drop-down list)            | `Driver_Field_Select`                 | `<select>`                    |
| Unique choice (inline list)               | `Driver_Field_Radio`                  | `<input type="radio">`        |
| Multiple choices (inline list)            | `Driver_Field_Checkbox`               | `<input type="checkbox">`     |
| Single line text                          | `Driver_Field_Hidden`                 | `<input type="hidden">`       |
| Separator                                 | `Driver_Field_Separator`              | `<hr>`                        |
| Variable                                  | `Driver_Field_Variable`               | `<input type="hidden">`       |
| Text message                              | `Driver_Field_Message`                | `<label>`                     |
| Form recipient choice (drop-down list)    | `Driver_Field_Recipient_Select`       | `<select>`                    |

#### How to create a new type of field

##### Driver

First you have to **create the driver**, it consists of a class that extends `Driver_Field_Abstract`.

By default there are only two methods to implement :
* `public function getHtml($inputValue = null, $formData = array()) {}` which returns the HTML of the field to be displayed in front office
* `public function getPreviewHtml() {}` which returns the HTML preview of the field to be displayed in backoffice

Take a look at `Driver_Field_Abstract` to have a full overview of what you can implement. You should also take a look at the traits in `classes/trait/driver` and interfaces in `classes/interface/driver` which provide some feature implementations (eg. placeholder, single or multiple choices, etc.).

##### Configuration

Then you have to **create the configuration file** for the driver, with at least the field's name :

```php
array(
    'name' => __('Paragraph text'),
),
```

Take a look at the existing drivers configurations to have a full overview of what you can implement. 

##### Registration

Finally you have to **register your field** in the list of available fields drivers, take a look at the `available_fields_drivers` key in `config/config.php`.

### Field layouts

To have the **ability to add a field to a form** in back-office, it have to be implemented as a field layout.

Take a look at the `available_fields_layouts` key in `config/config.php` for some examples.

#### How to create a new field layout

Here is an example of a field layout to add a `Single line text` field on 4 columns (100% width) :

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
                     'default_values' => array(
                         'field_label' => __('I\'m the label'),
                     ),
                 ),
             ),
         ),
     ),
),
```

The interesting parts are the keys in the `definition` array.

The `fields` key contains the fields configuration, each configuration consists of an array with an arbitrary identifier as key and containing at least the `driver` property. You can set predefined property values in the `default_values` array (including EAV attributes).

The `layout` key contains the fields layout, each field must be added in the form `XXX=Y` where `XXX` is the field's arbitrary identifier and `Y` is the number of columns. Each form row can contain up to 4 columns.


#### The default fields layout

When you create a new form, it will be populated with a default field layout.

You can change it via the `default_fields_layout` key in `config/config.php` :
```php
array(
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

### For contributors

See [How to build the CSS and Javascript assets](DEV.md#how-to-build-the-css-and-javascript-assets).

#### Support

* You’ll find help in [the forum](http://forums.novius-os.org/en).

## Demo & further information

* Try ‘Forms’ at [demo.novius-os.org](http://demo.novius-os.org/admin).
* [Watch the screencast](http://www.youtube.com/watch?v=mptrVkmsw5g&list=PL49B38887F978ED5E) on YouTube.
* [An extension to support Japanese addresses](https://github.com/ounziw/jaaddress) has been developed by Fumito Mizuno.

## License

‘Forms’ is licensed under [GNU Affero General Public License v3](http://www.gnu.org/licenses/agpl-3.0.html) or (at your option) any later version.
