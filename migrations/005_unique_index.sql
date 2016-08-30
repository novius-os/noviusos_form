/**
 * NOVIUS OS - Web OS for digital communication
 *
 * @copyright  2011 Novius
 * @license    GNU Affero General Public License v3 or (at your option) any later version
 *             http://www.gnu.org/licenses/agpl-3.0.html
 * @link http://www.novius-os.org
 */

ALTER TABLE `nos_form_answer_field`
DROP INDEX `anfi_answer_id` ,
ADD UNIQUE `anfi_answer_id`(`anfi_answer_id`, `anfi_field_id`);
