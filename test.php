<pre>
<?php
	include("functions.php");

	$b = set_null($a ?? '');

	echo '[userid] => ' . ($_SESSION['userid'] ?? '') . '<br/>';
	echo '[username] => ' . ($_SESSION['username'] ?? '') . '<br/>';
//	echo '[password] => ' . ($_SESSION['password'] ?? '') . '<br/>';
//	echo '[user_info] => '; print_r($_SESSION['user_info'] ?? ''); echo '<br/>';
	echo '[request] => '; print_r($_SESSION['request'] ?? ''); echo '<br/>';
/*
	foreach($_SESSION['request']['traveler_list'] as $uuid => $traveler) {
		print_r($traveler);
		echo $uuid . ' -> ' . $traveler['name'];
	}
*/
//	echo date('d.m.Y', strtotime('1977-12-17'));

//	print_r($_SESSION['traveler_list_test'] ?? '');
/*
	$array = ['a' => '1', 'b' => '2', 'c' => '3'];

	print_r($array);

	unset($array['b']);

	print_r($array);

	$array['b'] = 7;

	print_r($array);
*/
/*
$x = 5;
$y = "5";

if($x != $y) {
    echo "!= sonucu $x ve $y eşit değiller.<br/><br/>";
} else {
	echo "!= sonucu olumsuz<br/><br/>";
}

if($x !== $y) {
    echo "!== sonucu $x ve $y tam olarak eşit değiller.<br/><br/>";
} else {
	echo "!== sonucu olumsuz<br/><br/>";
}
*/
/*
$a = '';
$b = 'ahmet';
$c = 'nizam';

$a = $b ?? $c;
echo "1. $a ";

$b = '';
$a = $b ?? $c;
echo "2. $a ";

$b = null;
$a = $b ?? $c;
echo "3. $a ";

unset($b);
$a = $b ?? $c;
echo "4. $a ";
*/
?>
</pre>