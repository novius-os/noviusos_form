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

    protected static $_behaviours = array(
        'Nos\Orm_Behaviour_Virtualname' => array(
            'events' => array('before_save', 'after_save'),
            'virtual_name_property' => 'form_virtual_name',
        ),
    );

    protected static $_belongs_to = array(
        'folder' => array(
            'key_from'       => 'field_form_id',
            'model_to'       => 'Nos\Form\Model_Form',
            'key_to'         => 'form_id',
            'cascade_save'   => false,
            'cascade_delete' => false,
        ),
    );
}
