<?php
	session_start();

	$uuid = $_GET['uuid'] ?? '';

	if($uuid) {
		unset($_SESSION['request']['traveler_list'][$uuid]);
	} else {
		$_SESSION['request']['traveler_list'] = [];
	}
?>