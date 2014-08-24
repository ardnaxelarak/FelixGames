<?php
include_once '../../include/db_connect.php';
$name = $_POST['name'];
$comment = $_POST['comment'];
$index = $_POST['index'];

$comment = nl2br(htmlspecialchars($comment));

$stmt = $mysqli->prepare("INSERT INTO comments (game_id, name, time, comment) VALUES (?, ?, NOW(), ?)");
$stmt->bind_param('iss', $index, $name, $comment);
$stmt->execute();
$stmt->close();
?>
