<?php
session_start();
include("../functions.php");
$pdo = connect_to_db();
check_session_id();

$user_id = $_SESSION["user_id"];
$follow_id = $_GET["id"];



// var_dump($user_id);
// var_dump($follow_id);


// データ登録SQL作成
//update_atは更新した時間を入れる
$sql = "UPDATE follow_table
            SET username=:name,
                  updated_at=sysdate()
            WHERE id=:user_id";

$sql = 'INSERT INTO follow_table
(user_id, follow_user_id)
VALUES (:user_id ,:follow_id)';

// SQL準備&実行
$stmt = $pdo->prepare($sql);
$stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
$stmt->bindValue(':follow_id', $follow_id, PDO::PARAM_INT);
$status = $stmt->execute();
// unset($pdo);
// header('Location:../input.php');
// exit();

// データ登録処理後
if ($status == false) {
  // SQL実行に失敗した場合はここでエラーを出力し，以降の処理を中止する
  $error = $stmt->errorInfo();
  echo json_encode(["error_msg" => "{$error[2]}"]);
  exit();
} else {
  // 正常にSQLが実行された場合は入力ページファイルに移動し，入力ページの処理を実行する
  header("Location:../find_page.php");
  exit();
}
