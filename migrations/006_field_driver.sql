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

/* Converts field_type to field_driver for the existing fields and answer fields */
UPDATE `nos_form_field` SET `field_driver` = 'Nos\\Form\\Driver_Field_Input_Text' WHERE `field_type` = 'text';
UPDATE `nos_form_field` SET `field_driver` = 'Nos\\Form\\Driver_Field_Textarea' WHERE `field_type` = 'textarea';
UPDATE `nos_form_field` SET `field_driver` = 'Nos\\Form\\Driver_Field_Checkbox' WHERE `field_type` = 'checkbox';
UPDATE `nos_form_field` SET `field_driver` = 'Nos\\Form\\Driver_Field_Select' WHERE `field_type` = 'select';
UPDATE `nos_form_field` SET `field_driver` = 'Nos\\Form\\Driver_Field_Radio' WHERE `field_type` = 'radio';
UPDATE `nos_form_field` SET `field_driver` = 'Nos\\Form\\Driver_Field_Input_File' WHERE `field_type` = 'file';
UPDATE `nos_form_field` SET `field_driver` = 'Nos\\Form\\Driver_Field_Input_Email' WHERE `field_type` = 'email';
UPDATE `nos_form_field` SET `field_driver` = 'Nos\\Form\\Driver_Field_Input_Number' WHERE `field_type` = 'number';
UPDATE `nos_form_field` SET `field_driver` = 'Nos\\Form\\Driver_Field_Input_Date' WHERE `field_type` = 'date';
UPDATE `nos_form_field` SET `field_driver` = 'Nos\\Form\\Driver_Field_Message' WHERE `field_type` = 'message';
UPDATE `nos_form_field` SET `field_driver` = 'Nos\\Form\\Driver_Field_Hidden' WHERE `field_type` = 'hidden';
UPDATE `nos_form_field` SET `field_driver` = 'Nos\\Form\\Driver_Field_Separator' WHERE `field_type` = 'separator';
UPDATE `nos_form_field` SET `field_driver` = 'Nos\\Form\\Driver_Field_Variable' WHERE `field_type` = 'variable';
UPDATE `nos_form_field` SET `field_driver` = 'Nos\\Form\\Driver_Field_PageBreak' WHERE `field_type` = 'page_break';
UPDATE `nos_form_answer_field` SET `anfi_field_driver` = 'Nos\\Form\\Driver_Field_Input_Text' WHERE `anfi_field_type` = 'text';
UPDATE `nos_form_answer_field` SET `anfi_field_driver` = 'Nos\\Form\\Driver_Field_Textarea' WHERE `anfi_field_type` = 'textarea';
UPDATE `nos_form_answer_field` SET `anfi_field_driver` = 'Nos\\Form\\Driver_Field_Checkbox' WHERE `anfi_field_type` = 'checkbox';
UPDATE `nos_form_answer_field` SET `anfi_field_driver` = 'Nos\\Form\\Driver_Field_Select' WHERE `anfi_field_type` = 'select';
UPDATE `nos_form_answer_field` SET `anfi_field_driver` = 'Nos\\Form\\Driver_Field_Radio' WHERE `anfi_field_type` = 'radio';
UPDATE `nos_form_answer_field` SET `anfi_field_driver` = 'Nos\\Form\\Driver_Field_Input_File' WHERE `anfi_field_type` = 'file';
UPDATE `nos_form_answer_field` SET `anfi_field_driver` = 'Nos\\Form\\Driver_Field_Input_Email' WHERE `anfi_field_type` = 'email';
UPDATE `nos_form_answer_field` SET `anfi_field_driver` = 'Nos\\Form\\Driver_Field_Input_Number' WHERE `anfi_field_type` = 'number';
UPDATE `nos_form_answer_field` SET `anfi_field_driver` = 'Nos\\Form\\Driver_Field_Input_Date' WHERE `anfi_field_type` = 'date';
UPDATE `nos_form_answer_field` SET `anfi_field_driver` = 'Nos\\Form\\Driver_Field_Message' WHERE `anfi_field_type` = 'message';
UPDATE `nos_form_answer_field` SET `anfi_field_driver` = 'Nos\\Form\\Driver_Field_Hidden' WHERE `anfi_field_type` = 'hidden';
UPDATE `nos_form_answer_field` SET `anfi_field_driver` = 'Nos\\Form\\Driver_Field_Separator' WHERE `anfi_field_type` = 'separator';
UPDATE `nos_form_answer_field` SET `anfi_field_driver` = 'Nos\\Form\\Driver_Field_Variable' WHERE `anfi_field_type` = 'variable';

/* Deletes the page_break field (we don't need it anymore in the fields) */
DELETE FROM `nos_form_field` WHERE `field_type` = 'page_break';
DELETE FROM `nos_form_answer_field` WHERE `anfi_field_type` = 'page_break';

/* Drops the field_type column */
ALTER TABLE `nos_form_field` DROP COLUMN `field_type`;
ALTER TABLE `nos_form_answer_field` DROP COLUMN `anfi_field_type`;

/* Sets null fields */
ALTER TABLE `nos_form_field`
  CHANGE `field_label` `field_label` VARCHAR(255) NULL DEFAULT NULL,
  CHANGE `field_message` `field_message` TEXT NULL DEFAULT NULL,
  CHANGE `field_virtual_name` `field_virtual_name` VARCHAR(30) NULL DEFAULT NULL,
  CHANGE `field_choices` `field_choices` TEXT NULL DEFAULT NULL,
  CHANGE `field_default_value` `field_default_value` VARCHAR(255) NULL DEFAULT NULL,
  CHANGE `field_details` `field_details` TEXT NULL DEFAULT NULL,
  CHANGE `field_style` `field_style` VARCHAR(50) NULL DEFAULT NULL,
  CHANGE `field_width` `field_width` TINYINT(4) NULL DEFAULT NULL,
  CHANGE `field_height` `field_height` TINYINT(4) NULL DEFAULT NULL,
  CHANGE `field_limited_to` `field_limited_to` INT(11) NULL DEFAULT NULL,
  CHANGE `field_origin` `field_origin` VARCHAR(30) NULL DEFAULT NULL,
  CHANGE `field_origin_var` `field_origin_var` VARCHAR(30) NULL DEFAULT NULL,
  CHANGE `field_technical_id` `field_technical_id` VARCHAR(30) NULL DEFAULT NULL,
  CHANGE `field_technical_css` `field_technical_css` VARCHAR(100) NULL DEFAULT NULL,
  CHANGE `field_conditional` `field_conditional` TINYINT(1) NULL DEFAULT NULL,
  CHANGE `field_conditional_value` `field_conditional_value` VARCHAR(255) NULL DEFAULT NULL,
  CHANGE `field_conditional_form` `field_conditional_form` VARCHAR(255) NULL DEFAULT NULL;
