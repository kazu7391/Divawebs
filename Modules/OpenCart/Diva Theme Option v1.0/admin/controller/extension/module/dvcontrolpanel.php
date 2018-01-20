<?php
class ControllerExtensionModuleDvcontrolpanel extends Controller
{
    private $error = array();

    public function index() {
        $this->load->language('extension/module/dvcontrolpanel');

        $this->document->setTitle($this->language->get('page_title'));

        $this->load->model('setting/setting');

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
            $this->model_setting_setting->editSetting('module_dvcontrolpanel', $this->request->post);

            $this->session->data['success'] = $this->language->get('text_success');

            $this->response->redirect($this->url->link('extension/module/dvcontrolpanel', 'user_token=' . $this->session->data['user_token'], true));
        }

        $this->load->model('setting/store');

        $data['stores'] = array();

        $data['stores'][] = array(
            'store_id' => 0,
            'name'     => $this->config->get('config_name') . $this->language->get('text_default')
        );

        $stores = $this->model_setting_store->getStores();

        foreach ($stores as $store) {
            $data['stores'][] = array(
                'store_id' => $store['store_id'],
                'name'     => $store['name']
            );
        }

        $this->load->model('catalog/option');

        $data['options'] = array();

        $results = $this->model_catalog_option->getOptions();

        foreach ($results as $result) {
            $data['options'][] = array(
                'option_id'  => $result['option_id'],
                'type'       => $result['type'],
                'name'       => $result['name']
            );
        }

        if(isset($this->session->data['success'])) {
            $data['success'] = $this->session->data['success'];

            unset($this->session->data['success']);
        } else {
            $data['success'] = false;
        }

        if(isset($this->session->data['error_load_file'])) {
            $data['error_load_file'] = $this->session->data['error_load_file'];

            unset($this->session->data['error_load_file']);
        } else {
            $data['error_load_file'] = false;
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
            'href' => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true)
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link('extension/module/dvcontrolpanel', 'user_token=' . $this->session->data['user_token'], true)
        );

        $data['database'] = array(
            DIR_APPLICATION . '../divadata/diva_db1.sql' => 'Layout 1',
            DIR_APPLICATION . '../divadata/diva_db2.sql' => 'Layout 2'
        );

        $data['user_token'] = $this->session->data['user_token'];

        $data['action'] = $this->url->link('extension/module/dvcontrolpanel', 'user_token=' . $this->session->data['user_token'], true);
        $data['action_import'] = $this->url->link('extension/module/dvcontrolpanel/import', 'user_token=' . $this->session->data['user_token'], true);

        $data['cancel'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true);

        if (isset($this->request->post['module_dvcontrolpanel_status'])) {
            $data['module_dvcontrolpanel_status'] = $this->request->post['module_dvcontrolpanel_status'];
        } else {
            $data['module_dvcontrolpanel_status'] = $this->config->get('module_dvcontrolpanel_status');
        }

        if (isset($this->request->post['module_dvcontrolpanel_a_tag'])) {
            $data['module_dvcontrolpanel_a_tag'] = $this->request->post['module_dvcontrolpanel_a_tag'];
        } else {
            $data['module_dvcontrolpanel_a_tag'] = $this->config->get('module_dvcontrolpanel_a_tag');
        }

        if (isset($this->request->post['module_dvcontrolpanel_header_tag'])) {
            $data['module_dvcontrolpanel_header_tag'] = $this->request->post['module_dvcontrolpanel_header_tag'];
        } else {
            $data['module_dvcontrolpanel_header_tag'] = $this->config->get('module_dvcontrolpanel_header_tag');
        }

        if (isset($this->request->post['module_dvcontrolpanel_body'])) {
            $data['module_dvcontrolpanel_body'] = $this->request->post['module_dvcontrolpanel_body'];
        } else {
            $data['module_dvcontrolpanel_body'] = $this->config->get('module_dvcontrolpanel_body');
        }

        if (isset($this->request->post['module_dvcontrolpanel_catalog'])) {
            $data['module_dvcontrolpanel_catalog'] = $this->request->post['module_dvcontrolpanel_catalog'];
        } else {
            $data['module_dvcontrolpanel_catalog'] = $this->config->get('module_dvcontrolpanel_catalog');
        }

        if (isset($this->request->post['module_dvcontrolpanel_rotator'])) {
            $data['module_dvcontrolpanel_rotator'] = $this->request->post['module_dvcontrolpanel_rotator'];
        } else {
            $data['module_dvcontrolpanel_rotator'] = $this->config->get('module_dvcontrolpanel_rotator');
        }

        if (isset($this->request->post['module_dvcontrolpanel_quickview'])) {
            $data['module_dvcontrolpanel_quickview'] = $this->request->post['module_dvcontrolpanel_quickview'];
        } else {
            $data['module_dvcontrolpanel_quickview'] = $this->config->get('module_dvcontrolpanel_quickview');
        }

        if (isset($this->request->post['module_dvcontrolpanel_loader_img'])) {
            $data['module_dvcontrolpanel_loader_img'] = $this->request->post['module_dvcontrolpanel_loader_img'];
        } else {
            $data['module_dvcontrolpanel_loader_img'] = $this->config->get('module_dvcontrolpanel_loader_img');
        }

        if (isset($this->request->post['module_dvcontrolpanel_use_swatches'])) {
            $data['module_dvcontrolpanel_use_swatches'] = $this->request->post['module_dvcontrolpanel_use_swatches'];
        } else {
            $data['module_dvcontrolpanel_use_swatches'] = $this->config->get('module_dvcontrolpanel_use_swatches');
        }

        if (isset($this->request->post['module_dvcontrolpanel_swatches_width'])) {
            $data['module_dvcontrolpanel_swatches_width'] = $this->request->post['module_dvcontrolpanel_swatches_width'];
        } else {
            $data['module_dvcontrolpanel_swatches_width'] = $this->config->get('module_dvcontrolpanel_swatches_width');
        }

        if (isset($this->request->post['module_dvcontrolpanel_swatches_height'])) {
            $data['module_dvcontrolpanel_swatches_height'] = $this->request->post['module_dvcontrolpanel_swatches_height'];
        } else {
            $data['module_dvcontrolpanel_swatches_height'] = $this->config->get('module_dvcontrolpanel_swatches_height');
        }

        if (isset($this->request->post['module_dvcontrolpanel_swatches_option'])) {
            $data['module_dvcontrolpanel_swatches_option'] = $this->request->post['module_dvcontrolpanel_swatches_option'];
        } else {
            $data['module_dvcontrolpanel_swatches_option'] = $this->config->get('module_dvcontrolpanel_swatches_option');
        }

        if (isset($this->request->post['module_dvcontrolpanel_use_zoom'])) {
            $data['module_dvcontrolpanel_use_zoom'] = $this->request->post['module_dvcontrolpanel_use_zoom'];
        } else {
            $data['module_dvcontrolpanel_use_zoom'] = $this->config->get('module_dvcontrolpanel_use_zoom');
        }

        if (isset($this->request->post['module_dvcontrolpanel_zoom_position'])) {
            $data['module_dvcontrolpanel_zoom_position'] = $this->request->post['module_dvcontrolpanel_zoom_position'];
        } else {
            $data['module_dvcontrolpanel_zoom_position'] = $this->config->get('module_dvcontrolpanel_zoom_position');
        }

        if (isset($this->request->post['module_dvcontrolpanel_zoom_space'])) {
            $data['module_dvcontrolpanel_zoom_space'] = $this->request->post['module_dvcontrolpanel_zoom_space'];
        } else {
            $data['module_dvcontrolpanel_zoom_space'] = $this->config->get('module_dvcontrolpanel_zoom_space');
        }

        if (isset($this->request->post['module_dvcontrolpanel_zoom_background_status'])) {
            $data['module_dvcontrolpanel_zoom_background_status'] = $this->request->post['module_dvcontrolpanel_zoom_background_status'];
        } else {
            $data['module_dvcontrolpanel_zoom_background_status'] = $this->config->get('module_dvcontrolpanel_zoom_background_status');
        }

        if (isset($this->request->post['module_dvcontrolpanel_zoom_background_color'])) {
            $data['module_dvcontrolpanel_zoom_background_color'] = $this->request->post['module_dvcontrolpanel_zoom_background_color'];
        } else {
            $data['module_dvcontrolpanel_zoom_background_color'] = $this->config->get('module_dvcontrolpanel_zoom_background_color');
        }

        if (isset($this->request->post['module_dvcontrolpanel_zoom_background_opacity'])) {
            $data['module_dvcontrolpanel_zoom_background_opacity'] = $this->request->post['module_dvcontrolpanel_zoom_background_opacity'];
        } else {
            $data['module_dvcontrolpanel_zoom_background_opacity'] = $this->config->get('module_dvcontrolpanel_zoom_background_opacity');
        }

        if (isset($this->request->post['module_dvcontrolpanel_zoom_title'])) {
            $data['module_dvcontrolpanel_zoom_title'] = $this->request->post['module_dvcontrolpanel_zoom_title'];
        } else {
            $data['module_dvcontrolpanel_zoom_title'] = $this->config->get('module_dvcontrolpanel_zoom_title');
        }

        if (isset($this->request->post['module_dvcontrolpanel_custom_view'])) {
            $data['module_dvcontrolpanel_custom_view'] = $this->request->post['module_dvcontrolpanel_custom_view'];
        } else {
            $data['module_dvcontrolpanel_custom_view'] = $this->config->get('module_dvcontrolpanel_custom_view');
        }

        if (isset($this->request->post['module_dvcontrolpanel_category_view'])) {
            $data['module_dvcontrolpanel_category_view'] = $this->request->post['module_dvcontrolpanel_category_view'];
        } else {
            $data['module_dvcontrolpanel_category_view'] = $this->config->get('module_dvcontrolpanel_category_view');
        }

        if (isset($this->request->post['module_dvcontrolpanel_grid_columns'])) {
            $data['module_dvcontrolpanel_grid_columns'] = $this->request->post['module_dvcontrolpanel_grid_columns'];
        } else {
            $data['module_dvcontrolpanel_grid_columns'] = $this->config->get('module_dvcontrolpanel_grid_columns');
        }

        if (isset($this->request->post['module_dvcontrolpanel_use_layered'])) {
            $data['module_dvcontrolpanel_use_layered'] = $this->request->post['module_dvcontrolpanel_use_layered'];
        } else {
            $data['module_dvcontrolpanel_use_layered'] = $this->config->get('module_dvcontrolpanel_use_layered');
        }

        if (isset($this->request->post['module_dvcontrolpanel_layered_column'])) {
            $data['module_dvcontrolpanel_layered_column'] = $this->request->post['module_dvcontrolpanel_layered_column'];
        } else {
            $data['module_dvcontrolpanel_layered_column'] = $this->config->get('module_dvcontrolpanel_layered_column');
        }

        if (isset($this->request->post['module_dvcontrolpanel_use_cate_quickview'])) {
            $data['module_dvcontrolpanel_use_cate_quickview'] = $this->request->post['module_dvcontrolpanel_use_cate_quickview'];
        } else {
            $data['module_dvcontrolpanel_use_cate_quickview'] = $this->config->get('module_dvcontrolpanel_use_cate_quickview');
        }

        if (isset($this->request->post['module_dvcontrolpanel_image_effect'])) {
            $data['module_dvcontrolpanel_image_effect'] = $this->request->post['module_dvcontrolpanel_image_effect'];
        } else {
            $data['module_dvcontrolpanel_image_effect'] = $this->config->get('module_dvcontrolpanel_image_effect');
        }

        if (isset($this->request->post['module_dvcontrolpanel_cate_swatches_width'])) {
            $data['module_dvcontrolpanel_cate_swatches_width'] = $this->request->post['module_dvcontrolpanel_cate_swatches_width'];
        } else {
            $data['module_dvcontrolpanel_cate_swatches_width'] = $this->config->get('module_dvcontrolpanel_cate_swatches_width');
        }

        if (isset($this->request->post['module_dvcontrolpanel_cate_swatches_height'])) {
            $data['module_dvcontrolpanel_cate_swatches_height'] = $this->request->post['module_dvcontrolpanel_cate_swatches_height'];
        } else {
            $data['module_dvcontrolpanel_cate_swatches_height'] = $this->config->get('module_dvcontrolpanel_cate_swatches_height');
        }

        $this->load->model('tool/image');

        if (isset($this->request->post['module_dvcontrolpanel_loader_img']) && is_file(DIR_IMAGE . $this->request->post['module_dvcontrolpanel_loader_img'])) {
            $data['thumb'] = $this->model_tool_image->resize($this->request->post['module_dvcontrolpanel_loader_img'], 50, 50);
        } elseif (is_file(DIR_IMAGE . $this->config->get('module_dvcontrolpanel_loader_img'))) {
            $data['thumb'] = $this->model_tool_image->resize($this->config->get('module_dvcontrolpanel_loader_img'), 50, 50);
        } else {
            $data['thumb'] = $this->model_tool_image->resize('no_image.png', 50, 50);
        }

        $data['placeholder'] = $this->model_tool_image->resize('no_image.png', 50, 50);

        $this->document->addScript('view/javascript/divawebs/jscolor.js');
        $this->document->addStyle('view/stylesheet/divawebs/themeadmin.css');
        $this->document->addScript('view/javascript/divawebs/switch-toggle/js/bootstrap-toggle.min.js');
        $this->document->addStyle('view/javascript/divawebs/switch-toggle/css/bootstrap-toggle.min.css');

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('diva/module/dvcontrolpanel', $data));
    }

    public function import() {
        $this->load->language('extension/module/dvcontrolpanel');

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && isset($this->request->post['file'])) {
            $file = $this->request->post['file'];
        } else {
            $file = '';
        }

        if (!file_exists($file)) {
            unset($this->session->data['success']);

            $this->session->data['error_load_file'] = sprintf($this->language->get('error_load_file'), $file);

            $this->response->redirect($this->url->link('extension/module/dvcontrolpanel', 'user_token=' . $this->session->data['user_token'], true));
        } else {
            unset($this->session->data['error_load_file']);

            $lines = file($file);

            if($lines) {
                $sql = '';

                foreach($lines as $line) {
                    if ($line && (substr($line, 0, 2) != '--') && (substr($line, 0, 1) != '#')) {
                        $sql .= $line;

                        if (preg_match('/;\s*$/', $line)) {
                            $sql = str_replace("DROP TABLE IF EXISTS `oc_", "DROP TABLE IF EXISTS `" . DB_PREFIX, $sql);
                            $sql = str_replace("CREATE TABLE `oc_", "CREATE TABLE `" . DB_PREFIX, $sql);
                            $sql = str_replace("CREATE TABLE IF NOT EXISTS `oc_", "CREATE TABLE `" . DB_PREFIX, $sql);
                            $sql = str_replace("INSERT INTO `oc_", "INSERT INTO `" . DB_PREFIX, $sql);
                            $sql = str_replace("UPDATE `oc_", "UPDATE `" . DB_PREFIX, $sql);
                            $sql = str_replace("WHERE `oc_", "WHERE `" . DB_PREFIX, $sql);
                            $sql = str_replace("TRUNCATE TABLE `oc_", "TRUNCATE TABLE `" . DB_PREFIX, $sql);
                            $sql = str_replace("ALTER TABLE `oc_", "ALTER TABLE `" . DB_PREFIX, $sql);

                            $this->db->query($sql);

                            $sql = '';
                        }
                    }
                }
            }

            $this->session->data['success'] = $this->language->get('text_import_success');

            $this->response->redirect($this->url->link('extension/module/dvcontrolpanel', 'user_token=' . $this->session->data['user_token'], true));
        }
    }

    public function install() {
        $this->load->model('diva/controlpanel');
        $this->model_diva_controlpanel->setupData();
    }

    protected function validate() {
        if (!$this->user->hasPermission('modify', 'extension/module/dvcontrolpanel')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        return !$this->error;
    }
}