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
    protected static $_prefix = 'field_';

    protected static $_properties = array(
        'field_id' => array(
            'data_type' => 'int unsigned',
            'default' => null,
            'null' => false,
        ),
        'field_form_id' => array(
            'data_type' => 'int unsigned',
            'default' => null,
            'null' => false,
        ),
        'field_driver' => array(
            'data_type' => 'varchar',
            'default' => null,
            'null' => true,
        ),
        'field_label' => array(
            'data_type' => 'varchar',
            'default' => null,
            'null' => true,
        ),
        'field_message' => array(
            'data_type' => 'text',
            'default' => null,
            'null' => true,
        ),
        'field_virtual_name' => array(
            'data_type' => 'varchar',
            'default' => null,
            'null' => true,
        ),
        'field_choices' => array(
            'data_type' => 'serialize',
            'default' => null,
            'null' => true,
        ),
        'field_created_at' => array(
            'data_type' => 'datetime',
            'default' => '',
            'null' => false,
        ),
        'field_mandatory' => array(
            'data_type' => 'tinyint',
            'default' => 0,
            'null' => false,
        ),
        'field_default_value' => array(
            'data_type' => 'varchar',
            'default' => null,
            'null' => true,
        ),
        'field_details' => array(
            'data_type' => 'text',
            'default' => null,
            'null' => true,
        ),
        'field_style' => array(
            'data_type' => 'text',
            'default' => null,
            'null' => true,
        ),
        'field_width' => array(
            'data_type' => 'tinyint',
            'default' => null,
            'null' => true,
        ),
        'field_height' => array(
            'data_type' => 'tinyint',
            'default' => null,
            'null' => true,
        ),
        'field_limited_to' => array(
            'data_type' => 'int',
            'default' => null,
            'null' => true,
        ),
        'field_origin' => array(
            'data_type' => 'varchar',
            'default' => null,
            'null' => true,
        ),
        'field_origin_var' => array(
            'data_type' => 'varchar',
            'default' => null,
            'null' => true,
        ),
        'field_technical_id' => array(
            'data_type' => 'varchar',
            'default' => null,
            'null' => true,
        ),
        'field_technical_css' => array(
            'data_type' => 'varchar',
            'default' => null,
            'null' => true,
        ),
        'field_conditional' => array(
            'data_type' => 'tinyint',
            'default' => null,
            'null' => true,
        ),
        'field_conditional_form' => array(
            'data_type' => 'varchar',
            'default' => null,
            'null' => true,
        ),
        'field_conditional_value' => array(
            'data_type' => 'varchar',
            'default' => null,
            'null' => true,
        ),
    );

    protected static $_has_one = array();
    protected static $_many_many = array();
    protected static $_twinnable_has_one = array();
    protected static $_twinnable_has_many = array();
    protected static $_twinnable_belongs_to = array();
    protected static $_twinnable_many_many = array();

    protected static $_observers = array(
        'Orm\Observer_Self',
        'Nos\Form\Observer_Typing',
        'Orm\Observer_CreatedAt' => array(
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
            'model_to'       => 'Nos\Form\Model_Answer_Field',
            'key_to'         => 'anfi_field_id',
            'cascade_save'   => false,
            'cascade_delete' => true,
        ),
        'attributes' => array(
            'key_from' => 'field_id',
            'model_to' => Model_Field_Attribute::class,
            'key_to' => 'fiat_field_id',
            'cascade_save' => true,
            'cascade_delete' => true,
        ),
    );

    protected static $_eav = array(
        'attributes' => array(
            'attribute' => 'fiat_key',
            'value' => 'fiat_value',
        )
    );

    protected $_form_id_for_delete = null;
    protected $_field_id_for_delete = null;

    /**
     * The field drivers instances (per driver class)
     *
     * @var array
     */
    protected $driversInstance = array();

    /**
     * The form service instance
     *
     * @var Service_Field|null
     */
    protected $serviceInstance = null;

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
     * @throws Exception_Driver
     */
    public function getDriver($options = null, $reload = false)
    {
        // Gets the driver class
        $driverClass = $this->getDriverClass();
        if (empty($driverClass)) {
            return null;
        }

        // Checks if the class exists
        if (!class_exists($driverClass)) {
            throw new Exception_Driver(str_replace('{{class}}', $driverClass, __('Driver `{{class}}` not found.')));
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
     * Checks if field has a driver
     *
     * @return bool
     */
    public function hasDriver()
    {
        // Gets the driver class
        return !empty($this->getDriverClass()) && class_exists($this->getDriverClass());
    }

    /**
     * Gets the driver class name
     *
     * @return mixed|\Nos\Orm\Model|null
     */
    public function getDriverClass()
    {
        return $this->field_driver;
    }

    /**
     * Gets the field service
     *
     * @param bool $reload
     * @return Service_Field|null
     */
    public function getService($reload = false)
    {
        if (is_null($this->serviceInstance) || $reload) {
            $this->serviceInstance = Service_Field::forge($this);
        }
        return $this->serviceInstance;
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
