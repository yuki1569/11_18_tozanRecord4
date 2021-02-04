<?php
// まずはこれ
var_dump($_GET);
// exit();
// 関数ファイルの読み込み
include('../functions.php');
// GETデータ取得
$like_user_id = $_GET['like_user_id'];
$user_id = $_GET['user_id'];
$image_id = $_GET['image_id'];
// DB接続
$pdo = connect_to_db();

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
  // exit();
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
// INSERTのSQLは前項で使用したものと同じ！
// 以降（SQL実行部分と一覧画面への移動）は変更なし！
// SQL文は1行にまとめる

$status = $stmt->execute(); // SQL実行
if ($status == false) {
  // エラー処理
} else {
  header('Location:../follow_page.php');
}
