<?php
class ControllerDivaBlogList extends Controller
{
    private $error = array();

    public function index() {
        $this->load->language('diva/blog/list');
        $this->load->language('diva/adminmenu');

        $this->document->setTitle($this->language->get('page_title'));

        $this->load->model('diva/blog');

        $this->getList();
    }

    public function add() {
        $this->load->language('diva/blog/list');

        $this->document->setTitle($this->language->get('page_title'));

        $this->load->model('diva/blog');

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
            $post_list_id = $this->model_diva_blog->addPostList($this->request->post);
            $this->model_diva_blog->addPostToList($post_list_id, $this->request->post['post']);

            $this->session->data['success'] = $this->language->get('text_success');

            $url = '';

            if (isset($this->request->get['page'])) {
                $url .= '&page=' . $this->request->get['page'];
            }

            $this->response->redirect($this->url->link('diva/blog/list', 'user_token=' . $this->session->data['user_token'] . $url, true));
        }

        $this->getForm();
    }

    public function edit() {
        $this->load->language('diva/blog/list');

        $this->document->setTitle($this->language->get('page_title'));

        $this->load->model('diva/blog');

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
            $this->model_diva_blog->editPostList($this->request->get['post_list_id'], $this->request->post);

            $this->session->data['success'] = $this->language->get('text_success');

            $url = '';

            if (isset($this->request->get['page'])) {
                $url .= '&page=' . $this->request->get['page'];
            }

            $this->response->redirect($this->url->link('diva/blog/list', 'user_token=' . $this->session->data['user_token'] . $url, true));
        }

        $this->getForm();
    }

    public function delete() {
        $this->load->language('diva/blog/list');
        $this->load->language('diva/adminmenu');

        $this->document->setTitle($this->language->get('page_title'));

        $this->load->model('diva/blog');

        if (isset($this->request->post['selected']) && $this->validateCopy()) {
            foreach ($this->request->post['selected'] as $post_list_id) {
                $this->model_diva_blog->deletePostList($post_list_id);
            }

            $this->session->data['success'] = $this->language->get('text_success');

            $url = '';

            if (isset($this->request->get['page'])) {
                $url .= '&page=' . $this->request->get['page'];
            }

            $this->response->redirect($this->url->link('diva/blog/list', 'user_token=' . $this->session->data['user_token'] . $url, true));
        }

        $this->getList();
    }

    public function copy() {
        $this->load->language('diva/blog/list');
        $this->load->language('diva/adminmenu');

        $this->document->setTitle($this->language->get('page_title'));

        $this->load->model('diva/blog');

        if (isset($this->request->post['selected']) && $this->validateCopy()) {
            foreach ($this->request->post['selected'] as $post_list_id) {
                $this->model_diva_blog->copyPostList($post_list_id);
            }

            $this->session->data['success'] = $this->language->get('text_success');

            $url = '';

            if (isset($this->request->get['page'])) {
                $url .= '&page=' . $this->request->get['page'];
            }

            $this->response->redirect($this->url->link('diva/blog/list', 'user_token=' . $this->session->data['user_token'] . $url, true));
        }

        $this->getList();
    }

    public function getList() {
        $data = array();

        if (isset($this->request->get['page'])) {
            $page = $this->request->get['page'];
        } else {
            $page = 1;
        }

        $url = '';

        if (isset($this->request->get['page'])) {
            $url .= '&page=' . $this->request->get['page'];
        }

        if (isset($this->request->get['module_id'])) {
            $url .= '&module_id=' . $this->request->get['module_id'];
        }

        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_extension'),
            'href' => $this->url->link('diva/module', 'user_token=' . $this->session->data['user_token'], true)
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link('diva/blog/list', 'user_token=' . $this->session->data['user_token'] . $url, true)
        );

        $data['add'] = $this->url->link('diva/blog/list/add', 'user_token=' . $this->session->data['user_token'] . $url, true);
        $data['copy'] = $this->url->link('diva/blog/list/copy', 'user_token=' . $this->session->data['user_token'] . $url, true);
        $data['delete'] = $this->url->link('diva/blog/list/delete', 'user_token=' . $this->session->data['user_token'] . $url, true);

        $data['post_list'] = array();

        $filter_data = array(
            'start'           => ($page - 1) * $this->config->get('config_limit_admin'),
            'limit'           => $this->config->get('config_limit_admin')
        );

        $post_list_total = $this->model_diva_blog->getTotalPostList();

        $results = $this->model_diva_blog->getAllPostList($filter_data);

        foreach ($results as $result) {
            $data['post_list'][] = array(
                'post_list_id'  => $result['post_list_id'],
                'status'        => $result['status'],
                'name'          => $result['name'],
                'status_text'   => ($result['status']) ? $this->language->get('text_enabled') : $this->language->get('text_disabled'),
                'edit'          => $this->url->link('diva/blog/list/edit', 'user_token=' . $this->session->data['user_token'] . '&post_list_id=' . $result['post_list_id'] . $url, true)
            );
        }

        $data['user_token'] = $this->session->data['user_token'];

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

        if (isset($this->request->post['selected'])) {
            $data['selected'] = (array)$this->request->post['selected'];
        } else {
            $data['selected'] = array();
        }

        $url = '';

        if (isset($this->request->get['page'])) {
            $url .= '&page=' . $this->request->get['page'];
        }

        $pagination = new Pagination();
        $pagination->total = $post_list_total;
        $pagination->page = $page;
        $pagination->limit = $this->config->get('config_limit_admin');
        $pagination->url = $this->url->link('diva/blog/list', 'user_token=' . $this->session->data['user_token'] . $url . '&page={page}', true);

        $data['pagination'] = $pagination->render();

        $data['results'] = sprintf($this->language->get('text_pagination'), ($post_list_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($post_list_total - $this->config->get('config_limit_admin'))) ? $post_list_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $post_list_total, ceil($post_list_total / $this->config->get('config_limit_admin')));

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
                    'active' => 1
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
                    'active' => 1
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
                'active' => 0
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

        $this->response->setOutput($this->load->view('diva/blog/list/list', $data));
    }

    public function getForm() {
        $data['text_form'] = !isset($this->request->get['post_list_id']) ? $this->language->get('text_add') : $this->language->get('text_edit');

        if (isset($this->error['warning'])) {
            $data['error_warning'] = $this->error['warning'];
        } else {
            $data['error_warning'] = '';
        }

        if (isset($this->error['name'])) {
            $data['error_name'] = $this->error['name'];
        } else {
            $data['error_name'] = array();
        }

        $url = '';

        if (isset($this->request->get['page'])) {
            $url .= '&page=' . $this->request->get['page'];
        }

        if (isset($this->request->get['module_id'])) {
            $url .= '&module_id=' . $this->request->get['module_id'];
        }

        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_extension'),
            'href' => $this->url->link('diva/module', 'user_token=' . $this->session->data['user_token'], true)
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link('diva/blog/list', 'user_token=' . $this->session->data['user_token'] . $url, true)
        );

        if (!isset($this->request->get['post_list_id'])) {
            $data['action'] = $this->url->link('diva/blog/list/add', 'user_token=' . $this->session->data['user_token'] . $url, true);
        } else {
            $data['action'] = $this->url->link('diva/blog/list/edit', 'user_token=' . $this->session->data['user_token'] . '&post_list_id=' . $this->request->get['post_list_id'] . $url, true);
        }

        $data['cancel'] = $this->url->link('diva/blog/list', 'user_token=' . $this->session->data['user_token'] . $url, true);

        if (isset($this->request->get['post_list_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
            $post_list_info = $this->model_diva_blog->getPostList($this->request->get['post_list_id']);
            $post_list_info['post'] = $this->model_diva_blog->getPostToList($this->request->get['post_list_id']);
        }

        $data['user_token'] = $this->session->data['user_token'];

        if (isset($this->request->post['status'])) {
            $data['status'] = $this->request->post['status'];
        } elseif (!empty($post_list_info)) {
            $data['status'] = $post_list_info['status'];
        } else {
            $data['status'] = true;
        }

        if (isset($this->request->post['name'])) {
            $data['name'] = $this->request->post['name'];
        } elseif (!empty($post_list_info)) {
            $data['name'] = $post_list_info['name'];
        } else {
            $data['name'] = '';
        }

        $data['posts'] = array();

        if (isset($this->request->post['post'])) {
            $posts = $this->request->post['post'];
        } elseif (!empty($post_list_info)) {
            $posts = $post_list_info['post'];
        } else {
            $posts = array();
        }

        foreach ($posts as $post) {
            $post_info = $this->model_diva_blog->getPost($post['post_id']);

            if ($post_info) {
                $data['posts'][] = array(
                    'post_id' => $post_info['post_id'],
                    'name'       => $post_info['name']
                );
            }
        }

        $this->document->addStyle('view/stylesheet/divawebs/themeadmin.css');
        $this->document->addScript('view/javascript/divawebs/switch-toggle/js/bootstrap-toggle.min.js');
        $this->document->addStyle('view/javascript/divawebs/switch-toggle/css/bootstrap-toggle.min.css');

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('diva/blog/list/form', $data));
    }

    protected function validateForm() {
        if (!$this->user->hasPermission('modify', 'diva/blog/list')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        if ($this->error && !isset($this->error['warning'])) {
            $this->error['warning'] = $this->language->get('error_warning');
        }

        return !$this->error;
    }

    protected function validateDelete() {
        if (!$this->user->hasPermission('modify', 'diva/blog/list')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        return !$this->error;
    }

    protected function validateCopy() {
        if (!$this->user->hasPermission('modify', 'diva/blog/list')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        return !$this->error;
    }
}