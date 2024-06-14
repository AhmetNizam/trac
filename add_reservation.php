<?php
	require("./library.php");

	$status = '0';
	$requestid = $_GET['inp_requestid'];

	$conn = get_mysql_connection();

	if($conn) {
		$stmt = $conn->prepare(" SELECT TRANSPORTATION AS transportation, ACCOMMODATION AS accommodation
								 FROM REQUEST
								 WHERE ID = :requestid ");

		$stmt->bindParam(':requestid', $requestid, PDO::PARAM_INT);
		$stmt->execute();
		$row = $stmt->fetch(PDO::FETCH_ASSOC);
		$stmt->closeCursor();

		$transportation_on_off = $row['transportation'];
		$accommodation_on_off = $row['accommodation'];

		if($transportation_on_off) {
			$departure['transportation_modeid'] = set_null($_GET['sel_departure_transportation_mode'] ?? '');
			$departure['transportation_mode'] = set_null($_GET['inp_departure_transportation_mode'] ?? '');
			$departure['port'] = set_null($_GET['inp_departure_port'] ?? '');
			$departure['company'] = set_null($_GET['inp_departure_company'] ?? '');
			$departure['pnr_code'] = set_null($_GET['inp_departure_pnr_code'] ?? '');
			$departure['ticket_number'] = set_null($_GET['inp_departure_ticket_number'] ?? '');
			$departure['ticket_price'] = set_null($_GET['inp_departure_ticket_price'] ?? '');
			$departure['car_license_plate'] = set_null($_GET['inp_departure_car_license_plate'] ?? '');
			$departure['date'] = set_null($_GET['inp_departure_date'] ?? '');

			$return['transportation_modeid'] = set_null($_GET['sel_return_transportation_mode'] ?? '');
			$return['transportation_mode'] = set_null($_GET['inp_return_transportation_mode'] ?? '');
			$return['port'] = set_null($_GET['inp_return_port'] ?? '');
			$return['company'] = set_null($_GET['inp_return_company'] ?? '');
			$return['pnr_code'] = set_null($_GET['inp_return_pnr_code'] ?? '');
			$return['ticket_number'] = set_null($_GET['inp_return_ticket_number'] ?? '');
			$return['ticket_price'] = set_null($_GET['inp_return_ticket_price'] ?? '');
			$return['car_license_plate'] = set_null($_GET['inp_return_car_license_plate'] ?? '');
			$return['date'] = set_null($_GET['inp_return_date'] ?? '');
		} else {
			$departure['transportation_modeid'] = null;
			$departure['transportation_mode'] = null;
			$departure['port'] = null;
			$departure['company'] = null;
			$departure['pnr_code'] = null;
			$departure['ticket_number'] = null;
			$departure['ticket_price'] = null;
			$departure['car_license_plate'] = null;
			$departure['date'] = null;

			$return['transportation_modeid'] = null;
			$return['transportation_mode'] = null;
			$return['port'] = null;
			$return['company'] = null;
			$return['pnr_code'] = null;
			$return['ticket_number'] = null;
			$return['ticket_price'] = null;
			$return['car_license_plate'] = null;
			$return['date'] = null;
		}

		if($accommodation_on_off) {
			$accommodation['check-in_date'] = set_null($_GET['inp_check-in_date'] ?? '');
			$accommodation['check-out_date'] = set_null($_GET['inp_check-out_date'] ?? '');
			$accommodation['hotel_name'] = set_null($_GET['inp_hotel_name'] ?? '');
		} else {
			$accommodation['check-in_date'] = null;
			$accommodation['check-out_date'] = null;
			$accommodation['hotel_name'] = null;
		}

		$_SESSION['reservation']['uuid'] = gen_uuid();
		$_SESSION['reservation']['reservation_date'] = date('d.m.Y');
		$_SESSION['reservation']['requestid'] = $requestid;
		$_SESSION['reservation']['transportation_on_off'] = $transportation_on_off;
		$_SESSION['reservation']['transportation_info']['departure'] = $departure;
		$_SESSION['reservation']['transportation_info']['return'] = $return;
		$_SESSION['reservation']['accommodation_on_off'] = $accommodation_on_off;
		$_SESSION['reservation']['accommodation_info'] = $accommodation;

		$conn = null;
		$status = '1';
	}
?>
{"Rows":[{
	"status":"<?php echo $status; ?>"
}],"TableName":"Table",
"Columns":{
	"0":"status"
}}