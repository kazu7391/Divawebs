<?php
class ControllerExtensionModuleDvslider extends Controller
{
    public function index($setting) {
        $this->load->language('diva/module/dvslider');

        $this->load->model('diva/slider');
        $this->load->model('tool/image');

        $data = array();

        $data['dvsliders'] = array();

        $data['animate'] = 'animate-in';

        $results = array();

        if(isset($setting['slider'])) {
            $results = $this->model_diva_slider->getSliderDescription($setting['slider']);
        }

        if($results) {
            $store_id  = $this->config->get('config_store_id');

            foreach ($results as $result) {
                $slider_store = array();
                if(isset($result['slider_store'])) {
                    $slider_store = explode(',',$result['slider_store']);
                }

                if(in_array($store_id, $slider_store)) {
                    $data['dvsliders'][] = array(
                        'title'         => $result['title'],
                        'sub_title'     => $result['sub_title'],
                        'description'   => html_entity_decode($result['description'], ENT_QUOTES, 'UTF-8'),
                        'link'          => $result['link'],
                        'type'          => $result['type'],
                        'image'         => $this->model_tool_image->resize($result['image'], $setting['width'], $setting['height'])
                    );
                }
            }

            $data['slider'] = $this->model_diva_slider->getSlider($result['dvslider_id']);

            if (file_exists(DIR_TEMPLATE . $this->config->get($this->config->get('config_theme') . '_directory') . '/stylesheet/diva/slider/slider.css')) {
                $this->document->addStyle('catalog/view/theme/'.$this->config->get($this->config->get('config_theme') . '_directory').'/stylesheet/diva/slider/slider.css');
            } else {
                $this->document->addStyle('catalog/view/theme/default/stylesheet/diva/slider/slider.css');
            }

            $this->document->addScript('catalog/view/javascript/diva/slider/jquery.nivo.slider.js');

        }

        return $this->load->view('diva/module/dvslider', $data);
    }
}