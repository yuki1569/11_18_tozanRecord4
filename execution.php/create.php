<?php
include("../functions.php");
$pdo = connect_to_db();
print_r($_POST) . PHP_EOL;
print_r($_FILES) . PHP_EOL;
// exit();
if (
  //isset($var) varが存在してull以外の値をとればtrue,そうでなければfalse
  //ここでは!なので値がセットされていない場合となる
  !isset($_POST['name']) ||
  $_POST['name'] == '' ||
  !isset($_POST['date']) ||
  $_POST['date'] == ''
) {
  exit('ParamError');
}


$image = uniqid(mt_rand(), true); //ファイル名をユニーク化
$image .= '.' . substr(strrchr($_FILES['image']['name'], '.'), 1); //アップロードされたファイルの拡張子を取得
$user_id = $_POST['user_id'];
$file = "images/$image";
$name = $_POST['name'];
$date = $_POST['date'];
$time = $_POST['time'];
$distance = $_POST['distance'];
$maximumAltitude = $_POST['maximumAltitude'];

$sql = 'INSERT INTO tozan_record_table
(post_user_id, image_name, created_at,name,date,time,distance,maximumAltitude)
VALUES (:user_id ,:image,now(),:name,:date,:time,:distance,:maximumAltitude)';


$stmt = $pdo->prepare($sql);
$stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
$stmt->bindValue(':image', $image, PDO::PARAM_STR);
$stmt->bindValue(':name', $name, PDO::PARAM_STR);
$stmt->bindValue(':date', $date, PDO::PARAM_STR);
$stmt->bindValue(':time', $time, PDO::PARAM_STR);
$stmt->bindValue(':distance', $distance, PDO::PARAM_STR);
$stmt->bindValue(':maximumAltitude', $maximumAltitude, PDO::PARAM_STR);
move_uploaded_file($_FILES['image']['tmp_name'], './../images/' . $image);//imagesディレクトリにファイル保存

$stmt->execute();
// }
unset($pdo);
header('Location:../input.php');
exit();

// 失敗時にエラーを出力し，成功時は登録画面に戻る
if ($status == false) {
  $error = $stmt->errorInfo();
  // データ登録失敗次にエラーを表示
  exit('sqlError:' . $error[2]);
} else {
  // 登録ページへ移動
  header('Location:../user-login.php');
}
