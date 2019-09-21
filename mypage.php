<?php

// 共通変数・関数ファイルの読み込み
require('function.php');

debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debug('「 マイページ ');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');

// 商品情報を取得する
$dbProductData = getmyProducts($_SESSION['user_id']);
//debug('自分の出品情報：'.print_r($dbProductData,true));
$myReviewInfo = getMyReviewInfo($_SESSION['user_id']);
//debug('自分のレビュー情報：'.print_r($myReviewInfo,true));
?>
<?php
$siteTitle = 'Review | マイページ';
require('head.php');
?>

<body class="page-signup page-2colum">
  
  <!-- ヘッダー --> 
  <?php 
  require('header.php');
  ?>
  
  <?php 
  require('auth.php');
  ?>

  <p id="js-show-msg" class="msg-slide">
     <?php echo getSessionFlash('msg_success'); ?>
  </p>

  <div id="contents" class="site-width-2">
    
    <section id="main2">
      <div class="mycontents">
        <p class="title">登録商品</p>
        <div class="panel-list">
          <?php for($i = 0; $i <= 5; $i++): ?>
            <?php if(!empty($dbProductData[$i])) : ?>
              <a href="productRegist.php<?php echo (!empty(appendGetParam())) ? appendGetParam().'&p_id='.$dbProductData[$i]['id'] : '?p_id='.$dbProductData[$i]['id']; ?> " class="panel">
                <div class="panel-head">
                  <div class="head-right"><?php echo $dbProductData[$i]['name']; ?></div>
                </div>
                <div class="panel-left">
                  <img src="<?php echo sanitize($dbProductData[$i]['pic']); ?>" alt="<?php echo sanitize($dbProductData[$i]['name']); ?>">
                  <div class="head-left"><?php echo $dbProductData[$i]['purchasesite'];?></div>
                </div>
                <div class="panel-right">
                  <span class="data">販売価格</span>
                  <span class="data orange">￥<?php echo $dbProductData[$i]['price']; ?> 円</span>
                  <span class="data">レビュー平均</span>
                  <?php $dbProductInfo = getProductOne($dbProductData[$i]['id']); ?>
                  <span class="data orange"><?php echo $dbProductInfo['average_review']; ?></span>
                  <div class="wrap"><?php showReview($dbProductInfo['average_review']); ?></div>
                </div>  
              </a>
            <?php endif ?>
          <?php endfor ?>
          <button id="allcontents">一覧を見る</button>
        </div>
        
        <div class="panel-list2" style="display:none;">
          <?php foreach($dbProductData as $key => $val): ?>
            <a href="productRegist.php<?php echo (!empty(appendGetParam())) ? appendGetParam().'&p_id='.$val['id'] : '?p_id='.$val['id']; ?> " class="panel">
              <div class="panel-head">
                <div class="head-right"><?php echo $val['name']; ?></div>
              </div>
              <div class="panel-left">
                <img src="<?php echo sanitize($val['pic']); ?>" alt="<?php echo sanitize($val['name']); ?>">
                <div class="head-left"><?php echo $val['purchasesite'];?></div>
              </div>
              <div class="panel-right">
                <span class="data">販売価格</span>
                <span class="data orange">￥<?php echo $val['price']; ?> 円</span>
                <span class="data">レビュー平均</span>
                <?php $dbProductInfo = getProductOne($val['id']); ?>
                <span class="data orange"><?php echo $dbProductInfo['average_review']; ?></span>
                 <div class="wrap"> <?php showReview($dbProductInfo['average_review']); ?></div>
              </div>  
            </a>
          <?php endforeach ?>
          <button id="fewcontents">一覧表示を終える</button>
        </div> 
       
        <p class="title">投稿レビュー</p>
        <section class="panel-list3" id="show">
          <?php for($i = 0; $i <= 5 ; $i++): ?>
            <?php if(!empty($myReviewInfo[$i])) :?>
              <div class="panel pan2">
                <div class="panel-top">
                  <div class="product-name"><?php echo $myReviewInfo[$i]['name']; ?></div>
                  <div class="panel-left">
                    <img src="<?php echo $myReviewInfo[$i]['pic']; ?>" alt="">
                    <span class="price">¥ <?php echo $myReviewInfo[$i]['price']; ?>円</span>
                  </div>
                  <div class="panel-right">
                    <div class="purchaseplace"><?php echo $myReviewInfo[$i]['purchasesite']; ?></div>
                    <div class="show-review"><span class="title">レビュー平均</span>  <?php showReview($myReviewInfo[$i]['average_review']); ?></div>
                    <div class="show-review"><span class="title">投稿済みレビュー</span>  <?php showReview($myReviewInfo[$i]['review']); ?></div>
                  </div>
                </div>
                <p class="panel-bottom" style="display:none;"><?php echo $myReviewInfo[$i]['review_comment']; ?></p>
              </div>
            <?php endif ?>
          <?php endfor ?>
          <button id="allcontents2">一覧を見る</button>
        </section>

        <section class="panel-list3" id="blind">
          <?php foreach($myReviewInfo as $key => $val):?>
            <div class="panel pan2">
              <div class="panel-left">
                <img src="<?php echo $val['pic']; ?>" alt="">
                <span class="price">¥ <?php echo $val['price']; ?>円</span>
              </div>
              <div class="panel-right">
                <div class="purchaseplace"><?php echo $val['purchasesite']; ?></div>
                <div class="product-name"><?php echo $val['name']; ?></div>
                <div class="show-review"><span class="title">レビュー平均</span>  <?php showReview($val['average_review']); ?></div>
                <div class="show-review"><span class="title">投稿済みレビュー</span>  <?php showReview($val['review']); ?></div>
              </div>
              <p class="panel-bottom" style="display:none;"><?php echo $myReviewInfo[$i]['review_comment']; ?></p>
            </div>
          <?php endforeach ?>
          <button id="fewcontents2">一覧表示を終える</button>
        </section>

      </div>
    </section>

    <?php 
      require('sidebar.php');
    ?>
  </div>

  <div class="modal-bg2"></div>
  <div id="modal-window2" style="display:none;">
    <form action="withdraw.php" method="post">
      <p class="title">退会</p>
      <input type="submit" name="submit" value="退会する" class="withdraw-submit">
    </form>
  </div>

  <?php 
  require('footer.php');
  ?>


      
