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

class Helper_Foundation
{
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
                return $thing['args'][$key];
            }
        }
        return;
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
