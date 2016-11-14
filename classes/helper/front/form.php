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

class Helper_Front_Form
{
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


    public static function renderTemplate ($template, $args)
    {
        $replacements = array();
        foreach ($args as $name => $value) {
            $replacements['{' . $name . '}'] = static::renderThing($value);
        }
        return strtr($template, $replacements);
    }
}
