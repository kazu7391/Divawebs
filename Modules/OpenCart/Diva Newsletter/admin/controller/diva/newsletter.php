<?php
class ControllerDivaNewsletter extends Controller
{
    public function index() {
        $this->language->load('diva/newsletter');

        $this->document->setTitle($this->language->get('page_title'));

        $this->load->model('diva/newsletter');

        $this->getList();
    }

    public function editSubscribe() {
        $this->language->load('diva/newsletter');

        $this->document->setTitle($this->language->get('page_title'));

        $this->load->model('diva/newsletter');

        if(isset($this->request->get['mail_id']) && isset($this->request->get['subscribe'])) {
            $mail_id = $this->request->get['mail_id'];
            $subscribe_status = $this->request->get['subscribe'];

            $this->model_diva_newsletter->editSubscribe($mail_id, $subscribe_status);

            $this->session->data['success'] = $this->language->get('text_success');

            $url = '';

            if (isset($this->request->get['page'])) {
                $url .= '&page=' . $this->request->get['page'];
            }

            $this->response->redirect($this->url->link('diva/newsletter', 'user_token=' . $this->session->data['user_token'] . $url, true));
        }

        $this->getList();
    }
    
    public function delete() {
        $this->language->load('diva/newsletter');

        $this->document->setTitle($this->language->get('page_title'));

        $this->load->model('diva/newsletter');

        if(($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateDelete()) {
            foreach ($this->request->post['selected'] as $mail_id) {
                $this->model_diva_newsletter->deleteMail($mail_id);
            }

            $this->session->data['success'] = $this->language->get('text_success');

            $url = '';

            if (isset($this->request->get['page'])) {
                $url .= '&page=' . $this->request->get['page'];
            }

            $this->response->redirect($this->url->link('diva/newsletter', 'user_token=' . $this->session->data['user_token'] . $url, true));
        }

        $this->getList();
    }
    
    public function getList() {
        if (isset($this->request->get['page'])) {
            $page = $this->request->get['page'];
        } else {
            $page = 1;
        }

        $url = '';

        if (isset($this->request->get['page'])) {
            $url .= '&page=' . $this->request->get['page'];
        }

        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text'      => $this->language->get('text_home'),
            'href'      => $this->url->link('common/home', 'user_token=' . $this->session->data['user_token'], true)
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_mail_list'),
            'href' => $this->url->link('diva/newsletter', 'user_token=' . $this->session->data['user_token'] . $url, true)
        );

        $data['delete'] = $this->url->link('diva/newsletter/delete', 'user_token=' . $this->session->data['user_token'] . $url, true);
        $data['send_form'] = $this->url->link('diva/newsletter/sendForm', 'user_token=' . $this->session->data['user_token'] . $url, true);

        $data['mails'] = array();

        $filter_data = array(
            'start' => ($page - 1) * $this->config->get('config_limit_admin'),
            'limit' => $this->config->get('config_limit_admin')
        );

        $mails_total = $this->model_diva_newsletter->getTotalMails($filter_data);
        
        $results = $this->model_diva_newsletter->getMails($filter_data);

        foreach ($results as $result) {
            if($result['subscribe']) {
                $changeStatus = 0;
            } else {
                $changeStatus = 1;
            }

            $data['mails'][] = array(
                'mail_id' => $result['newsletter_id'],
                'mail' => $result['mail'],
                'subscribe' => $result['subscribe'] ? $this->language->get('text_subscribe') : $this->language->get('text_unsubscribe'),
                'status' => $result['subscribe'],
                'edit' => $this->url->link('diva/newsletter/editSubscribe', 'user_token=' . $this->session->data['user_token'] . '&mail_id=' . $result['newsletter_id'] . '&subscribe=' . $changeStatus . $url, true)
            );
        }

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

        $url = '';

        if (isset($this->request->get['page'])) {
            $url .= '&page=' . $this->request->get['page'];
        }

        $pagination = new Pagination();
        $pagination->total = $mails_total;
        $pagination->page = $page;
        $pagination->limit = $this->config->get('config_limit_admin');
        $pagination->text = $this->language->get('text_pagination');
        $pagination->url = $this->url->link('diva/newsletter', 'user_token=' . $this->session->data['user_token'] . $url . '&page={page}', true);

        $data['pagination'] = $pagination->render();
        $data['results'] = sprintf($this->language->get('text_pagination'), ($mails_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($mails_total - $this->config->get('config_limit_admin'))) ? $mails_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $mails_total, ceil($mails_total / $this->config->get('config_limit_admin')));

        $this->document->addStyle('view/stylesheet/divawebs/themeadmin.css');

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('diva/newsletter/list', $data));
    }

    public function sendForm() {
        $this->language->load('diva/newsletter');

        $this->document->setTitle($this->language->get('page_title'));

        $this->load->model('diva/newsletter');

        $data = array();

        $data['user_token'] = $this->session->data['user_token'];

        $url = '';

        if (isset($this->request->get['page'])) {
            $url .= '&page=' . $this->request->get['page'];
        }

        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_mail_form'),
            'href' => $this->url->link('diva/newsletter/sendForm', 'user_token=' . $this->session->data['user_token'] . $url, true)
        );

        $data['cancel'] = $this->url->link('diva/newsletter', 'user_token=' . $this->session->data['user_token'] . $url, true);

        $this->load->model('setting/store');

        $data['stores'] = $this->model_setting_store->getStores();

        $this->document->addStyle('view/stylesheet/divawebs/themeadmin.css');
        
        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('diva/newsletter/mail', $data));
    }

    public function sendMail() {
        $this->load->language('diva/newsletter');

        $json = array();

        if ($this->request->server['REQUEST_METHOD'] == 'POST') {
            if (!$this->user->hasPermission('modify', 'diva/newsletter')) {
                $json['error']['warning'] = $this->language->get('error_permission');
            }

            if (!$this->request->post['subject']) {
                $json['error']['subject'] = $this->language->get('error_subject');
            }

            if (!$this->request->post['message']) {
                $json['error']['message'] = $this->language->get('error_message');
            }

            if (!$json) {
                $this->load->model('setting/store');

                $store_info = $this->model_setting_store->getStore($this->request->post['store_id']);

                if ($store_info) {
                    $store_name = $store_info['name'];
                } else {
                    $store_name = $this->config->get('config_name');
                }

                $this->load->model('setting/setting');
                $setting = $this->model_setting_setting->getSetting('config', $this->request->post['store_id']);
                $store_email = isset($setting['config_email']) ? $setting['config_email'] : $this->config->get('config_email');

                $this->load->model('diva/newsletter');

                if (isset($this->request->get['page'])) {
                    $page = $this->request->get['page'];
                } else {
                    $page = 1;
                }

                $email_total = 0;

                $emails = array();

                switch ($this->request->post['to']) {
                    case 'all':
                        $filter_data = array(
                            'filter_subscribe' => 1,
                            'start'             => ($page - 1) * 10,
                            'limit'             => 10
                        );

                        $email_total = $this->model_diva_newsletter->getTotalMails($filter_data);

                        $results = $this->model_diva_newsletter->getMails($filter_data);

                        foreach ($results as $result) {
                            if($result['subscribe']) {
                                $emails[] = $result['mail'];
                            }
                        }

                        break;
                    case 'specified':
                        $subscribers = $this->request->post['subscribers'];
                        
                        foreach ($subscribers as $subscriber) {
                            $newsletter = $this->model_diva_newsletter->getMail($subscriber);
                            $emails[] = $newsletter['mail'];
                            $email_total++;
                        }

                        break;
                }

                if ($emails) {
                    $json['success'] = $this->language->get('text_success');

                    $start = ($page - 1) * 10;
                    $end = $start + 10;

                    $json['success'] = sprintf($this->language->get('text_sent'), $start, $email_total);

                    if ($end < $email_total) {
                        $json['next'] = str_replace('&amp;', '&', $this->url->link('diva/newsletter/sendMail', 'user_token=' . $this->session->data['user_token'] . '&page=' . ($page + 1), true));
                    } else {
                        $json['next'] = '';
                    }

                    $message  = '<html dir="ltr" lang="en">' . "\n";
                    $message .= '  <head>' . "\n";
                    $message .= '    <title>' . $this->request->post['subject'] . '</title>' . "\n";
                    $message .= '    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">' . "\n";
                    $message .= '  </head>' . "\n";
                    $message .= '  <body>' . html_entity_decode($this->request->post['message'], ENT_QUOTES, 'UTF-8') . '</body>' . "\n";
                    $message .= '</html>' . "\n";

                    foreach ($emails as $email) {
                        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                            $mail = new Mail($this->config->get('config_mail_engine'));
                            $mail->parameter = $this->config->get('config_mail_parameter');
                            $mail->smtp_hostname = $this->config->get('config_mail_smtp_hostname');
                            $mail->smtp_username = $this->config->get('config_mail_smtp_username');
                            $mail->smtp_password = html_entity_decode($this->config->get('config_mail_smtp_password'), ENT_QUOTES, 'UTF-8');
                            $mail->smtp_port = $this->config->get('config_mail_smtp_port');
                            $mail->smtp_timeout = $this->config->get('config_mail_smtp_timeout');

                            $mail->setTo($email);
                            $mail->setFrom($store_email);
                            $mail->setSender(html_entity_decode($store_name, ENT_QUOTES, 'UTF-8'));
                            $mail->setSubject(html_entity_decode($this->request->post['subject'], ENT_QUOTES, 'UTF-8'));
                            $mail->setHtml($message);
                            $mail->send();
                        }
                    }
                } else {
                    $json['error']['email'] = $this->language->get('error_email');
                }
            }
        }

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }

    protected function validateDelete() {
        if (!$this->user->hasPermission('modify', 'diva/newsletter')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        if (!$this->error) {
            return true;
        } else {
            return false;
        }
    }

    public function autocomplete() {
        $json = array();
        
        if (isset($this->request->get['filter_mail'])) {
            if (isset($this->request->get['filter_mail'])) {
                $filter_mail = $this->request->get['filter_mail'];
            } else {
                $filter_mail = '';
            }

            $this->load->model('diva/newsletter');

            $filter_data = array(
                'filter_mail'      => $filter_mail,
                'start'            => 0,
                'limit'            => 5
            );

            $results = $this->model_diva_newsletter->getMails($filter_data);

            foreach ($results as $result) {
                if($result['subscribe']) {
                    $json[] = array(
                        'newsletter_id'  => $result['newsletter_id'],
                        'mail'           => $result['mail'],
                    );
                }
            }
        }

        $sort_order = array();

        foreach ($json as $key => $value) {
            $sort_order[$key] = $value['newsletter_id'];
        }

        array_multisort($sort_order, SORT_ASC, $json);

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }
}