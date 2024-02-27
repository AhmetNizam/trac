<?php
	require("params.php");
	require("functions.php");

	// LDAP bağlantısı için gerekli bilgiler
	$ldapHost = "ldap://mlpcare.com";		// Active Directory sunucu adresi
	$ldapPort = 389;						// LDAP bağlantı portu
	$ldapDomainName = "MLPCARE";			// LDAP Active Directory Domain Name	
	$ldapUser = $_GET['username'];			// Active Directory kullanıcı adı
	$ldapPassword = $_GET['password'];		// Active Directory kullanıcı şifresi

	// Active Directory DN
	$dn = "DC=mlpcare,DC=com";
	$filter = "sAMAccountName=" . $ldapUser;
	$attr = $_PARAM['ldap_attribute'];

	$user_info = get_ldap_information($ldapHost, $ldapPort, $ldapDomainName, $ldapUser, $ldapPassword, $dn, $filter, $attr);

	if($user_info) {
		if(handle_user_login($ldapUser, $user_info)) {
			$_SESSION['username'] = $ldapUser;
			$_SESSION['password'] = $ldapPassword;
			$_SESSION['user_info'] = $user_info;

			$err_code = 0;
			$msg = 'Kullanıcı doğrulama başarılı';
		} else {
			$err_code = 2;
			$msg = 'Kullanıcı giriş kaydı başarısız';
		}
	} else {
		$err_code = 1;
		$msg = 'Kullanıcı doğrulama başarısız';
	}
?>
{"Rows":[{"err_code":"<?php echo $err_code; ?>","msg":"<?php echo $msg; ?>"}],"TableName":"Table","Columns":{"0":"err_code","1":"msg"}}