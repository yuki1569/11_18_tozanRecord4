<?php
// まずはこれ
// var_dump($_GET);
// exit();
// 関数ファイルの読み込み
include('../functions.php');
// GETデータ取得
$like_user_id = $_GET['like_user_id'];
$user_id = $_GET['user_id'];
$image_id = $_GET['image_id'];
// DB接続
$pdo = connect_to_db();

$sql = 'SELECT like_image_id, COUNT(id) AS cnt FROM tozan_record_like_table  
WHERE like_image_id=:image_id
GROUP BY like_image_id';
$stmt = $pdo->prepare($sql);
$stmt->bindValue(':image_id', $image_id, PDO::PARAM_INT);
$status = $stmt->execute();


if ($status == false) {
  // SQL実行に失敗した場合はここでエラーを出力し，以降の処理を中止する
  $error = $stmt->errorInfo();
  echo json_encode(["error_msg" => "{$error[2]}"]);
  exit();
} else {
  $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
  echo json_encode($result); // JSON形式にして出力
  exit();
}