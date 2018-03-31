<?php
class ControllerDivaBlogSetting extends Controller
{
    private $error = array();

    public function index() {
        $this->load->language('diva/blog/setting');

        $this->document->setTitle($this->language->get('page_title'));

        $this->load->model('setting/setting');
        $this->load->model('setting/store');
        $this->load->model('diva/blog');

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
            $this->model_setting_setting->editSetting('module_dvblog', $this->request->post);

            $blog_seo_url = $this->request->post['module_dvblog_seo_url'];

            $stores = array();

            $stores[] = array(
                'store_id' => 0,
                'name'     => $this->language->get('text_default')
            );

            $sts = $this->model_setting_store->getStores();

            foreach ($sts as $store) {
                $stores[] = array(
                    'store_id' => $store['store_id'],
                    'name'     => $store['name']
                );
            }

            $this->model_diva_blog->addBlogSeoUrl($blog_seo_url, $stores);

            $this->session->data['success'] = $this->language->get('text_success');

            $this->response->redirect($this->url->link('diva/blog/setting', 'user_token=' . $this->session->data['user_token'], true));
        }

        if(isset($this->session->data['success'])) {
            $data['success'] = $this->session->data['success'];
        }

        if (isset($this->error['warning'])) {
            $data['error_warning'] = $this->error['warning'];
        } else {
            $data['error_warning'] = '';
        }

        if (isset($this->error['post_limit'])) {
            $data['error_post_limit'] = $this->error['post_limit'];
        } else {
            $data['error_post_limit'] = '';
        }

        if (isset($this->error['meta_title'])) {
            $data['error_meta_title'] = $this->error['meta_title'];
        } else {
            $data['error_meta_title'] = '';
        }

        if (isset($this->error['meta_description'])) {
            $data['error_meta_description'] = $this->error['meta_description'];
        } else {
            $data['error_meta_description'] = '';
        }

        if (isset($this->error['meta_keyword'])) {
            $data['error_meta_keyword'] = $this->error['meta_keyword'];
        } else {
            $data['error_meta_keyword'] = '';
        }

        if (isset($this->error['blog_width'])) {
            $data['error_image_blog'] = $this->error['blog_width'];
        } else {
            $data['error_image_blog'] = '';
        }

        if (isset($this->error['blog_height'])) {
            $data['error_image_blog'] = $this->error['blog_height'];
        } else {
            $data['error_image_blog'] = '';
        }

        if (isset($this->error['post_width'])) {
            $data['error_image_post'] = $this->error['post_width'];
        } else {
            $data['error_image_post'] = '';
        }

        if (isset($this->error['post_height'])) {
            $data['error_image_post'] = $this->error['post_height'];
        } else {
            $data['error_image_post'] = '';
        }

        $this->load->model('localisation/language');

        $data['languages'] = $this->model_localisation_language->getLanguages();

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
            'href' => $this->url->link('diva/blog/setting', 'user_token=' . $this->session->data['user_token'], true)
        );

        $data['action'] = $this->url->link('diva/blog/setting', 'user_token=' . $this->session->data['user_token'], true);
        $data['cancel'] = $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true);

        if (isset($this->request->post['module_dvblog_post_limit'])) {
            $data['module_dvblog_post_limit'] = $this->request->post['module_dvblog_post_limit'];
        } else {
            $data['module_dvblog_post_limit'] = $this->config->get('module_dvblog_post_limit');
        }

        if (isset($this->request->post['module_dvblog_meta_title'])) {
            $data['module_dvblog_meta_title'] = $this->request->post['module_dvblog_meta_title'];
        } else {
            $data['module_dvblog_meta_title'] = $this->config->get('module_dvblog_meta_title');
        }

        if (isset($this->request->post['module_dvblog_meta_description'])) {
            $data['module_dvblog_meta_description'] = $this->request->post['module_dvblog_meta_description'];
        } else {
            $data['module_dvblog_meta_description'] = $this->config->get('module_dvblog_meta_description');
        }

        if (isset($this->request->post['module_dvblog_meta_keyword'])) {
            $data['module_dvblog_meta_keyword'] = $this->request->post['module_dvblog_meta_keyword'];
        } else {
            $data['module_dvblog_meta_keyword'] = $this->config->get('module_dvblog_meta_keyword');
        }

        if (isset($this->request->post['module_dvblog_blog_width'])) {
            $data['module_dvblog_blog_width'] = $this->request->post['module_dvblog_blog_width'];
        } else {
            $data['module_dvblog_blog_width'] = $this->config->get('module_dvblog_blog_width');
        }

        if (isset($this->request->post['module_dvblog_blog_height'])) {
            $data['module_dvblog_blog_height'] = $this->request->post['module_dvblog_blog_height'];
        } else {
            $data['module_dvblog_blog_height'] = $this->config->get('module_dvblog_blog_height');
        }

        if (isset($this->request->post['module_dvblog_post_width'])) {
            $data['module_dvblog_post_width'] = $this->request->post['module_dvblog_post_width'];
        } else {
            $data['module_dvblog_post_width'] = $this->config->get('module_dvblog_post_width');
        }

        if (isset($this->request->post['module_dvblog_post_height'])) {
            $data['module_dvblog_post_height'] = $this->request->post['module_dvblog_post_height'];
        } else {
            $data['module_dvblog_post_height'] = $this->config->get('module_dvblog_post_height');
        }

        if (isset($this->request->post['module_dvblog_seo_url'])) {
            $data['module_dvblog_seo_url'] = $this->request->post['module_dvblog_seo_url'];
        } else {
            $data['module_dvblog_seo_url'] = $this->config->get('module_dvblog_seo_url');
        }

        $this->document->addStyle('view/stylesheet/divawebs/themeadmin.css');

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('diva/blog/setting', $data));
    }

    public function validate() {
        if (!$this->user->hasPermission('modify', 'diva/blog/setting')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        if (!$this->request->post['module_dvblog_post_limit']) {
            $this->error['post_limit'] = $this->language->get('error_post_limit');
        }

        if (!$this->request->post['module_dvblog_meta_title']) {
            $this->error['meta_title'] = $this->language->get('error_meta_title');
        }

        if (!$this->request->post['module_dvblog_meta_description']) {
            $this->error['meta_description'] = $this->language->get('error_meta_description');
        }

        if (!$this->request->post['module_dvblog_meta_keyword']) {
            $this->error['meta_keyword'] = $this->language->get('error_meta_keyword');
        }

        if (!$this->request->post['module_dvblog_blog_width']) {
            $this->error['blog_width'] = $this->language->get('error_image_blog');
        }

        if (!$this->request->post['module_dvblog_blog_height']) {
            $this->error['blog_height'] = $this->language->get('error_image_blog');
        }

        if (!$this->request->post['module_dvblog_post_width']) {
            $this->error['post_width'] = $this->language->get('error_image_post');
        }

        if (!$this->request->post['module_dvblog_post_height']) {
            $this->error['post_height'] = $this->language->get('error_image_post');
        }

        if ($this->error && !isset($this->error['warning'])) {
            $this->error['warning'] = $this->language->get('error_warning');
        }

        return !$this->error;
    }
}