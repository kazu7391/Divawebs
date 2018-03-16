<?php
class ControllerDivaSlider extends Controller
{
    public function index() {
        $this->language->load('extension/module/dvslider');

        $this->document->setTitle($this->language->get('page_title'));

        $this->load->model('diva/slider');

        $this->getList();
    }

    public function insert() {
        $this->language->load('extension/module/dvslider');

        $this->document->setTitle($this->language->get('page_title'));

        $this->load->model('diva/slider');

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
            $this->model_diva_slider->addSlider($this->request->post);

            $this->session->data['success'] = $this->language->get('text_success');

            $url = '';

            if (isset($this->request->get['sort'])) {
                $url .= '&sort=' . $this->request->get['sort'];
            }

            if (isset($this->request->get['order'])) {
                $url .= '&order=' . $this->request->get['order'];
            }

            if (isset($this->request->get['page'])) {
                $url .= '&page=' . $this->request->get['page'];
            }

            $this->response->redirect($this->url->link('diva/slider', 'user_token=' . $this->session->data['user_token'] . $url, true));
        }

        $this->getForm();
    }

    public function update() {
        $this->language->load('extension/module/dvslider');

        $this->document->setTitle($this->language->get('page_title'));

        $this->load->model('diva/slider');

        if (($this->request->server['REQUEST_METHOD'] == 'POST') ) {
            $this->model_diva_slider->editSlider($this->request->get['dvslider_id'], $this->request->post);

            $this->session->data['success'] = $this->language->get('text_success');

            $url = '';

            if (isset($this->request->get['sort'])) {
                $url .= '&sort=' . $this->request->get['sort'];
            }

            if (isset($this->request->get['order'])) {
                $url .= '&order=' . $this->request->get['order'];
            }

            if (isset($this->request->get['page'])) {
                $url .= '&page=' . $this->request->get['page'];
            }

            $this->response->redirect($this->url->link('diva/slider', 'user_token=' . $this->session->data['user_token'] . $url, true));
        }

        $this->getForm();
    }

    public function copy() {
        $this->language->load('extension/module/dvslider');

        $this->document->setTitle($this->language->get('page_title'));

        $this->load->model('diva/slider');

        if (isset($this->request->post['selected']) && $this->validateDelete()) {
            foreach ($this->request->post['selected'] as $dvslider_id) {
                $this->model_diva_slider->copySlider($dvslider_id);
            }

            $this->session->data['success'] = $this->language->get('text_success');

            $url = '';

            if (isset($this->request->get['sort'])) {
                $url .= '&sort=' . $this->request->get['sort'];
            }

            if (isset($this->request->get['order'])) {
                $url .= '&order=' . $this->request->get['order'];
            }

            if (isset($this->request->get['page'])) {
                $url .= '&page=' . $this->request->get['page'];
            }

            $this->response->redirect($this->url->link('diva/slider', 'user_token=' . $this->session->data['user_token'] . $url, true));
        }

        $this->getList();
    }

    public function delete() {
        $this->language->load('extension/module/dvslider');

        $this->document->setTitle($this->language->get('page_title'));

        $this->load->model('diva/slider');

        if (isset($this->request->post['selected']) && $this->validateDelete()) {
            foreach ($this->request->post['selected'] as $dvslider_id) {
                $this->model_diva_slider->deleteSlider($dvslider_id);
            }

            $this->session->data['success'] = $this->language->get('text_success');

            $url = '';

            if (isset($this->request->get['sort'])) {
                $url .= '&sort=' . $this->request->get['sort'];
            }

            if (isset($this->request->get['order'])) {
                $url .= '&order=' . $this->request->get['order'];
            }

            if (isset($this->request->get['page'])) {
                $url .= '&page=' . $this->request->get['page'];
            }

            $this->response->redirect($this->url->link('diva/slider', 'user_token=' . $this->session->data['user_token'] . $url, true));
        }

        $this->getList();
    }

    public function getList() {
        if (isset($this->request->get['sort'])) {
            $sort = $this->request->get['sort'];
        } else {
            $sort = 'name';
        }

        if (isset($this->request->get['order'])) {
            $order = $this->request->get['order'];
        } else {
            $order = 'ASC';
        }

        if (isset($this->request->get['page'])) {
            $page = $this->request->get['page'];
        } else {
            $page = 1;
        }

        $url = '';

        if (isset($this->request->get['sort'])) {
            $url .= '&sort=' . $this->request->get['sort'];
        }

        if (isset($this->request->get['order'])) {
            $url .= '&order=' . $this->request->get['order'];
        }

        if (isset($this->request->get['page'])) {
            $url .= '&page=' . $this->request->get['page'];
        }

        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text'      => $this->language->get('text_home'),
            'href'      => $this->url->link('common/home', 'user_token=' . $this->session->data['user_token'], true)
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_banner_slider'),
            'href' => $this->url->link('diva/slider', 'user_token=' . $this->session->data['user_token'] . $url, true)
        );

        $data['insert'] = $this->url->link('diva/slider/insert', 'user_token=' . $this->session->data['user_token'] . $url, true);
        $data['delete'] = $this->url->link('diva/slider/delete', 'user_token=' . $this->session->data['user_token'] . $url, true);
        $data['copy'] = $this->url->link('diva/slider/copy', 'user_token=' . $this->session->data['user_token'] . $url, true);

        $data['dvsliders']= array();

        $filter_data = array(
            'sort'  => $sort,
            'order' => $order,
            'start' => ($page - 1) * $this->config->get('config_limit_admin'),
            'limit' => $this->config->get('config_limit_admin')
        );

        $sliders_total = $this->model_diva_slider->getTotalSliders();

        $results = $this->model_diva_slider->getSliders($filter_data);

        foreach ($results as $result) {
            $action = array();

            $action[] = array(
                'text' => $this->language->get('text_edit'),
                'href' => $this->url->link('diva/slider/update', 'user_token=' . $this->session->data['user_token'] . '&dvslider_id=' . $result['dvslider_id'] . $url, true)
            );

            $data['dvsliders'][] = array(
                'dvslider_id' => $result['dvslider_id'],
                'name'      => $result['name'],
                'status'    => ($result['status'] ? $this->language->get('text_enabled') : $this->language->get('text_disabled')),
                'selected'  => isset($this->request->post['selected']) && in_array($result['dvslider_id'], $this->request->post['selected']),
                'action'    => $action
            );
        }

        if (isset($this->error['warning'])) {
            $data['error_warning'] = $this->error['warning'];
        } else {
            $data['error_warning'] = '';
        }

        if (isset($this->session->data['success'])) {
            $data['success'] = $this->session->data['success'];

            unset($this->session->data['success']);
        } else {
            $data['success'] = '';
        }

        $url = '';

        if ($order == 'ASC') {
            $url .= '&order=DESC';
        } else {
            $url .= '&order=ASC';
        }

        if (isset($this->request->get['page'])) {
            $url .= '&page=' . $this->request->get['page'];
        }

        $data['sort_name'] = $this->url->link('diva/slider', 'user_token=' . $this->session->data['user_token'] . '&sort=name' . $url, true);
        $data['sort_status'] = $this->url->link('diva/slider', 'user_token=' . $this->session->data['user_token'] . '&sort=status' . $url, true);

        $url = '';

        if (isset($this->request->get['sort'])) {
            $url .= '&sort=' . $this->request->get['sort'];
        }

        if (isset($this->request->get['order'])) {
            $url .= '&order=' . $this->request->get['order'];
        }

        $pagination = new Pagination();
        $pagination->total = $sliders_total;
        $pagination->page = $page;
        $pagination->limit = $this->config->get('config_limit_admin');
        $pagination->text = $this->language->get('text_pagination');
        $pagination->url = $this->url->link('diva/slider', 'user_token=' . $this->session->data['user_token'] . $url . '&page={page}', true);

        $data['pagination'] = $pagination->render();
        $data['results'] = sprintf($this->language->get('text_pagination'), ($sliders_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($sliders_total - $this->config->get('config_limit_admin'))) ? $sliders_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $sliders_total, ceil($sliders_total / $this->config->get('config_limit_admin')));
        $data['sort'] = $sort;
        $data['order'] = $order;

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('diva/slider/list', $data));
    }

    protected function getForm() {
        if (isset($this->error['warning'])) {
            $data['error_warning'] = $this->error['warning'];
        } else {
            $data['error_warning'] = '';
        }

        if (isset($this->error['name'])) {
            $data['error_name'] = $this->error['name'];
        } else {
            $data['error_name'] = '';
        }

        if (isset($this->error['delay'])) {
            $data['error_delay'] = $this->error['delay'];
        } else {
            $data['error_delay'] = '';
        }

        $url = '';

        if (isset($this->request->get['sort'])) {
            $url .= '&sort=' . $this->request->get['sort'];
        }

        if (isset($this->request->get['order'])) {
            $url .= '&order=' . $this->request->get['order'];
        }

        if (isset($this->request->get['page'])) {
            $url .= '&page=' . $this->request->get['page'];
        }

        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text'      => $this->language->get('text_home'),
            'href'      => $this->url->link('common/home', 'user_token=' . $this->session->data['user_token'], true)
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_banner_slider'),
            'href' => $this->url->link('diva/slider', 'user_token=' . $this->session->data['user_token'], true)
        );

        if (!isset($this->request->get['dvslider_id'])) {
            $data['breadcrumbs'][] = array(
                'text'      => $this->language->get('heading_title'),
                'href'      => $this->url->link('diva/slider/insert', 'user_token=' . $this->session->data['user_token'] . $url, true)
            );

            $data['action'] = $this->url->link('diva/slider/insert', 'user_token=' . $this->session->data['user_token'] . $url, true);
        } else {
            $data['breadcrumbs'][] = array(
                'text'      => $this->language->get('heading_title'),
                'href'      => $this->url->link('diva/slider/update', 'user_token=' . $this->session->data['user_token'] . '&dvslider_id=' . $this->request->get['dvslider_id'] . $url, true)
            );

            $data['action'] = $this->url->link('diva/slider/update', 'user_token=' . $this->session->data['user_token'] . '&dvslider_id=' . $this->request->get['dvslider_id'] . $url, true);
        }

        $data['cancel'] = $this->url->link('diva/slider', 'user_token=' . $this->session->data['user_token'] . $url, true);

        if (isset($this->request->get['dvslider_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
            $slider_info = $this->model_diva_slider->getSlider($this->request->get['dvslider_id']);
        }

        $data['user_token'] = $this->session->data['user_token'];

        if (isset($this->request->post['name'])) {
            $data['name'] = $this->request->post['name'];
        } elseif (!empty($slider_info)) {
            $data['name'] = $slider_info['name'];
        } else {
            $data['name'] = '';
        }

        if (isset($this->request->post['effect'])) {
            $data['effect'] = $this->request->post['effect'];
        } elseif (!empty($slider_info)) {
            $data['effect'] = $slider_info['effect'];
        } else {
            $data['effect'] = '';
        }

        if (isset($this->request->post['status'])) {
            $data['status'] = $this->request->post['status'];
        } elseif (!empty($slider_info)) {
            $data['status'] = $slider_info['status'];
        } else {
            $data['status'] = true;
        }

        if (isset($this->request->post['auto'])) {
            $data['auto'] = $this->request->post['auto'];
        } elseif (!empty($slider_info)) {
            $data['auto'] = $slider_info['auto'];
        } else {
            $data['auto'] = true;
        }

        if (isset($this->request->post['effect'])) {
            $data['effect'] = $this->request->post['effect'];
        } elseif (!empty($slider_info)) {
            $data['effect'] = $slider_info['effect'];
        } else {
            $data['effect'] = 'random';
        }

        if (isset($this->request->post['delay'])) {
            $data['delay'] = $this->request->post['delay'];
        } elseif (!empty($slider_info)) {
            $data['delay'] = $slider_info['delay'];
        } else {
            $data['delay'] = '3000';
        }

        if (isset($this->request->post['hover'])) {
            $data['hover'] = $this->request->post['hover'];
        } elseif (!empty($slider_info)) {
            $data['hover'] = $slider_info['hover'];
        } else {
            $data['hover'] = true;
        }

        if (isset($this->request->post['nextback'])) {
            $data['nextback'] = $this->request->post['nextback'];
        } elseif (!empty($slider_info)) {
            $data['nextback'] = $slider_info['nextback'];
        } else {
            $data['nextback'] = true;
        }

        if (isset($this->request->post['contrl'])) {
            $data['contrl'] = $this->request->post['contrl'];
        } elseif (!empty($slider_info)) {
            $data['contrl'] = $slider_info['contrl'];
        } else {
            $data['contrl'] = true;
        }

        $this->load->model('localisation/language');

        $data['languages'] = $this->model_localisation_language->getLanguages();

        $this->load->model('tool/image');

        if (isset($this->request->post['dvslider_image'])) {
            $dvslider_images = $this->request->post['dvslider_image'];
        } elseif (isset($this->request->get['dvslider_id'])) {

            $dvslider_images = $this->model_diva_slider->getSliderImages($this->request->get['dvslider_id']);
        } else {
            $dvslider_images = array();
        }

        $data['dvslider_images'] = array();

        foreach ($dvslider_images as $key =>  $dvslider_image) {
            if ($dvslider_image['image'] && file_exists(DIR_IMAGE . $dvslider_image['image'])) {
                $image = $dvslider_image['image'];
            } else {
                $image = 'no_image.png';
            }

            if(isset($dvslider_image['slider_store'])) {
                $data['dvslider_images'][] = array(
                    'key' => $key,
                    'dvslider_image_description' => $dvslider_image['dvslider_image_description'],
                    'link'                       => $dvslider_image['link'],
                    'type'                       => $dvslider_image['type'],
                    'slider_store'               => explode(',',$dvslider_image['banner_store']),
                    'image'                      => $image,
                    'thumb'                      => $this->model_tool_image->resize($image, 100, 100)
                );
            } else {
                $data['dvslider_images'][] = array(
                    'key' => $key,
                    'dvslider_image_description' => $dvslider_image['dvslider_image_description'],
                    'link'                     => $dvslider_image['link'],
                    'type'                     => $dvslider_image['type'],
                    'image'                    => $image,
                    'thumb'                    => $this->model_tool_image->resize($image, 100, 100)
                );
            }
        }

        $effect_options =  array(
            array('value'=>'fade', 'label'=>'fade'),
            array('value'=>'slide', 'label'=>'slide'),
            array('value'=>'random', 'label'=>'random'),
            array('value'=>'sliceDown', 'label'=>'sliceDown'),
            array('value'=>'sliceDownLeft', 'label'=>'sliceDownLeft'),
            array('value'=>'sliceUp', 'label'=>'sliceUp'),
            array('value'=>'sliceUpLeft', 'label'=>'sliceUpLeft'),
            array('value'=>'sliceUpDown', 'label'=>'sliceUpDown'),
            array('value'=>'sliceUpDownLeft', 'label'=>'sliceUpDownLeft'),
            array('value'=>'fold', 'label'=>'fold'),
            array('value'=>'slideInRight', 'label'=>'slideInRight'),
            array('value'=>'slideInLeft', 'label'=>'slideInLeft'),
            array('value'=>'boxRandom', 'label'=>'boxRandom'),
            array('value'=>'boxRain', 'label'=>'boxRain'),
            array('value'=>'boxRainReverse', 'label'=>'boxRainReverse'),
            array('value'=>'boxRainGrow', 'label'=>'boxRainGrow'),
            array('value'=>'boxRainGrowReverse', 'label'=>'boxRainGrowReverse')
        );

        $data['effect_option']  = $effect_options ;

        $this->load->model('setting/store');

        $data['stores'] = $this->model_setting_store->getStores();
        $data['stores'][] = array(
            'store_id'  => 0,
            'name'      => 'Default Store'
        );
        
        $data['no_image'] = $this->model_tool_image->resize('no_image.png', 100, 100);
        $data['placeholder'] = $this->model_tool_image->resize('no_image.png', 100, 100);

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('diva/slider/form', $data));
    }

    protected function validateForm() {
        if (!$this->user->hasPermission('modify', 'diva/slider')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        if ((utf8_strlen($this->request->post['name']) < 3) || (utf8_strlen($this->request->post['name']) > 64)) {
            $this->error['name'] = $this->language->get('error_name');
        }

        if ($this->request->post['delay'] == '') {
            $this->error['delay'] = $this->language->get('error_delay');
        }

        if (!$this->error) {
            return true;
        } else {
            return false;
        }
    }

    protected function validateDelete() {
        if (!$this->user->hasPermission('modify', 'diva/slider')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        if (!$this->error) {
            return true;
        } else {
            return false;
        }
    }
}