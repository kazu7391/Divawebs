<?php
class ControllerExtensionModuleDvfeaturedcate extends Controller
{
    public function index($setting) {
        $this->load->model('diva/dvfeaturedcate');
        $this->load->model('catalog/category');
        $this->load->model('catalog/product');
        $this->load->model('tool/image');
        $this->load->language('diva/modules/dvfeaturedcate');

        $data = array();
        
        if(isset($setting['status']) && $setting['status']) {
            $data['status'] = true;
        } else {
            $data['status'] = false;
        }

        if(isset($setting['autoplay']) && $setting['autoplay']) {
            $autoplay = true;
        } else {
            $autoplay = false;
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

        $type = $setting['type'];

        if(isset($setting['limit'])) {
            $limit = $setting['limit'];
        } else {
            $limit = 10;
        }

        $data['categories'] = array();

        if($type == 'category') {
            $_featured_categories = $this->model_diva_dvfeaturedcate->getFeaturedCategories($limit);
        }

        if($type == 'product') {
            $_featured_categories = $this->model_diva_dvfeaturedcate->getFeaturedCategories();
        }
    }
}