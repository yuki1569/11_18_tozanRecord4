<?php
session_start(); // セッションの開始
include("../functions.php");
$pdo = connect_to_db();

$username = $_POST['username'];
$password = $_POST['password'];

if (
  !isset($_POST['username']) ||
  $_POST['username'] == '' ||
  !isset($_POST['password']) ||
  $_POST['password'] == ''
) {
  echo "ユーザー名、パスワードを入力してください";
  echo '<a href="../index.php">login</a>';
  exit();
}

//loginが押されたとき
if (isset($_POST['login'])) {
  // DBにデータがあるかどうか検索
  $sql = 'SELECT * FROM users_table
  -- WHEREで条件を指定！
  WHERE username=:username 
  AND password=:password';
  $stmt = $pdo->prepare($sql);
  $stmt->bindValue(':username', $username, PDO::PARAM_STR);
  $stmt->bindValue(':password', $password, PDO::PARAM_STR);
  $status = $stmt->execute();
  // DBのデータ有無で条件分岐
  $val = $stmt->fetch(PDO::FETCH_ASSOC); // 該当レコードだけ取得
  if (!$val) { // 該当データがないときはログインページへのリンクを表示
    echo "<p>ログイン情報に誤りがあります．</p>";
    echo '<a href="../index.php">login</a>';
    exit();
    // DBにデータがあればセッション変数に格納
  } elseif($val["is_deleted"] == 1) {
    echo "<p>アカウントが削除されています</p>";
    echo '<a href="../index.php">login</a>';
    exit();
  } else {
    $_SESSION = array(); // セッション変数を空にする
    $_SESSION["session_id"] = session_id();
    $_SESSION["is_admin"] = $val["is_admin"];
    $_SESSION["user_id"] = $val["id"];
    $_SESSION["username"] = $val["username"];
    // print_r($_SESSION["user_id"]);
    header("Location:../input.php"); // 一覧ページへ移動
    exit();
  }

//登録が押されたとき
} else {
  // ユーザ存在有無確認
  $sql = 'SELECT COUNT(*) FROM users_table WHERE username=:username';

  $stmt = $pdo->prepare($sql);
  $stmt->bindValue(':username', $username, PDO::PARAM_STR);
  $status = $stmt->execute();

  if ($status == false) {
    // SQL実行に失敗した場合はここでエラーを出力し，以降の処理を中止する
    $error = $stmt->errorInfo();
    echo json_encode(["error_msg" => "{$error[2]}"]);
    exit();
  }

  if ($stmt->fetchColumn() > 0) {
    // usernameが1件以上該当した場合はエラーを表示して元のページに戻る
    // $count = $stmt->fetchColumn();
    echo "<p>すでに登録されているユーザです．</p>";
    echo '<a href="../index.php">login</a>';
    exit();
  }

  // ユーザ登録SQL作成
  // `created_at`と`updated_at`には実行時の`sysdate()`関数を用いて実行時の日時を入力する
  $sql = 'INSERT INTO users_table(id, username, password, is_admin, is_deleted, created_at, updated_at) VALUES(NULL, :username, :password, 0, 0, sysdate(), sysdate())';

  // SQL準備&実行
  $stmt = $pdo->prepare($sql);
  $stmt->bindValue(':username', $username, PDO::PARAM_STR);
  $stmt->bindValue(':password', $password, PDO::PARAM_STR);
  $status = $stmt->execute();

  // データ登録処理後
  if ($status == false) {
    // SQL実行に失敗した場合はここでエラーを出力し，以降の処理を中止する
    $error = $stmt->errorInfo();
    echo json_encode(["error_msg" => "{$error[2]}"]);
    exit();
  } else {
    // 正常にSQLが実行された場合は入力ページファイルに移動し，入力ページの処理を実行する
    echo "<p>登録されました</p>";
    echo '<a href="../index.php">login</a>';
    exit();
  }
}
  // session変数には必要な値を保存する（今回は管理者フラグとユーザ名）．
  // 自身のアプリで使いたい値を保存しましょう！