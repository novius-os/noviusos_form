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

class Model_Answer extends \Nos\Orm\Model
{
    protected static $_table_name = 'nos_form_answer';
    protected static $_primary_key = array('answer_id');

    protected static $_properties = array(
        'answer_id' => array(
            'default' => null,
            'data_type' => 'int unsigned',
            'null' => false,
        ),
        'answer_form_id' => array(
            'default' => null,
            'data_type' => 'int unsigned',
            'null' => false,
        ),
        'answer_ip' => array(
            'default' => null,
            'data_type' => 'varchar',
            'null' => false,
        ),
        'answer_created_at' => array(
            'default' => null,
            'data_type' => 'datetime',
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
            'property' => 'answer_created_at',
        ),
    );

    protected static $_has_many = array(
        'fields' => array(
            'key_from'       => 'answer_id',
            'model_to'       => 'Nos\Form\\Model_Answer_Field',
            'key_to'         => 'anfi_answer_id',
            'cascade_save'   => false,
            'cascade_delete' => true,
        ),
    );

    protected static $_belongs_to = array(
        'form' => array(
            'key_from'       => 'answer_form_id',
            'model_to'       => 'Nos\Form\Model_Form',
            'key_to'         => 'form_id',
            'cascade_save'   => false,
            'cascade_delete' => false,
        ),
    );

    protected $_form_id_for_delete = null;
    protected $_answer_id_for_delete = null;

    public function getAttachment($field)
    {
        return \Nos\Attachment::forge($this->answer_id.'_'.$field->field_id, array(
            'dir' => 'apps'.DS.'noviusos_form'.DS.$this->form->form_id,
            'alias' => 'form/'.$this->form->form_id,
            'check' => array(__CLASS__, 'check_attachment'),
        ));
    }

    public static function check_attachment()
    {
        return \Nos\Auth::check();
    }

    public function _event_before_delete()
    {
        $this->_form_id_for_delete = $this->answer_form_id;
        $this->_answer_id_for_delete = $this->answer_id;
    }

    public function _event_after_delete()
    {
        if (is_dir(APPPATH.'data'.DS.'files'.DS.'apps'.DS.'noviusos_form'.DS.$this->_form_id_for_delete)) {
            $files = \File::read_dir(APPPATH.'data'.DS.'files'.DS.'apps'.DS.'noviusos_form'.DS.$this->_form_id_for_delete, 1, array('^'.$this->_answer_id_for_delete.'_'));
            foreach ($files as $dir => $file) {
                if (is_int($dir)) {
                    \File::delete(APPPATH.'data'.DS.'files'.DS.'apps'.DS.'noviusos_form'.DS.$this->_form_id_for_delete.DS .$file);
                } else {
                    \File::delete_dir(APPPATH.'data'.DS.'files'.DS.'apps'.DS.'noviusos_form'.DS.$this->_form_id_for_delete.DS.$dir);
                }
            }
        }
    }
}
