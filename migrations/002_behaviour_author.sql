ALTER TABLE `nos_form`
  ADD `form_created_by_id` INT UNSIGNED NULL AFTER `form_updated_at` ,
  ADD `form_updated_by_id` INT UNSIGNED NULL AFTER `form_created_by_id`;
