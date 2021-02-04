<?php
include("../functions.php");
$pdo = connect_to_db();

$sql = 'SELECT * FROM tozan_record_table ;
WHERE image_id = :image_id';
$stmt = $pdo->prepare($sql);
$stmt->bindValue(':image_id', (int)$_GET['id'], PDO::PARAM_INT);
$images = $stmt->fetchAll();
$stmt->execute();
$sql = 'DELETE FROM tozan_record_table WHERE image_id = :image_id';
$stmt = $pdo->prepare($sql);
$stmt->bindValue(':image_id', (int)$_GET['id'], PDO::PARAM_INT);
$stmt->execute();

unset($pdo);
header('Location:../input.php');
exit();
