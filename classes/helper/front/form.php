<?php
/**
 * NOVIUS OS - Web OS for digital communication
 *
 * @copyright  2011 Novius
 * @license    GNU Affero General Public License v3 or (at your option) any later version
 *             http://www.gnu.org/licenses/agpl-3.0.html
 * @link http://www.novius-os.org
 */

namespace Nos\Form;

use Fuel\Core\Html;

class Helper_Front_Form
{
    /**
     * Gets the first page with an error
     *
     * @param array $fieldsLayout
     * @param array $errors
     * @return int
     */
    public static function getFirstErrorPage($fieldsLayout, $errors)
    {
        if (!empty($errors)) {
            // Gets the first error field page
            foreach ($errors as $name => $error) {
                foreach ($fieldsLayout as $page => $rows) {
                    foreach ($rows as $cols) {
                        foreach ($cols as $field) {
                            if ($field['name'] === $name) {
                                return intval($page);
                            }
                        }
                    }
                }
            }
        }

        return 0;
    }

    /**
     * Gets the class name for the specified width
     *
     * @param $width
     * @return mixed
     */
    public static function getWidthClassName($width)
    {
        $widths = array(
            0 => '',
            1 => 'one',
            2 => 'two',
            3 => 'three',
            4 => 'four',
            5 => 'five',
            6 => 'six',
            7 => 'seven',
            8 => 'eight',
            9 => 'nine',
            10 => 'ten',
            11 => 'eleven',
            12 => 'twelve',
        );
        return \Arr::get($widths, $width);
    }

    /**
     * Gets the parsley locale for the specified context
     *
     * @param $context
     * @return int|string
     */
    public static function getParsleyLocale($context)
    {
        if (!empty($context)) {
            // Locales with their custom context match
            $locales = array(
                'fr' => array(),
                'en' => array(),
                'es' => array(),
                'ja' => array(),
                'pt' => array(),
                'pt-br' => array('pt_BR'),
            );

            // Checks if one of the custom context match
            foreach ($locales as $locale => $contexts) {
                if (in_array($context, $contexts)) {
                    return $locale;
                }
            }

            // Gets the context lang locale
            $localeCode = \Nos\Tools_Context::localeCode($context);
            list($lang, $locale) = explode('_', $localeCode . '_');
            $locale = mb_strtolower($locale);
            $lang = mb_strtolower($lang);

            // Checks if context lang locale match
            if (isset($locales[$locale])) {
                return $locale;
            } elseif (isset($locales[$lang])) {
                return $lang;
            }
        }

        return 'en';
    }

    /**
     * @param $form
     * @param $field
     * @param $template
     * @param array $options
     * @return string|void
     */
    public static function renderField($form, $field, $template, $options = array())
    {
        // Custom field view
        if (!empty($field['view'])) {
            \Nos\FrontCache::viewForgeUncached($field['view'], \Arr::merge($options, array(
                'form'          => $form,
                'field'         => $field,
                'template'      => $template,
                'labelClass'    => \Arr::get($options, 'labelClass'),
                'fieldClass'    => \Arr::get($options, 'fieldClass'),
                'fieldError'    => \Arr::get($options, 'fieldError'),
                'errors'        => \Arr::get($options, 'errors', array()),
                'page'          => \Arr::get($options, 'page', 0),
                'enhancer_args' => \Arr::get($options, 'enhancer_args', array()),
            )), false);
            return;
        }

        // Default
        else {

            // Builds the placeholders
            $placeholders = array(
                'label'         => $field['label'],
                'field'         => $field['field'],
                'instructions'  => $field['instructions'],
                'labelClass'    => \Arr::get($options, 'labelClass'),
                'fieldClass'    => \Arr::get($options, 'fieldClass'),
                'fieldError'    => \Arr::get($options, 'fieldError'),
                'page'          => \Arr::get($options, 'page', 0),
            );

            // Merges with the custom placeholders
            $placeholders = \Arr::merge($placeholders, \Arr::get($options, 'placeholders', array()));

            return static::renderTemplate($template, $placeholders);
        }
    }

    /**
     * Renders the field error
     *
     * @param $error
     * @return null|string
     */
    public static function renderFieldError($error)
    {
        // Builds the error
        if (empty($error)) {
            return null;
        }

        return html_tag('div', array(
            'class' => 'parsley-custom-error-message',
        ), $error);
    }

    /**
     * Renders the template by replacing the placeholders with the specified vars
     *
     * @param $template
     * @param $vars
     * @return string
     */
    public static function renderTemplate($template, $vars)
    {
        $replacements = array();
        foreach ($vars as $name => $value) {
            $replacements['{' . $name . '}'] = static::renderThing($value);
        }
        return strtr($template, $replacements);
    }

    /**
     * Adds attributes to thing
     *
     * @param $thing
     * @param $attr
     * @param $value
     */
    public static function addAttrToThing(&$thing, $attr, $value)
    {
        if (isset($thing['callback'])) {
            $key = false;
            if ($thing['callback'] == 'html_tag') {
                $key = 1;
            }
            if (is_array($thing['callback']) and $thing['callback'][0] == 'Form') {
                if (in_array($thing['callback'][1], array('select', 'checkbox'))) {
                    $key = 3;
                } else {
                    $key = 2;
                }
            }
            if (false !== $key) {
                if (!isset($thing['args'][$key][$attr])) {
                    $thing['args'][$key][$attr] = $value;
                } else {
                    $thing['args'][$key][$attr] .= ' '.$value;
                }
            }
        }
    }

    /**
     * Gets html attributes of thing
     *
     * @param $thing
     * @return null
     */
    public static function getHtmlAttrs($thing)
    {
        if (!isset($thing['callback'])) {
            return null;
        }
        $key = false;
        if ($thing['callback'] == 'html_tag') {
            $key = 1;
        }
        if (is_array($thing['callback']) and $thing['callback'][0] == 'Form') {
            if (in_array($thing['callback'][1], array('select', 'checkbox'))) {
                $key = 3;
            } else {
                $key = 2;
            }
        }
        if (false === $key) {
            return null;
        }
        return $thing['args'][$key];
    }

    /**
     * Adds content to thing
     *
     * @param $thing
     * @param $content
     */
    public static function addContentToThing(&$thing, $content)
    {
        if (isset($thing['callback'])) {
            $key = false;
            if ($thing['callback'] == 'html_tag') {
                $key = 2;
            }
            if (is_array($thing['callback']) and $thing['callback'][0] == 'Form') {
                $key = 0;
            }
            $thing['args'][$key] .= $content;
        }
    }


    /**
     * Renders a thing
     *
     * @param $thing
     * @return mixed|string
     */
    public static function renderThing ($thing)
    {
        if (is_string($thing)) {
            return $thing;
        }
        if (is_array($thing)) {
            if (isset($thing['callback']) && is_callable($thing['callback'])) {
                $args = isset($thing['args']) ? $thing['args'] : array();
                return call_user_func_array($thing['callback'], $args);
            } else {
                $out = array();
                foreach ($thing as $t) {
                    if (is_array($t) && isset($t['template'])) {
                        $template = $t['template'];
                        unset($t['template']);
                        $vars = $t;
                        $out[] = static::renderTemplate($template, $vars);
                    } else {
                        $out[] = static::renderThing($t);
                    }
                }
                return implode($out);
            }
        }
    }
}
