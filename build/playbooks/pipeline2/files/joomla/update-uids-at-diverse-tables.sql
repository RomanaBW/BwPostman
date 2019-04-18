UPDATE `jos_banners`
	SET `created_by`='200',
	    `modified_by`='200'
	WHERE `created_by`!='0'
	  AND `created_by` IS NOT NULL
	  AND `modified_by`!='0'
	  AND `modified_by` IS NOT NULL
;

UPDATE `jos_contact_details`
SET `created_by`='200',
	`modified_by`='200'
WHERE `created_by`!='0'
  AND `created_by` IS NOT NULL
  AND `modified_by`!='0'
  AND `modified_by` IS NOT NULL
;

UPDATE `jos_content`
SET `created_by`='200',
	`modified_by`='200'
WHERE `created_by`!='0'
  AND `created_by` IS NOT NULL
  AND `modified_by`!='0'
  AND `modified_by` IS NOT NULL
;

UPDATE `jos_finder_filters`
SET `created_by`='200',
	`modified_by`='200'
WHERE `created_by`!='0'
  AND `created_by` IS NOT NULL
  AND `modified_by`!='0'
  AND `modified_by` IS NOT NULL
;

UPDATE `jos_newsfeeds`
SET `created_by`='200',
	`modified_by`='200'
WHERE `created_by`!='0'
  AND `created_by` IS NOT NULL
  AND `modified_by`!='0'
  AND `modified_by` IS NOT NULL
;

UPDATE `jos_categories`
SET `created_user_id`='200',
	`modified_user_id`='200'
WHERE `created_user_id`!='0'
  AND `created_user_id` IS NOT NULL
  AND `modified_user_id`!='0'
  AND `modified_user_id` IS NOT NULL
;

UPDATE `jos_fields`
SET `created_user_id`='200',
	`modified_by`='200'
WHERE `created_user_id`!='0'
  AND `created_user_id` IS NOT NULL
  AND `modified_by`!='0'
  AND `modified_by` IS NOT NULL
;

UPDATE `jos_tags`
SET `created_user_id`='200',
	`modified_user_id`='200'
WHERE `created_user_id`!='0'
  AND `created_user_id` IS NOT NULL
  AND `modified_user_id`!='0'
  AND `modified_user_id` IS NOT NULL
;

UPDATE `jos_user_notes`
SET `created_user_id`='200',
	`modified_user_id`='200'
WHERE `created_user_id`!='0'
  AND `created_user_id` IS NOT NULL
  AND `modified_user_id`!='0'
  AND `modified_user_id` IS NOT NULL
;

UPDATE `jos_ucm_content`
SET `core_created_user_id`='200',
	`core_modified_user_id`='200'
WHERE `core_created_user_id`!='0'
  AND `core_created_user_id` IS NOT NULL
  AND `core_modified_user_id`!='0'
  AND `core_modified_user_id` IS NOT NULL
;

UPDATE `jos_ucm_history`
SET `editor_user_id`='200'
WHERE `editor_user_id`!='0'
  AND `editor_user_id` IS NOT NULL
;

