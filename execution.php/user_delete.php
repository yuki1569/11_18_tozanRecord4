<?php
session_start();
include("../functions.php");
$pdo = connect_to_db();
check_session_id();
$user_id = $_SESSION["user_id"];


$sql = "UPDATE users_table
            SET is_deleted='1',
                  updated_at=sysdate()
            WHERE id=:user_id";

$stmt = $pdo->prepare($sql);
$stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
$stmt->execute();


// unset($pdo);
session_start(); // セッションの開始
$_SESSION = array(); // セッション変数を空の配列で上書き
if (isset($_COOKIE[session_name()])) {
  setcookie(session_name(), '', time() - 42000, '/');
} // クッキーの保持期限を過去にする
session_destroy(); // セッションの破棄
header('Location:../index.php'); // ログインページヘ移動
exit();
