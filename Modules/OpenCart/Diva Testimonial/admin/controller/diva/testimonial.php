<?php
class ControllerDivaTestimonial extends Controller
{
    private $error = array();

    public function index() {
        $this->load->language('diva/testimonial');
        $this->load->language('diva/adminmenu');

        $this->load->model('diva/testimonial');

        $this->getList();
    }

    public function insert() {
        $this->load->language('diva/testimonial');

        $this->load->model('diva/testimonial');

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
            $this->model_diva_testimonial->addTestimonial($this->request->post);
            $this->session->data['success'] = $this->language->get('text_success');

            $url = '';

            if (isset($this->request->get['page'])) {
                $url .= '&page=' . $this->request->get['page'];
            }

            if (isset($this->request->get['sort'])) {
                $url .= '&sort=' . $this->request->get['sort'];
            }

            if (isset($this->request->get['order'])) {
                $url .= '&order=' . $this->request->get['order'];
            }

            $this->response->redirect($this->url->link('diva/testimonial', 'user_token=' . $this->session->data['user_token'] . $url, true));
        }

        $this->getForm();
    }

    public function update() {
        $this->load->language('diva/testimonial');

        $this->load->model('diva/testimonial');

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
            $this->model_diva_testimonial->editTestimonial($this->request->get['testimonial_id'], $this->request->post);
            $this->session->data['success'] = $this->language->get('text_success');

            $url = '';

            if (isset($this->request->get['page'])) {
                $url .= '&page=' . $this->request->get['page'];
            }

            if (isset($this->request->get['sort'])) {
                $url .= '&sort=' . $this->request->get['sort'];
            }

            if (isset($this->request->get['order'])) {
                $url .= '&order=' . $this->request->get['order'];
            }

            $this->response->redirect($this->url->link('diva/testimonial', 'user_token=' . $this->session->data['user_token'] . $url, true));
        }

        $this->getForm();
    }

    public function delete() {
        $this->load->language('diva/testimonial');
        $this->load->language('diva/adminmenu');

        $this->load->model('diva/testimonial');

        if (isset($this->request->post['selected']) && $this->validateDelete()) {
            foreach ($this->request->post['selected'] as $testimonial_id) {
                $this->model_diva_testimonial->deleteTestimonial($testimonial_id);
            }

            $this->session->data['success'] = $this->language->get('text_success');

            $url = '';

            if (isset($this->request->get['page'])) {
                $url .= '&page=' . $this->request->get['page'];
            }

            if (isset($this->request->get['sort'])) {
                $url .= '&sort=' . $this->request->get['sort'];
            }

            if (isset($this->request->get['order'])) {
                $url .= '&order=' . $this->request->get['order'];
            }

            $this->response->redirect($this->url->link('diva/testimonial', 'user_token=' . $this->session->data['user_token'] . $url, true));
        }

        $this->getList();
    }

    protected function getList() {
        $this->document->setTitle($this->language->get('page_title'));

        if (isset($this->request->get['page'])) {
            $page = $this->request->get['page'];
        } else {
            $page = 1;
        }
        if (isset($this->request->get['sort'])) {
            $sort = $this->request->get['sort'];
        } else {
            $sort = 'td.customer_last_name';
        }

        if (isset($this->request->get['order'])) {
            $order = $this->request->get['order'];
        } else {
            $order = 'ASC';
        }

        $url = '';

        if (isset($this->request->get['page'])) {
            $url .= '&page=' . $this->request->get['page'];
        }

        if (isset($this->request->get['sort'])) {
            $url .= '&sort=' . $this->request->get['sort'];
        }

        if (isset($this->request->get['order'])) {
            $url .= '&order=' . $this->request->get['order'];
        }

        $data['testimonials'] = array();

        $data = array(
            'sort'            => $sort,
            'order'           => $order,
            'start'           => ($page - 1) * $this->config->get('config_limit_admin'),
            'limit'           => $this->config->get('config_limit_admin')
        );

        $testimonial_total = $this->model_diva_testimonial->getTotalTestimonials($data);

        $results = $this->model_diva_testimonial->getTestimonials($data);

        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text'      => $this->language->get('text_home'),
            'href'      => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true),
            'separator' => false
        );

        $data['breadcrumbs'][] = array(
            'text'      => $this->language->get('heading_title'),
            'href'      => $this->url->link('diva/testimonial', 'user_token=' . $this->session->data['user_token'], true),
            'separator' => ' / '
        );

        $data['insert'] = $this->url->link('diva/testimonial/insert', 'user_token=' . $this->session->data['user_token'] . $url, true);
        $data['delete'] = $this->url->link('diva/testimonial/delete', 'user_token=' . $this->session->data['user_token'] . $url, true);

        foreach ($results as $result) {
            $action = array();
            $action[] = array(
                'text' => $this->language->get('text_edit'),
                'href' => $this->url->link('diva/testimonial/update', 'user_token=' . $this->session->data['user_token'] . '&testimonial_id=' . $result['dvtestimonial_id'], true)
            );
            $data['testimonials'][] = array(
                'testimonial_id'    => $result['dvtestimonial_id'],
                'status'            => $result['status'],
                'customer_name'     => $result['customer_name'],
                'sort_order'        => $result['sort_order'],
                'status_text'       => ($result['status'] ? $this->language->get('text_enabled') : $this->language->get('text_disabled')),
                'selected'          => isset($this->request->post['selected']) && in_array($result['testimonial_id'], $this->request->post['selected']),
                'url'               => $this->url->link('diva/testimonial/update', 'user_token=' . $this->session->data['user_token'] . '&testimonial_id=' . $result['dvtestimonial_id'], true)
            );
        }

        $data['user_token'] = $this->session->data['user_token'];

        $url = '';

        if ($order == 'ASC') {
            $url .= '&order=DESC';
        } else {
            $url .= '&order=ASC';
        }

        if (isset($this->request->get['page'])) {
            $url .= '&page=' . $this->request->get['page'];
        }

        if (isset($this->request->get['sort'])) {
            $url .= '&sort=' . $this->request->get['sort'];
        }

        if (isset($this->request->get['order'])) {
            $url .= '&order=' . $this->request->get['order'];
        }

        $pagination = new Pagination();
        $pagination->total = $testimonial_total;
        $pagination->page = $page;
        $pagination->limit = $this->config->get('config_limit_admin');
        $pagination->text = $this->language->get('text_pagination');
        $pagination->url = $this->url->link('diva/testimonial', 'user_token=' . $this->session->data['user_token'] . $url . '&page={page}', true);

        $data['pagination'] = $pagination->render();
        $data['sort'] = $sort;
        $data['order'] = $order;
        $data['results'] = sprintf($this->language->get('text_pagination'), ($testimonial_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($testimonial_total - $this->config->get('config_limit_admin'))) ? $testimonial_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $testimonial_total, ceil($testimonial_total / $this->config->get('config_limit_admin')));

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

        $data['diva_menus'] = array();

        if($this->user->hasPermission('access', 'extension/module/dvcontrolpanel')) {
            $data['diva_menus'][] = array(
                'title'  => '<i class="a fa fa-magic"></i> ' . $this->language->get('text_control_panel'),
                'url'    => $this->url->link('extension/module/dvcontrolpanel', 'user_token=' . $this->session->data['user_token'], true),
                'active' => 0
            );
        }

        if($this->user->hasPermission('access', 'diva/module')) {
            $data['diva_menus'][] = array(
                'title'  => '<i class="a fa fa-puzzle-piece"></i> ' . $this->language->get('text_theme_module'),
                'url'    => $this->url->link('diva/module', 'user_token=' . $this->session->data['user_token'], true),
                'active' => 0
            );
        }

        if($this->user->hasPermission('access', 'diva/featuredcate')) {
            $data['diva_menus'][] = array(
                'title'  => '<i class="a fa fa-tag"></i> ' . $this->language->get('text_special_category'),
                'url'    => $this->url->link('diva/featuredcate', 'user_token=' . $this->session->data['user_token'], true),
                'active' => 0
            );
        }

        if($this->user->hasPermission('access', 'diva/ultimatemenu')) {
            $data['diva_menus'][] = array(
                'title'  => '<i class="a fa fa-bars"></i> ' . $this->language->get('text_ultimate_menu'),
                'url'    => $this->url->link('diva/ultimatemenu/menuList', 'user_token=' . $this->session->data['user_token'], true),
                'active' => 0
            );
        }

        if ($this->user->hasPermission('access', 'diva/blog')) {
            $blog_menu = array();

            if ($this->user->hasPermission('access', 'diva/blog/post')) {
                $blog_menu[] = array(
                    'title'  => $this->language->get('text_posts'),
                    'url'    => $this->url->link('diva/blog/post', 'user_token=' . $this->session->data['user_token'], true),
                    'active' => 0
                );
            }

            if ($this->user->hasPermission('access', 'diva/blog/list')) {
                $blog_menu[] = array(
                    'title'  => $this->language->get('text_posts_list'),
                    'url'    => $this->url->link('diva/blog/list', 'user_token=' . $this->session->data['user_token'], true),
                    'active' => 0
                );
            }

            if ($this->user->hasPermission('access', 'diva/blog/setting')) {
                $blog_menu[] = array(
                    'title'  => $this->language->get('text_blog_setting'),
                    'url'    => $this->url->link('diva/blog/setting', 'user_token=' . $this->session->data['user_token'], true),
                    'active' => 0
                );
            }

            if($blog_menu) {
                $data['diva_menus'][] = array(
                    'title'  => '<i class="a fa fa-ticket"></i> ' . $this->language->get('text_blog'),
                    'child'  => $blog_menu,
                    'active' => 0
                );
            }
        }

        if($this->user->hasPermission('access', 'diva/slider')) {
            $data['diva_menus'][] = array(
                'title'  => '<i class="a fa fa-film"></i> ' . $this->language->get('text_slider'),
                'url'    => $this->url->link('diva/slider', 'user_token=' . $this->session->data['user_token'], true),
                'active' => 0
            );
        }

        if($this->user->hasPermission('access', 'diva/testimonial')) {
            $data['diva_menus'][] = array(
                'title'  => '<i class="a fa fa-comment"></i> ' . $this->language->get('text_testimonial'),
                'url'    => $this->url->link('diva/testimonial', 'user_token=' . $this->session->data['user_token'], true),
                'active' => 1
            );
        }

        if($this->user->hasPermission('access', 'diva/newsletter')) {
            $data['diva_menus'][] = array(
                'title'  => '<i class="a fa fa-envelope"></i> ' . $this->language->get('text_newsletter'),
                'url'    => $this->url->link('diva/newsletter', 'user_token=' . $this->session->data['user_token'], true),
                'active' => 0
            );
        }

        $this->document->addStyle('view/stylesheet/divawebs/themeadmin.css');
        $this->document->addScript('view/javascript/divawebs/switch-toggle/js/bootstrap-toggle.min.js');
        $this->document->addStyle('view/javascript/divawebs/switch-toggle/css/bootstrap-toggle.min.css');

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('diva/testimonial/list', $data));
    }

    private function getForm() {
        $this->document->setTitle($this->language->get('page_title'));

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

        if (isset($this->error['description'])) {
            $data['error_description'] = $this->error['description'];
        } else {
            $data['error_description'] = '';
        }

        $url = '';

        if (isset($this->request->get['page'])) {
            $url .= '&page=' . $this->request->get['page'];
        }

        if (isset($this->request->get['sort'])) {
            $url .= '&sort=' . $this->request->get['sort'];
        }
        if (isset($this->request->get['order'])) {
            $url .= '&order=' . $this->request->get['order'];
        }

        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text'      => $this->language->get('text_home'),
            'href'      => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true),
            'separator' => false
        );

        $data['breadcrumbs'][] = array(
            'text'      => $this->language->get('heading_title'),
            'href'      => $this->url->link('diva/testimonial', 'user_token=' . $this->session->data['user_token'] . $url, true),
            'separator' => ' / '
        );


        if (!isset($this->request->get['testimonial_id'])) {
            $data['action'] = $this->url->link('diva/testimonial/insert', 'user_token=' . $this->session->data['user_token'], true);
        } else {
            $data['action'] = $this->url->link('diva/testimonial/update', 'user_token=' . $this->session->data['user_token'] . '&testimonial_id=' . $this->request->get['testimonial_id'], true);
        }

        $data['cancel'] = $this->url->link('diva/testimonial', 'user_token=' . $this->session->data['user_token'], true);

        $data['user_token'] = $this->session->data['user_token'];

        if (isset($this->request->get['testimonial_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
            $testimonial_info = $this->model_diva_testimonial->getTestimonial($this->request->get['testimonial_id']);
        }

        $this->load->model('localisation/language');

        $data['languages'] = $this->model_localisation_language->getLanguages();

        if (isset($this->request->post['testimonial_description'])) {
            $data['testimonial_description'] = $this->request->post['testimonial_description'];
        } elseif (isset($this->request->get['testimonial_id'])) {
            $data['testimonial_description'] = $this->model_diva_testimonial->getTestimonialDescriptions($this->request->get['testimonial_id']);
        } else {
            $data['testimonial_description'] = array();
        }

        if (isset($this->request->post['sort_order'])) {
            $data['sort_order'] = $this->request->post['sort_order'];
        } elseif (isset($testimonial_info) && !empty($testimonial_info)) {
            $data['sort_order'] = $testimonial_info['sort_order'];
        } else {
            $data['sort_order'] = 1;
        }

        if (isset($this->request->post['status'])) {
            $data['status'] = $this->request->post['status'];
        } else if (isset($testimonial_info) && !empty($testimonial_info)) {
            $data['status'] = $testimonial_info['status'];
        } else {
            $data['status'] = 1;
        }

        if (isset($this->request->post['image'])) {
            $data['image'] = $this->request->post['image'];
        } elseif ( isset($testimonial_info) && !empty($testimonial_info)) {
            $data['image'] = $testimonial_info['image'];
        } else {
            $data['image'] = '';
        }

        $this->load->model('tool/image');

        if (isset($this->request->post['image']) && is_file(DIR_IMAGE . $this->request->post['image'])) {
            $data['thumb'] = $this->model_tool_image->resize($this->request->post['image'], 100, 100);
        } elseif (!empty($testimonial_info) && is_file(DIR_IMAGE . $testimonial_info['image'])) {
            $data['thumb'] = $this->model_tool_image->resize($testimonial_info['image'], 100, 100);
        } else {
            $data['thumb'] = $this->model_tool_image->resize('no_image.png', 100, 100);
        }

        $data['placeholder'] = $this->model_tool_image->resize('no_image.png', 100, 100);

        $this->document->addStyle('view/stylesheet/divawebs/themeadmin.css');
        $this->document->addScript('view/javascript/divawebs/switch-toggle/js/bootstrap-toggle.min.js');
        $this->document->addStyle('view/javascript/divawebs/switch-toggle/css/bootstrap-toggle.min.css');
        
        $data['header'] = $this->load->controller('common/header');
        $data['footer'] = $this->load->controller('common/footer');
        $data['column_left'] = $this->load->controller('common/column_left');

        $this->response->setOutput($this->load->view('diva/testimonial/form', $data));
    }

    private function validateForm() {
        if (!$this->user->hasPermission('modify', 'diva/testimonial')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        $testimonial = $this->request->post['testimonial_description'];

        if (isset($testimonial['customer_name']) && (strlen(utf8_decode($testimonial['customer_name'])) < 3) || (strlen(utf8_decode($testimonial['customer_name'])) > 255)) {
            $this->error['customer_name']= $this->language->get('error_name');
        }
        if ((strlen(utf8_decode($testimonial['content'])) < 3)) {
            $this->error['content'] = $this->language->get('error_description');
        }


        if (!$this->error) {
            return TRUE;
        } else {
            if (!isset($this->error['warning'])) {
                $this->error['warning'] = $this->language->get('error_required_data');
            }
            return FALSE;
        }
    }

    private function validateDelete() {
        if (!$this->user->hasPermission('modify', 'diva/testimonial')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        if (!$this->error) {
            return TRUE;
        } else {
            return FALSE;
        }
    }
}