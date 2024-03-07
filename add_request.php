<?php
	require("functions.php");

	$travel['travel_routeid'] = $_GET['inp_route'];
	$travel['travel_reasonid'] = $_GET['sel_travel_reason'];
	$travel['from_countryid'] = set_null($_GET['sel_from_country'] ?? '');
	$travel['from_locationid'] = set_null($_GET['sel_from_location'] ?? '');
	$travel['from_cityid'] = set_null($_GET['sel_from_city'] ?? '');
	$travel['from_city'] = set_null($_GET['inp_from_city'] ?? '');
	$travel['to_countryid'] = set_null($_GET['sel_to_country'] ?? '');
	$travel['to_locationid'] = set_null($_GET['sel_to_location'] ?? '');
	$travel['to_cityid'] = set_null($_GET['sel_to_city'] ?? '');
	$travel['to_city'] = set_null($_GET['inp_to_city'] ?? '');

	switch($_GET['rb_trac']) {
		case 1:
			$transportation_on_off = true;
			$accommodation_on_off = true;
			break;
		case 2:
			$transportation_on_off = true;
			$accommodation_on_off = false;
			break;
		case 3:
			$transportation_on_off = false;
			$accommodation_on_off = true;
			break;
	}

	if($transportation_on_off) {
		$transportation['departure_date'] = set_null($_GET['inp_departure_date'] ?? '');
		$transportation['return_date'] = set_null($_GET['inp_return_date'] ?? '');
		$transportation['transfer_need_situation'] = set_null($_GET['rb_tns'] ?? '');
		$transportation['transfer_need_detail'] = $transportation['transfer_need_situation'] ? set_null($_GET['inp_transfer_need_detail'] ?? '') : null;
		$transportation['transportation_modeid'] = set_null($_GET['sel_transportation_mode'] ?? '');
		$transportation['transportation_detail'] = set_null($_GET['txt_transportation_detail'] ?? '');
	} else {
		$transportation['departure_date'] = null;
		$transportation['return_date'] = null;
		$transportation['transfer_need_situation'] = null;
		$transportation['transfer_need_detail'] = null;
		$transportation['transportation_modeid'] = null;
		$transportation['transportation_detail'] = null;
	}

	if($accommodation_on_off) {
		$accommodation['check-in_date'] = set_null($_GET['inp_check-in_date'] ?? '');
		$accommodation['check-out_date'] = set_null($_GET['inp_check-out_date'] ?? '');
		$accommodation['accommodation_detail'] = set_null($_GET['txt_accommodation_detail'] ?? '');
	} else {
		$accommodation['check-in_date'] = null;
		$accommodation['check-out_date'] = null;
		$accommodation['accommodation_detail'] = null;
	}

	$_SESSION['request']['travel_info'] = $travel;
	$_SESSION['request']['transportation_on_off'] = $transportation_on_off;
	$_SESSION['request']['transportation_info'] = $transportation;
	$_SESSION['request']['accommodation_on_off'] = $accommodation_on_off;
	$_SESSION['request']['accommodation_info'] = $accommodation;
?>
{"Rows":[{
	"status":"1"
}],"TableName":"Table",
"Columns":{
	"0":"status"
}}