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

class Model_Answer_Field extends \Nos\Orm\Model
{
    protected static $_table_name = 'nos_form_answer_field';
    protected static $_primary_key = array('anfi_id');
    protected static $_prefix = 'anfi_';

    protected static $_properties = array(
        'anfi_id' => array(
            'default' => null,
            'data_type' => 'int unsigned',
            'null' => false,
        ),
        'anfi_answer_id' => array(
            'default' => null,
            'data_type' => 'int unsigned',
            'null' => false,
        ),
        'anfi_field_id' => array(
            'default' => null,
            'data_type' => 'int unsigned',
            'null' => false,
        ),
        'anfi_field_driver' => array(
            'default' => null,
            'data_type' => 'varchar',
            'null' => false,
        ),
        'anfi_value' => array(
            'default' => null,
            'data_type' => 'serialize',
            'null' => false,
        ),
    );

    protected static $_observers = array(
        'Nos\Form\Observer_Typing',
    );

    protected static $_has_one = array();
    protected static $_has_many  = array();
    protected static $_many_many = array();
    protected static $_twinnable_has_one = array();
    protected static $_twinnable_has_many = array();
    protected static $_twinnable_belongs_to = array();
    protected static $_twinnable_many_many = array();

    protected static $_belongs_to = array(
        'answer' => array(
            'key_from'       => 'anfi_answer_id',
            'model_to'       => 'Nos\Form\Model_Answer',
            'key_to'         => 'answer_id',
            'cascade_save'   => false,
            'cascade_delete' => false,
        ),
        'field' => array(
            'key_from'       => 'anfi_field_id',
            'model_to'       => 'Nos\Form\Model_Field',
            'key_to'         => 'field_id',
            'cascade_save'   => false,
            'cascade_delete' => false,
        ),
    );

    /**
     * Gets the attachments of the specified field
     *
     * @return \Nos\Attachment
     */
    public function getAttachment()
    {
        return \Nos\Attachment::forge($this->anfi_answer_id.'_'.$this->anfi_field_id, array(
            'dir' => 'apps'.DS.'noviusos_form'.DS.$this->answer->answer_form_id,
            'alias' => 'form/'.$this->answer->answer_form_id,
            'check' => array(__CLASS__, 'check_attachment'),
        ));
    }
}
