<?php

require('function.php');

debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debug('「  ユーザー登録ページ  ');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debugLogStart();

// POST送信されていた場合
if(!empty($_POST)){
   
  //変数にユーザー情報を代入
  $email = $_POST['email'];
  $pass = $_POST['pass'];
  $pass_re = $_POST['pass_re'];
  
  //未入力チェック
  validRequired($email,'email'); //まず、３項目とも未入力がないかチェック
  validRequired($pass,'pass');
  validRequired($pass_re,'pass_re');

  if(empty($err_msg)){ // エラーが無ければ

    //emailの形式チェック
    validEmail($email,'email'); //ここからの３つはeamilのバリデーション
    //emailの最大文字数チェック
    validMaxLen($email,'email');
    //emailの最小文字数チェック
    validMinLen($email,'email');

    //パスワードの半角英数字チェック //ここから３つはpasswordのバリデーションチェック
    validHalf($pass,'pass');
    //パスワードの最大文字数チェック
    validMaxLen($pass,'pass');
    //パスワードの最小文字数チェック
    validMinLen($pass,'pass');

    if(empty($err_msg)){

      //パスワードとパスワード（再入力）が合っているかチェック
      validMatch($pass,$pass_re,'pass_re'); //ここでパスワードとパスワード（再入力）が合っているかチェックするのでパスワード（再入力）のチェックは必要なし

      if(empty($err_msg)){

        //例外処理
        try{
          //DB接続
          $dbh = dbConnect();
          //SQL文作成
          $sql = 'INSERT INTO users (email,password,login_time,create_date) VALUES (:email,:password,:login_time,:create_date)';
          $data = array(':email' => $email,':password' => password_hash($pass,PASSWORD_DEFAULT),
                        ':login_time' => date('Y-m-d H:i:s'),
                        ':create_date' => date('Y-m-d H:i:s'),);
          //クエリ実行
          $stmt = queryPost($dbh,$sql,$data);

          //クエリ成功の場合
          if($stmt){
            //ログイン有効期限（デフォルトを１時間とする）
            $sesLimit = 60*60;
            //最終ログイン日時を現在日時に
            $_SESSION['login_date'] = time();
            $_SESSION['login_limit'] = $sesLimit;
            //ユーザーIDを格納
            $_SESSION['user_id'] = $dbh->lastInsertId();
            $_SESSION['msg_success'] = SUC01;
            debug('セッション変数の中身：'.print_r($_SESSION,true));

            header("Location:mypage.php");
          }

        } catch (Exception $e) {  //上のtryの中のSQL文が失敗すれば、catchの中で$err_msg['common']に
          error_log('エラー発生：'.$e->getMessage());// MSG07が入る
          $err_msg['common'] = MSG07;
        }
      }
    } 
  }
}
debug('画面表示処理終了 <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<');
?>
<?php
$siteTitle = 'Review | ユーザー登録ページ';
require('head.php');
?>

<body class="page-signup page-1colum">

<?php 
require('header.php');
?>

<div id="modal-window">
  <div class="form-container">
    <form action="" method="post" class="form">
      <div class="area-msg">
       <?php  if(!empty($err_msg['common'])) echo $err_msg['common']; ?>
      </div>
      <label class="<?php if(!empty($err_msg['email'])) echo 'err'; ?>">
         E-mail
         <input type="text" name="email" value="<?php if(!empty($_POST['email'])) echo $_POST['email']; ?>">
      </label>
      <div class="area-msg">
        <?php if(!empty($err_msg['email'])) echo $err_msg['email']; ?>
      </div>
      <label class="pass-size <?php if(!empty($err_msg['pass'])) echo 'err'; ?>">
        パスワード ※半角英数字６文字以上で入力
        <input type="password" name="pass" value="<?php if(!empty($_POST['pass'])) echo $_POST['pass']; ?>">
      </label>
      <div class="area-msg">
        <?php if(!empty($err_msg['pass'])) echo $err_msg['pass']; ?>
      </div>
      <label class="pass-size <?php if(!empty($err_msg['pass_re'])) echo 'err'; ?>">
      パスワード（再入力）
        <input type="password" name="pass_re" value="<?php if(!empty($_POST['pass_re'])) echo $_POST['pass_re']; ?>">
      </label>
      <div class="area-msg">
        <?php if(!empty($err_msg['pass_re'])) echo $err_msg['pass_re']; ?>
      </div>
      <div class="btn-container">
        <input type="submit" value="送信" class="btn-signup">
      </div>
    </form>
    <button class="modal-close">×</button>
  </div>
</div>

<div class="modal-bg"></div>

<?php 
require('footer.php');
?>


