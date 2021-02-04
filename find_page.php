<?php
session_start();
include("functions.php");
$pdo = connect_to_db();
check_session_id();
$user_id = $_SESSION["user_id"];

// $sql = 'SELECT * FROM follow_table
//   INNER JOIN tozan_record_table ON tozan_record_table.user_id = follow_table.follow_user_id 
//   INNER JOIN users_table ON
//   tozan_record_table.user_id = users_table.id
//   ORDER BY users_table.created_at DESC';

// フォロー済みアカウントの抽出
$sql = 'SELECT * FROM follow_table
INNER JOIN users_table ON
follow_table.follow_user_id = users_table.id
WHERE follow_table.user_id = :user_id
ORDER BY users_table.created_at DESC';
$stmt = $pdo->prepare($sql);
$stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
$stmt->execute();
$records = $stmt->fetchAll();

// 未フォローアカウントの抽出
// 自分のアカウントid以外
$sql = 'SELECT * FROM users_table
WHERE id != :user_id
ORDER BY users_table.created_at DESC
';
$stmt = $pdo->prepare($sql);
$stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
$stmt->execute();
$users_data = $stmt->fetchAll();

// 自分がフォローしているアカウント
$sql = 'SELECT * FROM follow_table WHERE user_id = :user_id
';
$stmt = $pdo->prepare($sql);
$stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
$stmt->execute();
$follow_data = $stmt->fetchAll();


$users_column = [];
foreach ($users_data as $user) {
  array_push($users_column, $user['id']);
}
$follow_column = [];
foreach ($follow_data as $follow) {
  array_push($follow_column, $follow['follow_user_id']);
}
// print_r($users_column);
// print_r($follow_column);
// print_r($follow_column[0]);

//自分がフォローしていないアカウントのidを抽出
foreach ($follow_column as $follow) {
  while (($index = array_search($follow, $users_column, true)) !== false) {
    unset($users_column[$index]);
  }
}




?>

<!DOCTYPE html>
<html lang="ja">

<head>
  <meta charset="utf-8">
  <title>Image Test</title>

  <!DOCTYPE html>
  <html lang="ja">
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>登山記録</title>

  <link rel="stylesheet" href="css/panel-btn-icon.css">
  <link rel="stylesheet" href="css/panel-menu.css">
  <link rel="stylesheet" href="css/styles.css">
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
  <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.2/css/all.css" integrity="sha384-fnmOCqbTlWIlj8LyTjo7mOUStjsKC4pOpQbqyi7RrhN7udi9RwhKkMHpvLbHG9Sr" crossorigin="anonymous">

  <!-- <script src="https://code.jquery.com/jquery-3.2.1.min.js"></script> -->
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>

  <style>
    .table {
      /* border-collapse: collapse; */
      table-layout: fixed;
    }

    .table th,
    .table td {
      /* border: 1px solid #CCCCCC; */
      padding: 5px 10px;
      text-align: left;
    }

    .table th {
      background-color: #FFFFFF;
    }

    main {
      margin-top: 50px;
      position: fixed;
      top: 0;
      width: 100%;
      height: 1000px;
      opacity: 1;
      display: none;
      z-index: 10;
    }

    main.on {
      display: inline;
    }

    .thumbnail {
      width: 32.3333%;
      display: inline-block;
      vertical-align: top;
      /* 要素を上揃えにする */
      margin-bottom: 10px;
      padding: 10px;
      box-sizing: border-box;
      /* 崩れ防止 */
    }

    #open,
    #close {
      cursor: pointer;
      width: 200px;
      border: 1px solid #ccc;
      border-radius: 4px;
      text-align: center;
      padding: 12px;
      margin: 16px auto 0;
      background: #4caf50;
      color: white;
    }

    #mask {
      background: rgba(0, 0, 0, 0.4);
      position: fixed;
      top: 0;
      bottom: 0;
      right: 0;
      left: 0;
      z-index: 1;
    }

    #modal {
      background: #fff;
      color: #555;
      width: 55%;
      padding: 40px;
      border-radius: 4px;
      position: absolute;
      top: 40px;
      left: 0;
      right: 0;
      margin: 0 auto;
      z-index: 2;
    }

    #modal p {
      margin: 0 0 20px;
    }

    #mask.hidden {
      display: none;
    }

    #modal.hidden {
      transform: translate(0, -10000px);
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


 <div style="margin:0 auto;">
  <div >
    <p>未フォロー</p>
    <?php for ($i = 0; $i < count($users_data); $i++) : ?>
      <div class="" style="margin-bottom:15px">
        <?php foreach ($users_column as $nofollow_user) : ?>
          <?php if ($users_data[$i]['id'] == $nofollow_user) : ?>
            <?php if ($users_data[$i]['user_image'] == '') : ?>
              <a class="modal-btn">
                <img src="user_images/circle12.png" width="100px" height="100px" style="border-radius: 80%; object-fit:cover;">
              </a>
            <?php else : ?>
              <a class="modal-btn">
                <img src="user_images/<?php echo $users_data[$i]['user_image']; ?>" data-id="<?= $users_data[$i]['image_id'] ?>" width="100px" height="100px" style="border-radius: 80%; object-fit:cover;">
              </a>
            <?php endif; ?>
            <p style="display:inline"><?php echo $users_data[$i]['username'] ?>さん</p>
            <!-- <form action="execution.php/follow_act.php" method="POST"><input type="text"></form> -->
            <a href="execution.php/follow_act.php?id=<?= $users_data[$i]["id"]; ?>">フォローする</a>

            <h5 style="display:inline">⛰
              <?php echo totalRecord($users_data[$i]['id']); ?>　</h5>
            <h5 style="display:inline">👣
              <?php echo totalDistance($users_data[$i]['id']); ?>m　</h5>
            <h5 style="display:inline">⌚
              <?php echo totalTime($users_data[$i]['id']); ?></h5>

      </div>
    <?php endif; ?>
  <?php endforeach ?>
<?php endfor; ?>
  </div>

  <!-- フォローしているユーザー一覧 -->
  <div>
    <p>フォロー済み</p>

    <?php for ($i = 0; $i < count($records); $i++) : ?>

      <div class="" style="margin-bottom:15px;">
        <?php if ($records[$i]['user_image'] == '') : ?>
          <a class="modal-btn">
            <img src="user_images\circle12.png" width="100px" height="100px" style="border-radius: 80%; object-fit:cover;">
          </a>
        <?php else : ?>
          <a class="modal-btn">
            <img src="user_images/<?php echo $records[$i]['user_image']; ?>" data-id="<?= $records[$i]['image_id'] ?>" width="100px" height="100px" style="border-radius: 80%; object-fit:cover;">
          </a>
        <?php endif; ?>

        <p style="display:inline"><?php echo $records[$i]['username'] ?>さん</p>

        <a href="execution.php/unfollow_act.php?id=<?= $records[$i]["id"]; ?>">フォローを外す</a>

        <h5 style="display:inline">
          ⛰
          <?php echo totalRecord($records[$i]['id']); ?>　</h5>
        <h5 style="display:inline">
          👣
          <?php echo totalDistance($records[$i]['id']); ?>m　</h5>
        <h5 style="display:inline">
          ⌚
          <?php echo totalTime($records[$i]['id']); ?>
        </h5>
        </div>

    <?php endfor; ?>
  </div>


  </div>

  <a href="execution.php/logout.php">logout</a>

  <script>
    $('#panel-btn').on('click', function() {
      if ($('.panel-menu').hasClass('active-menu')) {
        $('.panel-menu').removeClass('active-menu');
        $('.panel-menu').addClass('active-menu-remove');
        $('main').removeClass('on');

      } else {
        $('.panel-menu').addClass('active-menu');
        $('.panel-menu').removeClass('active-menu-remove');
        $('main').addClass('on');
      }
    });

    //サイドメニューが表示された時、ほかの部分をクリックするとサイドメニューが閉じる
    $('main').on('click', function() {
      $('.panel-menu').removeClass('active-menu');
      $('.panel-menu').addClass('active-menu-remove');
      $('main').removeClass('on');
      $("#panel-btn-icon").toggleClass("close");
    });

    //パネルボタンのアニメーション
    $(function() {
      $("#panel-btn").click(function() {
        $("#panel").slideToggle(200);
        $("#panel-btn-icon").toggleClass("close");
        $("*").removeClass("panel-button-active");
        return false;
      });
    });

    const targets = document.getElementsByClassName('modal-btn');

    for (let i = 0; i < targets.length; i++) {
      targets[i].addEventListener('click', () => {

        let records = JSON.parse('<?php echo json_encode($records) ?>');
        // alert(records[i]['name']);

        modal.classList.remove('hidden');
        mask.classList.remove('hidden');

        var mydiv = document.getElementById("modal");
        mydiv.innerHTML =
          `<h5>${records[i]['name']}</h5>
            <img src="images/${records[i]['image_name']}" data-id="${records[i]['image_id'] }" width="100%" height="auto" class="mr-3">
            <h5>${records[i]['name']} (${records[i]['maximumAltitude']}m)</h5>
            <h5>日時 ${records[i]['date']}</h5>
            <h5>活動時間 ${records[i]['time']}</h5>
            <h5>歩いた距離 ${records[i]['distance'] / 1000}km</h5>
            <div id="close">閉じる</div>
           `;
      }, false);
    }

    // const open = document.getElementById('open');
    const close = document.getElementById('close');
    const modal = document.getElementById('modal');
    const mask = document.getElementById('mask');

    // open.addEventListener('click', function() {
    //   modal.classList.remove('hidden');
    //   mask.classList.remove('hidden');
    // });
    $(document).on('click', '#close', function() {
      modal.classList.add('hidden');
      mask.classList.add('hidden');
    });
  </script>

  <!-- <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script> -->
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
</body>

</html>