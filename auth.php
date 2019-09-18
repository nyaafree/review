<?php

//=======================================
// ログイン認証・自動ログアウト
//=======================================
// ログインしてる場合
if(!empty($_SESSION['login_date'])){
  debug('ログイン済みユーザーです。');

  // 現在日時が最終ログイン日時＋有効期限を超えていた場合
  if( ($_SESSION['login_date'] + $_SESSION['login_limit']) < time() ){
    debug('ログイン有効期限オーバーです。');

    // セッションを削除する（ログアウト）
    session_destroy();
    // ログインページへ
    header("Location:login.php");

  }else{
    debug('ログイン有効期限以内です。');
    // 最終ログイン日時を現在日時に更新
    $_SESSION['login_date'] = time();

    // 現在のページがlogin.phpの場合
    // $_SERVER['PHP_SELF']はドメインからのパスを返すため、今回だと「review/login.php」が返ってくるので、
    // さらにbasename関数を使うことでファイル名を取り出せる
    if(basename($_SERVER['PHP_SELF']) === 'login.php'){
      header("Location:mypage.php");
    }
    
  }

}else{
  debug('未ログインユーザーです。');
  if(basename($_SERVER['PHP_SELF']) !== 'login.php' ){
    header("Location:login.php");
  }
}