<?php

function connect_to_db()
{
  // DB接続の設定
  $dbn = 'mysql:dbname=gsacf_d07_18;charset=utf8;port=3306;host=localhost';
  $user = 'root';
  $pwd = '';

  try {
    // ここでDB接続処理を実行する
    return new PDO($dbn, $user, $pwd);
  } catch (PDOException $e) {
    // DB接続に失敗した場合はここでエラーを出力し，以降の処理を中止する
    echo json_encode(["db error" => "{$e->getMessage()}"]);
    exit();
  }
}


// ログイン状態のチェック関数
// ログインしているかどうかのチェック→毎回id再生成
function check_session_id()
{
  // 失敗時はログイン画面に戻る
  if (
    !isset($_SESSION['session_id']) || // session_idがない
    $_SESSION['session_id'] != session_id() // idが一致しない
  ) {
    header('Location: index.php'); // ログイン画面へ移動
  } else {
    session_regenerate_id(true); // セッションidの再生成
    $_SESSION['session_id'] = session_id(); // セッション変数上書き
  }
}

//レコードを合計する関数
function totalRecord($id) {
  $pdo = connect_to_db();
  $sql = 'SELECT  COUNT(*) FROM tozan_record_table WHERE post_user_id=:user_id';
  $stmt = $pdo->prepare($sql);
  $stmt->bindValue(':user_id', $id, PDO::PARAM_INT);
  $stmt->execute();
  $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
  foreach ($result as $record) {
    return $record['COUNT(*)'];
  }
}

//歩行距離の合計を出す関数
function totalDistance($id) {
  $pdo = connect_to_db();
  $sql = 'SELECT  SUM(distance) FROM tozan_record_table WHERE post_user_id=:user_id';
  $stmt = $pdo->prepare($sql);
  $stmt->bindValue(':user_id', $id, PDO::PARAM_INT);
  $stmt->execute();
  $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
  foreach ($result as $record) {
    $total = $record['SUM(distance)'];
  }
  if($total > 0) {
    return $total;
  } else {
    return 0;
  }
}

//活動時間の合計を出す関数
function totalTime($id) {
  $pdo = connect_to_db();
  $sql = 'SELECT
      -- SUM( time_to_sec(time)) as total_sec,
      -- SEC_TO_TIME()秒を「HH:MM:SS」形式に変換します
      -- TIME_TO_SEC()秒に変換された引数を返します
      SEC_TO_TIME(SUM( TIME_TO_SEC(time))) as total_time
  FROM
      tozan_record_table WHERE post_user_id=:user_id';
  $stmt = $pdo->prepare($sql);
  $stmt->bindValue(':user_id', $id, PDO::PARAM_INT);
  $stmt->execute();
  $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
  foreach ($result as $record) {
    $total = $record['total_time'];
  }

  if ($total > 0) {
    return $total;
  } else {
    return 0;
  }
}

