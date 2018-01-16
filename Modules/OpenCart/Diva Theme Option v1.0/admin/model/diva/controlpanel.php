<?php
class ModelDivaControlpanel extends Model
{
    public function setupData() {
        $this->setupBlogData();
        $this->setupStaticBlockData();
        $this->setupBannerData();
        $this->setupTestimonialData();
        $this->setupMenuData();
        $this->setupFeaturedCategories();
        $this->setupRotateImage();
        $this->setupColorSwatches();
    }

    public function setupBlogData() {
        $this->db->query("
			CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "dvpost` (
			    `post_id` INT(11) NOT NULL AUTO_INCREMENT,
	            `status` TINYINT(1) NOT NULL DEFAULT '0',
	            `sort_order` INT(11) NOT NULL DEFAULT '0',
	            `image` varchar(255) DEFAULT NULL,
	            `author` varchar(100) DEFAULT NULL,
	            `date_added` DATETIME NOT NULL,
	            `date_modified` DATETIME NOT NULL,
	        PRIMARY KEY (`post_id`)
		) DEFAULT COLLATE=utf8_general_ci;");

        $this->db->query("
			CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "dvpost_description` (
			    `post_id` INT(11) NOT NULL,
                `language_id` INT(11) NOT NULL,
                `name` VARCHAR(255) NOT NULL,
                `description` TEXT NOT NULL,
                `intro_text` TEXT NOT NULL,
                `meta_title` VARCHAR(255) NOT NULL,
                `meta_description` VARCHAR(255) NOT NULL,
                `meta_keyword` VARCHAR(255) NOT NULL,
            PRIMARY KEY (`post_id`, `language_id`),
	        INDEX `name` (`name`)
		) DEFAULT COLLATE=utf8_general_ci;");

        $this->db->query("
			CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "dvpost_to_store` (
			    `post_id` INT(11) NOT NULL,
                `store_id` INT(11) NOT NULL
		) DEFAULT COLLATE=utf8_general_ci;");
    }

    public function setupStaticBlockData() {
        $this->db->query("
			CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "dvstaticblock` (
			  `staticblock_id` int(11) NOT NULL AUTO_INCREMENT,
			  `status` tinyint(1) NOT NULL,
			  `identify` varchar(255) DEFAULT NULL,
			  `link` varchar(255) DEFAULT NULL,
			  `type` tinyint(1) DEFAULT NULL,
			  `banner_store` varchar(255) DEFAULT '0',
			  `sort_order` tinyint(1) DEFAULT NULL,
			  PRIMARY KEY (`staticblock_id`)
        ) DEFAULT COLLATE=utf8_general_ci;");

        $this->db->query("
			CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "dvstaticblock_description` (
			  `staticblock_description_id` int(11) NOT NULL AUTO_INCREMENT,
			  `staticblock_id` int(11) NOT NULL,
			  `language_id` int(11) NOT NULL,
			  `title` varchar(64) NOT NULL,
			  `sub_title` varchar(64) DEFAULT NULL,
			  `description` text,
			  PRIMARY KEY (`staticblock_description_id`,`language_id`)
		) DEFAULT COLLATE=utf8_general_ci;");

        $this->db->query("
			CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "dvstaticblock_to_store` (
			  `staticblock_id` int(11) DEFAULT NULL,
			  `store_id` tinyint(4) DEFAULT NULL
		) DEFAULT COLLATE=utf8_general_ci;");
    }

    public function setupBannerData() {
        $this->db->query("
			CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "dvbanner` (
                `dvbanner_id` int(11) NOT NULL AUTO_INCREMENT,
                `name` varchar(64) NOT NULL,
                `status` tinyint(1) NOT NULL,
                `auto` tinyint(1) DEFAULT NULL,
                `delay` int(11) DEFAULT NULL,
                `hover` tinyint(1) DEFAULT NULL,
                `nextback` tinyint(1) DEFAULT NULL,
                `contrl` tinyint(1) DEFAULT NULL,
                `effect` varchar(64) NOT NULL,
                PRIMARY KEY (`dvbanner_id`)
		) DEFAULT COLLATE=utf8_general_ci;");

        $this->db->query("
			CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "dvbanner_image` (
                `dvbanner_image_id` int(11) NOT NULL AUTO_INCREMENT,
                `dvbanner_id` int(11) NOT NULL,
                `link` varchar(255) NOT NULL,
                `type` int(11) NOT NULL,
                `banner_store` varchar(110) DEFAULT '0',
                `image` varchar(255) NOT NULL,
                `secondary_image` varchar(255) DEFAULT NULL,
                PRIMARY KEY (`dvbanner_image_id`)
		) DEFAULT COLLATE=utf8_general_ci;");

        $this->db->query("
			CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "dvbanner_image_description` (
                `dvbanner_image_id` int(11) NOT NULL,
                `dvbanner_id` int(11) NOT NULL,
                `language_id` int(11) NOT NULL,
                `title` varchar(64) NOT NULL,
                `sub_title` varchar(64) DEFAULT NULL,
                `description` text,
                PRIMARY KEY (`dvbanner_image_id`,`language_id`)
		) DEFAULT COLLATE=utf8_general_ci;");
    }

    public function setupTestimonialData() {
        $this->db->query("
            CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "dvtestimonial` (
                `dvtestimonial_id` int(11) NOT NULL AUTO_INCREMENT,
                `status` int(1) NOT NULL DEFAULT '0',
                `sort_order` int(11) NOT NULL DEFAULT '0',
            PRIMARY KEY (`dvtestimonial_id`)
        ) DEFAULT COLLATE=utf8_general_ci;");

        $this->db->query("
            CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "dvtestimonial_description`(
                `dvtestimonial_id` int(11) unsigned NOT NULL,
                `language_id` int(11) NOT NULL,
                `image` varchar(255) NOT NULL,
                `customer_name` varchar(255) NOT NULL,
                `content` text,
                PRIMARY KEY (`dvtestimonial_id`,`language_id`)
            ) DEFAULT COLLATE=utf8_general_ci;");
    }

    public function setupMenuData() {
        $this->db->query("
			CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "dvmegamenu` (
			    `menu_id` INT(11) NOT NULL AUTO_INCREMENT,
	            `status` TINYINT(1) NOT NULL DEFAULT '0',
	            `name` VARCHAR(255) NOT NULL,
	            `menu_type` VARCHAR(255) NOT NULL,
	        PRIMARY KEY (`menu_id`)
		) DEFAULT COLLATE=utf8_general_ci;");

        $this->db->query("
			CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "dvmegamenu_top_item` (
			    `menu_item_id` INT(11) NOT NULL AUTO_INCREMENT,
			    `menu_id` INT(11) NOT NULL,
	            `status` TINYINT(1) NOT NULL DEFAULT '0',
	            `has_title` TINYINT(1) NOT NULL DEFAULT '0',
	            `has_link` TINYINT(1) NOT NULL DEFAULT '0',
	            `has_child` TINYINT(1) NOT NULL DEFAULT '0',
                `category_id` INT(11),
                `position` INT(11) NOT NULL DEFAULT '0',
	            `name` VARCHAR(255) NOT NULL,
	            `link` VARCHAR(255),
	            `icon` VARCHAR(255),
	            `item_align` VARCHAR(255) NOT NULL,
	            `sub_menu_type` VARCHAR(255) NOT NULL,
	            `sub_menu_content_type` VARCHAR(255) NOT NULL,
	            `sub_menu_content_columns` INT(11),
	            `sub_menu_content` text,
	        PRIMARY KEY (`menu_item_id`)
		) DEFAULT COLLATE=utf8_general_ci;");

        $this->db->query("
			CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "dvmegamenu_top_item_description` (
			    `menu_item_id` INT(11) NOT NULL,
			    `language_id` int(11) NOT NULL,
	            `title` VARCHAR(255) NOT NULL,
	            PRIMARY KEY (`menu_item_id`,`language_id`)
		) DEFAULT COLLATE=utf8_general_ci;");

        $this->db->query("
			CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "dvmegamenu_sub_item` (
			    `sub_menu_item_id` INT(11) NOT NULL AUTO_INCREMENT,
			    `parent_menu_item_id` INT(11) NOT NULL,
			    `level` INT(11) NOT NULL,
	            `status` TINYINT(1) NOT NULL DEFAULT '0',
	            `name` VARCHAR(255) NOT NULL,
	            `position` INT(11) NOT NULL,
	            `link` VARCHAR(255),
	        PRIMARY KEY (`sub_menu_item_id`)
		) DEFAULT COLLATE=utf8_general_ci;");

        $this->db->query("
			CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "dvmegamenu_sub_item_description` (
			    `sub_menu_item_id` INT(11) NOT NULL,
			    `language_id` int(11) NOT NULL,
	            `title` VARCHAR(255) NOT NULL,
	            PRIMARY KEY (`sub_menu_item_id`,`language_id`)
		) DEFAULT COLLATE=utf8_general_ci;");
    }

    public function setupFeaturedCategories() {
        $this->model_user_user_group->addPermission($this->user->getGroupId(), 'access', 'diva/featuredcate');
        $this->model_user_user_group->addPermission($this->user->getGroupId(), 'modify', 'diva/featuredcate');

        $this->load->model('diva/featuredcate');

        $this->model_diva_featuredcate->createFeaturedCate();
    }

    public function setupRotateImage() {
        $query = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "product_image` LIKE 'is_rotate'");
        if($query->rows) {
            return;
        } else {
            $sql = "ALTER TABLE `" . DB_PREFIX . "product_image` ADD `is_rotate` tinyint(1) DEFAULT 0";
            $this->db->query($sql);
            return;
        }
    }

    public function setupColorSwatches() {
        $query = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "product_image` LIKE 'product_option_value_id'");
        if($query->rows) {
            return;
        } else {
            $sql = "ALTER TABLE `" . DB_PREFIX . "product_image` ADD `product_option_value_id` INT(11) NULL";
            $this->db->query($sql);
            return;
        }
    }
}