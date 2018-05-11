<?php
class ModelDivaSlider extends Model
{
    public function addSlider($data) {
        $this->db->query("INSERT INTO " . DB_PREFIX . "dvslider SET name = '" . $this->db->escape($data['name']) . "', status = '" . (int)$data['status'] . "',auto = '" . (int)$data['auto'] . "',delay = '" . (int)$data['delay'] . "',hover = '" . (int)$data['hover'] . "',nextback = '" . (int)$data['nextback'] . "',contrl = '" . (int)$data['contrl'] . "',effect = '" . $data['effect'] . "'");

        $dvslider_id = $this->db->getLastId();

        if (isset($data['dvslider_image'])) {
            foreach ($data['dvslider_image'] as $dvslider_image) {

                $slider_store = "";
                if(isset($data['slider_store'])) {
                    $slider_store = implode(',', $data['slider_store']);
                }

                $this->db->query("INSERT INTO " . DB_PREFIX . "dvslider_image SET dvslider_id = '" . (int) $dvslider_id . "', link = '" .  $this->db->escape($dvslider_image['link']) . "', type = '" .  $this->db->escape($dvslider_image['type']) . "', image = '" .  $this->db->escape($dvslider_image['image']) . "', slider_store = '" .$slider_store. "'");

                $dvslider_image_id = $this->db->getLastId();

                foreach ($dvslider_image['dvslider_image_description'] as $language_id => $dvslider_image_description) {
                    $this->db->query("INSERT INTO " . DB_PREFIX . "dvslider_image_description SET dvslider_image_id = '" . (int) $dvslider_image_id . "', language_id = '" . (int) $language_id . "', dvslider_id = '" . (int) $dvslider_id . "', title = '" .  $this->db->escape($dvslider_image_description['title']) . "',sub_title = '" .  $this->db->escape($dvslider_image_description['sub_title']) . "',description = '" .  $this->db->escape($dvslider_image_description['description']) . "'");
                }
            }
        }
    }

    public function copySlider($dvslider_id) {
        $slider = $this->getSlider($dvslider_id);
        $sliderImages = $this->getSliderImages($dvslider_id);

        $this->db->query("INSERT INTO " . DB_PREFIX . "dvslider SET name = '" . $this->db->escape($slider['name']) . "', status = '" . (int) $slider['status'] . "',auto = '" . (int) $slider['auto'] . "',delay = '" . (int) $slider['delay'] . "',hover = '" . (int) $slider['hover'] . "',nextback = '" . (int) $slider['nextback'] . "',contrl = '" . (int) $slider['contrl'] . "',effect = '" . $slider['effect'] . "'");

        $dvslider_id = $this->db->getLastId();

        if (isset($sliderImages)) {
            foreach ($sliderImages as $dvslider_image) {

                $slider_store = "";
                if(isset($dvslider_image['slider_store'])) {
                    $slider_store = implode(',', $dvslider_image['slider_store']);
                }

                $this->db->query("INSERT INTO " . DB_PREFIX . "dvslider_image SET dvslider_id = '" . (int) $dvslider_id . "', link = '" .  $this->db->escape($dvslider_image['link']) . "', type = '" .  $this->db->escape($dvslider_image['type']) . "', image = '" .  $this->db->escape($dvslider_image['image']) . "', slider_store = '" .$slider_store. "'");

                $dvslider_image_id = $this->db->getLastId();

                foreach ($dvslider_image['dvslider_image_description'] as $language_id => $dvslider_image_description) {
                    $this->db->query("INSERT INTO " . DB_PREFIX . "dvslider_image_description SET dvslider_image_id = '" . (int) $dvslider_image_id . "', language_id = '" . (int) $language_id . "', dvslider_id = '" . (int) $dvslider_id . "', title = '" .  $this->db->escape($dvslider_image_description['title']) . "',sub_title = '" .  $this->db->escape($dvslider_image_description['sub_title']) . "',description = '" .  $this->db->escape($dvslider_image_description['description']) . "'");
                }
            }
        }
    }

    public function editSlider($dvslider_id, $data) {
        $this->db->query("UPDATE " . DB_PREFIX . "dvslider SET name = '" . $this->db->escape($data['name']) . "', status = '" . (int) $data['status'] . "', auto = '" . (int) $data['auto'] . "', delay = '" . (int) $data['delay'] . "', hover = '" . (int) $data['hover'] . "', nextback = '" . (int) $data['nextback'] . "', effect = '" . $data['effect'] . "', contrl = '" . (int) $data['contrl'] . "' WHERE dvslider_id = '" . (int) $dvslider_id . "'");

        $this->db->query("DELETE FROM " . DB_PREFIX . "dvslider_image WHERE dvslider_id = '" . (int) $dvslider_id . "'");
        $this->db->query("DELETE FROM " . DB_PREFIX . "dvslider_image_description WHERE dvslider_id = '" . (int) $dvslider_id . "'");

        if (isset($data['dvslider_image'])) {

            foreach ($data['dvslider_image'] as $dvslider_image) {

                $slider_store = "";
                if(isset($data['slider_store'])) {
                    $slider_store = implode(',', $data['slider_store']);
                }
                $this->db->query("INSERT INTO " . DB_PREFIX . "dvslider_image SET dvslider_id = '" . (int) $dvslider_id . "', link = '" .  $this->db->escape($dvslider_image['link']) . "', type = '" .  $this->db->escape($dvslider_image['type']) . "', image = '" .  $this->db->escape($dvslider_image['image']) . "', slider_store = '" .  $slider_store . "'");

                $dvslider_image_id = $this->db->getLastId();

                foreach ($dvslider_image['dvslider_image_description'] as $language_id => $dvslider_image_description) {
                    $this->db->query("INSERT INTO " . DB_PREFIX . "dvslider_image_description SET dvslider_image_id = '" . (int) $dvslider_image_id . "', language_id = '" . (int) $language_id . "', dvslider_id = '" . (int) $dvslider_id . "', title = '" .  $this->db->escape($dvslider_image_description['title']) . "', sub_title = '" .  $this->db->escape($dvslider_image_description['sub_title']) . "', description = '" .  $this->db->escape($dvslider_image_description['description']) . "'");
                }
            }
        }
    }

    public function deleteSlider($dvslider_id) {
        $this->db->query("DELETE FROM " . DB_PREFIX . "dvslider WHERE dvslider_id = '" . (int) $dvslider_id . "'");
        $this->db->query("DELETE FROM " . DB_PREFIX . "dvslider_image WHERE dvslider_id = '" . (int) $dvslider_id . "'");
        $this->db->query("DELETE FROM " . DB_PREFIX . "dvslider_image_description WHERE dvslider_id = '" . (int) $dvslider_id . "'");
    }

    public function getSlider($dvslider_id) {
        $query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "dvslider WHERE dvslider_id = '" . (int) $dvslider_id . "'");

        return $query->row;
    }

    public function getSliders($data = array()) {
        $sql = "SELECT * FROM " . DB_PREFIX . "dvslider";

        $sort_data = array(
            'name',
            'status'
        );

        if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
            $sql .= " ORDER BY " . $data['sort'];
        } else {
            $sql .= " ORDER BY name";
        }

        if (isset($data['order']) && ($data['order'] == 'DESC')) {
            $sql .= " DESC";
        } else {
            $sql .= " ASC";
        }

        if (isset($data['start']) || isset($data['limit'])) {
            if ($data['start'] < 0) {
                $data['start'] = 0;
            }

            if ($data['limit'] < 1) {
                $data['limit'] = 20;
            }

            $sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
        }

        $query = $this->db->query($sql);

        return $query->rows;
    }

    public function getSliderImages($dvslider_id) {
        $dvslider_image_data = array();

        $dvslider_image_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "dvslider_image WHERE dvslider_id = '" . (int) $dvslider_id . "'");

        foreach ($dvslider_image_query->rows as $dvslider_image) {
            $dvslider_image_description_data = array();

            $dvslider_image_description_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "dvslider_image_description WHERE dvslider_image_id = '" . (int) $dvslider_image['dvslider_image_id'] . "' AND dvslider_id = '" . (int) $dvslider_id . "'");

            foreach ($dvslider_image_description_query->rows as $dvslider_image_description) {
                $dvslider_image_description_data[$dvslider_image_description['language_id']] = array('title' => $dvslider_image_description['title'],
                    'sub_title' => $dvslider_image_description['sub_title'],
                    'description' => $dvslider_image_description['description'],

                );
            }

            $dvslider_image_data[] = array(
                'dvslider_image_description'    => $dvslider_image_description_data,
                'link'                          => $dvslider_image['link'],
                'type'                          => $dvslider_image['type'],
                'image'                         => $dvslider_image['image'],
                'slider_store'                  => $dvslider_image['slider_store']
            );
        }

        return $dvslider_image_data;
    }

    public function getTotalSliders() {
        $query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "dvslider");

        return $query->row['total'];
    }

    public function setupData() {
        $this->db->query("
			CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "dvslider` (
                `dvslider_id` int(11) NOT NULL AUTO_INCREMENT,
                `name` varchar(64) NOT NULL,
                `status` tinyint(1) NOT NULL,
                `auto` tinyint(1) DEFAULT NULL,
                `delay` int(11) DEFAULT NULL,
                `hover` tinyint(1) DEFAULT NULL,
                `nextback` tinyint(1) DEFAULT NULL,
                `contrl` tinyint(1) DEFAULT NULL,
                `effect` varchar(64) NOT NULL,
                PRIMARY KEY (`dvslider_id`)
		) DEFAULT COLLATE=utf8_general_ci;");

        $this->db->query("
			CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "dvslider_image` (
                `dvslider_image_id` int(11) NOT NULL AUTO_INCREMENT,
                `dvslider_id` int(11) NOT NULL,
                `link` varchar(255) NOT NULL,
                `type` int(11) NOT NULL,
                `slider_store` varchar(110) DEFAULT '0',
                `image` varchar(255) NOT NULL,
                `secondary_image` varchar(255) DEFAULT NULL,
                PRIMARY KEY (`dvslider_image_id`)
		) DEFAULT COLLATE=utf8_general_ci;");

        $this->db->query("
			CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "dvslider_image_description` (
                `dvslider_image_id` int(11) NOT NULL,
                `dvslider_id` int(11) NOT NULL,
                `language_id` int(11) NOT NULL,
                `title` varchar(64) NOT NULL,
                `sub_title` varchar(64) DEFAULT NULL,
                `description` text,
                PRIMARY KEY (`dvslider_image_id`,`language_id`)
		) DEFAULT COLLATE=utf8_general_ci;");

        $this->load->model('user/user_group');
        $this->model_user_user_group->addPermission($this->user->getGroupId(), 'access', 'diva/slider');
        $this->model_user_user_group->addPermission($this->user->getGroupId(), 'modify', 'diva/slider');
    }
    
    public function deleteSliderData() {
        $this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "dvslider_image_description`;");
        $this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "dvslider_image`;");
        $this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "dvslider`;");

        $this->load->model('user/user_group');
        $this->model_user_user_group->removePermission($this->user->getGroupId(), 'access', 'diva/slider');
        $this->model_user_user_group->removePermission($this->user->getGroupId(), 'modify', 'diva/slider');
    }
}