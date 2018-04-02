<?php
class ModelDivaNewsletter extends Model
{
    public function install() {
        $this->db->query("
			CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "dvnewsletter_email` (
			    `newsletter_id` INT(11) NOT NULL AUTO_INCREMENT,
			    `subscribe` TINYINT(1) NOT NULL DEFAULT '1',
	            `mail` varchar(255) NOT NULL,
	        PRIMARY KEY (`newsletter_id`)
		) DEFAULT COLLATE=utf8_general_ci;");

        $this->load->model('user/user_group');
        $this->model_user_user_group->addPermission($this->user->getGroupId(), 'access', 'diva/newsletter');
        $this->model_user_user_group->addPermission($this->user->getGroupId(), 'modify', 'diva/newsletter');
    }

    public function uninstall() {
        $this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "dvnewsletter_email`");

        $this->load->model('user/user_group');
        $this->model_user_user_group->removePermission($this->user->getGroupId(), 'access', 'diva/newsletter');
        $this->model_user_user_group->removePermission($this->user->getGroupId(), 'modify', 'diva/newsletter');
    }
}