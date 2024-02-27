<?php
	session_start();

	include("connect_mysql.php");

	function set_null($variable) {
		if(empty($variable) || $variable == '') {
			return null;
		} else {
			return $variable;
		}
	}

	function translate_turkish_char($input) {
		$trans = array(
			'ı' => 'i', 'ğ' => 'g', 'ü' => 'u', 'ş' => 's', 'ö' => 'o', 'ç' => 'c',
			'İ' => 'I', 'Ğ' => 'G', 'Ü' => 'U', 'Ş' => 'S', 'Ö' => 'O', 'Ç' => 'C'
		);

		return strtr($input, $trans);
	}

	function resolve_name_surname($displayname, $surname) {
		$surname = preg_replace('/\d/', '', $surname);
		$pos = iconv_strrpos(strtoupper(translate_turkish_char($displayname)), strtoupper(translate_turkish_char($surname)));
		$fullname['name'] = Transliterator::create('tr-upper')->transliterate(trim(mb_substr($displayname, 0, $pos)));
		$fullname['surname'] = Transliterator::create('tr-upper')->transliterate(mb_substr($displayname, $pos, mb_strlen($surname)));

		return $fullname;
	}

	function get_user_info_from_ad($ldapConn, $dn, $filter, $attr) {
		$result = ldap_search($ldapConn, $dn, $filter, $attr); // or exit("LDAP sunucusunda arama yapılamıyor.");

		if($result) {
			$entries = ldap_get_entries($ldapConn, $result);

			if($entries['count'] > 0) {
				if($entries['count'] > 1) {
					for($i = 0; $i < $entries['count']; $i++) {
						if(array_key_exists('sn', $entries[$i])) {
							$entries = $entries[$i];
							break;
						}
					}
				} else {
					$entries = $entries[0];
				}

				if(array_key_exists('displayname', $entries)) {
					$displayname = $entries['displayname'][0];
				}

				if(array_key_exists('sn', $entries)) {
					$surname = $entries['sn'][0];
				}

				if(array_key_exists('mail', $entries)) {
					$email = $entries['mail'][0];
				}

				if(array_key_exists('title', $entries)) {
					$position = str_replace('.', '', $entries['title'][0]);
				}

				if(array_key_exists('department', $entries)) {
					$department = $entries['department'][0];
				}

				if(array_key_exists('physicaldeliveryofficename', $entries)) {
					$location = $entries['physicaldeliveryofficename'][0];
				}

				if(array_key_exists('manager', $entries)) {
					// echo "<b>manager</b>\r";
					$filter = "CN=" . substr($entries['manager'][0], 3, strpos($entries['manager'][0], ',') - 3);
					$manager_info = get_user_info_from_ad($ldapConn, $dn, $filter, $attr);
				}

				$fullname = resolve_name_surname($displayname, $surname);

				return array('name' => $fullname['name'] ?? '', 'surname' => $fullname['surname'] ?? '', 'mail' => $email ?? '', 'position' => $position ?? '', 'department' => $department ?? '', 'location' => $location ?? '', 'manager' => $manager_info ?? '');
			} else {
				return false;
			}
		} else {
			return false;
		}
	}

	function get_ldap_information($ldapHost, $ldapPort, $ldapDomainName, $ldapUser, $ldapPassword, $dn, $filter, $attr) {
		// LDAP bağlantısını oluştur
		$ldapConn = ldap_connect($ldapHost, $ldapPort);

		if($ldapConn) {
			// echo("LDAP bağlantısı başarılı.\r");

			// LDAP bağlantısını yapılandır
			ldap_set_option($ldapConn, LDAP_OPT_PROTOCOL_VERSION, 3);
			ldap_set_option($ldapConn, LDAP_OPT_REFERRALS, 0);

			// Kullanıcı doğrulama
			$ldapBind = ldap_bind($ldapConn, $ldapDomainName . '\\' . $ldapUser, $ldapPassword);

			$user_info = get_user_info_from_ad($ldapConn, $dn, $filter, $attr);

			// LDAP bağlantısını kapat
			ldap_close($ldapConn);

			if($user_info) {
				// Kullanıcı doğrulama başarılı
				return $user_info;
			} else {
				// Kullanıcı doğrulama başarısız
				return false;
			}
		} else {
			// echo("LDAP bağlantısı başarısız.\r");
			return false;
		}
	}

	function handle_user_login($username, $user_info) {

		$conn = get_mysql_connection();

		if($conn) {
			// Prosedürü çağır (User kontrol et / yoksa ekle)
			$stmt = $conn->prepare("CALL HANDLE_USER_LOGIN(:username, :name, :surname, :mail, :position, :department, :location, @oUserId)");
			$stmt->bindParam(':username', $username, PDO::PARAM_STR);
			$stmt->bindParam(':name', $user_info['name'], PDO::PARAM_STR);
			$stmt->bindParam(':surname', $user_info['surname'], PDO::PARAM_STR);
			$stmt->bindParam(':mail', $user_info['mail'], PDO::PARAM_STR);
			$stmt->bindParam(':position', $user_info['position'], PDO::PARAM_STR);
			$stmt->bindParam(':department', $user_info['department'], PDO::PARAM_STR);
			$stmt->bindParam(':location', $user_info['location'], PDO::PARAM_STR);
			$stmt->execute();
			$stmt->closeCursor();

			// OUT parametresini al (oUserId)
			$stmt = $conn->query("SELECT @oUserId AS userid");
			$row = $stmt->fetch(PDO::FETCH_ASSOC);
			$stmt->closeCursor();

			$_SESSION['userid'] = $row['userid'];
			
			// Prosedürü çağır (User giriş kaydı yap)
			$stmt = $conn->prepare("CALL LOG_LOGIN_ACTIVITY(:userid, @oResult)");
			$stmt->bindParam(':userid', $_SESSION['userid'], PDO::PARAM_INT);
			$stmt->execute();
			$stmt->closeCursor();

			// OUT parametresini al (oResult)
			$stmt = $conn->query("SELECT @oResult AS result");
			$row = $stmt->fetch(PDO::FETCH_ASSOC);
			$stmt->closeCursor();

			$result = $row['result'];

			$conn = null;

			if($result) {
				return true;
			} else {
				return false;
			}
		} else {		
			return false;
		}
	}
?>