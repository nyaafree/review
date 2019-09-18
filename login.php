<?php 

require('function.php');

debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debug('「　ログインページ　');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debugLogStart();

// ログイン認証
require('auth.php');

//==================================================
// ログイン画面処理
//==================================================
// POST送信されていた場合
if(!empty($_POST)){
  debug('POST送信があります。');

  $email = $_POST['email'];
  $pass = $_POST['pass'];
  $pass_save = (!empty($_POST['pass_save'])) ? true : false; 

  //未入力チェック
  validRequired($email, 'email');
  validRequired($pass, 'pass');
  
  if(empty($err_msg)){

    //emailの形式チェック
    validEmail($email,'email');
    //emailの最大文字数チェック
    validMaxLen($email,'email');

    //パスワードの半角英数字チェック
    validHalf($pass, 'pass');
    //パスワードの最大文字数チェック
    validMaxLen($pass, 'pass');
    //パスワードの最小文字数チェック
    validMinLen($pass, 'pass');
    
    if(empty($err_msg)){
      debug('バリデーションOKです。');
      
      //例外処理   ← DBに接続るする場合は例外処理（try & catch）で処理を行う
      try{
        // DBへ接続
        $dbh = dbConnect();
        // SQL文を作成
        $sql = 'SELECT password,id FROM users WHERE email = :email AND delete_flg = 0';
        $data = array(':email' => $email);
        // クエリ実行
        $stmt = queryPost($dbh, $sql, $data);
        // クエリ結果の値を取得
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        debug('クエリ結果の中身：'.print_r($result,true));

        // パスワード照合
        if(!empty($result) && password_verify($pass,array_shift($result)) ){ // password_verifyは第一引数に普通のパスワードを、第二引数にハッシュ化されたパスワードを入れて合致すればtrueを返す
          debug('パスワードがマッチしました。');

           // ログイン有効期限(デフォルトを１時間とする)
           $sesLimit = 60*60;
           // 最終ログイン日時を現在に
           $_SESSION['login_date'] = time(); // time()は１９７０年１月１日から現在までのユニックスタイムスタンプを取得
        
          // ログイン保持にチェックがある場合
          if($pass_save){
            // ログイン有効期限を３０日にしてセット
            $_SESSION['login_limit'] = $sesLimit * 24 * 30;
          }else{
            debug('ログイン保持にチェックはありません。');
            // デフォルト通りログイン有効期限を１時間に設定
            $_SESSION['login_limit'] = $sesLimit;
          }
          // ユーザーIDを格納
          $_SESSION['user_id'] = $result['id'];
          $_SESSION['msg_success'] = SUC02;

          debug('セッション変数の中身：'.print_r($_SESSION,true));
          debug('マイページへ遷移します。');
          header("Location:mypage.php");
        }else{
          debug('パスワードはアンマッチです。');
          $err_msg['common'] = MSG09; // パスワードが違うと断言したらメールアドレスは合致してると知られてしまうので、力業でパスワードを特定されればログインされてしまうから
        }

      } catch (Exception $e) {
        error_log('エラー発生：'. $e->getMessage());
        $err_msg['common'] = MSG07;
      }
    }


  }
  
}
debug('画面表示処理終了 <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<');
?>

<?php
$siteTitle = 'Review | ログインページ';
require('head.php');
?>

  <body class="page-1colum page-login">
    
    <!-- ヘッダー --> 
    <?php
      require('header.php');
    ?>

    <div id="modal-window">
      <div class="form-container">
        
        <form action="" method="post" class="form">
          <div class="area-msg">
            <?php if(!empty($err_msg['common'])) echo $err_msg['common']; ?>
          </div>
          <label class="<?php if(!empty($err_msg['email'])) echo 'err'; ?>">
            E-mail 
            <input type="text" name="email" value="<?php if(!empty($_POST['email'])) echo $_POST['email']; ?>">
          </label>
          <div class="area-msg">
            <?php if(!empty($err_msg['email'])) echo $err_msg['email']; ?>
          </div>
          <label class="<?php if(!empty($err_msg['pass'])) echo 'err'; ?>">
            パスワード
            <input type="password" name="pass" value="<?php if(!empty($_POST['pass'])) echo $_POST['pass']; ?>">
          </label>
          <div class="area-msg">
            <?php if(!empty($err_msg['pass'])) echo $err_msg['pass']; ?>
          </div>
          <label class="log-keep">
            <input type="checkbox" name="pass_save">ログイン状態を保持する(30日間)
          </label>
          <div class="btn-container">
            <input type="submit" value="ログイン">
          </div>
          <div class="pass-remind-container">
            パスワードを忘れた方は<a href="passRemindsend.php">コチラ</a>
          </div>
        </form> 
      </div>
      <button class="modal-close">×</button>
    </div>

  <div class="modal-bg"></div>
  <?php
  require('footer.php');
  ?>