<?php
/**
 * NOVIUS OS - Web OS for digital communication
 *
 * @copyright  2017 Novius
 * @license    GNU Affero General Public License v3 or (at your option) any later version
 *             http://www.gnu.org/licenses/agpl-3.0.html
 * @link http://www.novius-os.org
 */

namespace Nos\Form;

class Model_Field_Attribute extends \Nos\Orm\Model
{
    protected static $_table_name = 'nos_form_field_attributes';
    protected static $_primary_key = array('fiat_id');
    protected static $_prefix = 'fiat_';

    protected static $_title_property = 'fiat_key';
    protected static $_properties = array(
        'fiat_id' => array(
            'default' => null,
            'data_type' => 'int',
            'null' => false,
        ),
        'fiat_field_id' => array(
            'default' => null,
            'data_type' => 'int',
            'null' => false,
        ),
        'fiat_key' => array(
            'default' => '',
            'data_type' => 'varchar',
            'null' => false,
        ),
        'fiat_value' => array(
            'default' => '',
            'data_type' => 'varchar',
            'null' => false,
        ),
    );
}
