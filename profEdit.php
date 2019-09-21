<?php

// 共通変数・関数ファイル読み込み
require('function.php');

debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debug('「 プロフィール編集ページ ');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debugLogStart();

// ログイン認証
require('auth.php');

//======================================================
// 画面処理
//======================================================
// DBからユーザーデータを取得
$dbFormData = getUser($_SESSION['user_id']);

debug('ユーザー情報：'.print_r($dbFormData,true));

// POST送信されていた場合
if(!empty($_POST)){
  debug('POST送信があります。');
  debug('POST情報：'.print_r($_POST,true));
  debug('FILE情報：'.print_r($_FILES,true));

  // 変数にユーザー情報を代入
  $username = $_POST['username'];
  $email = $_POST['email'];
  $sex = $_POST['sex'];
  $age = $_POST['age'];
  // 画像をアップロードし、パスを格納
  $pic = (!empty($_FILES['pic']['name'])) ? uploadImg($_FILES['pic'],'pic') : '';
  // 画像をPOSTしていない（登録していない）が既にDBに登録されている場合はDBのパスを格納する（POSTには反映されないから）
  $pic = ( empty($pic) && !empty($dbFormData['pic']) ) ? $dbFormData['pic'] : $pic;

  // DBの情報と入力情報が異なる場合にバリデーションを行う
  if($dbFormData['username'] !== $username){
    // 名前の最大文字数チェック
    validMaxLen($username,'username');
  }
  if($dbFormData['email'] !== $email){
    //emailの最大文字数チェック
    validMaxLen($email,'email');
    if(empty($err_msg['email'])){
      //emailの重複チェック
      validEmailDup($email,'email');
    }
    // emailの形式チェック
    validEmail($email,'email');
    // emailの未入力チェック
    validRequired($email,'email');
  }

  if(empty($err_msg)){
    // 例外処理
    try{
      // DBへ接続
      $dbh = dbConnect();
      // SQL文作成
      $sql = 'UPDATE users SET username = :u_name, email = :email, sex = :sex, age = :age, pic = :pic WHERE id = :u_id';
      $data = array(':u_name' => $username, ':email' => $email, ':sex' => $sex, ':age' => $age, ':pic' => $pic,':u_id' => $dbFormData['id']);
      // クエリ実行
      $stmt = queryPost($dbh, $sql, $data);

      if($stmt){
        $_SESSION['msg_success'] = SUC03;
        debug('マイページへ遷移します。');
        header("Location:mypage.php");
      }else{
        debug('プロフィール更新に失敗しました。');
      }

    } catch (Exception $e){
      error_log('エラー発生：'.$e->getMessage());
      $err_msg['common'] = MSG07;
    }
  }

}


?>
<?php
$siteTitle = 'review | プロフィール更新ページ';
require('head.php');
?>

<body class="page-2colum">
  
  <!-- ヘッダー --> 
  <?php
  require('header.php');
  ?>
  
  <div class="site-width" id="contents">
    
    <section id="main">
      <div class="regist-area">
        <form action="" method="post" enctype="multipart/form-data">
           <div class="area-msg">
            <?php
              if(!empty($err_msg['common'])) echo $err_msg['common'];
            ?>
           </div>
           <label class="<?php if(!empty($err_msg['username'])) echo 'err'; ?>">
             ユーザー名
             <input type="text" name="username" value="<?php echo getFormData('username'); ?>">
           </label>
           <div class="area-msg">
             <?php if(!empty($err_msg['username'])) echo $err_msg['username']; ?>
           </div>
           <label class="<?php if(!empty($err_msg['email'])) echo 'err'; ?>">
             E-mail
             <input type="text" name="email" value="<?php echo getFormData('email'); ?>">
          </label>
          <div class="area-msg">
            <?php
              if(!empty($err_msg['email'])) echo $err_msg['email'];
            ?>
          </div>
          <p class="title">性別</p>
          <div class="gender-container">
            <label class="sex"><input type="radio" name="sex" value="1" <?php if(getFormData('sex') == 1) echo 'checked'; ?>>男性</label>
            <label class="sex"><input type="radio" name="sex" value="2" <?php if(getFormData('sex') == 2) echo 'checked'; ?>>女性</label>
          </div>
          <p class="title">年齢</p>
          <select name="age" id="" class="age-select">
              <option value="0" <?php if(getFormData('age') == 0) echo 'selected'; ?>>未選択</option>
              <?php for($i=15; $i <= 100; $i++): ?>
                <option value="<?php echo $i ?>" <?php if(getFormData('age') == $i) echo 'selected'; ?>><?php echo $i; ?></option>
              <?php endfor ?>
          </select>
          <div class="imgDrop-container">
            <label class="area-drop <?php if(!empty($err_msg['pic'])) echo 'err'; ?>">
              <input type="hidden" name="MAX_FILE_SIZE" value="3145728">
              <input type="file" name="pic" class="input-file">
              <img src="<?php echo getFormData('pic'); ?>" alt="" class="prev-img" style="<?php if(empty(getFormData('pic'))) echo 'display:none;' ?>">
            </label>
          </div>
          <input type="submit" value="更新する" class="submit"> 
        </form>
      </div>
    </section>
    

    <!-- サイドバー --> 
    <?php
    require('sidebar.php');
    ?>
  </div>

  
  <!-- フッター -->
  <?php
  require('footer.php');
  ?>