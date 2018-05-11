<?php
class ControllerExtensionModuleDvajaxlogin extends Controller
{
    private $error = array();

    public function install() {
        $config = array(
            'module_dvajaxlogin_status' => 1,
            'module_dvajaxlogin_loader_img' => 'diva/ajax-loader.gif'
        );
        $this->load->model('setting/setting');
        $this->model_setting_setting->editSetting('module_dvajaxlogin', $config);
    }

    public function index() {
        $this->load->language('extension/module/dvajaxlogin');

        $this->document->setTitle($this->language->get('page_title'));

        $this->load->model('setting/setting');
        $this->load->model('tool/image');
        $this->load->model('setting/module');

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
            $this->model_setting_setting->editSetting('module_dvajaxlogin', $this->request->post);

            $this->session->data['success'] = $this->language->get('text_success');

            $this->response->redirect($this->url->link('diva/module', 'user_token=' . $this->session->data['user_token'], true));
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
            'href' => $this->url->link('diva/module', 'user_token=' . $this->session->data['user_token'], true)
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link('extension/module/dvajaxlogin', 'user_token=' . $this->session->data['user_token'], true)
        );

        $data['action'] = $this->url->link('extension/module/dvajaxlogin', 'user_token=' . $this->session->data['user_token'], true);

        $data['cancel'] = $this->url->link('diva/module', 'user_token=' . $this->session->data['user_token'], true);

        if (isset($this->request->post['module_dvajaxlogin_status'])) {
            $data['module_dvajaxlogin_status'] = $this->request->post['module_dvajaxlogin_status'];
        } else {
            $data['module_dvajaxlogin_status'] = $this->config->get('module_dvajaxlogin_status');
        }

        if (isset($this->request->post['module_dvajaxlogin_redirect_status'])) {
            $data['module_dvajaxlogin_redirect_status'] = $this->request->post['module_dvajaxlogin_redirect_status'];
        } else {
            $data['module_dvajaxlogin_redirect_status'] = $this->config->get('module_dvajaxlogin_redirect_status');
        }

        $this->document->addStyle('view/stylesheet/divawebs/themeadmin.css');
        $this->document->addScript('view/javascript/divawebs/switch-toggle/js/bootstrap-toggle.min.js');
        $this->document->addStyle('view/javascript/divawebs/switch-toggle/css/bootstrap-toggle.min.css');
        
        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('diva/module/dvajaxlogin', $data));
    }

    protected function validate() {
        if (!$this->user->hasPermission('modify', 'extension/module/dvajaxlogin')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        return !$this->error;
    }
}