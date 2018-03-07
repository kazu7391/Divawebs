<?php
class ControllerExtensionModuleDvmenu extends Controller
{
    private $error = array();

    public function install() {
        $this->load->model('diva/ultimatemenu');

        $this->model_diva_ultimatemenu->createMenuTable();
    }

    public function uninstall() {
        $this->load->model('diva/ultimatemenu');

        $this->model_diva_ultimatemenu->deleteMenuData();
    }

    public function index() {
        $this->load->language('extension/module/dvmenu');
    }
}