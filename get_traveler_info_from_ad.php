<?php
	include("params.php");
	include("functions.php");

	// LDAP bağlantısı için gerekli bilgiler
	$ldapHost = "ldap://mlpcare.com";		// Active Directory sunucu adresi
	$ldapPort = 389;						// LDAP bağlantı portu
	$ldapDomainName = "MLPCARE";			// LDAP Active Directory Domain Name	
	$ldapUser = $_SESSION['username'];		// Active Directory kullanıcı adı
	$ldapPassword = $_SESSION['password'];	// Active Directory kullanıcı şifresi

	// Active Directory DN
	$dn = "DC=mlpcare,DC=com";
	$filter = "sAMAccountName=" . str_replace('@mlpcare.com', '', $_GET['mail']);
	$attr = $_PARAM['ldap_attribute'];

	$traveler_info = get_ldap_information($ldapHost, $ldapPort, $ldapDomainName, $ldapUser, $ldapPassword, $dn, $filter, $attr);

	if($traveler_info) {
?>
{"Rows":[{
	"status":"1",
	"name":"<?php echo $traveler_info['name']; ?>",
	"surname":"<?php echo $traveler_info['surname']; ?>",
	"mail":"<?php echo $traveler_info['mail']; ?>",
	"positionname":"<?php echo $traveler_info['position']; ?>",
	"departmentname":"<?php echo $traveler_info['department']; ?>",
	"locationname":"<?php echo $traveler_info['location']; ?>"
}],"TableName":"Table","Columns":{
	"0":"status",
	"1":"name",
	"2":"surname",
	"3":"mail",
	"4":"positionname",
	"5":"departmentname",
	"6":"locationname"
}}
<?php
	} else {
?>
{"Rows":[{
	"status":"0"
}],"TableName":"Table","Columns":{
	"0":"status"
}}
<?php
	}
?>