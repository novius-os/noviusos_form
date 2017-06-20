<?php
/**
 * Novius Blocks
 *
 * @copyright  2014 Novius
 * @license    GNU Affero General Public License v3 or (at your option) any later version
 *             http://www.gnu.org/licenses/agpl-3.0.html
 * @link       http://www.novius-os.org
 */

namespace Nos\Form\Migrations;

use Fuel\Core\DB;
use Nos\Form\Model_Answer_Field;

class Field_Eav extends \Nos\Migration
{
    public function up()
    {
        parent::up();

        \Autoloader::add_class('Nos\\Form\\Model_Answer_Field', __DIR__ . '/../classes/model/answer/field.model.php');

        $query         = DB::select_array(array('anfi_id', 'anfi_value'))
            ->from(Model_Answer_Field::table())
            ->where('anfi_field_driver', 'NOT LIKE', '%Driver_Field_Checkbox%');
        $results       = $query->execute()->as_array();
        $answer_fields = \Arr::pluck($results, 'anfi_value', 'anfi_id');
        foreach ($answer_fields as $anfi_id => $value) {
            $serialised_value = serialize($value);
            DB::update(Model_Answer_Field::table())
                ->value('anfi_value', $serialised_value)
                ->where('anfi_id', $anfi_id)->execute();
        }

        // Checkbox serialized
        $query         = DB::select_array(array('anfi_id', 'anfi_value'))
            ->from(Model_Answer_Field::table())
            ->where('anfi_field_driver', 'LIKE', '%Driver_Field_Checkbox%');
        $results       = $query->execute()->as_array();
        $answer_fields = \Arr::pluck($results, 'anfi_value', 'anfi_id');
        foreach ($answer_fields as $anfi_id => $value) {
            $arr_value        = explode("\n", $value);
            $serialised_value = serialize($arr_value);
            DB::update(Model_Answer_Field::table())
                ->value('anfi_value', $serialised_value)
                ->where('anfi_id', $anfi_id)->execute();
        }

    }

}
