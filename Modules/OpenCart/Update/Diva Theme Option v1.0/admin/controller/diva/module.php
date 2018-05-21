<?php
class ControllerDivaModule extends Controller
{
    public function index() {
        $this->load->language('diva/module');
        $this->load->language('diva/adminmenu');

        $this->load->model('setting/extension');

        $this->load->model('setting/module');

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

        $this->document->setTitle($this->language->get('page_title'));

        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link('diva/module', 'user_token=' . $this->session->data['user_token'], true)
        );

        $data['user_token'] = $this->session->data['user_token'];

        $extensions = $this->model_setting_extension->getInstalled('module');

        foreach ($extensions as $key => $value) {
            if (!is_file(DIR_APPLICATION . 'controller/extension/module/' . $value . '.php') && !is_file(DIR_APPLICATION . 'controller/module/' . $value . '.php')) {
                $this->model_setting_extension->uninstall('module', $value);

                unset($extensions[$key]);

                $this->model_setting_module->deleteModulesByCode($value);
            }
        }

        $data['extensions'] = array();

        // Create a new language container so we don't pollute the current one
        $language = new Language($this->config->get('config_language'));

        // Compatibility code for old extension folders
        $files = glob(DIR_APPLICATION . 'controller/extension/module/dv*.php');

        if ($files) {
            foreach ($files as $file) {
                $extension = basename($file, '.php');

                $this->load->language('extension/module/' . $extension, 'extension');

                $module_data = array();

                $modules = $this->model_setting_module->getModulesByCode($extension);

                foreach ($modules as $module) {
                    if ($module['setting']) {
                        $setting_info = json_decode($module['setting'], true);
                    } else {
                        $setting_info = array();
                    }

                    $module_data[] = array(
                        'module_id' => $module['module_id'],
                        'name'      => $module['name'],
                        'status'    => (isset($setting_info['status']) && $setting_info['status']) ? 1 : 0,
                        'stt_text'  => (isset($setting_info['status']) && $setting_info['status']) ? $this->language->get('text_enabled') : $this->language->get('text_disabled'),
                        'edit'      => $this->url->link('extension/module/' . $extension, 'user_token=' . $this->session->data['user_token'] . '&module_id=' . $module['module_id'], true),
                        'delete'    => $this->url->link('diva/module/delete', 'user_token=' . $this->session->data['user_token'] . '&module_id=' . $module['module_id'], true)
                    );
                }

                $data['extensions'][] = array(
                    'name'      => $this->language->get('extension')->get('heading_title'),
                    'status'    => $this->config->get('module_' . $extension . '_status') ? 1 : 0,
                    'stt_text'  => $this->config->get('module_' . $extension . '_status') ? $this->language->get('text_enabled') : $this->language->get('text_disabled'),
                    'module'    => $module_data,
                    'install'   => $this->url->link('diva/module/install', 'user_token=' . $this->session->data['user_token'] . '&extension=' . $extension, true),
                    'uninstall' => $this->url->link('diva/module/uninstall', 'user_token=' . $this->session->data['user_token'] . '&extension=' . $extension, true),
                    'installed' => in_array($extension, $extensions),
                    'edit'      => $this->url->link('extension/module/' . $extension, 'user_token=' . $this->session->data['user_token'], true)
                );
            }
        }

        $sort_order = array();

        foreach ($data['extensions'] as $key => $value) {
            $sort_order[$key] = $value['name'];
        }

        array_multisort($sort_order, SORT_ASC, $data['extensions']);

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
                'active' => 1
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
                    'active' => 0
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
                    'active' => 0
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
        
        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('diva/module/list', $data));
    }

    public function install() {
        $this->load->language('diva/module');

        $this->load->model('setting/extension');

        $this->load->model('setting/module');

        if ($this->validate()) {
            $this->model_setting_extension->install('module', $this->request->get['extension']);

            $this->load->model('user/user_group');

            $this->model_user_user_group->addPermission($this->user->getGroupId(), 'access', 'extension/module/' . $this->request->get['extension']);
            $this->model_user_user_group->addPermission($this->user->getGroupId(), 'modify', 'extension/module/' . $this->request->get['extension']);

            // Call install method if it exsits
            $this->load->controller('extension/module/' . $this->request->get['extension'] . '/install');

            $this->session->data['success'] = $this->language->get('text_success');
        } else {
            $this->session->data['error'] = $this->error['warning'];
        }

        $this->response->redirect($this->url->link('diva/module', 'user_token=' . $this->session->data['user_token'], true));
    }

    public function uninstall() {
        $this->load->language('diva/module');

        $this->load->model('setting/extension');

        $this->load->model('setting/module');

        if ($this->validate()) {
            $this->model_setting_extension->uninstall('module', $this->request->get['extension']);

            $this->model_setting_module->deleteModulesByCode($this->request->get['extension']);

            // Call uninstall method if it exsits
            $this->load->controller('extension/module/' . $this->request->get['extension'] . '/uninstall');

            $this->session->data['success'] = $this->language->get('text_success');
        }

        $this->response->redirect($this->url->link('diva/module', 'user_token=' . $this->session->data['user_token'], true));
    }

    public function add() {
        $this->load->language('diva/module');

        $this->load->model('setting/extension');

        $this->load->model('setting/module');

        if ($this->validate()) {
            $this->load->language('module' . '/' . $this->request->get['extension']);

            $this->model_setting_module->addModule($this->request->get['extension'], $this->language->get('heading_title'));

            $this->session->data['success'] = $this->language->get('text_success');
        }

        $this->response->redirect($this->url->link('diva/module', 'user_token=' . $this->session->data['user_token'], true));
    }

    public function delete() {
        $this->load->language('diva/module');

        $this->load->model('setting/extension');

        $this->load->model('setting/module');

        if (isset($this->request->get['module_id']) && $this->validate()) {
            $this->model_setting_module->deleteModule($this->request->get['module_id']);

            $this->session->data['success'] = $this->language->get('text_success');
        }

        $this->response->redirect($this->url->link('diva/module', 'user_token=' . $this->session->data['user_token'], true));
    }

    protected function validate() {
        if (!$this->user->hasPermission('modify', 'extension/extension/module')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        return !$this->error;
    }
}