<?php
require_once("mygassi-config.php");
require_once("mygassi-logger.php");
require_once(mageroot);

class EmailNotification
{
	static private $message;
	static private $subject = "Prozessbenachrichtigungsemail";

	static public function add($message)
	{
		self::$message .= $message;
		self::$message .= "<br>\n";
	}

	static public function setSubject($subject)
	{
		self::$subject = $subject;
	}

	static public function send()
	{
		self::add("");
		self::add("");
		self::add("<span style='color:black'></span>");
		try{
			$mail = new Zend_Mail("UTF-8");
			$mail->setFrom("dev@mygassi.com", "MyGassi");
			$mail->addTo("dev@mygassi.com", "Dev");
			$mail->setSubject(self::$subject);
			$mail->setBodyHtml(self::$message); 
			/*
			$at = new Zend_Mime_Part($body);
			$at->type        = 'application/csv'; // if u have PDF then it would like -> 'application/pdf'
			$at->disposition = Zend_Mime::DISPOSITION_INLINE;
			$at->encoding    = Zend_Mime::ENCODING_8BIT;
			$at->filename    = $file;
			$mail->addAttachment($at);
			*/
			$mail->send();
		}
		catch(Exception $e){
			print $e->getMassage();
		}
	}
}

