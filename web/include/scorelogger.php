<?php
include_once '../../include/db_connect.php';
$scorelist = $_POST['scorelist'];
$name = $_POST['name'];
$col1 = $_POST['col1'];
$col2 = $_POST['col2'];
$col3 = $_POST['col3'];
$col4 = $_POST['col4'];
$col5 = $_POST['col5'];

$stmt = $mysqli->prepare("SELECT id FROM scorelists WHERE short_name = ?");
$stmt->bind_param('s', $scorelist);
$stmt->execute();
$stmt->bind_result($index);
if ($stmt->fetch())
{
	$stmt->close();
	$stmt = $mysqli->prepare("INSERT INTO scores (scorelist_id, name, time, col1, col2, col3, col4, col5) VALUES (?, ?, NOW(), ?, ?, ?, ?, ?)");
	$stmt->bind_param('isiiiii', $index, $name, $col1, $col2, $col3, $col4, $col5);
	$stmt->execute();
	$stmt->close();
}
else
{
	$stmt->close();
	echo "Scorelist $scorelist not found.";
}
?>
