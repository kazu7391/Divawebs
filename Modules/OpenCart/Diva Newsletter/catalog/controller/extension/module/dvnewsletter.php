<?php
class ControllerExtensionModuleDvnewsletter extends Controller
{
    public function index($setting) {
        $this->language->load('diva/module/dvnewsletter');

        $data = array();

        if (isset($setting['popup']) && $setting['popup']) {
            $data['popup'] = true;
        } else {
            $data['popup'] = false;
        }

        return $this->load->view('diva/module/dvnewsletter', $data);
    }
}