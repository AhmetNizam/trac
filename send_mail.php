<?php
	require("./library.php");

	use PHPMailer\PHPMailer\Exception;
	use PHPMailer\PHPMailer\PHPMailer;
	use PHPMailer\PHPMailer\SMTP;

	require("params.php");
	require("./mail/PHPMailer/src/Exception.php");
	require("./mail/PHPMailer/src/PHPMailer.php");
	require("./mail/PHPMailer/src/SMTP.php");

	if($_SERVER["REQUEST_METHOD"] == "POST") {
		$mail = new PHPMailer();
		try {
			//Server settings
			$mail->SMTPDebug = 0;	// SMTP hata ayıklama // 0 = mesaj göstermez (testler bittikten sonra kullanılmalıdır) // 1 = sadece mesaj gösterir // 2 = hata ve mesaj gösterir
			$mail->isSMTP();
			$mail->SMTPAuth = true;
			$mail->Username = $_PARAM['mailUsername'];
			$mail->Password = $_PARAM['mailPassword'];						
			$mail->Host = $_PARAM['mailHost'];
			$mail->Port = $_PARAM['mailPort'];
			$mail->SMTPSecure = $_PARAM['mailSMTPSecure'];
			$mail->SMTPOptions = array(
				'ssl' => [
					'verify_peer' => false,
					'verify_peer_name' => false,
					'allow_self_signed' => true,
				],
			);
			$mail->SetLanguage('tr', 'PHPMailer/language/');

			//Recipients
			$mail->setFrom($_PARAM['mailSenderMail'], $_PARAM['mailSenderName']);
			$mail->addAddress($_SESSION['approval_authority_mail'], $_SESSION['approval_authority_name']);

			//Content
			$mail->isHTML(true);
			$mail->CharSet = 'utf-8';
			$mail->Subject = 'Ulaşım ve Konaklama Talebi';
			$mail->Body = $_POST['mailBody'];
			$mail->send();

			echo 'Mesaj gönderildi';
		} catch (Exception $e) {
			echo 'Mesaj gönderilemedi. Hata: ', $mail->ErrorInfo;
		}
	}
?>