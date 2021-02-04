<?php
session_start();
include("../functions.php");
$pdo = connect_to_db();
check_session_id();

$user_id = $_SESSION["user_id"];
$name = $_POST['name'];

// var_dump($user_id);
var_dump($_FILES['user_image']);
// exit();

if($_FILES['user_image']['name'] != '') {

  $uploaded_file_name = $_FILES['user_image']['name']; //ファイル名の取得
  $temp_path = $_FILES['user_image']['tmp_name']; //tmpフォルダの場所
  // $directory_path = '../user_images/'; //アップロード先ォルダ
  // ファイルの拡張子の種類を取得．
  $extension = pathinfo($uploaded_file_name, PATHINFO_EXTENSION);
  // ファイルごとにユニークな名前を作成．（最後に拡張子を追加）
  $unique_name = date('YmdHis') . md5(session_id()) . "." . $extension;
  // // ファイルの保存場所をファイル名に追加．
  // $filename_to_save = $directory_path . $unique_name;

  // if (!is_uploaded_file($temp_path)) {
  //   exit('Error:画像がありません'); // tmpフォルダにデータがない
  // } else { // ↓ここでtmpファイルを移動する
  //   if (!move_uploaded_file($temp_path, $filename_to_save)) {
  //     exit('Error:アップロードできませんでした'); // 画像の保存に失敗
  //   } else {
  //     chmod($filename_to_save, 0644); // 権限の変更
  //     // 今回は権限を変更するところまで
  //   }
  // }

  $sql = "UPDATE users_table
            SET username=:name,
                  user_image=:user_image,
                  updated_at=sysdate()
            WHERE id=:user_id";


  // SQL準備&実行
  $stmt = $pdo->prepare($sql);
  $stmt->bindValue(':name', $name, PDO::PARAM_STR);
  $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
  $stmt->bindValue(':user_image', $unique_name, PDO::PARAM_STR);
  move_uploaded_file($_FILES['user_image']['tmp_name'], './../user_images/' . $unique_name);//imagesディレクトリにファイル保存
  $status = $stmt->execute();


} else {

  //update_atは更新した時間を入れる
  $sql = "UPDATE users_table
              SET username=:name,
              user_image='circle12.png',
              updated_at=sysdate()
              WHERE id=:user_id";
  
  // SQL準備&実行
  $stmt = $pdo->prepare($sql);
  $stmt->bindValue(':name', $name, PDO::PARAM_STR);
  $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
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
