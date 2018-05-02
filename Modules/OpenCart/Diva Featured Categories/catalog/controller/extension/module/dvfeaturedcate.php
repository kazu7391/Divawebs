<?php
class ControllerExtensionModuleDvfeaturedcate extends Controller
{
    public function index($setting) {
        $this->load->model('diva/featuredcate');
        $this->load->model('catalog/category');
        $this->load->model('catalog/product');
        $this->load->model('tool/image');
        $this->load->model('diva/rotateimage');
        $this->load->language('diva/module/dvfeaturedcate');

        $this->document->addStyle('catalog/view/javascript/jquery/swiper/css/swiper.min.css');
        $this->document->addStyle('catalog/view/javascript/jquery/swiper/css/opencart.css');
        $this->document->addScript('catalog/view/javascript/jquery/swiper/js/swiper.jquery.js');
        if (file_exists('catalog/view/theme/' . $this->config->get('theme_' . $this->config->get('config_theme') . '_directory') . '/stylesheet/diva/module.css')) {
            $this->document->addStyle('catalog/view/theme/' . $this->config->get('theme_' . $this->config->get('config_theme') . '_directory') . '/stylesheet/diva/module.css');
        } else {
            $this->document->addStyle('catalog/view/theme/default/stylesheet/diva/module.css');
        }

        $data = array();

        /* Catalog Settings */
        $store_id = $this->config->get('config_store_id');

        if(isset($this->config->get('module_dvcontrolpanel_module_price')[$store_id])) {
            $data['show_module_price'] = (int) $this->config->get('module_dvcontrolpanel_module_price')[$store_id];
        } else {
            $data['show_module_price'] = 0;
        }

        if(isset($this->config->get('module_dvcontrolpanel_module_cart')[$store_id])) {
            $data['show_module_cart'] = (int) $this->config->get('module_dvcontrolpanel_module_cart')[$store_id];
        } else {
            $data['show_module_cart'] = 0;
        }

        if(isset($this->config->get('module_dvcontrolpanel_module_wishlist')[$store_id])) {
            $data['show_module_wishlist'] = (int) $this->config->get('module_dvcontrolpanel_module_wishlist')[$store_id];
        } else {
            $data['show_module_wishlist'] = 0;
        }

        if(isset($this->config->get('module_dvcontrolpanel_module_compare')[$store_id])) {
            $data['show_module_compare'] = (int) $this->config->get('module_dvcontrolpanel_module_compare')[$store_id];
        } else {
            $data['show_module_compare'] = 0;
        }

        if(isset($this->config->get('module_dvcontrolpanel_module_hover')[$store_id])) {
            $data['show_module_hover'] = (int) $this->config->get('module_dvcontrolpanel_module_hover')[$store_id];
        } else {
            $data['show_module_hover'] = 0;
        }

        if(isset($this->config->get('module_dvcontrolpanel_module_quickview')[$store_id])) {
            $data['show_module_quickview'] = (int) $this->config->get('module_dvcontrolpanel_module_quickview')[$store_id];
        } else {
            $data['show_module_quickview'] = 0;
        }

        if(isset($this->config->get('module_dvcontrolpanel_module_label')[$store_id])) {
            $data['show_module_label'] = (int) $this->config->get('module_dvcontrolpanel_module_label')[$store_id];
        } else {
            $data['show_module_label'] = 0;
        }

        /* Module Settings */
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

        $data['slide_settings'] = array(
            'items' => $item,
            'autoplay' => $autoplay,
            'shownextback' => $nextback,
            'shownav' => $pagination,
            'speed' => $speed,
            'rows' => $rows
        );

        /* Category Settings */
        if(isset($setting['slider']) && $setting['slider']) {
            $data['use_slider'] = true;
        } else {
            $data['use_slider'] = false;
        }

        if(isset($setting['showcatedes']) && $setting['showcatedes']) {
            $data['show_cate_des'] = true;
        } else {
            $data['show_cate_des'] = false;
        }

        if(isset($setting['showsub']) && $setting['showsub']) {
            $data['show_child'] = true;
        } else {
            $data['show_child'] = false;
        }

        if(isset($setting['showsubnumber']) && $setting['showsubnumber']) {
            $data['child_number'] = (int) $setting['showsubnumber'];
        } else {
            $data['child_number'] = 4;
        }

        if(isset($setting['use_cate_second_image']) && $setting['use_cate_second_image']) {
            $data['use_second_img'] = true;
        } else {
            $data['use_second_img'] = false;
        }

        /* Product Settings */
        $data['categories'] = array();

        if($data['type'] == 'category') {
            $_featured_categories = $this->model_diva_featuredcate->getFeaturedCategories($limit);

            if ($_featured_categories) {
                foreach ($_featured_categories as $_category) {
                    $sub_categories = array();

                    $sub_data_categories = $this->model_catalog_category->getCategories($_category['category_id']);

                    foreach($sub_data_categories as $sub_category) {
                        $filter_data = array('filter_category_id' => $sub_category['category_id'], 'filter_sub_category' => true);

                        $sub_categories[] = array(
                            'category_id' => $sub_category['category_id'],
                            'name' => $sub_category['name'],
                            'href' => $this->url->link('product/category', 'path=' . $_category['category_id'] . '_' . $sub_category['category_id'])
                        );
                    }

                    if ($_category['secondary_image']) {
                        $secondary_image = $this->model_tool_image->resize($_category['secondary_image'], $setting['width'], $setting['height']);
                    } else {
                        $secondary_image = $this->model_tool_image->resize('placeholder.png', $setting['width'], $setting['height']);
                    }
                    
                    if ($_category['description']) {
                        $description = utf8_substr(strip_tags(html_entity_decode($_category['description'], ENT_QUOTES, 'UTF-8')), 0, 80) . '..';
                    } else {
                        $description = false;
                    }

                    $data['categories'][] = array(
                        'children'			=> $sub_categories,
                        'category_id'  		=> $_category['category_id'],
                        'secondary_image'   => $secondary_image,
                        'name'        		=> $_category['name'],
                        'description' 		=> $description,
                        'href'        		=> $this->url->link('product/category', 'path=' . $_category['category_id']),
                    );
                }
            }
        }

        if($data['type'] == 'product') {
            if(isset($setting['pro_fcategory']) && $setting['pro_fcategory']) {
                $pro_fcategories = $setting['pro_fcategory'];
            } else {
                $pro_fcategories = array();
            }

            $filter_data = array(
                'hover_image' => $data['show_module_hover'],
                'sort'  => 'p.date_added',
                'order' => 'DESC',
                'start' => 0,
                'limit' => $limit
            );

            if($pro_fcategories) {
                foreach ($pro_fcategories as $category_id) {
                    $_category = $this->model_catalog_category->getCategory($category_id);

                    $filter_data['filter_category_id'] = $_category['category_id'];

                    if ($_category['secondary_image']) {
                        $secondary_image = $this->model_tool_image->resize($_category['secondary_image'], 100, 100);
                    } else {
                        $secondary_image = $this->model_tool_image->resize('placeholder.png', 100, 100);
                    }

                    $data['categories'][] = array(
                        'category_id'  		=> $_category['category_id'],
                        'secondary_image'   => $secondary_image,
                        'name'        		=> $_category['name'],
                        'description' 		=> utf8_substr(trim(strip_tags(html_entity_decode($_category['description'], ENT_QUOTES, 'UTF-8'))), 0, 80) . '..',
                        'href'        		=> $this->url->link('product/category', 'path=' . $_category['category_id']),
                        'products'          => $this->getProductFromData($filter_data, $setting)
                    );
                }
            }
        }

        if ($data['categories']) {
            return $this->load->view('diva/module/dvfeaturedcate', $data);
        }
    }

    public function getProductFromData($data= array(), $setting = array()) {
        $product_list = array();

        $use_hover_image = $data['hover_image'];

        $new_results = $this->model_catalog_product->getLatestProducts(10);

        $results = $this->model_catalog_product->getProducts($data);

        foreach ($results as $result) {
            if ($result['image']) {
                $image = $this->model_tool_image->resize($result['image'], 200, 200);
            } else {
                $image = false;
            }

            if (($this->config->get('config_customer_price') && $this->customer->isLogged()) || !$this->config->get('config_customer_price')) {
                $price = $this->currency->format($this->tax->calculate($result['price'], $result['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
            } else {
                $price = false;
            }

            if ((float)$result['special']) {
                $special = $this->currency->format($this->tax->calculate($result['special'], $result['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
            } else {
                $special = false;
            }

            if ($this->config->get('config_review_status')) {
                $rating = $result['rating'];
            } else {
                $rating = false;
            }

            if(isset($setting['showprodes']) && $setting['showprodes']) {
                $description = utf8_substr(trim(strip_tags(html_entity_decode($result['description'], ENT_QUOTES, 'UTF-8'))), 0, $this->config->get('theme_' . $this->config->get('config_theme') . '_product_description_length')) . '..';
            } else {
                $description = false;
            }

            $is_new = false;
            if ($new_results) {
                foreach($new_results as $new_r) {
                    if($result['product_id'] == $new_r['product_id']) {
                        $is_new = true;
                    }
                }
            }

            if($use_hover_image == 1) {
                $product_id = $result['product_id'];
                $rotate_image = $this->model_diva_rotateimage->getProductRotateImage($product_id);

                if($rotate_image) {
                    $rotate_image = $this->model_tool_image->resize($rotate_image, $setting['width'], $setting['height']);
                } else {
                    $rotate_image = false;
                }
            } else {
                $rotate_image = false;
            }

            $product_list[] = array(
                'product_id' => $result['product_id'],
                'thumb'   	 => $image,
                'rotate_image' => $rotate_image,
                'name'    	 => $result['name'],
                'price'   	 => $price,
                'special' 	 => $special,
                'is_new'      => $is_new,
                'rating'     => $rating,
                'description' => $description,
                'reviews'    => sprintf($this->language->get('text_reviews'), (int)$result['reviews']),
                'href'    	 => $this->url->link('product/product', 'product_id=' . $result['product_id']),
            );
        }

        return $product_list;
    }
}