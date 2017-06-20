/**
 * NOVIUS OS - Web OS for digital communication
 *
 * @copyright  2017 Novius
 * @license    GNU Affero General Public License v3 or (at your option) any later version
 *             http://www.gnu.org/licenses/agpl-3.0.html
 * @link http://www.novius-os.org
 */

ALTER TABLE `nos_form_field`
  ADD `field_conditional` tinyint(1) NOT NULL,
  ADD `field_conditional_value` varchar(255) NOT NULL,
  ADD `field_conditional_form` varchar(255) NOT NULL;