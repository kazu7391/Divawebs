<?php
class ControllerExtensionModuleDvnewsletter extends Controller
{
    public function index($setting) {
        $this->load->language('diva/module/dvnewsletter');

        $data = array();

        if (isset($setting['popup']) && $setting['popup']) {
            $data['popup'] = true;
        } else {
            $data['popup'] = false;
        }

        $this->document->addScript('catalog/view/javascript/diva/newsletter/mail.js');

        return $this->load->view('diva/module/dvnewsletter', $data);
    }
}