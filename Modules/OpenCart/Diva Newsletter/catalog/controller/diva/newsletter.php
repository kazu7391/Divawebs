<?php
class ControllerDivaNewsletter extends Controller
{
    public function subscribe() {
        $this->load->language('diva/module/dvnewsletter');

        $this->load->model('diva/newsletter');

        $json = array();

        if(isset($this->request->post['mail'])) {
            $mail = $this->request->post['mail'];
        } else {
            $mail = '';
        }

        if(filter_var($mail, FILTER_VALIDATE_EMAIL)) {
            $valid_mail = $this->model_diva_newsletter->checkMail($mail);
            if($valid_mail) {
                $this->model_diva_newsletter->subscribeMail($mail);
                $json['status'] = true;
                $json['success'] = $this->language->get('subscribe_success');
            } else {
                $json['status'] = false;
                $json['error'] = $this->language->get('error_existed_mail');
            }
        } else {
            $json['status'] = false;
            $json['error'] = $this->language->get('error_validate_mail');
        }

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }
}