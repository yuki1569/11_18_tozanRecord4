<?php
include("functions.php");
$pdo = connect_to_db();

?>

<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">

<body style="max-width: 700px; margin: 0 auto;">
  <!-- <a href="input.php">戻る</a> -->
  <!-- <div style="text-align:center">
    <img src="image.php?id=<?= $record['image_id']; ?>" width="500px" height="auto">
  </div> -->
  <div style="text-align: center; margin-top:30px;">
    <h1>ログイン・登録</h1>
  </div>
  <form action="execution.php/user-login.php" method="POST" style="width: 60%; margin:0 auto;">
    <table class="table">
      <thead>
      </thead>
      <tbody>
        <tr>
          <td class="col-md-5">Name</td>
          <td class="col-md-5"><input type="text" name="username" value=""></td>
        </tr>
        <tr>
          <td class="col-md-5">Pass</td>
          <td class="col-md-5"><input type="text" name="password" value=""></td>
        </tr>

    </table>
    <button type="submit" class="btn btn-primary">登録</button>
    <button type="submit" name="login" class="btn btn-info">ログイン</button>


  </form>
</body>