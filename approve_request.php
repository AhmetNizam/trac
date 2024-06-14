<?php
	require("./library.php");

	$request_uuid = $_GET['inp_request_uuid'] ?? '';
	$request_approver_uuid = $_GET['inp_request_approver_uuid'] ?? '';
	$explanation = $_GET['txt_explanation'] ?? '';

	$status_id = 2;

	if($request_uuid && $request_approver_uuid) {
		$conn = get_mysql_connection();

		if($conn) {
			// Prosedürü çağır (User Id bul)
			$stmt = $conn->prepare("SELECT RAD.AUTHORIZED_PERSON_ID AS userid
									FROM REQUEST_APPROVER_DETAIL RAD
									JOIN REQUEST R ON R.ID = RAD.REQUEST_ID
									WHERE R.UUID = :request_uuid
									  AND RAD.UUID = :request_approver_uuid");
			$stmt->bindParam(':request_uuid', $request_uuid, PDO::PARAM_STR);
			$stmt->bindParam(':request_approver_uuid', $request_approver_uuid, PDO::PARAM_STR);
			$stmt->execute();
			$row = $stmt->fetch(PDO::FETCH_ASSOC);
			$stmt->closeCursor();

			$userid = $row['userid'] ?? 0;

			if($userid > 0) {
				// Prosedürü çağır (User giriş kaydı yap)
				$stmt = $conn->prepare("CALL LOG_LOGIN_ACTIVITY(:userid, @oResult)");
				$stmt->bindParam(':userid', $userid, PDO::PARAM_INT);
				$stmt->execute();
				$stmt->closeCursor();

				// OUT parametresini al (oResult)
				$stmt = $conn->query("SELECT @oResult AS result");
				$row = $stmt->fetch(PDO::FETCH_ASSOC);
				$stmt->closeCursor();

				$result = $row['result'];

				if($result) {
					// Prosedürü çağır (Request_Approver_Detail güncelle)
					$stmt = $conn->prepare("CALL UPDATE_REQUEST_APPROVER_DETAIL(:request_uuid, :request_approver_uuid, :userid, :status_id, :explanation, @oUpdatedRowCount)");
					$stmt->bindParam(':request_uuid', $request_uuid, PDO::PARAM_STR);
					$stmt->bindParam(':request_approver_uuid', $request_approver_uuid, PDO::PARAM_STR);
					$stmt->bindParam(':userid', $userid, PDO::PARAM_INT);
					$stmt->bindParam(':status_id', $status_id, PDO::PARAM_INT);
					$stmt->bindParam(':explanation', $explanation, PDO::PARAM_STR);
					$stmt->execute();
					$stmt->closeCursor();

					// OUT parametresini al (oRequestApproverDetailId)
					$stmt = $conn->query("SELECT @oUpdatedRowCount AS updatedrowcount");
					$row = $stmt->fetch(PDO::FETCH_ASSOC);
					$stmt->closeCursor();

					$updatedrowcount = $row['updatedrowcount'];

					if($updatedrowcount) {
						$new_request_approver_uuid = gen_uuid();

						// Prosedürü çağır (Request_Approver_Detail ekle)
						$stmt = $conn->prepare("CALL ADD_REQUEST_APPROVER_DETAIL(:request_uuid, :request_approver_uuid, :authorized_person_id, @oRequestApproverDetailId)");
						$stmt->bindParam(':request_uuid', $request_uuid, PDO::PARAM_STR);
						$stmt->bindParam(':request_approver_uuid', $new_request_approver_uuid, PDO::PARAM_STR);
						$stmt->bindParam(':authorized_person_id', $userid, PDO::PARAM_INT);
						$stmt->execute();
						$stmt->closeCursor();
					}
				}
			}
			$conn = null;
		}
	}

	if($updatedrowcount) {
?>
{"Rows":[{
	"status":"1",
	"requestid":"<?php echo $updatedrowcount; ?>"
}],"TableName":"Table",
"Columns":{
	"0":"status",
	"1":"requestid"
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