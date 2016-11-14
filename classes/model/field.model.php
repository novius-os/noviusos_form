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
//        'field_type' => array(
//            'default' => '',
//            'data_type' => 'varchar',
//            'null' => false,
//        ),
        'field_driver' => array(
            'default' => null,
            'data_type' => 'varchar',
            'null' => true,
        ),
        'field_label' => array(
            'default' => '',
            'data_type' => 'varchar',
            'null' => false,
        ),
        'field_message' => array(
            'default' => '',
            'data_type' => 'text',
            'null' => false,
        ),
        'field_virtual_name' => array(
            'default' => '',
            'data_type' => 'varchar',
            'null' => false,
        ),
        'field_choices' => array(
            'default' => '',
            'data_type' => 'serialize',
            'null' => false,
        ),
        'field_created_at' => array(
            'default' => '',
            'data_type' => 'datetime',
            'null' => false,
        ),
        'field_mandatory' => array(
            'default' => 0,
            'data_type' => 'tinyint',
            'null' => false,
        ),
        'field_default_value' => array(
            'default' => '',
            'data_type' => 'varchar',
            'null' => false,
        ),
        'field_details' => array(
            'default' => '',
            'data_type' => 'text',
            'null' => false,
        ),
        'field_style' => array(
            'default' => '',
            'data_type' => 'enum',
            'options' => array('', 'p','h1','h2','h3'),
            'null' => false,
        ),
        'field_width' => array(
            'default' => 0,
            'data_type' => 'tinyint',
            'null' => false,
        ),
        'field_height' => array(
            'default' => 0,
            'data_type' => 'tinyint',
            'null' => false,
        ),
        'field_limited_to' => array(
            'default' => 0,
            'data_type' => 'int',
            'null' => false,
        ),
        'field_origin' => array(
            'default' => '',
            'data_type' => 'varchar',
            'null' => false,
        ),
        'field_origin_var' => array(
            'default' => '',
            'data_type' => 'varchar',
            'null' => false,
        ),
        'field_technical_id' => array(
            'default' => '',
            'data_type' => 'varchar',
            'null' => false,
        ),
        'field_technical_css' => array(
            'default' => '',
            'data_type' => 'varchar',
            'null' => false,
        ),
        'field_conditional' => array(
            'default' => 0,
            'data_type' => 'tinyint',
            'null' => false,
        ),
        'field_conditional_form' => array(
            'default' => '',
            'data_type' => 'varchar',
            'null' => false,
        ),
        'field_conditional_value' => array(
            'default' => '',
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
        'Orm\\Observer_Typing',
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

    /**
     * Drivers instances
     *
     * @var array
     */
    protected $driversInstance = array();

    /**
     * The form service
     *
     * @var Service_Field|null
     */
    protected $service = null;

    /**
     * Gets the input name
     *
     * @return \Nos\Orm\Model|null|string
     */
    public function getInputName()
    {
        if (!empty($this->virtual_name)) {
            return $this->virtual_name;
        } else if (!empty($this->id)) {
            return 'field_' . $this->id;
        } else {
            return uniqid('field_');
        }
    }

    /**
     * Gets the driver
     *
     * @param null|array $options
     * @param bool $reload
     * @return Driver_Field_Abstract|null
     */
    public function getDriver($options = null, $reload = false)
    {
        $driverClass = $this->getDriverClass();

        // Gets the driver class
        if (empty($driverClass) || !class_exists($driverClass)) {
            return null;
        }

        // Forges if not already forged or if reload
        if (!isset($this->driversInstance[$driverClass]) || $reload) {
            $this->driversInstance[$driverClass] = $driverClass::forge($this, $options);
        }
        // Otherwise reset options if specified
        elseif (!is_null($options)) {
            $this->driversInstance[$driverClass]->setOptions($options);
        }

        return $this->driversInstance[$driverClass];
    }

    /**
     * Gets the driver class name
     *
     * @return mixed|\Nos\Orm\Model|null
     */
    public function getDriverClass()
    {
        return $this->driver;
    }

    /**
     * Gets the field service
     *
     * @param bool $reload
     * @return Service_Field|null
     */
    public function getService($reload = false)
    {
        if (is_null($this->service) || $reload) {
            $this->service = Service_Field::forge($this);
        }
        return $this->service;
    }

    /**
     * Triggered before delete
     */
    public function _event_before_delete()
    {
        // Store field and form IDs
        $this->_form_id_for_delete = $this->form_id;
        $this->_field_id_for_delete = $this->id;
    }

    /**
     * Triggered after delete
     */
    public function _event_after_delete()
    {
        // Deletes related files
        if (is_dir(APPPATH.'data'.DS.'files'.DS.'apps'.DS.'noviusos_form'.DS.$this->_form_id_for_delete)) {
            $files = \File::read_dir(APPPATH.'data'.DS.'files'.DS.'apps'.DS.'noviusos_form'.DS.$this->_form_id_for_delete, 1, array('^\d+_'.$this->_field_id_for_delete));
            foreach ($files as $dir => $file) {
                if (is_int($dir)) {
                    \File::delete(APPPATH.'data'.DS.'files'.DS.'apps/noviusos_form'.DS.$this->_form_id_for_delete.DS.$file);
                } else {
                    \File::delete_dir(APPPATH.'data'.DS.'files'.DS.'apps/noviusos_form'.DS.$this->_form_id_for_delete.DS.$dir);
                }
            }
        }
    }
}
