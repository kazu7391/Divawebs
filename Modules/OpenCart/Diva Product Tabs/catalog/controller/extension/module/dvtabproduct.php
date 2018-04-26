<?php
class ControllerExtensionModuleDvtabproduct extends Controller
{
    public function index($setting) {
        $this->load->language('diva/module/dvtabproduct');

        $this->load->model('catalog/product');
        $this->load->model('diva/product');
        $this->load->model('tool/image');
        $this->load->model('diva/rotateimage');

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
        if (isset($setting['description']) && $setting['description']) {
            $data['show_description'] = true;
        } else {
            $data['show_description'] = false;
        }

        if(isset($setting['limit'])) {
            $limit = (int) $setting['limit'];
        } else {
            $limit = 10;
        }

        if (isset($setting['rows'])) {
            $rows = $setting['rows'];
        } else {
            $rows = 1;
        }

        if (isset($setting['items'])) {
            $items = $setting['items'];
        } else {
            $items = 4;
        }

        if (isset($setting['speed'])) {
            $speed = $setting['speed'];
        } else {
            $speed = 500;
        }

        if (isset($setting['auto']) && $setting['auto']) {
            $auto = true;
        } else {
            $auto = false;
        }

        if (isset($setting['navigation']) && $setting['navigation']) {
            $navigation = true;
        } else {
            $navigation = false;
        }

        if (isset($setting['pagination']) && $setting['pagination']) {
            $pagination = true;
        } else {
            $pagination = false;
        }

        $use_hover_image = $data['show_module_hover'];

        /* Get new product */
        $filter_data = array(
            'sort'  => 'p.date_added',
            'order' => 'DESC',
            'start' => 0,
            'limit' => 10
        );

        $new_results = $this->model_catalog_product->getProducts($filter_data);
        /* End */

        $product_tabs = array();

        if($setting['types']) {
            foreach($setting['types'] as $type) {
                if($type == "bestseller") {
                    $product_bestseller = array();

                    $bestseller_products = $this->model_catalog_product->getBestSellerProducts($limit);

                    if ($bestseller_products) {
                        foreach ($bestseller_products as $result) {
                            if ($result['image']) {
                                $image = $this->model_tool_image->resize($result['image'], $setting['width'], $setting['height']);
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

                            if($result['description']) {
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

                            $product_bestseller[] = array(
                                'product_id'    => $result['product_id'],
                                'thumb'   	    => $image,
                                'rotate_image'  => $rotate_image,
                                'name'    	    => $result['name'],
                                'price'   	    => $price,
                                'special' 	    => $special,
                                'is_new'        => $is_new,
                                'rating'        => $rating,
                                'description'   => $description,
                                'href'    	    => $this->url->link('product/product', 'product_id=' . $result['product_id']),
                            );
                        }
                    }

                    $product_tabs[] = array(
                        'id' => 'bestseller_product',
                        'name' => $this->language->get('text_bestseller'),
                        'productInfo' => $product_bestseller
                    );
                }

                if($type == "mostviewed") {
                    $product_mostviewed = array();

                    $mostviewed_products = $this->model_diva_product->getMostViewed($limit);

                    if ($mostviewed_products) {
                        foreach ($mostviewed_products as $result) {
                            if ($result['image']) {
                                $image = $this->model_tool_image->resize($result['image'], $setting['width'], $setting['height']);
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

                            if($result['description']) {
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

                            $product_mostviewed[] = array(
                                'product_id'    => $result['product_id'],
                                'thumb'   	    => $image,
                                'rotate_image'  => $rotate_image,
                                'name'    	    => $result['name'],
                                'price'   	    => $price,
                                'special' 	    => $special,
                                'is_new'        => $is_new,
                                'rating'        => $rating,
                                'description'   => $description,
                                'href'    	    => $this->url->link('product/product', 'product_id=' . $result['product_id']),
                            );
                        }
                    }

                    $product_tabs[] = array(
                        'id' => 'mostviewed_product',
                        'name' => $this->language->get('text_mostviewed'),
                        'productInfo' => $product_mostviewed
                    );
                }

                if($type == "random") {
                    $product_random = array();

                    $random_products = $this->model_diva_product->getRandom($limit);

                    foreach ($random_products as $result) {
                        if ($result['image']) {
                            $image = $this->model_tool_image->resize($result['image'], $setting['width'], $setting['height']);
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

                        if($result['description']) {
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

                        $product_random[] = array(
                            'product_id'    => $result['product_id'],
                            'thumb'   	    => $image,
                            'rotate_image'  => $rotate_image,
                            'name'    	    => $result['name'],
                            'price'   	    => $price,
                            'special' 	    => $special,
                            'is_new'        => $is_new,
                            'rating'        => $rating,
                            'description'   => $description,
                            'href'    	    => $this->url->link('product/product', 'product_id=' . $result['product_id']),
                        );
                    }

                    $product_tabs[] = array(
                        'id' => 'random_product',
                        'name' => $this->language->get('text_random'),
                        'productInfo' => $product_random
                    );
                }

                if($type == "special") {
                    $product_special = array();

                    $filter_data = array(
                        'start' => 0,
                        'limit' => $limit
                    );

                    $results = $this->model_catalog_product->getProductSpecials($filter_data);

                    if ($results) {
                        foreach ($results as $result) {
                            if ($result['image']) {
                                $image = $this->model_tool_image->resize($result['image'], $setting['width'], $setting['height']);
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

                            if($result['description']) {
                                $description = utf8_substr(trim(strip_tags(html_entity_decode($result['description'], ENT_QUOTES, 'UTF-8'))), 0, $this->config->get('theme_' . $this->config->get('config_theme') . '_product_description_length')) . '..';
                            } else {
                                $description = false;
                            }

                            $is_new = false;
                            if ($new_results) {
                                foreach($new_results as $new_product) {
                                    if($result['product_id'] == $new_product['product_id']) {
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

                            $product_special[] = array(
                                'product_id'    => $result['product_id'],
                                'thumb'   	    => $image,
                                'rotate_image'  => $rotate_image,
                                'name'    	    => $result['name'],
                                'price'   	    => $price,
                                'special' 	    => $special,
                                'is_new'        => $is_new,
                                'rating'        => $rating,
                                'description'   => $description,
                                'href'    	    => $this->url->link('product/product', 'product_id=' . $result['product_id']),
                            );
                        }
                    }

                    $product_tabs[] = array(
                        'id' => 'special_product',
                        'name' => $this->language->get('text_special'),
                        'productInfo' => $product_special
                    );
                }

                if($type == "latest") {
                    $product_latest = array();

                    $results = $this->model_catalog_product->getLatestProducts($limit);

                    if($results) {
                        foreach ($results as $result) {
                            if ($result['image']) {
                                $image = $this->model_tool_image->resize($result['image'], $setting['width'], $setting['height']);
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

                            if($result['description']) {
                                $description = utf8_substr(trim(strip_tags(html_entity_decode($result['description'], ENT_QUOTES, 'UTF-8'))), 0, $this->config->get('theme_' . $this->config->get('config_theme') . '_product_description_length')) . '..';
                            } else {
                                $description = false;
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

                            $product_latest[] = array(
                                'product_id'    => $result['product_id'],
                                'thumb'   	    => $image,
                                'rotate_image'  => $rotate_image,
                                'name'    	    => $result['name'],
                                'is_new'        => false,
                                'price'   	    => $price,
                                'special' 	    => $special,
                                'rating'        => $rating,
                                'description'   => $description,
                                'href'    	    => $this->url->link('product/product', 'product_id=' . $result['product_id']),
                            );
                        }
                    }

                    $product_tabs[] = array(
                        'id' => 'latest_product',
                        'name' => $this->language->get('text_latest'),
                        'productInfo' => $product_latest
                    );
                }
            }
        }

        $data['slide'] = array(
            'auto'  => $auto,
            'rows'  => $rows,
            'navigation' => $navigation,
            'pagination' => $pagination,
            'speed' => $speed,
            'items' => $items
        );

        $data['product_tabs'] = $product_tabs;

        return $this->load->view('diva/module/dvtabproduct', $data);
    }
}