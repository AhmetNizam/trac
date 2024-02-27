<?php
	$servername = "localhost";
	$dbname = "transportation_accommodation";
	$username = "root";
	$password = "an112743.";

	try {
		$conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
		// set the PDO error mode to exception
		$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

		echo "Connected successfully<br/><br/>";

		$result = $conn->query('SELECT * FROM reason_for_travel');

		foreach ($result as $row) {
			print $row['ID'] . "&#09;";
			print $row['NAME'] . "<br/>";
		}

		$sth = null;
		$dbh = null;
	} catch(PDOException $e) {
		echo "Connection failed: " . $e->getMessage();
	}
?>

<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>MySQL Connect Test</title>
</head>

<body>
</body>
</html>