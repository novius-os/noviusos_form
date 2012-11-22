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

    protected static $_observers = array(
        'Orm\\Observer_CreatedAt' => array(
            'events' => array('before_insert'),
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
            'cascade_delete' => false,
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

    public function getAttachment($field) {
        return \Nos\Attachment::forge($this->form->form_id.'_'.$this->answer_id.'_'.$field->field_id, array(
            'dir' => 'apps/noviusos_form',
            'alias' => 'form',
            'check' => array(__CLASS__, 'check_attachment'),
        ));
    }

    public static function check_attachment() {
        return \Nos\Auth::check();
    }
}
