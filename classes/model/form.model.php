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

class Model_Form extends \Nos\Orm\Model
{
    protected static $_table_name = 'nos_form';
    protected static $_primary_key = array('form_id');

    protected static $_title_property = 'form_name';
    protected static $_properties = array(
        'form_id' => array(
            'default' => null,
            'data_type' => 'int unsigned',
            'null' => false,
        ),
        'form_context' => array(
            'default' => null,
            'data_type' => 'varchar',
            'null' => false,
        ),
        'form_name' => array(
            'default' => null,
            'data_type' => 'varchar',
            'null' => false,
        ),
        'form_virtual_name' => array(
            'default' => null,
            'data_type' => 'varchar',
            'null' => false,
        ),
        'form_manager_id' => array(
            'default' => null,
            'data_type' => 'int unsigned',
            'null' => true,
            'convert_empty_to_null' => true,
        ),
        'form_client_email_field_id' => array(
            'default' => null,
            'data_type' => 'int unsigned',
            'null' => true,
            'convert_empty_to_null' => true,
        ),
        'form_layout' => array(
            'default' => null,
            'data_type' => 'text',
            'null' => false,
        ),
        'form_captcha' => array(
            'default' => null,
            'data_type' => 'tinyint',
            'null' => false,
        ),
        'form_submit_label' => array(
            'default' => null,
            'data_type' => 'varchar',
            'null' => false,
        ),
        'form_submit_email' => array(
            'default' => null,
            'data_type' => 'text',
            'null' => true,
            'convert_empty_to_null' => true,
        ),
        'form_created_at' => array(
            'default' => null,
            'data_type' => 'datetime',
            'null' => false,
        ),
        'form_updated_at' => array(
            'default' => null,
            'data_type' => 'datetime',
            'null' => false,
        ),
        'form_created_by_id' => array(
            'default' => null,
            'data_type' => 'int unsigned',
            'null' => true,
            'convert_empty_to_null' => true,
        ),
        'form_updated_by_id' => array(
            'default' => null,
            'data_type' => 'int unsigned',
            'null' => true,
            'convert_empty_to_null' => true,
        ),
    );

    protected static $_observers = array(
        'Orm\\Observer_Self',
        'Orm\\Observer_CreatedAt' => array(
            'mysql_timestamp' => true,
            'property' => 'form_created_at',
        ),
        'Orm\\Observer_UpdatedAt' => array(
            'mysql_timestamp' => true,
            'property' => 'form_updated_at',
        ),
    );

    protected static $_behaviours = array(
        'Nos\Orm_Behaviour_Contextable' => array(
            'context_property'      => 'form_context',
        ),
        'Nos\Orm_Behaviour_Virtualname' => array(
            'virtual_name_property' => 'form_virtual_name',
        ),
        'Nos\Orm_Behaviour_Author' => array(
            'created_by_property' => 'form_created_by_id',
            'updated_by_property' => 'form_updated_by_id',
        ),
    );

    protected static $_has_many = array(
        'fields' => array(
            'key_from'       => 'form_id',
            'model_to'       => 'Nos\Form\\Model_Field',
            'key_to'         => 'field_form_id',
            'cascade_save'   => false,
            'cascade_delete' => true,
        ),
        'answers' => array(
            'key_from'       => 'form_id',
            'model_to'       => 'Nos\Form\\Model_Answer',
            'key_to'         => 'answer_form_id',
            'cascade_save'   => false,
            'cascade_delete' => true,
        ),
    );

    protected $_form_id_for_delete = null;

    public function _event_before_delete()
    {
        $this->_form_id_for_delete = $this->form_id;
    }

    public function _event_after_delete()
    {
        if (is_dir(APPPATH.'data'.DS.'files'.DS.'apps'.DS.'noviusos_form'.DS.$this->_form_id_for_delete)) {
            \Fuel\Core\File::delete_dir(APPPATH.'data'.DS.'files'.DS.'apps'.DS.'noviusos_form'.DS.$this->_form_id_for_delete);
        }

        \Nos\Attachment::deleteAlias('form/'.$this->_form_id_for_delete);
    }
}
