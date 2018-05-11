<?php
class ControllerExtensionModuleDvtweet extends Controller
{
    private $error = array();

    public function index() {
        $this->load->language('extension/module/dvtweet');

        $this->document->setTitle($this->language->get('page_title'));

        $this->load->model('setting/setting');

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
            $this->model_setting_setting->editSetting('module_dvtweet', $this->request->post);

            $this->session->data['success'] = $this->language->get('text_success');

            $this->response->redirect($this->url->link('diva/module', 'user_token=' . $this->session->data['user_token'], true));
        }

        if (isset($this->error['warning'])) {
            $data['error_warning'] = $this->error['warning'];
        } else {
            $data['error_warning'] = '';
        }

        if (isset($this->error['id'])) {
            $data['error_id'] = $this->error['id'];
        } else {
            $data['error_id'] = '';
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
            'href' => $this->url->link('extension/module/dvtweet', 'user_token=' . $this->session->data['user_token'], true)
        );

        $data['action'] = $this->url->link('extension/module/dvtweet', 'user_token=' . $this->session->data['user_token'], true);

        $data['cancel'] = $this->url->link('diva/module', 'user_token=' . $this->session->data['user_token'], true);

        if (isset($this->request->post['module_dvtweet_status'])) {
            $data['module_dvtweet_status'] = $this->request->post['module_dvtweet_status'];
        } else {
            $data['module_dvtweet_status'] = $this->config->get('module_dvtweet_status');
        }

        if (isset($this->request->post['module_dvtweet_id'])) {
            $data['module_dvtweet_id'] = $this->request->post['module_dvtweet_id'];
        } else {
            $data['module_dvtweet_id'] = $this->config->get('module_dvtweet_id');
        }

        if (isset($this->request->post['module_dvtweet_limit'])) {
            $data['module_dvtweet_limit'] = $this->request->post['module_dvtweet_limit'];
        } else {
            $data['module_dvtweet_limit'] = $this->config->get('module_dvtweet_limit');
        }

        if (isset($this->request->post['module_dvtweet_show_time'])) {
            $data['module_dvtweet_show_time'] = $this->request->post['module_dvtweet_show_time'];
        } else {
            $data['module_dvtweet_show_time'] = $this->config->get('module_dvtweet_show_time');
        }

        if (isset($this->request->post['module_dvtweet_consumer_key'])) {
            $data['module_dvtweet_consumer_key'] = $this->request->post['module_dvtweet_consumer_key'];
        } else {
            $data['module_dvtweet_consumer_key'] = $this->config->get('module_dvtweet_consumer_key');
        }

        if (isset($this->request->post['module_dvtweet_consumer_secret'])) {
            $data['module_dvtweet_consumer_secret'] = $this->request->post['module_dvtweet_consumer_secret'];
        } else {
            $data['module_dvtweet_consumer_secret'] = $this->config->get('module_dvtweet_consumer_secret');
        }

        if (isset($this->request->post['module_dvtweet_access_token'])) {
            $data['module_dvtweet_access_token'] = $this->request->post['module_dvtweet_access_token'];
        } else {
            $data['module_dvtweet_access_token'] = $this->config->get('module_dvtweet_access_token');
        }

        if (isset($this->request->post['module_dvtweet_access_token_secret'])) {
            $data['module_dvtweet_access_token_secret'] = $this->request->post['module_dvtweet_access_token_secret'];
        } else {
            $data['module_dvtweet_access_token_secret'] = $this->config->get('module_dvtweet_access_token_secret');
        }

        $this->document->addStyle('view/stylesheet/divawebs/themeadmin.css');
        $this->document->addScript('view/javascript/divawebs/switch-toggle/js/bootstrap-toggle.min.js');
        $this->document->addStyle('view/javascript/divawebs/switch-toggle/css/bootstrap-toggle.min.css');

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('diva/module/dvtweet', $data));
    }

    public function validate() {
        if (!$this->user->hasPermission('modify', 'extension/module/dvtweet')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        if (!$this->request->post['module_dvtweet_id']) {
            $this->error['id'] = $this->language->get('error_id');
        }

        return !$this->error;
    }

    public function install() {
        $config = array(
            'module_dvtweet_id' => 'divawebs',
            'module_dvtweet_status' => 1,
            'module_dvtweet_limit' => 3,
            'module_dvtweet_show_time' => 1,
            'module_dvtweet_consumer_key' => '7aQm44MGRc1OUNsZv45retuYq',
            'module_dvtweet_consumer_secret' => 'YK0WGasUF5hq8SX7wSq7NZVQRinsOx3gx0YD0NYBIzYZVZZRGF',
            'module_dvtweet_access_token' => '952805191929090048-LYcMJq5IjALqXRsglkMAVpgX4a7qRUP',
            'module_dvtweet_access_token_secret' => 'zJ2nLNfbsZX3MmEjM275zHS3rMJWmbKEMSOWVULPVUwME'
        );

        $this->load->model('setting/setting');
        $this->model_setting_setting->editSetting('module_dvtweet', $config);
    }
}