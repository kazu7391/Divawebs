<?php
class ControllerExtensionModuleDvcontrolpanel extends Controller
{
    private $error = array();

    public function index() {
        $this->load->language('extension/module/dvcontrolpanel');

        $this->document->setTitle($this->language->get('page_title'));

        $this->load->model('setting/setting');

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
            $this->model_setting_setting->editSetting('module_dvcontrolpanel', $this->request->post);

            $this->session->data['success'] = $this->language->get('text_success');

            $this->response->redirect($this->url->link('extension/module/dvcontrolpanel', 'user_token=' . $this->session->data['user_token'], true));
        }

        $this->load->model('setting/store');

        $data['stores'] = array();

        $data['stores'][] = array(
            'store_id' => 0,
            'name'     => $this->config->get('config_name') . $this->language->get('text_default')
        );

        $stores = $this->model_setting_store->getStores();

        foreach ($stores as $store) {
            $data['stores'][] = array(
                'store_id' => $store['store_id'],
                'name'     => $store['name']
            );
        }

        $this->load->model('catalog/option');

        $data['options'] = array();

        $results = $this->model_catalog_option->getOptions();

        foreach ($results as $result) {
            $data['options'][] = array(
                'option_id'  => $result['option_id'],
                'type'       => $result['type'],
                'name'       => $result['name']
            );
        }

        if(isset($this->session->data['success'])) {
            $data['success'] = $this->session->data['success'];

            unset($this->session->data['success']);
        } else {
            $data['success'] = false;
        }

        if(isset($this->session->data['error_load_file'])) {
            $data['error_load_file'] = $this->session->data['error_load_file'];

            unset($this->session->data['error_load_file']);
        } else {
            $data['error_load_file'] = false;
        }

        if (isset($this->error['warning'])) {
            $data['error_warning'] = $this->error['warning'];
        } else {
            $data['error_warning'] = '';
        }

        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_extension'),
            'href' => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true)
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link('extension/module/dvcontrolpanel', 'user_token=' . $this->session->data['user_token'], true)
        );

        $data['database'] = array(
            DIR_APPLICATION . '../divadata/diva_db1.sql' => 'Layout 1',
            DIR_APPLICATION . '../divadata/diva_db2.sql' => 'Layout 2'
        );

        $fonts_file = file_get_contents('https://www.googleapis.com/webfonts/v1/webfonts?key=AIzaSyCKhdPP0mANQLtEY8Br0H71OqpQHxfu8wo');

        $google_fonts = json_decode($fonts_file, true);

        $fonts = $google_fonts['items'];

        foreach ($fonts as $key => $font) {
            $font_family_val = str_replace(' ', '+', $font['family']);
            $variants = implode(',', $font['variants']);
            $subsets = implode(',', $font['subsets']);
            $data['fonts'][] = array(
                'id'    => $key,
                'family' => $font['family'],
                'family_val' => $font_family_val,
                'variants' => $variants,
                'subsets' => $subsets,
                'category' => $font['category']
            );
        }

        $data['user_token'] = $this->session->data['user_token'];

        $data['action'] = $this->url->link('extension/module/dvcontrolpanel', 'user_token=' . $this->session->data['user_token'], true);
        $data['action_import'] = $this->url->link('extension/module/dvcontrolpanel/import', 'user_token=' . $this->session->data['user_token'], true);

        $data['cancel'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true);

        /* General */
        if (isset($this->request->post['module_dvcontrolpanel_lazy_load'])) {
            $data['module_dvcontrolpanel_lazy_load'] = $this->request->post['module_dvcontrolpanel_lazy_load'];
        } else {
            $data['module_dvcontrolpanel_lazy_load'] = $this->config->get('module_dvcontrolpanel_lazy_load');
        }

        if (isset($this->request->post['module_dvcontrolpanel_sticky_header'])) {
            $data['module_dvcontrolpanel_sticky_header'] = $this->request->post['module_dvcontrolpanel_sticky_header'];
        } else {
            $data['module_dvcontrolpanel_sticky_header'] = $this->config->get('module_dvcontrolpanel_sticky_header');
        }

        if (isset($this->request->post['module_dvcontrolpanel_scroll_top'])) {
            $data['module_dvcontrolpanel_scroll_top'] = $this->request->post['module_dvcontrolpanel_scroll_top'];
        } else {
            $data['module_dvcontrolpanel_scroll_top'] = $this->config->get('module_dvcontrolpanel_scroll_top');
        }

        $this->load->model('tool/image');

        foreach ($data['stores'] as $store) {
            if (isset($this->request->post['module_dvcontrolpanel_loader_img'][$store['store_id']]) && is_file(DIR_IMAGE . $this->request->post['module_dvcontrolpanel_loader_img'][$store['store_id']])) {
                $data['thumb'][$store['store_id']] = $this->model_tool_image->resize($this->request->post['module_dvcontrolpanel_loader_img'][$store['store_id']], 50, 50);
            } elseif (is_file(DIR_IMAGE . $this->config->get('module_dvcontrolpanel_loader_img')[$store['store_id']])) {
                $data['thumb'][$store['store_id']] = $this->model_tool_image->resize($this->config->get('module_dvcontrolpanel_loader_img')[$store['store_id']], 50, 50);
            } else {
                $data['thumb'][$store['store_id']] = $this->model_tool_image->resize('no_image.png', 50, 50);
            }
        }

        $data['placeholder'] = $this->model_tool_image->resize('no_image.png', 50, 50);

        /* Font & CSS */
        /* Body */
        if (isset($this->request->post['module_dvcontrolpanel_body_font_family_id'])) {
            $data['module_dvcontrolpanel_body_font_family_id'] = $this->request->post['module_dvcontrolpanel_body_font_family_id'];
        } else {
            $data['module_dvcontrolpanel_body_font_family_id'] = $this->config->get('module_dvcontrolpanel_body_font_family_id');
        }

        if (isset($this->request->post['module_dvcontrolpanel_body_font_family_name'])) {
            $data['module_dvcontrolpanel_body_font_family_name'] = $this->request->post['module_dvcontrolpanel_body_font_family_name'];
        } else {
            $data['module_dvcontrolpanel_body_font_family_name'] = $this->config->get('module_dvcontrolpanel_body_font_family_name');
        }

        if (isset($this->request->post['module_dvcontrolpanel_body_font_family_cate'])) {
            $data['module_dvcontrolpanel_body_font_family_cate'] = $this->request->post['module_dvcontrolpanel_body_font_family_cate'];
        } else {
            $data['module_dvcontrolpanel_body_font_family_cate'] = $this->config->get('module_dvcontrolpanel_body_font_family_cate');
        }

        if (isset($this->request->post['module_dvcontrolpanel_body_font_family_link'])) {
            $data['module_dvcontrolpanel_body_font_family_link'] = $this->request->post['module_dvcontrolpanel_body_font_family_link'];
        } else {
            $data['module_dvcontrolpanel_body_font_family_link'] = $this->config->get('module_dvcontrolpanel_body_font_family_link');
        }

        if (isset($this->request->post['module_dvcontrolpanel_body_font_size'])) {
            $data['module_dvcontrolpanel_body_font_size'] = $this->request->post['module_dvcontrolpanel_body_font_size'];
        } else {
            $data['module_dvcontrolpanel_body_font_size'] = $this->config->get('module_dvcontrolpanel_body_font_size');
        }

        if (isset($this->request->post['module_dvcontrolpanel_body_font_weight'])) {
            $data['module_dvcontrolpanel_body_font_weight'] = $this->request->post['module_dvcontrolpanel_body_font_weight'];
        } else {
            $data['module_dvcontrolpanel_body_font_weight'] = $this->config->get('module_dvcontrolpanel_body_font_weight');
        }

        if (isset($this->request->post['module_dvcontrolpanel_body_color'])) {
            $data['module_dvcontrolpanel_body_color'] = $this->request->post['module_dvcontrolpanel_body_color'];
        } else {
            $data['module_dvcontrolpanel_body_color'] = $this->config->get('module_dvcontrolpanel_body_color');
        }

        /* Heading */
        if (isset($this->request->post['module_dvcontrolpanel_heading_font_family_id'])) {
            $data['module_dvcontrolpanel_heading_font_family_id'] = $this->request->post['module_dvcontrolpanel_heading_font_family_id'];
        } else {
            $data['module_dvcontrolpanel_heading_font_family_id'] = $this->config->get('module_dvcontrolpanel_heading_font_family_id');
        }

        if (isset($this->request->post['module_dvcontrolpanel_heading_font_family_name'])) {
            $data['module_dvcontrolpanel_heading_font_family_name'] = $this->request->post['module_dvcontrolpanel_heading_font_family_name'];
        } else {
            $data['module_dvcontrolpanel_heading_font_family_name'] = $this->config->get('module_dvcontrolpanel_heading_font_family_name');
        }

        if (isset($this->request->post['module_dvcontrolpanel_heading_font_family_cate'])) {
            $data['module_dvcontrolpanel_heading_font_family_cate'] = $this->request->post['module_dvcontrolpanel_heading_font_family_cate'];
        } else {
            $data['module_dvcontrolpanel_heading_font_family_cate'] = $this->config->get('module_dvcontrolpanel_heading_font_family_cate');
        }

        if (isset($this->request->post['module_dvcontrolpanel_heading_font_family_link'])) {
            $data['module_dvcontrolpanel_heading_font_family_link'] = $this->request->post['module_dvcontrolpanel_heading_font_family_link'];
        } else {
            $data['module_dvcontrolpanel_heading_font_family_link'] = $this->config->get('module_dvcontrolpanel_heading_font_family_link');
        }

        if (isset($this->request->post['module_dvcontrolpanel_heading_font_weight'])) {
            $data['module_dvcontrolpanel_heading_font_weight'] = $this->request->post['module_dvcontrolpanel_heading_font_weight'];
        } else {
            $data['module_dvcontrolpanel_heading_font_weight'] = $this->config->get('module_dvcontrolpanel_heading_font_weight');
        }

        if (isset($this->request->post['module_dvcontrolpanel_heading_color'])) {
            $data['module_dvcontrolpanel_heading_color'] = $this->request->post['module_dvcontrolpanel_heading_color'];
        } else {
            $data['module_dvcontrolpanel_heading_color'] = $this->config->get('module_dvcontrolpanel_heading_color');
        }

        /* Link */
        if (isset($this->request->post['module_dvcontrolpanel_link_color'])) {
            $data['module_dvcontrolpanel_link_color'] = $this->request->post['module_dvcontrolpanel_link_color'];
        } else {
            $data['module_dvcontrolpanel_link_color'] = $this->config->get('module_dvcontrolpanel_link_color');
        }

        if (isset($this->request->post['module_dvcontrolpanel_link_hover_color'])) {
            $data['module_dvcontrolpanel_link_hover_color'] = $this->request->post['module_dvcontrolpanel_link_hover_color'];
        } else {
            $data['module_dvcontrolpanel_link_hover_color'] = $this->config->get('module_dvcontrolpanel_link_hover_color');
        }

        /* Button */
        if (isset($this->request->post['module_dvcontrolpanel_button_color'])) {
            $data['module_dvcontrolpanel_button_color'] = $this->request->post['module_dvcontrolpanel_button_color'];
        } else {
            $data['module_dvcontrolpanel_button_color'] = $this->config->get('module_dvcontrolpanel_button_color');
        }
        
        if (isset($this->request->post['module_dvcontrolpanel_button_hover_color'])) {
            $data['module_dvcontrolpanel_button_hover_color'] = $this->request->post['module_dvcontrolpanel_button_hover_color'];
        } else {
            $data['module_dvcontrolpanel_button_hover_color'] = $this->config->get('module_dvcontrolpanel_button_hover_color');
        }
        
        if (isset($this->request->post['module_dvcontrolpanel_button_bg_color'])) {
            $data['module_dvcontrolpanel_button_bg_color'] = $this->request->post['module_dvcontrolpanel_button_bg_color'];
        } else {
            $data['module_dvcontrolpanel_button_bg_color'] = $this->config->get('module_dvcontrolpanel_button_bg_color');
        }
        
        if (isset($this->request->post['module_dvcontrolpanel_button_bg_hover_color'])) {
            $data['module_dvcontrolpanel_button_bg_hover_color'] = $this->request->post['module_dvcontrolpanel_button_bg_hover_color'];
        } else {
            $data['module_dvcontrolpanel_button_bg_hover_color'] = $this->config->get('module_dvcontrolpanel_button_bg_hover_color');
        }

        /* Catalog */
        /* Header */
        if (isset($this->request->post['module_dvcontrolpanel_header_cart'])) {
            $data['module_dvcontrolpanel_header_cart'] = $this->request->post['module_dvcontrolpanel_header_cart'];
        } else {
            $data['module_dvcontrolpanel_header_cart'] = $this->config->get('module_dvcontrolpanel_header_cart');
        }

        if (isset($this->request->post['module_dvcontrolpanel_header_currency'])) {
            $data['module_dvcontrolpanel_header_currency'] = $this->request->post['module_dvcontrolpanel_header_currency'];
        } else {
            $data['module_dvcontrolpanel_header_currency'] = $this->config->get('module_dvcontrolpanel_header_currency');
        }

        /* Module */
        if (isset($this->request->post['module_dvcontrolpanel_module_price'])) {
            $data['module_dvcontrolpanel_module_price'] = $this->request->post['module_dvcontrolpanel_module_price'];
        } else {
            $data['module_dvcontrolpanel_module_price'] = $this->config->get('module_dvcontrolpanel_module_price');
        }

        if (isset($this->request->post['module_dvcontrolpanel_module_cart'])) {
            $data['module_dvcontrolpanel_module_cart'] = $this->request->post['module_dvcontrolpanel_module_cart'];
        } else {
            $data['module_dvcontrolpanel_module_cart'] = $this->config->get('module_dvcontrolpanel_module_cart');
        }

        if (isset($this->request->post['module_dvcontrolpanel_module_wishlist'])) {
            $data['module_dvcontrolpanel_module_wishlist'] = $this->request->post['module_dvcontrolpanel_module_wishlist'];
        } else {
            $data['module_dvcontrolpanel_module_wishlist'] = $this->config->get('module_dvcontrolpanel_module_wishlist');
        }

        if (isset($this->request->post['module_dvcontrolpanel_module_compare'])) {
            $data['module_dvcontrolpanel_module_compare'] = $this->request->post['module_dvcontrolpanel_module_compare'];
        } else {
            $data['module_dvcontrolpanel_module_compare'] = $this->config->get('module_dvcontrolpanel_module_compare');
        }

        if (isset($this->request->post['module_dvcontrolpanel_module_hover'])) {
            $data['module_dvcontrolpanel_module_hover'] = $this->request->post['module_dvcontrolpanel_module_hover'];
        } else {
            $data['module_dvcontrolpanel_module_hover'] = $this->config->get('module_dvcontrolpanel_module_hover');
        }

        if (isset($this->request->post['module_dvcontrolpanel_module_quickview'])) {
            $data['module_dvcontrolpanel_module_quickview'] = $this->request->post['module_dvcontrolpanel_module_quickview'];
        } else {
            $data['module_dvcontrolpanel_module_quickview'] = $this->config->get('module_dvcontrolpanel_module_quickview');
        }

        if (isset($this->request->post['module_dvcontrolpanel_module_label'])) {
            $data['module_dvcontrolpanel_module_label'] = $this->request->post['module_dvcontrolpanel_module_label'];
        } else {
            $data['module_dvcontrolpanel_module_label'] = $this->config->get('module_dvcontrolpanel_module_label');
        }

        /* Product catalog */
        if (isset($this->request->post['module_dvcontrolpanel_product_price'])) {
            $data['module_dvcontrolpanel_product_price'] = $this->request->post['module_dvcontrolpanel_product_price'];
        } else {
            $data['module_dvcontrolpanel_product_price'] = $this->config->get('module_dvcontrolpanel_product_price');
        }

        if (isset($this->request->post['module_dvcontrolpanel_product_cart'])) {
            $data['module_dvcontrolpanel_product_cart'] = $this->request->post['module_dvcontrolpanel_product_cart'];
        } else {
            $data['module_dvcontrolpanel_product_cart'] = $this->config->get('module_dvcontrolpanel_product_cart');
        }

        if (isset($this->request->post['module_dvcontrolpanel_product_wishlist'])) {
            $data['module_dvcontrolpanel_product_wishlist'] = $this->request->post['module_dvcontrolpanel_product_wishlist'];
        } else {
            $data['module_dvcontrolpanel_product_wishlist'] = $this->config->get('module_dvcontrolpanel_product_wishlist');
        }

        if (isset($this->request->post['module_dvcontrolpanel_product_compare'])) {
            $data['module_dvcontrolpanel_product_compare'] = $this->request->post['module_dvcontrolpanel_product_compare'];
        } else {
            $data['module_dvcontrolpanel_product_compare'] = $this->config->get('module_dvcontrolpanel_product_compare');
        }

        if (isset($this->request->post['module_dvcontrolpanel_product_options'])) {
            $data['module_dvcontrolpanel_product_options'] = $this->request->post['module_dvcontrolpanel_product_options'];
        } else {
            $data['module_dvcontrolpanel_product_options'] = $this->config->get('module_dvcontrolpanel_product_options');
        }

        /* Category Catalog */
        if (isset($this->request->post['module_dvcontrolpanel_category_price'])) {
            $data['module_dvcontrolpanel_category_price'] = $this->request->post['module_dvcontrolpanel_category_price'];
        } else {
            $data['module_dvcontrolpanel_category_price'] = $this->config->get('module_dvcontrolpanel_category_price');
        }

        if (isset($this->request->post['module_dvcontrolpanel_category_cart'])) {
            $data['module_dvcontrolpanel_category_cart'] = $this->request->post['module_dvcontrolpanel_category_cart'];
        } else {
            $data['module_dvcontrolpanel_category_cart'] = $this->config->get('module_dvcontrolpanel_category_cart');
        }

        if (isset($this->request->post['module_dvcontrolpanel_category_wishlist'])) {
            $data['module_dvcontrolpanel_category_wishlist'] = $this->request->post['module_dvcontrolpanel_category_wishlist'];
        } else {
            $data['module_dvcontrolpanel_category_wishlist'] = $this->config->get('module_dvcontrolpanel_category_wishlist');
        }

        if (isset($this->request->post['module_dvcontrolpanel_category_compare'])) {
            $data['module_dvcontrolpanel_category_compare'] = $this->request->post['module_dvcontrolpanel_category_compare'];
        } else {
            $data['module_dvcontrolpanel_category_compare'] = $this->config->get('module_dvcontrolpanel_category_compare');
        }

        if (isset($this->request->post['module_dvcontrolpanel_category_prodes'])) {
            $data['module_dvcontrolpanel_category_prodes'] = $this->request->post['module_dvcontrolpanel_category_prodes'];
        } else {
            $data['module_dvcontrolpanel_category_prodes'] = $this->config->get('module_dvcontrolpanel_category_prodes');
        }

        if (isset($this->request->post['module_dvcontrolpanel_category_label'])) {
            $data['module_dvcontrolpanel_category_label'] = $this->request->post['module_dvcontrolpanel_category_label'];
        } else {
            $data['module_dvcontrolpanel_category_label'] = $this->config->get('module_dvcontrolpanel_category_label');
        }

        /* Product */
        if (isset($this->request->post['module_dvcontrolpanel_related'])) {
            $data['module_dvcontrolpanel_related'] = $this->request->post['module_dvcontrolpanel_related'];
        } else {
            $data['module_dvcontrolpanel_related'] = $this->config->get('module_dvcontrolpanel_related');
        }

        if (isset($this->request->post['module_dvcontrolpanel_social'])) {
            $data['module_dvcontrolpanel_social'] = $this->request->post['module_dvcontrolpanel_social'];
        } else {
            $data['module_dvcontrolpanel_social'] = $this->config->get('module_dvcontrolpanel_social');
        }

        if (isset($this->request->post['module_dvcontrolpanel_tax'])) {
            $data['module_dvcontrolpanel_tax'] = $this->request->post['module_dvcontrolpanel_tax'];
        } else {
            $data['module_dvcontrolpanel_tax'] = $this->config->get('module_dvcontrolpanel_tax');
        }

        if (isset($this->request->post['module_dvcontrolpanel_tags'])) {
            $data['module_dvcontrolpanel_tags'] = $this->request->post['module_dvcontrolpanel_tags'];
        } else {
            $data['module_dvcontrolpanel_tags'] = $this->config->get('module_dvcontrolpanel_tags');
        }

        if (isset($this->request->post['module_dvcontrolpanel_use_zoom'])) {
            $data['module_dvcontrolpanel_use_zoom'] = $this->request->post['module_dvcontrolpanel_use_zoom'];
        } else {
            $data['module_dvcontrolpanel_use_zoom'] = $this->config->get('module_dvcontrolpanel_use_zoom');
        }

        if (isset($this->request->post['module_dvcontrolpanel_zoom_type'])) {
            $data['module_dvcontrolpanel_zoom_type'] = $this->request->post['module_dvcontrolpanel_zoom_type'];
        } else {
            $data['module_dvcontrolpanel_zoom_type'] = $this->config->get('module_dvcontrolpanel_zoom_type');
        }

        if (isset($this->request->post['module_dvcontrolpanel_zoom_space'])) {
            $data['module_dvcontrolpanel_zoom_space'] = $this->request->post['module_dvcontrolpanel_zoom_space'];
        } else {
            $data['module_dvcontrolpanel_zoom_space'] = $this->config->get('module_dvcontrolpanel_zoom_space');
        }

        if (isset($this->request->post['module_dvcontrolpanel_zoom_title'])) {
            $data['module_dvcontrolpanel_zoom_title'] = $this->request->post['module_dvcontrolpanel_zoom_title'];
        } else {
            $data['module_dvcontrolpanel_zoom_title'] = $this->config->get('module_dvcontrolpanel_zoom_title');
        }

        if (isset($this->request->post['module_dvcontrolpanel_use_swatches'])) {
            $data['module_dvcontrolpanel_use_swatches'] = $this->request->post['module_dvcontrolpanel_use_swatches'];
        } else {
            $data['module_dvcontrolpanel_use_swatches'] = $this->config->get('module_dvcontrolpanel_use_swatches');
        }

        if (isset($this->request->post['module_dvcontrolpanel_swatches_width'])) {
            $data['module_dvcontrolpanel_swatches_width'] = $this->request->post['module_dvcontrolpanel_swatches_width'];
        } else {
            $data['module_dvcontrolpanel_swatches_width'] = $this->config->get('module_dvcontrolpanel_swatches_width');
        }

        if (isset($this->request->post['module_dvcontrolpanel_swatches_height'])) {
            $data['module_dvcontrolpanel_swatches_height'] = $this->request->post['module_dvcontrolpanel_swatches_height'];
        } else {
            $data['module_dvcontrolpanel_swatches_height'] = $this->config->get('module_dvcontrolpanel_swatches_height');
        }
        
        if (isset($this->request->post['module_dvcontrolpanel_swatches_option'])) {
            $data['module_dvcontrolpanel_swatches_option'] = $this->request->post['module_dvcontrolpanel_swatches_option'];
        } else {
            $data['module_dvcontrolpanel_swatches_option'] = $this->config->get('module_dvcontrolpanel_swatches_option');
        }
        
        /* Category */
        if (isset($this->request->post['module_dvcontrolpanel_category_image'])) {
            $data['module_dvcontrolpanel_category_image'] = $this->request->post['module_dvcontrolpanel_category_image'];
        } else {
            $data['module_dvcontrolpanel_category_image'] = $this->config->get('module_dvcontrolpanel_category_image');
        }

        if (isset($this->request->post['module_dvcontrolpanel_category_description'])) {
            $data['module_dvcontrolpanel_category_description'] = $this->request->post['module_dvcontrolpanel_category_description'];
        } else {
            $data['module_dvcontrolpanel_category_description'] = $this->config->get('module_dvcontrolpanel_category_description');
        }

        if (isset($this->request->post['module_dvcontrolpanel_sub_category'])) {
            $data['module_dvcontrolpanel_sub_category'] = $this->request->post['module_dvcontrolpanel_sub_category'];
        } else {
            $data['module_dvcontrolpanel_sub_category'] = $this->config->get('module_dvcontrolpanel_sub_category');
        }

        if (isset($this->request->post['module_dvcontrolpanel_use_filter'])) {
            $data['module_dvcontrolpanel_use_filter'] = $this->request->post['module_dvcontrolpanel_use_filter'];
        } else {
            $data['module_dvcontrolpanel_use_filter'] = $this->config->get('module_dvcontrolpanel_use_filter');
        }

        if (isset($this->request->post['module_dvcontrolpanel_filter_position'])) {
            $data['module_dvcontrolpanel_filter_position'] = $this->request->post['module_dvcontrolpanel_filter_position'];
        } else {
            $data['module_dvcontrolpanel_filter_position'] = $this->config->get('module_dvcontrolpanel_filter_position');
        }

        if (isset($this->request->post['module_dvcontrolpanel_cate_quickview'])) {
            $data['module_dvcontrolpanel_cate_quickview'] = $this->request->post['module_dvcontrolpanel_cate_quickview'];
        } else {
            $data['module_dvcontrolpanel_cate_quickview'] = $this->config->get('module_dvcontrolpanel_cate_quickview');
        }

        if (isset($this->request->post['module_dvcontrolpanel_img_effect'])) {
            $data['module_dvcontrolpanel_img_effect'] = $this->request->post['module_dvcontrolpanel_img_effect'];
        } else {
            $data['module_dvcontrolpanel_img_effect'] = $this->config->get('module_dvcontrolpanel_img_effect');
        }

        if (isset($this->request->post['module_dvcontrolpanel_cate_swatches_width'])) {
            $data['module_dvcontrolpanel_cate_swatches_width'] = $this->request->post['module_dvcontrolpanel_cate_swatches_width'];
        } else {
            $data['module_dvcontrolpanel_cate_swatches_width'] = $this->config->get('module_dvcontrolpanel_cate_swatches_width');
        }

        if (isset($this->request->post['module_dvcontrolpanel_cate_swatches_height'])) {
            $data['module_dvcontrolpanel_cate_swatches_height'] = $this->request->post['module_dvcontrolpanel_cate_swatches_height'];
        } else {
            $data['module_dvcontrolpanel_cate_swatches_height'] = $this->config->get('module_dvcontrolpanel_cate_swatches_height');
        }

        if (isset($this->request->post['module_dvcontrolpanel_advance_view'])) {
            $data['module_dvcontrolpanel_advance_view'] = $this->request->post['module_dvcontrolpanel_advance_view'];
        } else {
            $data['module_dvcontrolpanel_advance_view'] = $this->config->get('module_dvcontrolpanel_advance_view');
        }

        if (isset($this->request->post['module_dvcontrolpanel_default_view'])) {
            $data['module_dvcontrolpanel_default_view'] = $this->request->post['module_dvcontrolpanel_default_view'];
        } else {
            $data['module_dvcontrolpanel_default_view'] = $this->config->get('module_dvcontrolpanel_default_view');
        }

        if (isset($this->request->post['module_dvcontrolpanel_product_row'])) {
            $data['module_dvcontrolpanel_product_row'] = $this->request->post['module_dvcontrolpanel_product_row'];
        } else {
            $data['module_dvcontrolpanel_product_row'] = $this->config->get('module_dvcontrolpanel_product_row');
        }
        
        //$this->document->addStyle('view/stylesheet/divawebs/themeadmin.css');
        $this->document->addScript('view/javascript/divawebs/jscolor.min.js');
        $this->document->addScript('view/javascript/divawebs/switch-toggle/js/bootstrap-toggle.min.js');
        $this->document->addStyle('view/javascript/divawebs/switch-toggle/css/bootstrap-toggle.min.css');

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('diva/module/dvcontrolpanel', $data));
    }

    public function import() {
        $this->load->language('extension/module/dvcontrolpanel');

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && isset($this->request->post['file'])) {
            $file = $this->request->post['file'];
        } else {
            $file = '';
        }

        if (!file_exists($file)) {
            unset($this->session->data['success']);

            $this->session->data['error_load_file'] = sprintf($this->language->get('error_load_file'), $file);

            $this->response->redirect($this->url->link('extension/module/dvcontrolpanel', 'user_token=' . $this->session->data['user_token'], true));
        } else {
            unset($this->session->data['error_load_file']);

            $lines = file($file);

            if($lines) {
                $sql = '';

                foreach($lines as $line) {
                    if ($line && (substr($line, 0, 2) != '--') && (substr($line, 0, 1) != '#')) {
                        $sql .= $line;

                        if (preg_match('/;\s*$/', $line)) {
                            $sql = str_replace("DROP TABLE IF EXISTS `oc_", "DROP TABLE IF EXISTS `" . DB_PREFIX, $sql);
                            $sql = str_replace("CREATE TABLE `oc_", "CREATE TABLE `" . DB_PREFIX, $sql);
                            $sql = str_replace("CREATE TABLE IF NOT EXISTS `oc_", "CREATE TABLE `" . DB_PREFIX, $sql);
                            $sql = str_replace("INSERT INTO `oc_", "INSERT INTO `" . DB_PREFIX, $sql);
                            $sql = str_replace("UPDATE `oc_", "UPDATE `" . DB_PREFIX, $sql);
                            $sql = str_replace("WHERE `oc_", "WHERE `" . DB_PREFIX, $sql);
                            $sql = str_replace("TRUNCATE TABLE `oc_", "TRUNCATE TABLE `" . DB_PREFIX, $sql);
                            $sql = str_replace("ALTER TABLE `oc_", "ALTER TABLE `" . DB_PREFIX, $sql);

                            $this->db->query($sql);

                            $sql = '';
                        }
                    }
                }
            }

            $this->session->data['success'] = $this->language->get('text_import_success');

            $this->response->redirect($this->url->link('extension/module/dvcontrolpanel', 'user_token=' . $this->session->data['user_token'], true));
        }
    }

    public function install() {
        $this->load->model('diva/controlpanel');
        $this->model_diva_controlpanel->setupData();
    }

    protected function validate() {
        if (!$this->user->hasPermission('modify', 'extension/module/dvcontrolpanel')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        return !$this->error;
    }
}