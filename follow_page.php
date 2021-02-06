<?php
session_start();
include("functions.php");
$pdo = connect_to_db();
check_session_id();
$user_id = $_SESSION["user_id"];

// 一時的にテーブルを作成
$sql = 'CREATE TEMPORARY TABLE new_table(
SELECT *
FROM  tozan_record_like_table
WHERE tozan_record_like_table.like_user_id = :user_id)';


$stmt = $pdo->prepare($sql);
$stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
$stmt->execute();

$sql = 'SELECT * FROM tozan_record_table
  INNER JOIN follow_table ON tozan_record_table.post_user_id = follow_table.follow_user_id 
  LEFT OUTER JOIN users_table ON
  tozan_record_table.post_user_id = users_table.id
  LEFT OUTER JOIN 
  new_table ON
 tozan_record_table.image_id =
new_table.like_image_id
  LEFT OUTER JOIN
  (SELECT like_image_id, COUNT(id) AS cnt FROM tozan_record_like_table  GROUP BY like_image_id) AS likes ON
  tozan_record_table.image_id=likes.like_image_id
  WHERE follow_table.user_id = :user_id
  ORDER BY tozan_record_table.created_at';

$stmt = $pdo->prepare($sql);
$stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
$stmt->execute();
$records = $stmt->fetchAll();

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

  <link rel="stylesheet" href="css/styles.css">
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
  <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.2/css/all.css" integrity="sha384-fnmOCqbTlWIlj8LyTjo7mOUStjsKC4pOpQbqyi7RrhN7udi9RwhKkMHpvLbHG9Sr" crossorigin="anonymous">

  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>


  <style>
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

    #mask {
      background: rgba(0, 0, 0, 0.4);
      position: fixed;
      top: 0;
      bottom: 0;
      right: 0;
      left: 0;
      z-index: 1;
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

    #modal {
      background: #fff;
      color: #555;
      width: 48%;
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

    .red {
      color: red;
    }

    .blue {
      color: blue;
    }
  </style>

</head>


<body style="width: 70%; margin: 0 auto;">
  <!-- モーダル表示 -->
  <div id="mask" class="hidden"></div>
  <section id="modal" class="hidden">

  </section>
  <div id="navbar">
    <ul>
      <li><a href="input.php">TOP</a></li>
      <li><a href="follow_page.php">フォロー</a></li>
      <li><a href="find_page.php">探す</a></li>
      <li><a href="data.php">Record</a></li>
      <li><a href="profile_edit.php">プロフィール</a></li>
    </ul>
  </div>

  <!-- フォローしているユーザーの画像一覧 -->
  <div class="container mt-5">
    <?php for ($i = 0; $i < count($records); $i++) : ?>
      <div class="thumbnail">
        <a><img src="user_images/<?php echo $records[$i]['user_image'] ?>" width="60px" height="60px" style="border-radius: 80%; object-fit:cover;" style=""></a>
        <p><?php echo $records[$i]['username'] ?>さんの投稿</p>
        <a class="modal-btn">
          <img src="images/<?php echo $records[$i]['image_name']; ?>" data-id="<?= $records[$i]['image_id'] ?>" width="100%" height="auto" class="mr-3">
        </a>

        <a class="media-body" style="display:inline">
          <!-- <h5><?= $records[$i]['name']; ?> (<?= $records[$i]['maximumAltitude']; ?>m)</h5>
          <h5>日時 <?= $records[$i]['date']; ?></h5>
          <h5>活動時間 <?= $records[$i]['time']; ?></h5>
          <h5>歩いた距離 <?= $records[$i]['distance'] / 1000; ?>km</h5> -->
        </a>

        <!-- 読み込み時のハートの状態を制御 -->
        <?php if ($records[$i]['like_user_id'] != NULL) : ?>
          <span class="red like<?php echo $records[$i]['image_id'] ?>">
            <a class='fas fa-heart red'></a>
          </span>
        <?php else : ?>
          <span class="like<?php echo $records[$i]['image_id'] ?>">
            <a class="fas fa-heart"></a>
          </span>
        <?php endif; ?>


        <a id="text"></a>

        <script>
          $('.like<?php echo $records[$i]['image_id'] ?>').on('click', function() {
            const requestUrl = 'execution.php/like_create.php'; // リクエスト送信先のファイル
            // phpへリクエストを送って結果を出力する処理
            axios.get(`${requestUrl}?like_user_id=<?php echo $user_id ?>&image_id=<?php echo $records[$i]['image_id'] ?>&user_id=<?php echo $records[$i]['post_user_id'] ?>`) // リクエスト送信
              .then(function(response) {

                // ハートのCSS（色）をtoggleで切り替える
                $('.like<?php echo $records[$i]['image_id'] ?>').toggleClass('red');

                const requestUrl2 = 'execution.php/like_create2.php'; // リクエスト送信先のファイル
                axios.get(`${requestUrl2}?like_user_id=<?php echo $user_id ?>&image_id=<?php echo $records[$i]['image_id'] ?>&user_id=<?php echo $records[$i]['post_user_id'] ?>`) // リクエスト送信
                  .then(function(response) {

                    console.log(response.data);

                    if (response.data.length === 0) {
                      document.getElementById('likes_total<?php echo $records[$i]['image_id'] ?>').innerHTML = `<span>0</span>`;
                    } else if (response.data[0].cnt >= 1) {
                      document.getElementById('likes_total<?php echo $records[$i]['image_id'] ?>').innerHTML = `<span>${response.data[0].cnt}</span>`;

                    }

                  })
                  .catch(function(error) {})
                  .finally(function() {});

              })
              .catch(function(error) {})
              .finally(function() {});
          });
        </script>

        <?php if ($records[$i]['cnt'] == 0) : ?>
          <span id="likes_total<?php echo $records[$i]['image_id'] ?>">0</span>
        <?php else : ?>
          <span id="likes_total<?php echo $records[$i]['image_id'] ?>"><?php echo $records[$i]['cnt'] ?></span>
        <?php endif ?>
        <!-- <a><?php echo $records[$i]['image_id'] ?></a> -->
      </div>

    <?php endfor; ?>

  </div>

  <a href="execution.php/logout.php">logout</a>

  <script>
    const targets = document.getElementsByClassName('modal-btn');

    for (let i = 0; i < targets.length; i++) {
      targets[i].addEventListener('click', () => {

        let records = JSON.parse('<?php echo json_encode($records) ?>');
        // alert(records[i]['name']);

        modal.classList.remove('hidden');
        mask.classList.remove('hidden');

        var mydiv = document.getElementById("modal");
        mydiv.innerHTML =
          `<h3>${records[i]['name']}</h3>
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