<?php
	require("./library.php");

	$authorized_person['uuid'] = $_SESSION['approval_authority_uuid'];
	$authorized_person['name'] = $_SESSION['approval_authority_name'];
	$authorized_person['mail'] = $_SESSION['approval_authority_mail'];
	$mail_recipient_list[] = $authorized_person;

	$conn = get_mysql_connection();

	if($conn) {
		// Prosedürü çağır (User Id bul)
		$stmt = $conn->prepare("SELECT U.UUID AS uuid, CONCAT(U.NAME, ' ', U.SURNAME) AS name, U.EMAIL AS mail
								FROM AUTHORIZED_PERSON_GROUP APG
								JOIN USER U ON U.ID = APG.USER_ID
								WHERE APG.AUTHORIZED_PERSON_ID = :authorized_person_id");
		$stmt->bindParam(':authorized_person_id', $_SESSION['approval_authority_id'], PDO::PARAM_INT);
		$stmt->execute();
		$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
		$stmt->closeCursor();

		foreach($rows as $row) {
			$mail_recipient_list[] = $row;
		}

		$conn = null;
	}

	$_SESSION['mail_recipient_list'] = $mail_recipient_list;

	if(count($mail_recipient_list)) {
?>
{"Rows":[{
	"status":"1"
}],"TableName":"Table",
"Columns":{
	"0":"status"
}}
<?php
	} else {
?>
{"Rows":[{
	"status":"0"
}],"TableName":"Table",
"Columns":{
	"0":"status"
}}
<?php
	}
?>