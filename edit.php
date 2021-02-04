<?php
session_start();
include("functions.php");
$pdo = connect_to_db();
check_session_id();

$id = $_GET['id'];
//:idはユーザーから送られてきたidデータ
$sql = 'SELECT * FROM tozan_record_table WHERE image_id=:id';
$stmt = $pdo->prepare($sql);
//送られてきたidをバインド変数にする
$stmt->bindValue(':id', $id, PDO::PARAM_INT);
$status = $stmt->execute();

if ($status == false) {
  // SQL実行に失敗した場合はここでエラーを出力し，以降の処理を中止する
  $error = $stmt->errorInfo();
  echo json_encode(["error_msg" => "{$error[2]}"]);
  exit();
} else {
  // fetch()で1レコード取得できる////////////////////////////////////////////////////////////////////////////////////////////////////////////
  $record = $stmt->fetch(PDO::FETCH_ASSOC);
}

?>

<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
<link rel="stylesheet" href="css/styles.css">

<body style="max-width: 700px; margin: 0 auto;">
  <div id="navbar">
    <ul>
      <li><a href="input.php">TOP</a></li>
      <li><a href="follow_page.php">フォロー</a></li>
      <li><a href="find_page.php">探す</a></li>
      <li><a href="data.php">Record</a></li>
      <li><a href="profile_edit.php">プロフィール</a></li>
    </ul>
  </div>
  <div style="text-align:center">

    <img src="images/<?php echo $record['image_name']; ?>" width="500px" height="auto">
  </div>

  <!-- 写真をupすときはenctype="multipart/form-data"が必要みたい -->
  <form action="execution.php/update.php" method="POST" enctype="multipart/form-data" style="width: 60%; margin:0 auto;">
    <table class="table">
      <thead>
      </thead>
      <tbody>
        <tr>
          <td class="col-md-5">山名:</td>
          <td class="col-md-5"><input type="text" name="name" value="<?= $record["name"] ?>"></td>
        </tr>
        <tr>
          <td>日付:</td>
          <td><input type="date" name="date" value="<?= $record["date"] ?>"></td>
        </tr>
        <tr>
          <td>時間:</td>
          <td><input type="time" step="300" name="time" value="<?= $record["time"] ?>"></td>
        </tr>
        <tr>
          <td>距離:</td>
          <td><input type="text" name="distance" value="<?= $record["distance"] ?>"></td>
        </tr>
        <tr>
          <td>最大標高:</td>
          <td><input type="text" name="maximumAltitude" value="<?= $record["maximumAltitude"] ?>"></td>
        </tr>
        <tr>
          <td>画像を選択</td>
          <td><input type="file" name="image2"></td>
        </tr>
      </tbody>
      <!-- ユーザーに編集されたくないデータはhiddenで隠す。たとえはidとか -->
      <input type="hidden" name="id" value="<?= $record['image_id'] ?>">
    </table>
    <button type="submit" class="btn btn-primary">保存</button>

  </form>
</body>