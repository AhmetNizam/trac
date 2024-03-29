<?php
	require("./library.php");

	$ldapUser = $_GET['username'];			// Active Directory kullanıcı adı
	$ldapPassword = $_GET['password'];		// Active Directory kullanıcı şifresi
	$filter = "sAMAccountName=" . $ldapUser;

	$user_info = get_ldap_information($ldapUser, $ldapPassword, $filter);

	if($user_info) {
		if(handle_user_login($ldapUser, $user_info)) {
			$_SESSION['username'] = $ldapUser;
			$_SESSION['password'] = $ldapPassword;
			$_SESSION['user_info'] = $user_info;

			$_SESSION['approval_authority_name'] = $user_info['name'] . ' ' . $user_info['surname'];
			$_SESSION['approval_authority_mail'] = $user_info['mail'];

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