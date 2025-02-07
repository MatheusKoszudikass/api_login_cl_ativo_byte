<?php   

namespace App\Service\Email;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class EmailService
{
    private PHPMailer $_mailer;

    public function __construct()
    {
        $this->_mailer = new PHPMailer(true);
        $this->configureSMTP();
    }

    private function configureSMTP()
    {
        $this->_mailer->isSMTP();
        $this->_mailer->Host = $_SERVER['SMTP_HOST'];
        $this->_mailer->SMTPAuth = $_SERVER['SMTP_AUTH'];
        $this->_mailer->Username = $_SERVER['SMTP_USER'];
        $this->_mailer->Password = $_SERVER['SMTP_PASS'];
        $this->_mailer->SMTPSecure = $_SERVER['SMTP_SECURE'];
        $this->_mailer->Port = $_SERVER['SMTP_PORT'];
        $this->_mailer->CharSet = $_SERVER['SMTP_CHARSET'];
        $this->_mailer->SMTPKeepAlive = $_SERVER['SMTP_KEEPALIVE'];
    }

    public function sendEmail($to, $subject, $body)
    {
        try {
        $this->_mailer->setFrom($_SERVER['SMTP_USER'], 'AtivoByte');
        $this->_mailer->addAddress($to);
        $this->_mailer->Subject = $subject;
        $this->_mailer->Body = $body;
        $this->_mailer->isHTML(true);

        $this->_mailer->send();
        } catch (Exception $e) {
            
            throw new Exception ("Erro ao enviar email: {$e->getMessage()}");
        }
    }
}