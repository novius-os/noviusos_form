<?php

namespace Nos\Form;

class Helper_Admin_Form
{
    /**
     * @return array
     */
    public static function getAvailableFields()
    {
        $config = \Config::load('noviusos_form::config', true);

        // Gets the available fields drivers
        $availableFieldsDrivers = \Arr::get($config, 'available_fields_drivers', array());

        // Ajout des drivers aux layouts
        $driversLayouts = array();
        foreach ($availableFieldsDrivers as $driverClass) {
            $driverId = \Str::lower(\Inflector::friendly_title($driverClass, '_'));

            // Loads the driver's configuration
            $driverConfig = $driverClass::getConfig();

            // Skips if not available as field
            if (!\Arr::get($driverConfig, 'available_as_field', true)) {
                continue;
            }

            // Skips if restricted to experts and current user is not an expert
            if (\Arr::get($driverConfig, 'expert') && !\Session::user()->user_expert) {
                continue;
            }

            $driversLayouts[$driverClass] = array(
                'icon' => \Arr::get($driverConfig, 'icon'),
                'title' => \Arr::get($driverConfig, 'name', \Inflector::humanize($driverClass)),
                'special' => \Arr::get($driverConfig, 'special'),
                'definition' => array(
                    'layout' => 'default=4',
                    'fields' => array(
                        'default' => array(
                            'driver' => $driverClass,
                        ),
                    ),
                ),
            );
        }

        $driversLayouts = \Arr::merge($driversLayouts, \Arr::get($config, 'available_drivers_layouts', array()));

        return $driversLayouts;
    }

    /**
     * @return mixed
     */
    public static function getAvailableTemplates()
    {
        $config = \Config::load('noviusos_form::config', true);

        // Gets the available fields layouts
        $availableFieldsLayouts = \Arr::get($config, 'available_fields_layouts', array());

        return $availableFieldsLayouts;
    }
}