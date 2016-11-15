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

/**
 * Typing observer with safe unserialize
 */
class Observer_Typing extends \Orm\Observer_Typing
{
    /**
     * @var  array  db data types with the method(s) to use, optionally pre- or post-database
     */
    public static $type_methods = array(
        'serialize' => array(
            'before' => 'Orm\\Observer_Typing::type_serialize',
            'after'  => 'Nos\Form\Observer_Typing::type_unserialize',
        ),
    );

	/**
	 * Safely unserializes the input
	 *
	 * @param   string  value
	 *
	 * @return  mixed
	 */
	public static function type_unserialize($var)
	{
	    if (empty($var)) {
	        return array();
        }

        // Tries to unserialize
        $result = @unserialize($var);

        return $result !== $var ? $result : $var;
	}
}
