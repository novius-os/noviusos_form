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

class Model_Field extends \Nos\Orm\Model
{
    protected static $_table_name = 'nos_form_field';
    protected static $_primary_key = array('field_id');

    protected static $_properties = array(
        'field_id' => array(
            'default' => null,
            'data_type' => 'int unsigned',
            'null' => false,
        ),
        'field_form_id' => array(
            'default' => null,
            'data_type' => 'int unsigned',
            'null' => false,
        ),
        'field_type' => array(
            'default' => null,
            'data_type' => 'varchar',
            'null' => false,
        ),
        'field_label' => array(
            'default' => null,
            'data_type' => 'varchar',
            'null' => false,
        ),
        'field_message' => array(
            'default' => null,
            'data_type' => 'text',
            'null' => false,
        ),
        'field_virtual_name' => array(
            'default' => null,
            'data_type' => 'varchar',
            'null' => false,
        ),
        'field_choices' => array(
            'default' => null,
            'data_type' => 'text',
            'null' => false,
        ),
        'field_created_at' => array(
            'default' => null,
            'data_type' => 'datetime',
            'null' => false,
        ),
        'field_mandatory' => array(
            'default' => null,
            'data_type' => 'tinyint',
            'null' => false,
        ),
        'field_default_value' => array(
            'default' => null,
            'data_type' => 'varchar',
            'null' => false,
        ),
        'field_details' => array(
            'default' => null,
            'data_type' => 'text',
            'null' => false,
        ),
        'field_style' => array(
            'default' => null,
            'data_type' => 'enum',
            'null' => false,
        ),
        'field_width' => array(
            'default' => null,
            'data_type' => 'tinyint',
            'null' => false,
        ),
        'field_height' => array(
            'default' => null,
            'data_type' => 'tinyint',
            'null' => false,
        ),
        'field_limited_to' => array(
            'default' => null,
            'data_type' => 'int',
            'null' => false,
        ),
        'field_origin' => array(
            'default' => null,
            'data_type' => 'varchar',
            'null' => false,
        ),
        'field_origin_var' => array(
            'default' => null,
            'data_type' => 'varchar',
            'null' => false,
        ),
        'field_technical_id' => array(
            'default' => null,
            'data_type' => 'varchar',
            'null' => false,
        ),
        'field_technical_css' => array(
            'default' => null,
            'data_type' => 'varchar',
            'null' => false,
        ),
    );

    protected static $_has_one = array();
    protected static $_many_many = array();
    protected static $_twinnable_has_one = array();
    protected static $_twinnable_has_many = array();
    protected static $_twinnable_belongs_to = array();
    protected static $_twinnable_many_many = array();

    protected static $_observers = array(
        'Orm\\Observer_Self',
        'Orm\\Observer_CreatedAt' => array(
            'mysql_timestamp' => true,
            'property' => 'field_created_at',
        ),
    );

    protected static $_belongs_to = array(
        'form' => array(
            'key_from'       => 'field_form_id',
            'model_to'       => 'Nos\Form\Model_Form',
            'key_to'         => 'form_id',
            'cascade_save'   => false,
            'cascade_delete' => false,
        ),
    );

    protected static $_has_many = array(
        'answer_fields' => array(
            'key_from'       => 'field_id',
            'model_to'       => 'Nos\Form\\Model_Answer_Field',
            'key_to'         => 'anfi_field_id',
            'cascade_save'   => false,
            'cascade_delete' => true,
        ),
    );

    protected $_form_id_for_delete = null;
    protected $_field_id_for_delete = null;

    public function _event_before_delete()
    {
        $this->_form_id_for_delete = $this->field_form_id;
        $this->_field_id_for_delete = $this->field_id;
    }

    public function _event_after_delete()
    {
        if (is_dir(APPPATH.'data'.DS.'files'.DS.'apps'.DS.'noviusos_form'.DS.$this->_form_id_for_delete)) {
            $files = \File::read_dir(APPPATH.'data'.DS.'files'.DS.'apps'.DS.'noviusos_form'.DS.$this->_form_id_for_delete, 1, array('^\d+_'.$this->_field_id_for_delete));
            foreach ($files as $dir => $file) {
                if (is_int($dir)) {
                    \File::delete(APPPATH.'data'.DS.'files'.DS.'apps/noviusos_form'.DS.$file);
                } else {
                    \File::delete_dir(APPPATH.'data'.DS.'files'.DS.'apps/noviusos_form'.DS.$dir);
                }
            }
        }
    }
}
