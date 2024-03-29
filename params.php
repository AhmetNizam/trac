<?php
	error_reporting(0);

	// LDAP Parametreleri
	$_PARAM['ldapHost'] = 'ldap://mlpcare.com';		// Active Directory sunucu adresi
	$_PARAM['ldapPort'] = 389;						// LDAP bağlantı portu
	$_PARAM['ldapDomainName'] = 'MLPCARE';			// LDAP Active Directory Domain Name
	$_PARAM['ldapBase'] = "DC=mlpcare,DC=com";		// Active Directory DN
	$_PARAM['ldapAttributes'] = array("displayname", "sn", "mail", "title", "department", "physicaldeliveryofficename", "manager"); //array("memberof", "mail", "sn", "cn");

	// SMTP Mail Parametreleri
	$_PARAM['mailUsername'] = 'mlpcare\ahmet.nizam1';			// SMTP kullanıcı adı/mail 				
	$_PARAM['mailPassword'] = 'An998367$';						// SMTP şifre
	$_PARAM['mailHost'] = 'relayin.mlpcare.com';				// Mail sunucu adresi
	$_PARAM['mailPort'] = 465;									// Normal bağlantı için 587, güvenli bağlantı için 465
	$_PARAM['mailSMTPSecure'] = 'none';							// Enable TLS encryption, '' , 'ssl' , 'tls'
	$_PARAM['mailSenderName'] = 'Ulaşım ve Konaklama';			// Mail atıldığında görülecek isim
	$_PARAM['mailSenderMail'] = 'ahmet.nizam1@mlpcare.com';		// Mail atıldığında görülecek email
?>