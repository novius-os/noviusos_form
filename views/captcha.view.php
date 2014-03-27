<?php
/**
 * NOVIUS OS - Web OS for digital communication
 *
 * @copyright  2011 Novius
 * @license    GNU Affero General Public License v3 or (at your option) any later version
 *             http://www.gnu.org/licenses/agpl-3.0.html
 * @link http://www.novius-os.org
 */

use \Nos\Form\Helper_Foundation;

\Nos\I18n::current_dictionary('noviusos_form::front');

$number_1 = mt_rand(1, 10);
$number_2 = mt_rand(1, 50);
if (mt_rand(1, 2) == 1) {
    list($number_2, $number_1) = array($number_1, $number_2);
}
\Session::set('captcha.'.$form_id, $number_1 + $number_2);

$config['label'] = strtr(__('Help us prevent spam: How much is {{number_1}} plus {{number_2}}?'), array(
    '{{number_1}}' => $number_1,
    '{{number_2}}' => $number_2,
));

Helper_Foundation::addAttrToThing(
    $config['field'],
    'data-captcha',
    mt_rand(100, 999).'-'.\Session::get('captcha.'.$form_id).'-'.mt_rand(100, 999)
);
Helper_Foundation::addAttrToThing(
    $config['field'],
    'data-custom-validity',
    __('You have not passed the spam test. Please try again.')
);

//\Debug::dump($config);

echo \Nos\Form\Helper_Foundation::renderTemplate($template, $config);
