<?php
session_start();
include("functions.php");
check_session_id();
$pdo = connect_to_db();
$user_id = $_SESSION["user_id"];

// 参照はSELECT文！
$sql = 'SELECT * FROM users_table WHERE id=:user_id';
$stmt = $pdo->prepare($sql);
$stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
$status = $stmt->execute();
$profile = $stmt->fetch();
// $statusにSQLの実行結果が入る（取得したデータではない点に注意）

//データを表示しやすいようにまとめる
if ($status == false) {
  $error = $stmt->errorInfo();
  exit('sqlError:' . $error[2]);
} else {
}
?>

<!DOCTYPE html>
<html lang="ja">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>プロフィール</title>
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
  <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.2/css/all.css" integrity="sha384-fnmOCqbTlWIlj8LyTjo7mOUStjsKC4pOpQbqyi7RrhN7udi9RwhKkMHpvLbHG9Sr" crossorigin="anonymous">
  <link rel="stylesheet" href="css/styles.css">
  <style>
    .sort-button {
      margin-bottom: 10px;
    }
  </style>

  <script>
    function check() {
      if (window.confirm("変更しますか？")) {
        return true;
      } else {
        return false;
      }
    }

    function delet() {
      if (window.confirm("アカウントを削除してもいいですか？")) {
        return true;
      } else {
        return false;
      }
    }
  </script>
</head>

<body style="max-width: 900px; margin: 0 auto;">

  <div id="navbar">
    <ul>
      <li><a href="input.php">TOP</a></li>
      <li><a href="follow_page.php">フォロー</a></li>
      <li><a href="find_page.php">探す</a></li>
      <li><a href="data.php">Record</a></li>
      <li><a href="profile_edit.php">プロフィール</a></li>
    </ul>
  </div>



  <form action="execution.php/profile_update.php" method="POST" enctype="multipart/form-data" style="width: 60%; margin:0 auto;" onsubmit="return check()">

    <table class="table">
      <thead>
        <h2>プロフィール</h2>
      </thead>
      <tbody>
        <tr>
          <td class="col-md-5">名前</td>
          <td class="col-md-5"><input type="text" name="name" value="<?= $profile["username"] ?>"></td>
        </tr>
        <tr>
          <td>画像を選択</td>
          <td><input type="file" name="user_image" accept="image/*" capture="camera"></td>

        </tr>
      </tbody>
    </table>
    <button type="submit" id="submit" class="btn btn-primary">保存</button>
  </form>

  <form action="execution.php/user_delete.php" style="width: 60%; margin:0 auto; margin-top:10px;" onsubmit="return delet()">
    <button type="submit" class="btn btn-danger">削除</button>
  </form>


  <!-- 削除ボタン -->
  <!-- <a href="javascript:void(0);" onclick="var ok = confirm('削除しますか？'); if (ok) location.href='delete.php?id=<?= $images[$i]['image_id']; ?>'"> -->


</body>

</html>