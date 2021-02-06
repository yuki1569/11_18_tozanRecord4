<?php
// 関数ファイルの読み込み
include('../functions.php');
// DB接続
$pdo = connect_to_db();


// GETデータ取得
$like_user_id = $_GET['like_user_id'];
$user_id = $_GET['user_id'];
$image_id = $_GET['image_id'];
// いいね状態のチェック（COUNTで件数を取得できる！）
$sql = 'SELECT COUNT(*) FROM tozan_record_like_table
 WHERE like_user_id=:like_user_id AND like_image_id=:image_id';
$stmt = $pdo->prepare($sql);
$stmt->bindValue(':like_user_id', $like_user_id, PDO::PARAM_INT);
$stmt->bindValue(':image_id', $image_id, PDO::PARAM_INT);
$status = $stmt->execute();
if ($status == false) {
  // エラー処理
} else {
  $like_count = $stmt->fetch();
  var_dump($like_count[0]); // データの件数を確認しよう！
}

// いいねしていれば削除，していなければ追加のSQLを作成
if ($like_count[0] != 0) {
  $sql = 'DELETE FROM tozan_record_like_table
 WHERE like_user_id=:like_user_id AND like_image_id=:image_id';
  $stmt = $pdo->prepare($sql);
  $stmt->bindValue(':like_user_id', $like_user_id, PDO::PARAM_INT);
  $stmt->bindValue(':image_id', $image_id, PDO::PARAM_INT);
} else {
  $sql = 'INSERT INTO tozan_record_like_table
  (id, user_id, like_user_id, like_image_id)
 VALUES(NULL, :user_id, :like_user_id, :image_id)';
  $stmt = $pdo->prepare($sql);
  $stmt->bindValue(':like_user_id', $like_user_id, PDO::PARAM_INT);
  $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
  $stmt->bindValue(':image_id', $image_id, PDO::PARAM_INT);
}
$stmt->execute(); // SQL実行


// DB接続
$pdo = connect_to_db();
// GETデータ取得
$image_id = $_GET['image_id'];
$user_id = $_GET['user_id'];
$image_id = $_GET['image_id'];
$sql = 'SELECT like_image_id, COUNT(id) AS cnt FROM tozan_record_like_table  
WHERE like_image_id=:image_id
GROUP BY like_image_id';
$stmt = $pdo->prepare($sql);
$stmt->bindValue(':image_id', $image_id, PDO::PARAM_INT);
$stmt->execute();
// $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
// echo json_encode($result); // JSON形式にして出力

// if ($status == false) {
//   // SQL実行に失敗した場合はここでエラーを出力し，以降の処理を中止する
//   $error = $stmt->errorInfo();
//   echo json_encode(["error_msg" => "{$error[2]}"]);
//   exit();
// } else {
//   $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
//   echo json_encode($result); // JSON形式にして出力
//   exit();
// }
