<?php

// 共通変数・関数ファイルの読み込み
require('function.php');

debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debug('「　退会ページ　');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');

if(!empty($_POST)){
  debug('POST送信があります。');

  // 例外処理
  try{
    // DB接続
    $dbh = dbConnect();
    // SQL文作成
    $sql1 = 'UPDATE users SET delete_flg = 1 WHERE id = :u_id';
    $sql2 = 'UPDATE product SET delete_flg = 1 WHERE user_id = :u_id';

    $data = array(':u_id' => $_SESSION['user_id']);

    // クエリ実行
    $stmt1 = queryPost($dbh, $sql1, $data);
    $stmt2 = queryPost($dbh, $sql2, $data);

    if($stmt1 && $stmt2){
      // セッション削除
      session_destroy();
      debug('セッション変数の中身：'.print_r($_SESSION,true));
      debug('トップページへ遷移します。');
      $_SESSION['msg_success'] = SUC06;
      header('Location:index.php');
    }else{
      debug('クエリが失敗しました。');
      $_SESSION['msg_success'] = FAL01;
      header('Location:mypage.php');
    }
 
  } catch (Exception $e) {
    error_log('エラー発生：'.$e->getMessage());
    $err_msg['common'] = MSG07;
  }
}
?>


