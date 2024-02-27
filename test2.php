<?php
	include("params.php");
	include("functions.php");

	$ldapHost = "ldap://mlpcare.com";		// "ldap://your_domain_controller";	// Active Directory sunucu adresi
	$ldapPort = 389;						// 389; Default						// LDAP bağlantı portu
	$ldapDomainName = "MLPCARE";			// "MLPCARE" + "\\";				// LDAP Active Directory Domain Name	
	$ldapUser = $_SESSION['username'];		// "ahmet.nizam1";					// Active Directory kullanıcı adı
	$ldapPassword = $_SESSION['password'];	// "an112743.";						// Active Directory kullanıcı şifresi

	// Active Directory DN
	$dn = "DC=mlpcare,DC=com";
	$filter = "sAMAccountName=" . str_replace('@mlpcare.com', '', $_GET['mail']);
	$attr = $_PARAM['ldap_attribute'];

	$traveler_info = get_ldap_information($ldapHost, $ldapPort, $ldapDomainName, $ldapUser, $ldapPassword, $dn, $filter, $attr);

	print_r($traveler_info);
?>