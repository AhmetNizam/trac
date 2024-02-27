<?php
	use PHPMailer\PHPMailer\PHPMailer;
	use PHPMailer\PHPMailer\Exception;

	require './PHPMailer/src/Exception.php';
	require './PHPMailer/src/PHPMailer.php';
	require './PHPMailer/src/SMTP.php';

	if($_POST['eposta'] <> '' && $_POST['isim'] <> '' && $_POST['telefon'] <> '' && $_POST['mesaj'] <> '') {

	$isim = $_POST['isim'];
	$eposta = $_POST['eposta'];
	$telefon = $_POST['telefon'];
	$mesaj = $_POST['mesaj'];

	$mail = new PHPMailer();										// Passing `true` enables exceptions
		try {
			//Server settings
			$mail->SMTPDebug = 0;									// SMTP hata ayıklama // 0 = mesaj göstermez (testler bittikten sonra kullanılmalıdır) // 1 = sadece mesaj gösterir // 2 = hata ve mesaj gösterir
			$mail->isSMTP();										
			$mail->SMTPAuth = true;									// SMTP doğrulamayı etkinleştirir
			//$mail->Username = 'ahmetnizam.bizmed@gmail.com';		// SMTP kullanıcı adı (gönderici adresi)
			$mail->Username = 'mlpcare\ahmet.nizam1';
			//$mail->Password = 'hsuo laqm hons iwcv';				// SMTP şifre
			$mail->Password = 'An26071#';
			//$mail->Host = 'smtp.gmail.com';						// Mail sunucusunun adresi
			$mail->Host = 'relayin.mlpcare.com';
			$mail->Port = 465;										// Normal bağlantı için 587, güvenli bağlantı için 465 yazın
			$mail->SMTPSecure = 'none';								// Enable TLS encryption, '' , 'ssl' , 'tls'
			$mail->SMTPOptions = array(
				'ssl' => [
					'verify_peer' => false,
					'verify_peer_name' => false,
					'allow_self_signed' => true,
				],
			);
			$mail->SetLanguage('tr', 'PHPMailer/language/');

			//Recipients
			$mail->setFrom('ahmet.nizam1@mlpcare.com', $isim);		// Mail atıldığında gorulecek isim ve email
			$mail->addAddress('ahmet.nizam1@mlpcare.com');			// Mailin gönderileceği alıcı adresi
			$mail->addAddress('cemal.aybek@mlpcare.com');
			$mail->addAddress('seray.cicek@mlpcare.com');
			//$mail->addAddress('utku.yurtcu@mlpcare.com');

			//Content
			$mail->isHTML();
			$mail->CharSet = 'utf-8';

			$mail->Subject = 'İletişim formundan mesajınız var!';			// Email konusu
			$mail->Body = "$isim<br />$eposta<br />$telefon<br />$mesaj";	// Mailin içeriği
			$mail->send();

			echo 'Mesaj gönderildi';
		} catch (Exception $e) {
			echo 'Mesaj gönderilmedi. Hata: ', $mail->ErrorInfo;
		}
	}
?>