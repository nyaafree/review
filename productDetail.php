<?php

// 共通変数・関数ファイルの読み込み
require('function.php');

debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debug('「　コンテンツ詳細ページ　');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debugLogStart();

if(empty($_SESSION['login_date'])) header('Location:index.php');

//===============================================================
// 画面処理
//===============================================================

// 画面表示用データ取得
//===============================================================
// 商品IDのGETパラメータ取得
$p_id = (!empty($_GET['p_id'])) ? $_GET['p_id'] : '';
// DBから商品データを取得
$dbProductData = getProductOne($p_id); 
// DBからコンテンツレビュー情報を取得
$dbReviewData = getReviewInfo($p_id);
// レビュー投稿したすべてのユーザー情報を取得
$dbAllUserReview = getUserReview($p_id);
// 最新のユーザーのレビュー情報を取得
$dbUserReview = array_shift($dbAllUserReview);
debug('全てのレビュー情報：'.print_r($dbAllUserReview,true));
debug('ユーザーの最新レビュー情報：'.print_r($dbUserReview,true));
debug('レビュー情報：'.print_r($dbReviewData,true));
// パラメータに不正な値が入っているかチェック
if(empty($dbProductData)){
  error_log('エラー発生：指定ページに不正な値が入りました。');
  header("Location:index.php"); //マイページへ遷移
}
debug('取得したDBデータ：'.print_r($dbProductData,true));

if(!empty($_POST)){
  debug('POST送信があります。');

  // ログイン認証
  require('auth.php');

  $review_comment = $_POST['review_comment'];
  $star = $_POST['star'];

  // 最大文字数チェック
  validMaxLen($review_comment,'review_comment');

  if(empty($err_msg)){
    debug('バリデーションOKです。');

    // 例外処理
    try{
      // DBへ接続
      $dbh = dbConnect();
      // SQL文作成
      $sql = 'INSERT INTO product_review (product_id, review, reviewer_id,review_comment, create_date) VALUES(:p_id, :review, :u_id, :review_comment, :date)';
      $data = array(':p_id' => $p_id, ':review' => $star, ':u_id' => $_SESSION['user_id'], ':review_comment' => $review_comment, ':date' => date('Y-m-d H:i:s'));
      // クエリ実行
      $stmt = queryPost($dbh, $sql, $data);

      if($stmt){
        debug('データ挿入に成功。');
        $_SESSION['msg_success'] = SUC05;
      }else{
        return false;
      }

      $sql = 'SELECT AVG(review) FROM product_review WHERE product_id = :p_id';
      $data = array(':p_id' => $p_id);
      $stmt = queryPost($dbh, $sql, $data);
      
      $avg = $stmt->fetch(PDO::FETCH_ASSOC);
      debug('$avg:'.print_r($avg,true));
      $avg = array_shift($avg);
      $avg = round($avg,1);
      debug('レビュー平均：'.$avg);

      if(!$stmt){
        debug('クエリ失敗。');
        return false;
      }

      $sql = 'UPDATE product SET average_review = :avg WHERE id = :p_id';
      $data = array(':avg' => $avg, ':p_id' => $p_id);
      $stmt = queryPost($dbh, $sql, $data);

      if($stmt){
        debug('平均レビュー追加完了！！');
        header('Location:index.php');
      }

    } catch (Exception $e) {
      error_log('エラー発生：'.$e->getMessage());
    }
  }

}


debug('画面表示処理終了 <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<');
?>
<?php
$siteTitle = 'Review | コンテンツ詳細ページ ';
require('head.php');
?>

<body class="page-1colum">
  
  <!-- ヘッダー　--> 
  <?php
  require('header.php');
  ?>
  
  <div class="site-width" id="contents">
    
    <section id="main">
      <div class="detail-head">
        <div class="head-left">
          <div class="left-name">
            <?php echo $dbProductData['name']; ?>
          </div>
          <img src="<?php echo $dbProductData['pic']; ?>" alt="<?php echo $dbProductData['name']; ?>" class="detail-pic">
          <div class="left-purchase">
            <?php echo $dbProductData['category']; ?>
          </div>
        </div>
        <div class="head-right">
          <div class="comment">
            <?php echo $dbProductData['comment']; ?>
          </div>
          <div class="price">
            販売価格： ¥ <?php echo $dbProductData['price']; ?>円
          </div>
          <div class="price">
            購入サイト： <?php echo $dbProductData['purchasesite']; ?>
          </div>
          <div class="price">
            レビュー平均(全<?php echo  count($dbReviewData); ?>件中)：<?php echo $dbProductData['average_review']; ?>
            <?php showReview($dbProductData['average_review']); ?>
            
          </div>
          <a href="<?php echo $dbProductData['url']; ?>" id="purchase-link" target="_blank">購入したい方はコチラ >></a>
        </div>
      </div>
      <div class="detail-center">
        <p class="title">最新レビュー</p>
        <div class="review-show1">
          <div class="review-head">
            <img src="<?php echo $dbUserReview['pic']; ?>" alt=""> <p><?php echo $dbUserReview['username']; ?>
             <?php echo (!empty($dbUserReview['age'])) ? $dbUserReview['age'].'歳' : ''; ?>
             <?php if($dbUserReview['sex'] == 1): ?>
               男性
             <?php elseif($dbUserReview['sex'] == 2): ?>
               女性
             <?php endif ?> <?php showReview($dbUserReview['review']); ?></p>
          </div>
          <p class="review-bottom">
            <?php echo $dbUserReview['review_comment']; ?>
          </p>
        </div>
        <span class="button"><button class="allreview">他のレビュー一覧を見る</button></span>
      </div>
      <div class="detail-bottom">
        <p class="title">レビューしてみる</p>
        <form action="" method = "post">
          <div class="left">
            <label>　<span>コメント</span><br>
              <textarea name="review_comment" cols="25" rows="10"></textarea>
            </label>
            <div class="area-msg">
              <?php if(!empty($err_msg['review_comment'])) echo $err_msg['review_comment']; ?>
            </div>
          </div>
          <div class="center">
            <label> <span>評価</span><br>
              <select name="star" >
                <option value="1">★</option>
                <option value="2">★★</option>
                <option value="3">★★★</option>
                <option value="4">★★★★</option>
                <option value="5">★★★★★</option>
              </select>
            </label>
            <a href="index.php<?php echo appendGetParam(array('p_id')); ?>" class="back-index"><i class="fa fa-undo" aria-hidden="true"></i>コンテンツ一覧へ戻る</a>
          </div>
          <div class="right">
            <input type="submit" value="レビュー送信" class="submit-review" id="submit-review">
          </div>
          
          
        </form>
      </div>
    </section>
  </div>

  <?php if($_SESSION['user_id'] == $dbProductData['user_id']): ?> 
    <script>
        document.getElementById('submit-review').onclick = function(){
        alert('自分で登録したコンテンツへレビュー投稿は出来ません');
        return false;
        }
    </script>
  <?php endif ?>
  
  <?php if(in_array($_SESSION['user_id'],array_column($dbReviewData,'reviewer_id'))): ?> 
    <script>
      　document.getElementById('submit-review').onclick = function(){
        alert('１つのコンテンツにつき１回しかレビューは投稿は出来ません');
        return false;
      }
    </script>
  <?php endif ?>


       <div class="review-modal" style="display:none;" >
         <?php foreach($dbAllUserReview as $key => $val): ?> 
         <div class="review-show" style="display:none;">
            
            <div class="review-head">
              <img src="<?php echo $val['pic']; ?>" alt=""> <p><?php echo $val['username']; ?>
              <?php echo (!empty($val['age'])) ? $val['age'].'歳' : ''; ?>
              <?php if($val['sex'] == 1): ?>
               男性
              <?php elseif($val['sex'] == 2): ?>
               女性
              <?php endif ?> <?php showReview($val['review']); ?></p>
            </div>
            <p class="review-bottom">
              <?php echo $val['review_comment']; ?>
            </p>
            
          </div>
          <?php endforeach ?> 
       </div>
        <div class="modal-bg2"></div>

<!-- フッター -->
<?php 
 require('footer.php');
?>