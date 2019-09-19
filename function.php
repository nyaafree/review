<?php
//=================================
// ログ
//=================================
// ログを取るか
ini_set('log_errors','on');
// ログの出力ファイルを指定
ini_set('error_log','php.log');

//=================================
// デバッグ
//=================================
// デバッグフラグ
$debug_flg = true;
// デバッグログ関数
function debug($str){
  global $debug_flg;
  if(!empty($debug_flg)){
    error_log("デバッグ：".$str);
  }
}

//=================================
// セッション準備・セッション有効期限を延ばす
//=================================
// セッションファイルの置き場を変更する（/var/tmp/以下に置くと３０日は削除されない）
session_save_path("/var/tmp/");
// ガーベージコレクションが削除するセッションの有効期限を設定（３０日以上経っているものに対して１００分の１の確率で削除）
ini_set("session.gc_maxlifetime", 60*60*24*30);
// ブラウザを閉じても削除されないようにクッキー自体の有効期限を延ばす
ini_set('session.cookie_lifetime', 60*60*24*30);
// セッションを使う
session_start();
// 現在のセッションIDを新しく生成したものと置き換える（成りすましのセキュリティ対策）
session_regenerate_id();

//==================================
// 画面表示処理開始ログ吐き出し関数
//==================================
function debugLogStart(){
  debug('>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>> 画面表示処理開始');
  debug('セッションID：'.session_id()); // session_id()メソッドでセッションIDを取得できる
  debug('セッション変数の中身：'.print_r($_SESSION,true));
  debug('現在日時タイムスタンプ：'.time());
  if( !empty($_SESSION['login_date']) && !empty($_SESSION['login_limit']) ){
    debug('ログイン期限日時タイムスタンプ：'.($_SESSION['login_date'] + $_SESSION['login_limit']) );
  }
}

//==================================
// 定数
//==================================
// エラーメッセージを定数に設定
define('MSG01','入力必須です');
define('MSG02','Emailの形式で入力してください');
define('MSG03','パスワード（再入力が合っていません）');
define('MSG04','半角英数字のみご利用いただけます');
define('MSG05','６文字以上で入力してください');
define('MSG06','255文字で入力してください');
define('MSG07','エラーが発生しました。しばらく経ってからやり直してください。');
define('MSG08','そのEmailはすでに登録されています');
define('MSG09', 'メールアドレスまたはパスワードが違います');





define('MSG15','正しくありません');

define('MSG17','半角数字のみご利用いただけます');




define('SUC01','会員登録完了しました！！');
define('SUC02', 'ログイン成功です！！');
define('SUC03', 'プロフィール更新完了しました！！');
define('SUC04','商品登録完了しました！！');
define('SUC05','レビュー投稿完了しました！！');
define('SUC06','退会処理完了しました！！');
define('SUC07','コンテンツ削除に成功しました！！');
define('SUC08', 'パスワード再発行のためのメールを送信しました');
define('SUC09', '再発行したパスワード付きのメールを送信しました');
define('SUC10', 'パスワード変更完了しました！！');
define('FAL01','退会処理に失敗しました。');


//=========================================
// グローバル変数
//=========================================
// エラーメッセージ格納用の配列
$err_msg = array();

//=========================================
// バリデーション関数
//=========================================

//バリデーション関数（未入力チェック）
function validRequired($str,$key){
  if($str === ''){ //金額フォームなどを考えると、数字の０や数値の0はOKにし、空文字はダメにする
    global $err_msg;
    $err_msg[$key] = MSG01;
  }
}
//バリデーション関数（Emain形式チェック）
function validEmail($str,$key){
  if(!preg_match("/^([a-zA-Z0-9])+([a-zA-Z0-9\._-])*@([a-zA-Z0-9_-])+([a-zA-Z0-9\._-]+)+$/",$str)){
    global $err_msg;
    $err_msg[$key] = MSG02;
  }
}
//バリデーション関数（Email重複チェック）
function validEmailDup($email){
  global $err_msg;
  //例外処理
  try{
    // DBへ接続
    $dbh = dbConnect();
    // SQL文作成
    $sql = 'SELECT count(*) FROM users WHERE email = :email AND delete_flg = 0';
    $data = array(':email' => $email);
    //クエリ実行
    $stmt = queryPost($dbh,$sql,$data);
    //クエリ結果の値を取得
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    //array_shift関数は配列の先頭を取り出す関数です。クエリ結果は配列形式で入っているので、array_shiftで１つ目だけ取り出して判定します
    debug('$result:'.print_r($result,true));
    if(!empty(array_shift($result))){
      $err_msg['email'] = MSG08;
    }

  } catch (Exception $e) {
    error_log('エラー発生：'.$e->getMessage());
    $err_msg['common'] = MSG07;
  }
}
//バリデーション関数（同値チェック）
function validMatch($str1,$str2,$key){
  global $err_msg;
  if($str1 !== $str2){
    $err_msg[$key] = MSG03;
  }
}
//バリデーション関数（最小文字数チェック）
function validMinLen($str,$key,$min = 6){
  global $err_msg;
  if(mb_strlen($str) < $min ){
    $err_msg[$key] = MSG05;
  }
}
//バリデーション関数（最大文字数チェック）
function validMaxLen($str,$key,$max = 255){
  global $err_msg;
  if(mb_strlen($str) > $max ){
    $err_msg[$key] = MSG06;
  }
}
function validHalf($str,$key){
  global $err_msg;
  if(!preg_match("/^[a-zA-Z0-9]+$/",$str)){
    $err_msg[$key] = MSG04;
  }
}
//パスワードチェック
function validPass($str, $key){
  //半角英数字チェック
  validHalf($str, $key);
  //最大文字数チェック
  validMaxLen($str, $key);
  //最小文字数チェック
  validMinLen($str, $key);
}










function validNumber($str, $key){
  if(!preg_match("/^[0-9]+$/",$str)){
    global $err_msg;
    $err_msg[$key] = MSG17;
  }
}























// セレクトボックスチェック
function validSelect($str, $key){
  if(!preg_match("/^[0-9]+$/",$str)){
    global $err_msg;
    $err_msg[$key] = MSG15; 
  }
}

//=================================
// ログイン認証
//=================================
function isLogin(){
  // ログインしてる場合
  if(!empty($_SESSION['login_date'])){
    debug('ログイン済みユーザーです。');

    // 現在日時がログイン日時＋ログイン有効期限を超えていたら
    if( ($_SESSION['login_date'] + $_SESSION['login_limit']) < time() ){
      debug('ログイン有効期限外です');

      // セッションを削除（ログアウトする）
      session_destroy();
      return false;
    }else{
      debug('ログイン有効期限内です。');
      return true;
    }

  }else{
    debug('未ログインユーザーです。');
    return false;
  }
}







// エラーメッセージを表示
function getErrMsg($key){
  global $err_msg;
  if(!empty($err_msg[$key])){
    return $err_msg[$key];
  }
}



























//============================================
// データベース
//============================================
// DB接続関数
function dbConnect(){
  //DBへの接続準備
  $dsn = 'mysql:dbname=review;host=localhost;charset=utf8';
  $user = 'root';
  $password = '';
  $options = array(
    // SQL実行失敗時にはエラーコードのみ設定 ← PDO::ERRMODE_EXCEPTION にすると SQL失敗時に$err_msg[common]にすべてエラーメッセージが入ってしまうのでPDO::ERRMODE_SILENTにする
    PDO::ATTR_ERRMODE => PDO::ERRMODE_SILENT,
    // デフォルトフェッチモードを連想配列形式に設定
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    // バッファードクエリを使う（一度に結果セットをすべて取得し、サーバー負荷を軽減）
    // selectで得た結果に対してもrowCountメソッドを使えるようにする
    PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => true,
  );
  // PDOオブジェクト生成
  $dbh = new PDO($dsn,$user,$password,$options);
  return $dbh;
}
//SQL実行関数
//function queryPost($dbh, $sql, $data){
//  //クエリー作成
//  $stmt = $dbh->prepare($sql);
//  //プレースホルダに値をセットし、SQL文を実行
//  $stmt->execute($data);
//  return $stmt;
//}
function queryPost($dbh,$sql,$data){
  //クエリー作成
  $stmt = $dbh->prepare($sql);
  //プレースフォルダに値をセットし、SQL文を実行
  if(!$stmt->execute($data)){
    debug('クエリに失敗しました。');
    debug('SQLエラー：'.print_r($stmt->errorInfo(),true));
    debug('失敗したSQL：'.print_r($stmt,true));
    $err_msg['common'] = MSG07;
    return 0;
  }
  debug('クエリ成功。');
  return $stmt;
}
function getUser($u_id){
  // 例外処理
  try{
    // DBへ接続
    $dbh = dbConnect();
    // SQL文作成
    $sql = 'SELECT * FROM users WHERE id = :u_id AND delete_flg = 0';
    $data = array(':u_id' => $u_id);
    // クエリ実行
    $stmt = queryPost($dbh, $sql, $data);

    // クエリ結果のレコードを１レコード返却
    if($stmt){
      return $stmt->fetch(PDO::FETCH_ASSOC);
    }else{
      return false;
    }
  } catch (Exception $e) {
    error_log('エラー発生：'.$e->getMessage());
    $err_msg['common'] = MSG07;
  }
}
function showReview($ReviewInfo){
  if (0.5 < $ReviewInfo && $ReviewInfo <= 1.0){
        echo "<span class='rate rate1'></span>";
  }elseif (1 < $ReviewInfo && $ReviewInfo <= 1.5){
       echo "<span class='rate rate1-5'></span>";
  }elseif(1.5 < $ReviewInfo && $ReviewInfo <= 2.0){
        echo "<span class='rate rate2'></span>";
  }elseif(2.0 < $ReviewInfo && $ReviewInfo <= 2.5){
        echo "<span class='rate rate2-5'></span>";
  }elseif(2.5 < $ReviewInfo && $ReviewInfo <= 3.0){
        echo "<span class='rate rate3'></span>";
  }elseif(3.0 <$ReviewInfo && $ReviewInfo <= 3.5){
        echo "<span class='rate rate3-5'></span>";
  }elseif(3.5 < $ReviewInfo && $ReviewInfo <= 4.0){
        echo "<span class='rate rate4'></span>";
  }elseif(4.0 < $ReviewInfo && $ReviewInfo <= 4.5){
        echo "<span class='rate rate4-5'></span>";
  }elseif(4.5 < $ReviewInfo && $ReviewInfo <= 5.0){
        echo "<span class='rate rate5'></span>";
  }
        
}
function getMyproducts($u_id){
  // 例外処理
  try{
    // DB接続
    $dbh = dbConnect();
    // SQL文作成
    $sql = 'SELECT * FROM product WHERE user_id = :u_id AND delete_flg = 0';
    $data = array(':u_id' => $u_id);
    // クエリ実行
    $stmt = queryPost($dbh, $sql, $data);

    if($stmt){
      // クエリ結果の全レコードを返却
      return $stmt->fetchAll();
    }else{
      return false;
    }

  } catch (Exception $e) {
    error_log('エラー発生：'.$e->getMessage());
  }
}
function getMyReviewInfo($u_id){
  // 例外処理
  try{
    // DB接続
    $dbh = dbConnect();
    // SQL文作成
    $sql = 'SELECT p.name, p.price, p.purchasesite, p.pic, p.average_review, r.review, r.review_comment FROM product AS p RIGHT JOIN product_review AS r 
            ON p.id = r.product_id WHERE r.reviewer_id = :u_id AND r.delete_flg = 0';
    $data = array(':u_id' => $u_id);
    // クエリ実行
    $stmt = queryPost($dbh, $sql, $data);

    if($stmt){
      // クエリ結果をすべて取得
      return $stmt->fetchAll();
    }else{
      return false;
    }

  } catch (Exception $e) {
    error_log('エラー発生：'.$e->getMessage());
  }
}






function getProduct($u_id, $p_id){
  debug('商品情報を取得します。');
  debug('ユーザーID：'.$u_id);
  debug('商品ID：'.$p_id);
  // 例外処理
  try{
    // DB接続
    $dbh = dbConnect();
    // SQL文作成
    $sql = 'SELECT * FROM product WHERE user_id = :u_id AND id = :p_id AND delete_flg = 0';
    $data = array(':u_id' => $u_id, ':p_id' => $p_id);
    // クエリ実行
    $stmt = queryPost($dbh, $sql, $data);

    if($stmt){
      // クエリ結果の１レコードを返却
     return $stmt->fetch(PDO::FETCH_ASSOC);
    }else{
      return false;
    }

  } catch (Exception $e) {
    error_log('エラー発生：'. $e->getMessage());
  }
}
function getProductOne($p_id){
  debug('商品単体の情報を取得します。');
  debug('商品ID：'.$p_id);
  // 例外処理
  try{
    // DB接続
    $dbh = dbConnect();
    // SQL文作成
    $sql = 'SELECT p.id, p.name, p.category_id, p.price, p.comment, p.purchasesite, p.pic, p.user_id, p.average_review,p.url, p.create_date, p.update_date,
           c.name AS category FROM product AS p LEFT JOIN category AS c ON p.category_id = c.id WHERE p.id = :p_id AND p.delete_flg = 0 AND c.delete_flg = 0';
    $data = array('p_id' => $p_id);
    // クエリ実行
    $stmt = queryPost($dbh, $sql, $data);
    
    if($stmt){
      // クエリ結果のデータを１レコード返却
      return $stmt->fetch(PDO::FETCH_ASSOC);
    }else{
      return false;
    }

  } catch (Exception $e) {
    error_log('エラー発生：'.$e->getMessage());
  }
}
function getProductList($currentMinNum = 0, $category, $freeWord, $sort, $span = 15){
  debug('商品リストを取得します。');
  // 例外処理
  try{
    // DBへ接続
    $dbh = dbConnect();
    // 件数用のSQL文作成
    $sql = 'SELECT id FROM product';
    if(!empty($freeWord)){ 
      $sql .= ' WHERE (name LIKE "%'.$freeWord.'%" OR comment 
      LIKE "%'.$freeWord.'%" OR purchasesite LIKE "%'.$freeWord.'%" ) AND delete_flg = 0';
      if(!empty($category)) $sql .= ' AND category_id = '.$category;
    }else{
      if(!empty($category)) $sql .= ' WHERE category_id = '.$category;
    }
   
    if(!empty($sort)){
      switch($sort){
        case 1:
          $sql .= ' ORDER BY average_review DESC';
          break;
        case 2:
          $sql .= ' ORDER BY average_review ASC';
          break;
      }
    }
    $data = array();
    // クエリ実行
    $stmt = queryPost($dbh, $sql, $data);
    $rst['total'] = $stmt->rowCount(); // 総レコード数
    $rst['total_page'] = ceil($rst['total'] / $span); //総ページ数
    if(!$stmt){
      return false;
    }

    // ページング用のSQL文作成
    $sql = 'SELECT * FROM product';
    if(!empty($freeWord)){ 
      $sql .= ' WHERE (name LIKE "%'.$freeWord.'%" OR comment 
      LIKE "%'.$freeWord.'%" OR purchasesite LIKE "%'.$freeWord.'%" ) AND delete_flg = 0';
      if(!empty($category)) $sql .= ' AND category_id = '.$category;
    }else{
      if(!empty($category)) $sql .= ' WHERE category_id = '.$category;
    }
    if(!empty($sort)){
      switch($sort){
        case 1:
          $sql .= ' ORDER BY average_review DESC';
          break;
        case 2:
          $sql .= ' ORDER BY average_review ASC';
          break;
      }
    }
    $sql .= ' LIMIT '.$span.' OFFSET '.$currentMinNum;
    $data = array();
    debug('SQL：'.$sql);
    // クエリ実行
    $stmt = queryPost($dbh, $sql, $data);

    if($stmt){
      // クエリ結果の全レコードを格納
      $rst['data'] = $stmt->fetchAll();
      return $rst;

    }else{
      return false;
    }


  }catch (Excption $e){
    error_log('エラー発生：'.$e->getMessage());
  }
}
function getReviewInfo($p_id){
  
  // 例外処理
  try {
    // DB接続
    $dbh = dbConnect();
    // SQL文作成
    $sql = 'SELECT id, review, reviewer_id, review_comment FROM product_review WHERE product_id = :p_id';
    $data = array(':p_id' => $p_id);
    // クエリ実行
    $stmt = queryPost($dbh, $sql, $data);

    if($stmt){
      // クエリ結果の全レコードを取得
      return $stmt->fetchAll();
    }else{
      return false;
    }

  } catch (Exception $e) {
    error_log('エラー発生：'.$e->getMessage());
  }
}
// ユーザーの最新レビュー情報を取得
function getUserReview($p_id){
  // 例外処理
  try{
    // DB接続
    $dbh = dbConnect();
    // SQL文作成
    $sql = 'SELECT u.username, u.age, u.sex, u.pic, p.review, p.review_comment, p.create_date FROM users AS u RIGHT JOIN product_review AS p
             ON u.id = p.reviewer_id WHERE p.product_id = :p_id AND u.delete_flg = 0 AND p.delete_flg = 0 ORDER BY p.create_date DESC';
    $data = array(':p_id' => $p_id);
    // クエリ実行
    $stmt = queryPost($dbh, $sql, $data);

    if($stmt){
      //クエリ結果のレコードをすべて取得
      return $stmt->fetchAll();
    }else{
      return false;
    }

  } catch (Exception $e) {
    error_log('エラー発生：'.$e->getMessage());
  }
}


//===============================================================
// メール送信
//===============================================================

function sendMail($from, $to, $subject, $comment){
  if(!empty($to) && !empty($subject) && !empty($comment)){
      //文字化けしないように設定（お決まりパターン）
      mb_language("Japanese"); //現在使っている言語を設定する
      mb_internal_encoding("UTF-8"); //内部の日本語をどうエンコーディング（機械が分かる言葉へ変換）するかを設定
      
      //メールを送信（送信結果はtrueかfalseで返ってくる）
      $result = mb_send_mail($to, $subject, $comment, "From: ".$from);
      //送信結果を判定
      if ($result) {
        debug('メールを送信しました。');
      } else {
        debug('【エラー発生】メールの送信に失敗しました。');
      }
  }
}















































































































































function getCategory(){
  debug('カテゴリーデータを取得します。');
  //例外処理
  try {
    // DBへ接続
    $dbh = dbConnect();
    // SQL文作成
    $sql = 'SELECT * FROM category';
    $data = array();
    // クエリ実行
    $stmt = queryPost($dbh ,$sql, $data);

    if($stmt){
      // クエリ結果の全データを返却
      return $stmt->fetchAll();
    }else{
      return false;
    }

  } catch (Exception $e) {
    error_log('エラー発生：'.$e->getMessage());
  }
}









//=============================================================
// その他
//=============================================================
// サニタイズ
function sanitize($str){
  return htmlspecialchars($str,ENT_QUOTES);                 
}
// フォーム入力保持
function getFormData($str,$flg = false){
  if($flg){
    $method = $_GET;
  }else{
    $method = $_POST;
  }
  global $dbFormData;
  // ユーザーデータがある場合
  if(!empty($dbFormData)){
    // フォームエラーがある場合
    if(!empty($err_msg[$str])){
      // POSTにエラーがある場合
      if(isset($method[$str])){
        return sanitize($method[$str]);
      }else{
        // ない場合（基本ありえない）はDBの情報を表示
        return sanitize($dbFormData[$str]);
      }
    }else{
      // POSTにデータがあり、DBの情報と違う場合
      if(isset($method[$str]) && $method[$str] !== $dbFormData[$str]){
        return sanitize($method[$str]);
      }else{
        return sanitize($dbFormData[$str]);
      }
    }
  }else{
    if(isset($method[$str])){
      return sanitize($method[$str]);
    }
  }
}
// ページング
// $currentPageNum 現在のページ数
// $totalPageNum 総ページ数
// $link 検索用GETパラメータリンク
// $pageColNum ページネーション表示数
function pagination( $currentPageNum, $totalPageNum, $link = '', $pageColNum = 5){
  // 現在のページ数が総ページ数と同じ　かつ　総ページ数が表示項目数以上なら左にリンクを４個出す
  if($currentPageNum == $totalPageNum && $totalPageNum >= $pageColNum ){
    $minPageNum = $currentPageNum - 4;
    $maxPageNum = $currentPageNum;
  // 現在のページ数が総ページの総ページ数の１ページ前なら、左にリンク３個、右にリンク１個出す
  }elseif($currentPageNum == $totalPageNum - 1 && $totalPageNum >= $pageColNum ){
    $minPageNum = $currentPageNum - 3;
    $maxPageNum = $currentPageNum + 1;
  // 現在のページ数が２ページ目なら左にリンク１個、右にリンク３個出す
  }elseif($currentPageNum == 2 && $totalPageNum >= $pageColNum ){
    $minPageNum = $currentPageNum - 1;
    $maxPageNum = $currentPageNum + 3;
  // 現在のページ数が１ページ目なら右にリンク４個出す
  }elseif($currentPageNum == 1 && $totalPageNum >= $pageColNum ){
    $minPageNum = 1;
    $maxPageNum = 5;
  // 総ページ数が表示項目数未満の場合は総ページ数を$maxPageNumに、１ページ目を$minPageNumに変更
  }elseif($totalPageNum < $pageColNum){
    $minPageNum = 1;
    $maxPageNum = $totalPageNum;
  }else{
    $minPageNum = $currentPageNum - 2;
    $maxPageNum = $currentPageNum + 2;
  }

  echo '<div class="pagination">';
    echo '<ul class="pagination-list">';
      if($currentPageNum != 1){
        echo '<li class="list-item"><a href="?p=1'.$link.'">&lt;</a></li>';
      }
      for($i = $minPageNum; $i <= $maxPageNum; $i++){
        echo '<li class="list-item ';
        if($currentPageNum == $i){ echo 'active'; }
        echo '"><a href="?p='.$i.$link.'">'.$i.'</a></li>';
      }
      if($currentPageNum != $maxPageNum && $maxPageNum > 1){
        echo '<li class="list-item"><a href="?p='.$maxPageNum.$link.'">&gt;</a></li>';
      }
    echo '</ul>';
  echo '</div>';
}
















// 画像処理
function uploadImg($file, $key){
  debug('画像アップロード処理開始。');
  debug('FILE情報：'.print_r($file,true));

  if( isset($file['error']) && is_int($file['error']) ){
    try{
      // バリデーション
      // $file['error']の値を確認。配列内には「UPLOAD_ERR_OK」などの定数が入っている。
      // 「UPLOAD_ERR_OK」などの定数はphpでファイルアップロード時に自動的に定義される。定数には値として0や1などの値が入っている。
      switch($file['error']){
        case UPLOAD_ERR_OK: //OK
          break;
        case UPLOAD_ERR_NO_FILE: // ファイル未選択の場合
          throw new RuntimeException('ファイルが選択されていません。'); // RuntimeExceptionはphpの実行過程で起きた例外を入れる
          break;  
        case UPLOAD_ERR_INI_SIZE: // php.ini定義の最大サイズを超過した場合
        case UPLOAD_ERR_FORM_SIZE: // フォーム定義の最大サイズを超過した場合
          throw new RuntimeException('ファイルサイズが大きすぎます。');
          break;
        default: // その他の場合
          throw new RuntimeException('その他のエラーが発生しました。');
          break;
      }

      // $file['mime']の数はブラウザ側で偽装可能なので、MIMEタイプを自前でチェックする
      // exif_imagetype関数は「IMAGETYPE_GIF]、「IMAGETYPE_JPEG」などの定数を返す
      $type = @exif_imagetype($file['tmp_name']); // 引数にはファイルパスを指定
      if(!in_array($type,[IMAGETYPE_GIF,IMAGETYPE_JPEG,IMAGETYPE_PNG],true)){
        throw new RuntimeException('画像形式が未対応です。');
      }

      // ファイルデータからSHA-1ハッシュをとってファイル名を決定し、ファイルを保存
      // ハッシュ化しておかずにアップロードされたそのままのファイル名で保存すると同じファイル名がアップロードされる可能性があり、
      // DBにパスを保存した場合どちらの画像のパスなのか判断がつかなくなってしまう
      // image_type_to_extension関数はファイル拡張子を取得するもの(引数にはMIMEタイプを入れる)
      $path = 'uploads/'.sha1_file($file['tmp_name']).image_type_to_extension($type); 
      if(!move_uploaded_file($file['tmp_name'], $path)){ // ファイルを移動する
        throw new RuntimeException('ファイル保存時にエラーが発生しました。');
      }
      // 保存したファイルのパーミッション（権限）を変更する
      chmod($path, 0644);

      debug('ファイルは正常にアップロードされました。');
      debug('ファイルパス：'.$path);
      return $path;

    } catch (RuntimeException $e) {
      debug($e->getMessage());
      global $err_msg;
      $err_msg[$key] = $e->getMessage();
    }
  }
}
// GETパラメータ付与
// $arr_del_key : 付与から取り除きたいGETパラメータのキー
function appendGetParam($arr_del_key = array()){
  if(!empty($_GET)){
    $str = '?';
    foreach($_GET as $key => $val){
      if(!in_array($key,$arr_del_key,true)){
        $str .= $key.'='.$val.'&';
      }
    }
    $str = mb_substr($str,0,-1,"UTF-8");
    return $str;
  }
}
function addParam($arr_del_key = array()){ // 引数に指定したGETパラメータのキーを配列形式で入れれば残りの現在ページのGETパラメータを追加できる関数
  if(!empty($_GET)){
    $str = '&';
    foreach($_GET as $key => $val){
      if(!in_array($key,$arr_del_key,true)){
        $str .= $key.'='.$val.'&';
      }
    }
    $str = mb_substr($str, 0, -1, "UTF-8");
    return $str;
  }
}









































//sessionを１回だけ取得できる
function getSessionFlash($key){
  if(!empty($_SESSION[$key])){
    $data = $_SESSION[$key];
    $_SESSION[$key] = '';
    return $data;
  }
}
function flashErrMsg(){
  global $err_msg;
  if(!empty($err_msg)){
    $data = $err_msg;
    $err_msg = array();
    return $data;
  }
}


































?>
