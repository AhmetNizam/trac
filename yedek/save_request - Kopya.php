<?php
	session_start();

	include("connect_mysql.php");

	$conn = get_mysql_connection();

	if($conn) {

		foreach($_SESSION['request']['traveler_list'] as $traveler) {

			if($traveler['type'] == 'staff') {
				// Prosedürü çağır (Kullanıcıyı kontrol et / Yoksa ekle)
				$stmt = $conn->prepare("CALL ADD_STAFF(:name, :surname, :birthdate, :identityno, :passportno, :phone, :mail, :position, :positionid, :department, :departmentid, :location, :locationid, @oStaffId)");
				$stmt->bindParam(':name', $traveler['name'], PDO::PARAM_STR);
				$stmt->bindParam(':surname', $traveler['surname'], PDO::PARAM_STR);
				$stmt->bindParam(':birthdate', $traveler['birthdate'], PDO::PARAM_STR);
				$stmt->bindParam(':identityno', $traveler['identityno'], PDO::PARAM_INT);
				$stmt->bindParam(':passportno', $traveler['passportno'], PDO::PARAM_INT);
				$stmt->bindParam(':phone', $traveler['phone'], PDO::PARAM_STR);
				$stmt->bindParam(':mail', $traveler['mail'], PDO::PARAM_STR);
				$stmt->bindParam(':position', $traveler['position'], PDO::PARAM_STR);
				$stmt->bindParam(':positionid', $traveler['positionid'], PDO::PARAM_INT);
				$stmt->bindParam(':department', $traveler['department'], PDO::PARAM_STR);
				$stmt->bindParam(':departmentid', $traveler['departmentid'], PDO::PARAM_INT);
				$stmt->bindParam(':location', $traveler['location'], PDO::PARAM_STR);
				$stmt->bindParam(':locationid', $traveler['locationid'], PDO::PARAM_INT);
				$stmt->execute();
				$stmt->closeCursor();

				// OUT parametresini al (UserId)
				$stmt = $conn->query("SELECT @oStaffId AS staffid");
				$row = $stmt->fetch(PDO::FETCH_ASSOC);
				$stmt->closeCursor();

				$staff[] = $row['staffid'];

			} else if($traveler['type'] == 'guest') {

				// Prosedürü çağır (Kullanıcıyı kontrol et / Yoksa ekle)
				$stmt = $conn->prepare("CALL ADD_GUEST(:name, :surname, :birthdate, :identityno, :passportno, :phone, :mail, @oGuestId)");
				$stmt->bindParam(':name', $traveler['name'], PDO::PARAM_STR);
				$stmt->bindParam(':surname', $traveler['surname'], PDO::PARAM_STR);
				$stmt->bindParam(':birthdate', $traveler['birthdate'], PDO::PARAM_STR);
				$stmt->bindParam(':identityno', $traveler['identityno'], PDO::PARAM_INT);
				$stmt->bindParam(':passportno', $traveler['passportno'], PDO::PARAM_INT);
				$stmt->bindParam(':phone', $traveler['phone'], PDO::PARAM_STR);
				$stmt->bindParam(':mail', $traveler['mail'], PDO::PARAM_STR);
				$stmt->execute();
				$stmt->closeCursor();

				// OUT parametresini al (UserId)
				$stmt = $conn->query("SELECT @oGuestId AS guestid");
				$row = $stmt->fetch(PDO::FETCH_ASSOC);
				$stmt->closeCursor();

				$guest[] = $row['guestid'];
			}
		}
		$_SESSION['staff'] = $staff ?? '';
		$_SESSION['guest'] = $guest ?? '';
	}
?>
{"Rows":[{
	"status":"<?php echo 'Başarılı.'; ?>"
}],"TableName":"Table",
"Columns":{
	"0":"status"
}}