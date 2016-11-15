/**
 * Novius Blocks
 *
 * @copyright  2014 Novius
 * @license    GNU Affero General Public License v3 or (at your option) any later version
 *             http://www.gnu.org/licenses/agpl-3.0.html
 * @link http://www.novius-os.org
 */

CREATE TABLE `nos_form_field_attributes` (
  `fiat_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `fiat_field_id` int(11) unsigned NOT NULL,
  `fiat_key` varchar(50) NOT NULL,
  `fiat_value` varchar(512) NOT NULL,
  PRIMARY KEY (`fiat_id`),
  KEY `fiat_key` (`fiat_key`),
  KEY `fiat_field_id` (`fiat_field_id`)
);
