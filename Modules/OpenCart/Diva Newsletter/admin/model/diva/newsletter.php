<?php
class ModelDivaNewsletter extends Model
{
    public function getMails($data = array()) {
        $sql = "SELECT * FROM " . DB_PREFIX . "dvnewsletter_email";

        if (isset($data['start']) || isset($data['limit'])) {
            if ($data['start'] < 0) {
                $data['start'] = 0;
            }

            if ($data['limit'] < 1) {
                $data['limit'] = 20;
            }

            $sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
        }

        $query = $this->db->query($sql);

        return $query->rows;
    }

    public function getTotalMails() {
        $query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "dvnewsletter_email");

        return $query->row['total'];
    }

    public function editSubscribe($mail_id, $subscribe) {
        $this->db->query("UPDATE " . DB_PREFIX . "dvnewsletter_email SET subscribe = '" . (int) $subscribe . "' WHERE newsletter_id = '" . (int) $mail_id . "'");
    }

    public function deleteMail($mail_id) {
        $this->db->query("DELETE FROM " . DB_PREFIX . "dvnewsletter_email WHERE newsletter_id = '" . (int) $mail_id . "'");
    }

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