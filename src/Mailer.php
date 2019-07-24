<?php

namespace ShoppingCart;

use PHPMailer\PHPMailer\PHPMailer;
use Configuration\Config;

class Mailer {
    
    protected static $mail;
    
    /**
     * Sends an email 
     * @param string $to This should be the email address the email is going to 
     * @param string $subject This should be the subject of the email
     * @param string $plain This should be the email content as plain test
     * @param string $html This should be the HTML format of the email
     * @param string $from This should be the email address that the email is coming from
     * @param string $fromname This should be the name to be displayed, who the email is from
     * @param string $replyto This should be any reply to email address
     * @param array $attachment And attachments should be attached as ana array
     * @return boolean If the email has been sent successfully will return true else returns false
     */
    public static function sendEmail($to, $subject, $plain, $html, $from, $fromname, $replyto = '', $attachment = []) {
        self::$mail = new PHPMailer();
        self::$mail->CharSet = 'UTF-8';
        self::$mail->SMTPDebug = SMTP_DEBUG;
        if(USE_SMTP){
            self::$mail->isSMTP();
            self::$mail->Host = SMTP_HOST;
            self::$mail->SMTPAuth = SMTP_AUTH;
            self::$mail->AuthType = SMTP_AUTHTYPE;
            if(!is_null(SMTP_AUTH)){
                self::$mail->Username = SMTP_USERNAME;
                self::$mail->Password = SMTP_PASSWORD;
            }
            self::$mail->Port = SMTP_PORT;
            if(!is_null(SMTP_SECURITY)){
                self::$mail->SMTPSecure = SMTP_SECURITY;
            }
            self::$mail->smtpConnect(
                [
                    "ssl" => [
                        "verify_peer" => false,
                        "verify_peer_name" => false,
                        "allow_self_signed" => true
                    ]
                ]
            );
        }
        
        self::$mail->From = $from;
        self::$mail->FromName = $fromname;
        if(!empty($replyto)){
            self::$mail->AddReplyTo($replyto, $fromname);
        }
        self::$mail->addAddress($to);
        self::$mail->isHTML(true);
        if(!empty($attachment)){
            foreach($attachment as $file){
                if(@file_exists($file[0])){
                    self::$mail->addAttachment($file[0], $file[1]);
                }
                else{
                    self::$mail->addStringAttachment($file[0], $file[1]);
                }
            }
        }
        self::$mail->Subject = $subject;
        self::$mail->Body = $html;
        self::$mail->AltBody = $plain;
        return self::$mail->send();
    }
    
    public static function htmlWrapper(Config $config, $content, $subject){
        $image = '';
        if(file_exists($_SERVER['DOCUMENT_ROOT'].$config->logo_root_path)){
            $image = '<div class="align-center"><img src="'.$config->site_url.$config->logo_root_path.'" alt="'.$config->site_name.' Logo" height="100" /></div>';
        }
        return sprintf($config->email_html_wrapper, $content, $subject, $config->registered_address, $image);
    }
    
}
