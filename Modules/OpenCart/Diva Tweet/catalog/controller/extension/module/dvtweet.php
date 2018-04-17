<?php
class ControllerExtensionModuleDvtweet extends Controller
{
    public function index() {
        $this->load->language('diva/module/dvtweet');
        $data['heading_title'] = $this->language->get('heading_title');

        $data['dvtweet_user'] = $this->config->get('module_dvtweet_id');
        $data['dvtweet_limit'] = $this->config->get('module_dvtweet_limit');
        $data['dvtweet_consumer_key'] = $this->config->get('module_dvtweet_consumer_key');
        $data['dvtweet_consumer_secret'] = $this->config->get('module_dvtweet_consumer_secret');
        $data['dvtweet_access_token'] = $this->config->get('module_dvtweet_access_token');
        $data['dvtweet_access_token_secret'] = $this->config->get('module_dvtweet_access_token_secret');

        $show_time = (int) $this->config->get('module_dvtweet_show_time');

        if($show_time) {
            $data['show_time'] = true;
        } else {
            $data['show_time'] = false;
        }

        if (!empty($_SERVER['HTTPS'])) {
            // SSL connection
            $base_url = str_replace('http', 'https', $this->config->get('config_url'));
        } else {
            $base_url = $this->config->get('config_url');
        }

        $data['base_url'] = $base_url;

        return $this->load->view('diva/module/dvtweet', $data);
    }
}