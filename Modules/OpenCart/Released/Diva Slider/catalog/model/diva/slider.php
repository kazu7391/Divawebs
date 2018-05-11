<?php
class ModelDivaSlider extends Model
{
    public function getSlider($slider_id) {
        $query = "SELECT * FROM " . DB_PREFIX . "dvslider WHERE dvslider_id = ". (int) $slider_id;
        $result = $this->db->query($query);

        return $result->rows;
    }

    public function getSliderDescription($slider_id) {
        $select ="SELECT * FROM " . DB_PREFIX . "dvslider_image di LEFT JOIN " . DB_PREFIX . "dvslider_image_description did ON (di.dvslider_image_id  = did.dvslider_image_id) WHERE di.dvslider_id = '" . (int) $slider_id . "' AND did.language_id = '" . (int) $this->config->get('config_language_id') . "'";
        $query = $this->db->query($select);

        return $query->rows;
    }
}