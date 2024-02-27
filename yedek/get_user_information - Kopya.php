<?php
	session_start();
	$user_id = $_GET['user_id'];

	include("connect_mysql.php");

	$connection = get_mysql_connection();

	if(!$connection) {
		
	} else {
		$dataset = $connection->query(" SELECT USR.ID, POS.NAME AS POSITION_NAME, DEP.NAME AS DEPARTMENT_NAME, LOC.NAME AS LOCATION_NAME
										FROM USER USR
										JOIN POSITION POS ON POS.ID = USR.POSITION_ID
										JOIN DEPARTMENT DEP ON DEP.ID = USR.DEPARTMENT_ID
										JOIN LOCATION LOC ON LOC.ID = USR.LOCATION_ID
										WHERE USR.ID = " . $user_id );
?>
{"Rows":[
<?php
		$comma = "";
		foreach ($dataset as $datarow) {
			echo $comma;
?>
{"id":"<?php echo $datarow['ID']; ?>","pos_name":"<?php echo trim($datarow['POSITION_NAME']); ?>","dep_name":"<?php echo trim($datarow['DEPARTMENT_NAME']); ?>","loc_name":"<?php echo trim($datarow['LOCATION_NAME']); ?>"}
<?php
			$comma = ",";
		}

		$result = null;
		$connection = null;		
	}
?>
],"TableName":"Table","Columns":{"0":"id","1":"pos_name","2":"dep_name","3":"loc_name"}}