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

    protected static $_belongs_to = array(
        'answer' => array(
            'key_from'       => 'anfi_answer_id',
            'model_to'       => 'Nos\Form\Model_Answer',
            'key_to'         => 'answer_id',
            'cascade_save'   => false,
            'cascade_delete' => false,
        ),
    );
}
