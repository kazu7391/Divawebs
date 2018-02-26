<?php
class ControllerExtensionModuleDvfeaturedcate extends Controller
{
    public function index($setting) {
        $this->load->model('diva/featuredcate');
        $this->load->model('catalog/category');
        $this->load->model('catalog/product');
        $this->load->model('tool/image');
        $this->load->language('diva/module/dvfeaturedcate');

        echo "<pre>"; var_dump($setting);

        $data = array();

        $data['demo'] = "demo";
        
        if(isset($setting['status']) && $setting['status']) {
            $data['status'] = true;
        } else {
            $data['status'] = false;
        }

        if(isset($setting['type']) && $setting['type']) {
            $data['type'] = $setting['type'];;
        } else {
            $data['type'] = false;
        }

        /* Slider Settings */
        if(isset($setting['width']) && $setting['width']) {
            $width = (int) $setting['width'];
        } else {
            $width = 200;
        }

        if(isset($setting['height']) && $setting['height']) {
            $height = (int) $setting['height'];
        } else {
            $height = 200;
        }

        if(isset($setting['limit']) && $setting['limit']) {
            $limit = (int) $setting['limit'];
        } else {
            $limit = 10;
        }

        if(isset($setting['item']) && $setting['item']) {
            $item = (int) $setting['item'];
        } else {
            $item = 4;
        }

        if(isset($setting['speed']) && $setting['speed']) {
            $speed = (int) $setting['speed'];
        } else {
            $speed = 3000;
        }

        if(isset($setting['autoplay']) && $setting['autoplay']) {
            $autoplay = true;
        } else {
            $autoplay = false;
        }

        if(isset($setting['rows']) && $setting['rows']) {
            $rows = (int) $setting['rows'];
        } else {
            $rows = 1;
        }

        if(isset($setting['shownextback']) && $setting['shownextback']) {
            $nextback = true;
        } else {
            $nextback = false;
        }

        if(isset($setting['shownav']) && $setting['shownav']) {
            $pagination = true;
        } else {
            $pagination = false;
        }

        /* Category Settings */
        if(isset($setting['slider']) && $setting['slider']) {
            $slider = true;
        } else {
            $slider = false;
        }

        if(isset($setting['showcatedes']) && $setting['showcatedes']) {
            $show_cate_des = true;
        } else {
            $show_cate_des = false;
        }

        if(isset($setting['showsub']) && $setting['showsub']) {
            $show_child = true;
        } else {
            $show_child = false;
        }

        if(isset($setting['use_cate_second_image']) && $setting['use_cate_second_image']) {
            $use_second_image = true;
        } else {
            $use_second_image = false;
        }

        if(isset($setting['showlabel']) && $setting['showlabel']) {
            $show_label = true;
        } else {
            $show_label = false;
        }

        if(isset($setting['showprodes']) && $setting['showprodes']) {
            $show_pro_des = true;
        } else {
            $show_pro_des = false;
        }





        $data['categories'] = array();

        if($type == 'category') {
            $_featured_categories = $this->model_diva_featuredcate->getFeaturedCategories($limit);
        }

        if($type == 'product') {
            $_featured_categories = $this->model_diva_featuredcate->getFeaturedCategories();
        }

        return $this->load->view('diva/module/dvfeaturedcate', $data);
    }
}