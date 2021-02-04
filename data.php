<?php
session_start();
include("functions.php");
check_session_id();
$pdo = connect_to_db();
$user_id = $_SESSION["user_id"];

// 参照はSELECT文！
$sql = 'SELECT * FROM tozan_record_table WHERE post_user_id=:user_id';
$stmt = $pdo->prepare($sql);
$stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
$status = $stmt->execute();
// $statusにSQLの実行結果が入る（取得したデータではない点に注意）

//データを表示しやすいようにまとめる
if ($status == false) {
  $error = $stmt->errorInfo();
  exit('sqlError:' . $error[2]);
} else {
  $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
  $output = "";
  foreach ($result as $record) {
    $output .= "<tr>";
    $output .= "<td>{$record["date"]}</td>";
    $output .= "<td>{$record["name"]}</td>";
    $output .= "<td>{$record["time"]}</td>";
    // $A = $record["distance"]}/1000;
    $output .= "<td>" . $record["distance"] / 1000 . "km</td>";
    $output .= "<td>{$record["maximumAltitude"]}m</td>";
    $output .= "</tr>";
  }
}

//歩行距離の合計
$sql = 'SELECT  SUM(distance) FROM tozan_record_table WHERE post_user_id=:user_id';
$stmt = $pdo->prepare($sql);
$stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
$status = $stmt->execute();
$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
// print_r($result[0]);
$totalDistance;
foreach ($result as $record) {
  $totalDistance = $record['SUM(distance)'];
}
//レコードの合計
$sql = 'SELECT  COUNT(*) FROM tozan_record_table WHERE post_user_id=:user_id';
$stmt = $pdo->prepare($sql);
$stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
$status = $stmt->execute();
$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
// print_r($result[0]);
$totalRecord;
foreach ($result as $record) {
  $totalRecord = $record['COUNT(*)'];
}
//活動時間の合計
$sql = 'SELECT
    -- SUM( time_to_sec(time)) as total_sec,
    -- SEC_TO_TIME()秒を「HH:MM:SS」形式に変換します
    -- TIME_TO_SEC()秒に変換された引数を返します
    SEC_TO_TIME(SUM( TIME_TO_SEC(time))) as total_time
FROM
    tozan_record_table WHERE post_user_id=:user_id';
$stmt = $pdo->prepare($sql);
$stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
$status = $stmt->execute();
$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
// print_r($result);
$totalTime;
foreach ($result as $record) {
  $totalTime = $record['total_time'];
}

//日にち順に並び替え
if (isset($_POST['sort-date'])) {
  $sql = 'SELECT * FROM tozan_record_table WHERE user_id=:user_id ORDER BY date DESC';
  $stmt = $pdo->prepare($sql);
  $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
  $status = $stmt->execute();
  if ($status == false) {
    $error = $stmt->errorInfo();
    exit('sqlError:' . $error[2]);
  } else {
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $output = "";
    foreach ($result as $record) {
      $output .= "<tr>";
      $output .= "<td>{$record["date"]}</td>";
      $output .= "<td>{$record["name"]}</td>";
      $output .= "<td>{$record["time"]}</td>";
      $output .= "<td>{$record["distance"]}m</td>";
      $output .= "<td>{$record["maximumAltitude"]}m</td>";
      $output .= "</tr>";
    }
  }
  //活動時間で並び替え
} elseif (isset($_POST['sort-time'])) {
  $sql = 'SELECT * FROM tozan_record_table WHERE user_id=:user_id ORDER BY time DESC';
  $stmt = $pdo->prepare($sql);
  $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
  $status = $stmt->execute();
  if ($status == false) {
    $error = $stmt->errorInfo();
    exit('sqlError:' . $error[2]);
  } else {
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $output = "";
    foreach ($result as $record) {
      $output .= "<tr>";
      $output .= "<td>{$record["date"]}</td>";
      $output .= "<td>{$record["name"]}</td>";
      $output .= "<td>{$record["time"]}</td>";
      $output .= "<td>{$record["distance"]}m</td>";
      $output .= "<td>{$record["maximumAltitude"]}m</td>";
      $output .= "</tr>";
    }
  }
} elseif (isset($_POST['sort-elevation'])) {
  $sql = 'SELECT * FROM tozan_record_table WHERE user_id=:user_id ORDER BY maximumAltitude DESC';
  $stmt = $pdo->prepare($sql);
  $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
  $status = $stmt->execute();
  if ($status == false) {
    $error = $stmt->errorInfo();
    exit('sqlError:' . $error[2]);
  } else {
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $output = "";
    foreach ($result as $record) {
      $output .= "<tr>";
      $output .= "<td>{$record["date"]}</td>";
      $output .= "<td>{$record["name"]}</td>";
      $output .= "<td>{$record["time"]}</td>";
      $output .= "<td>{$record["distance"]}m</td>";
      $output .= "<td>{$record["maximumAltitude"]}m</td>";
      $output .= "</tr>";
    }
  }
} elseif (isset($_POST['sort-distance'])) {
  $sql = 'SELECT * FROM tozan_record_table WHERE user_id=:user_id ORDER BY distance DESC';
  $stmt = $pdo->prepare($sql);
  $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
  $status = $stmt->execute();
  if ($status == false) {
    $error = $stmt->errorInfo();
    exit('sqlError:' . $error[2]);
  } else {
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $output = "";
    foreach ($result as $record) {
      $output .= "<tr>";
      $output .= "<td>{$record["date"]}</td>";
      $output .= "<td>{$record["name"]}</td>";
      $output .= "<td>{$record["time"]}</td>";
      $output .= "<td>{$record["distance"]}m</td>";
      $output .= "<td>{$record["maximumAltitude"]}m</td>";
      $output .= "</tr>";
    }
  }
}

?>



<!DOCTYPE html>
<html lang="ja">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>集計画面</title>
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
  <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.2/css/all.css" integrity="sha384-fnmOCqbTlWIlj8LyTjo7mOUStjsKC4pOpQbqyi7RrhN7udi9RwhKkMHpvLbHG9Sr" crossorigin="anonymous">
  <link rel="stylesheet" href="css/styles.css">

  <style>
    .record {
      text-align: center;
      margin-bottom: 20px;
    }

    .sort-button {
      margin-bottom: 10px;
    }
  </style>
</head>

<body style="width: 70%; margin: 0 auto;">

  <div id="navbar">
    <ul>
      <li><a href="input.php">TOP</a></li>
      <li><a href="follow_page.php">フォロー</a></li>
      <li><a href="find_page.php">探す</a></li>
      <li><a href="data.php">Record</a></li>
      <li><a href="profile_edit.php">プロフィール</a></li>
    </ul>
  </div>
  <br>
  <div class="record">
    <h4>合計登山数</h4>
    <h4> <?= $totalRecord; ?>⛰</h4>
    <h4>合計歩行距離 </h4>
    <h4> <?= $totalDistance / 1000; ?>km</h4>
    <h4>合計活動時間 </h4>
    <h4> <?= $totalTime  ?></h4>
  </div>

  <!-- <table>
        <thead>
          <tr>

          </tr>
        </thead>

        <tbody>
          <tr>
            <td>
              <h4>合計登山数</h4>
            </td>
            <td>
              <h4> <?= $totalRecord; ?>⛰</h4>
            </td>
          </tr>

          <tr>
            <td>
              <h4>合計歩行距離 </h4>
            </td>
            <td>
              <h4> <?= $totalDistance / 1000; ?>km</h4>
            </td>
          </tr>

          <tr>
            <td>
              <h4>合計活動時間 </h4>
            </td>
            <td>
              <h3> <?= $totalTime  ?></h3>
            </td>
          </tr>


        </tbody>
      </table> -->

  <table class="table">
    <div class="sort-button">
      <form action="data.php" method="post">
        <button type="submit" name="sort-date" class="btn btn-info" style="margin-right: 10px;">日にち(降順)</button>
        <button type="submit" name="sort-time" class="btn btn-info" style="margin-right: 10px;">活動時間</button>
        <button type="submit" name="sort-distance" class="btn btn-info" style="margin-right: 10px;">歩行距離</button>
        <button type="submit" name="sort-elevation" class="btn btn-info" style="margin-right: 10px;">標高</button>
      </form>
    </div>
    <thead>
      <tr>
        <th scope="col">活動日</th>
        <th scope="col">山名</th>
        <th scope="col">活動時間</th>
        <th scope="col">歩行距離</th>
        <th scope="col">標高</th>
      </tr>
    </thead>
    <tbody>
      <!-- ここに<tr><td>deadline</td><td>todo</td><tr>の形でデータが入る -->
      <!-- <th scope="row"></th> -->
      <?= $output ?>
    </tbody>
  </table>

</body>

</html>