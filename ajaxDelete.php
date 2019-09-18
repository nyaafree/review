<?php

// 共通変数・関数ファイルの読み込み
require('function.php');

debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debug('「　Ajax ');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debugLogStart();

//============================================================================
// Ajax処理
//============================================================================

// POST送信がされていてかつ$_SESSION['user_id']がありログインしてる場合
if( isset($_POST['productid']) && isset($_SESSION['user_id']) && isLogin() ){
  debug('POST送信があります。');
  $p_id = $_POST['productid'];
  debug('削除したい商品ID：'.$p_id);
  // 例外処理
  try {
    // DBへ接続
    $dbh = dbConnect();
    // SQL文作成
    $sql = 'UPDATE product SET delete_flg = 1 WHERE id = :p_id';
    $data = array(':p_id' => $p_id);
    // クエリ実行
    $stmt = queryPost($dbh, $sql, $data);

    if($stmt){
       echo json_encode(array('success' => SUC07 ));
    }else{
      debug('商品削除失敗！！');
    }

  } catch (Exception $e) {
    error_log('エラー内容：'.$e->getMessage());
  }
}
debug('Ajax通信終了 <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<');

?>