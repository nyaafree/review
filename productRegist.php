<?php 

//共通変数・関数ファイルの読み込み
require('function.php');

debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debug('「　商品登録画面　');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debugLogStart();

// ログイン認証
require('auth.php');

//====================================================================
// 画面処理
//====================================================================

// 画面表示用データ取得
//====================================================================
// GETデータを格納
$p_id = (!empty($_GET['p_id'])) ? $_GET['p_id'] : '';
// DBから商品データを取得
$dbFormData = (!empty($p_id)) ? getProduct($_SESSION['user_id'],$p_id) : '';
// 新規登録用画面か編集画面かの判別用フラグ
$edit_flg = (empty($dbFormData)) ? false : true;
// カテゴリーデータを取得
$dbCategoryData = getCategory();
debug('商品ID：'.print_r($p_id,true));
debug('フォーム用DBデータ：'.print_r($dbFormData,true));
debug('カテゴリデータの中身：'.print_r($dbCategoryData,true));

// パラメータ改ざんチェック
//=======================================================================
// GETパラメータはあるが改ざんされている（URLをいじくった）場合、正しい商品データが取れないのでマイページへ遷移させる
if(!empty($p_id) && empty($dbFormData)){
  debug('GETパラメータの商品IDが違います。マイページへ遷移します。');
  header("Location:mypage.php");
}

// POST送信時処理
//========================================================================
if(!empty($_POST)){
  debug('POST送信があります。');
  debug('POST情報：'.print_r($_POST,true));
  debug('FILE情報：'.print_r($_FILES,true));

  // 変数にユーザー情報を代入
  $name = $_POST['name'];
  $category = $_POST['category_id'];
  $comment = $_POST['comment'];
  $purchaseSite = $_POST['purchasesite'];
  $price = (!empty($_POST['price'])) ? $_POST['price'] : 0; // priceカラムはint型ゆえデフォルトではDBに０が入っている　その為、空文字で送信すると以下で行うバリデーションに引っかかる為、空文字または数字や数値の０を入力した場合は0がPOST送信されたことにする
  // 画像をアップロードしパスを格納
  $pic = ( !empty($_FILES['pic']['name']) ) ? uploadImg($_FILES['pic'],'pic') : '';
  // 画像をPOSTしていないが既にDBに登録されている場合、DBのパスを入れる（POSTには反映されない為）
  $pic = ( empty($pic) && !empty($dbFormData['pic'])) ? $dbFormData['pic'] : $pic;
  $url = $_POST['url'];
  //更新の場合はDBの情報と入力情報が異なる場合にバリデーションを行う
  if(empty($dbFormData)){
    //未入力チェック
    validRequired($name, 'name');
    // 最大文字数チェック
    validMaxLen($name,'name');
    // セレクトボックスチェック
    validSelect($category, 'category_id');
    // 最大文字数チェック
    validMaxLen($comment,'comment', 500);
    // 未入力チェック
    validRequired($purchaseSite,'purchasesite');
    // 最大文字数チェック
    validMaxLen($purchaseSite,'purchasesite');
    // 未入力チェック
    validRequired($price, 'price');
    // 半角数字チェック
    validNumber($price, 'price');
  }else{
    if($dbFormData['name'] !== $name ){
      // 未入力チェック
      validRequired($name, 'name');
      // 最大文字数チェック
      validMaxLen($name, 'name');
    }
    if((int)$dbFormData['category_id'] !== $category){
      // セレクトボックスチェック
      validSelect($category, 'category_id');
    }
    if($dbFormData['comment'] !== $comment){
      // 最大文字数チェック
      validMaxLen($comment,'comment',500);
    }
    if($dbFormData['purchasesite'] !== $purchaseSite){
      // 未入力チェック
      validRequired($purchaseSite,'purchasesite');
      // 最大文字数チェック
      validMaxLen($purchaseSite,'purchasesite');
    }
    if((int)$dbFormData['price'] !== $price){
      // 未入力チェック
      validRequired($price, 'price');
      // 半角数字チェック
      validNumber($price, 'price');
    }
  }
  
  if(empty($err_msg)){
    debug('バリデーションOKです。');

    // 例外処理
    try{
      // DBへ接続
      $dbh = dbConnect();
      // SQL文作成
      // 編集画面の場合はUPDATE文、新規登録画面の場合はINSERT文を作成
      if($edit_flg){
        debug('DB更新です。');
        $sql = 'UPDATE product SET name = :name, category_id = :category, comment = :comment, purchasesite = :purchasesite, price = :price, pic = :pic, url = :url WHERE user_id = :id AND id = :p_id';
        $data = array(':name' => $name, ':category' => $category, 'comment' => $comment, ':purchasesite' => $purchaseSite, 'price' => $price, ':pic' => $pic, ':url' => $url, ':id' => $_SESSION['user_id'], ':p_id' => $p_id);
      }else{
        debug('DB新規登録です。');
        $sql = 'INSERT INTO product (name, category_id, comment, purchasesite, price, pic, url, user_id, create_date) VALUES (:name, :category_id, :comment, :purchasesite, :price, :pic, :url, :u_id, :date) ';
        $data = array(':name' => $name, ':category_id' => $category, ':comment' => $comment, ':purchasesite' => $purchaseSite, ':price' => $price, ':pic' => $pic, ':url' => $url, ':u_id' => $_SESSION['user_id'], ':date' => date('Y-m-d H:i:s'));
      }
      debug('SQL：'.$sql);
      debug('流し込みデータ：'.print_r($data,true));
      // クエリ実行
      $stmt = queryPost($dbh, $sql, $data);

      // クエリ成功の場合
      if($stmt){
        $_SESSION['msg_success'] = SUC04;
        debug('マイページへ遷移します。');
        header("Location:mypage.php");
      }

    } catch (Exception $e) {
      error_log('エラー発生：'. $e->getMessage());
      $err_msg['common'] = MSG07;
    }

  }
}
debug('画面表示処理終了 <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<');                                       
?>
<?php
$siteTitle = 'Review | 商品登録・編集ページ';
require('head.php');
?>

  <body class="product-regist page-2colum">
    
     <!-- ヘッダー --> 
     <?php
     require('header.php');
     ?>

     <div id="contents" class="site-width">
       
       <section id="main">
          <div class="regist-area">
            <form action="" method="post" enctype="multipart/form-data">
              <div class="area-msg">
                <?php if(!empty($err_msg['common'])) echo $err_msg['common']; ?>
              </div>
              <label class="<?php if(!empty($err_msg['name'])) echo 'err'; ?>">
                商品名
                <input type="text" name="name" value="<?php echo getFormData('name'); ?>">
              </label>
              <div class="area-msg">
                <?php if(!empty($err_msg['name'])) echo $err_msg['name']; ?>
              </div>
              <label>
                カテゴリー<br>
                <select name="category_id">
                  <option value="0" <?php if(getFormData('category_id') == 0) echo 'selected'; ?>>選択してください</option>
                  <?php foreach($dbCategoryData as $key => $val): ?>
                    <option value="<?php echo $val['id']; ?>" <?php if(getFormData('category_id') == $val['id']) echo 'selected'; ?>><?php echo $val['name']; ?></option>
                  <?php endforeach ?>
                </select>
              </label>
              <div class="area-msg">
                <?php if(!empty($err_msg['category_id'])) echo $err_msg['category_id']; ?>
              </div>
              <label class="<?php if(!empty($err_msg['purchasesite'])) echo 'err'; ?>">
                購入サイト
                <input type="text" name="purchasesite" value="<?php echo getFormData('purchasesite'); ?>">
              </label>
              <div class="area-msg">
                <?php if(!empty($err_msg['purchasesite'])) echo $err_msg['purchasesite']; ?>
              </div>
              <label class="<?php if(!empty($err_msg['comment'])) echo 'err'; ?>">
                 詳細<br>
                <textarea name="comment" cols="50" rows="5"><?php echo getFormData('comment'); ?></textarea>
              </label>
              <div class="area-msg">
                <?php if(!empty($err_msg['comment'])) echo $err_msg['comment']; ?>
              </div>
              <label class="<?php if(!empty($err_msg['price'])); ?>">
                価格<br>
                ¥ <input type="text" name="price" value="<?php echo getFormData('price'); ?>" class="input-price"> 円
              </label>
              <div class="area-msg">
                <?php if(!empty($err_msg['price'])) echo $err_msg['price']; ?>
              </div>
              <label class="<?php if(!empty($err_msg['url'])) echo 'err'; ?>">
                購入サイトのURL<br>
                <input type="text" name="url" value="<?php echo getFormData('url'); ?>" class="url"> 
              </label>
              <div class="area-msg">
                <?php if(!empty($err_msg['url'])) echo $err_msg['url']; ?>
              </div>
              <div class="imgDrop-container">
                画像
                <label class="area-drop <?php if(!empty($err_msg['pic'])) echo 'err'; ?>">
                  <input type="hidden" name="MAX_FILE_SIZE" value="3145728">
                  <input type="file" name="pic" class="input-file">
                  <img src="<?php echo getFormData('pic'); ?>" alt="" class="prev-img" style="<?php if(empty(getFormData('pic'))) echo 'display:none;'; ?>">
                  <span>ドラッグ＆ドロップ</span> 
                </label>
              </div>
              <div class="area-msg">
                <?php if(!empty($err_msg['pic'])) echo $err_msg['pic']; ?>
              </div>
              
              <input type="submit" value="<?php echo (!empty($edit_flg)) ? '更新する' : '登録する'; ?>" class="submit">
            </form>
            <?php if(!empty($edit_flg)) : ?>
              <button class="js-click-delete" data-productid="<?php echo sanitize($dbFormData['id']); ?>"><i class="fa fa-trash" aria-hidden="true"></i> 削除する</button>
            <?php endif ?>
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