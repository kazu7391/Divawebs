<?php
class ControllerExtensionModuleDvajaxlogin extends Controller
{
    public function index() {
        $this->load->language('diva/module/dvajaxlogin');

        $enable_status = $this->config->get('module_dvajaxlogin_status');
        if($enable_status == '1') {
            $data['status'] = true;
        } else {
            $data['status'] = false;
        }

        $enable_redirect = $this->config->get('module_dvajaxlogin_redirect_status');
        if($enable_redirect == '1') {
            $data['redirect'] = true;
        } else {
            $data['redirect'] = false;
        }

        $store_id = $this->config->get('config_store_id');

        if (!empty($_SERVER['HTTPS'])) {
            // SSL connection
            $common_url = str_replace('http', 'https', $this->config->get('config_url'));
        } else {
            $common_url = $this->config->get('config_url');
        }

        if(isset($this->config->get('module_dvcontrolpanel_loader_img')[$store_id])) {
            $data['loader_img'] = $common_url . 'image/' . $this->config->get('module_dvcontrolpanel_loader_img')[$store_id];
        } else {
            $data['loader_img'] = $common_url . 'image/diva/ajax-loader.gif';;
        }

        if (file_exists(DIR_TEMPLATE . $this->config->get($this->config->get('config_theme') . '_directory') . '/stylesheet/diva/ajaxlogin/ajaxlogin.css')) {
            $this->document->addStyle('catalog/view/theme/'.$this->config->get($this->config->get('config_theme') . '_directory').'/stylesheet/diva/ajaxlogin/ajaxlogin.css');
        } else {
            $this->document->addStyle('catalog/view/theme/default/stylesheet/diva/ajaxlogin/ajaxlogin.css');
        }

        $this->document->addScript('catalog/view/javascript/diva/ajaxlogin/ajaxlogin.js');

        $data['ajax_login_content'] = $this->load->controller('diva/login');
        $data['ajax_register_content'] = $this->load->controller('diva/register');
        $data['ajax_success_content'] = $this->load->controller('diva/register/success');
        $data['ajax_logoutsuccess_content'] = $this->load->controller('diva/login/logoutSuccess');

        return $this->load->view('diva/module/dvajaxlogin', $data);
    }
}