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

        $this->getList();
    }
    
    public function delete() {
        
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
        
        $data['mails'] = array();

        $filter_data = array(
            'start' => ($page - 1) * $this->config->get('config_limit_admin'),
            'limit' => $this->config->get('config_limit_admin')
        );

        $mails_total = $this->model_diva_newsletter->getTotalMails();
        
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
                'subscribe' => $result['subscribe'] ? 'Subscribe' : 'Unsubcribe',
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
}