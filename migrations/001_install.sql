/**
 * NOVIUS OS - Web OS for digital communication
 *
 * @copyright  2011 Novius
 * @license    GNU Affero General Public License v3 or (at your option) any later version
 *             http://www.gnu.org/licenses/agpl-3.0.html
 * @link http://www.novius-os.org
 */

CREATE TABLE IF NOT EXISTS `nos_form` (
  `form_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `form_context` varchar(25) NOT NULL,
  `form_name` varchar(255) NOT NULL,
  `form_virtual_name` varchar(30) NOT NULL,
  `form_manager_id` int(10) unsigned DEFAULT NULL,
  `form_client_email_field_id` int(10) unsigned DEFAULT NULL,
  `form_layout` text NOT NULL,
  `form_captcha` tinyint(1) NOT NULL,
  `form_submit_label` varchar(255) NOT NULL,
  `form_submit_email` text,
  `form_created_at` datetime NOT NULL,
  `form_updated_at` datetime NOT NULL,
  PRIMARY KEY (`form_id`),
  KEY `form_context` (`form_context`)
) DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `nos_form_answer` (
  `answer_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `answer_form_id` int(10) unsigned NOT NULL,
  `answer_ip` varchar(40) NOT NULL,
  `answer_created_at` datetime NOT NULL,
  PRIMARY KEY (`answer_id`),
  KEY `response_form_id` (`answer_form_id`)
) DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `nos_form_answer_field` (
  `anfi_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `anfi_answer_id` int(10) unsigned NOT NULL,
  `anfi_field_id` int(10) unsigned NOT NULL,
  `anfi_field_type` varchar(100) NOT NULL,
  `anfi_value` text NOT NULL,
  PRIMARY KEY (`anfi_id`),
  KEY `anfi_answer_id` (`anfi_answer_id`,`anfi_field_id`)
) DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `nos_form_field` (
  `field_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `field_form_id` int(10) unsigned NOT NULL,
  `field_type` varchar(100) NOT NULL,
  `field_label` varchar(255) NOT NULL,
  `field_message` text NOT NULL,
  `field_virtual_name` varchar(30) NOT NULL,
  `field_choices` text NOT NULL,
  `field_created_at` datetime NOT NULL,
  `field_mandatory` tinyint(1) NOT NULL,
  `field_default_value` varchar(255) NOT NULL,
  `field_details` text NOT NULL,
  `field_style` enum('p','h1','h2','h3') NOT NULL,
  `field_width` tinyint(4) NOT NULL,
  `field_height` tinyint(4) NOT NULL,
  `field_limited_to` int(11) NOT NULL,
  `field_origin` varchar(30) NOT NULL,
  `field_origin_var` varchar(30) NOT NULL,
  `field_technical_id` varchar(30) NOT NULL,
  `field_technical_css` varchar(100) NOT NULL,
  PRIMARY KEY (`field_id`),
  KEY `field_form_id` (`field_form_id`)
) DEFAULT CHARSET=utf8;