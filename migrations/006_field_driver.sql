/**
 * NOVIUS OS - Web OS for digital communication
 *
 * @copyright  2011 Novius
 * @license    GNU Affero General Public License v3 or (at your option) any later version
 *             http://www.gnu.org/licenses/agpl-3.0.html
 * @link http://www.novius-os.org
 */

/* Creates the driver columns */
ALTER TABLE `nos_form_field` ADD `field_driver` varchar(500) NULL DEFAULT NULL AFTER `field_type`;
ALTER TABLE `nos_form_answer_field` ADD `anfi_field_driver` varchar(500) NULL DEFAULT NULL AFTER `anfi_field_type`;

/* Drops the field_type column */
ALTER TABLE `nos_form_field` DROP COLUMN `field_type`;
