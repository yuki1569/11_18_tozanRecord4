<?php
session_start();
include("../functions.php");
$pdo = connect_to_db();
check_session_id();

$id = $_POST['id'];

// var_dump($_FILES['image2']['name']);
// var_dump($_FILES['image2']['type']);
// var_dump($_FILES['image2']['tmp_name']);
// var_dump($_FILES['image2']['size']);
// var_dump($_POST['name']);

$name = $_POST['name'];
$date = $_POST['date'];
$time = $_POST['time'];
$distance = $_POST['distance'];
$maximumAltitude = $_POST['maximumAltitude'];

//写真のデータが送信されているか分岐
if(empty($_FILES['image2']['name'])) {
  $sql = "UPDATE tozan_record_table
            SET name=:name,
                  date=:date,
                  time=:time,
                  distance = :distance,
                  maximumAltitude=:maximumAltitude,
                  created_at=sysdate()
            WHERE image_id=:id";
  // SQL準備&実行
  $stmt = $pdo->prepare($sql);
  $stmt->bindValue(':id', $id, PDO::PARAM_INT);
  $stmt->bindValue(':name', $name, PDO::PARAM_STR);
  $stmt->bindValue(':date', $date, PDO::PARAM_STR);
  $stmt->bindValue(':time', $time, PDO::PARAM_STR);
  $stmt->bindValue(':distance', $distance, PDO::PARAM_STR);
  $stmt->bindValue(':maximumAltitude', $maximumAltitude, PDO::PARAM_STR);
  $status = $stmt->execute();
} elseif(!empty($_FILES['image2']['name'])) {
  $file = "images/$image";
  $image = uniqid(mt_rand(), true); //ファイル名をユニーク化
  $image .= '.' . substr(strrchr($_FILES['image2']['name'], '.'), 1); //アップロードされたファイルの拡張子を取得
// データ登録SQL作成
//update_atは更新した時間を入れる
$sql = "UPDATE tozan_record_table
            SET image_name=:image,
                  name=:name,
                  date=:date,
                  time=:time,
                  distance = :distance,
                  maximumAltitude=:maximumAltitude,
                  created_at=sysdate()
            WHERE image_id=:id";
// SQL準備&実行
$stmt = $pdo->prepare($sql);
$stmt->bindValue(':id', $id, PDO::PARAM_INT);
$stmt->bindValue(':image', $image, PDO::PARAM_STR);
$stmt->bindValue(':name', $name, PDO::PARAM_STR);
$stmt->bindValue(':date', $date, PDO::PARAM_STR);
$stmt->bindValue(':time', $time, PDO::PARAM_STR);
$stmt->bindValue(':distance', $distance, PDO::PARAM_STR);
$stmt->bindValue(':maximumAltitude',$maximumAltitude, PDO::PARAM_STR);
move_uploaded_file($_FILES['image2']['tmp_name'], './../images/' . $image);//imagesディレクトリにファイル保存
$status = $stmt->execute();

}


// データ登録処理後
if ($status == false) {
  // SQL実行に失敗した場合はここでエラーを出力し，以降の処理を中止する
  $error = $stmt->errorInfo();
  echo json_encode(["error_msg" => "{$error[2]}"]);
  exit();
} else {
  // 正常にSQLが実行された場合は入力ページファイルに移動し，入力ページの処理を実行する
  header("Location:../input.php");
  exit();
}

