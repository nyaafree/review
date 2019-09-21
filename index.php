<?php

// 共通変数・関数ファイルの読み込み
require('function.php');

debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debug('「 トップページ ');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debugLogStart();

//===============================================================
// 画面処理
//===============================================================

// 画面表示用データ取得
//===============================================================
// GETパラメータ取得
//===============================================================
// 現在のページ番号
$currentPageNum = (!empty($_GET['p'])) ? $_GET['p'] : 1; // ページ用GETパラメータがなければ１ページ目
// カテゴリー
$category = (!empty($_GET['c_id'])) ? $_GET['c_id'] : ''; 
// フリーワード
$freeWord = (!empty($_GET['free'])) ? $_GET['free'] : ''; 
// レビューソート
$sort = (!empty($_GET['sort'])) ? $_GET['sort'] : '';

// パラメータに不正な値が入っているかチェック
if(!is_int((int)$currentPageNum)){
  error_log('エラー発生：指定ページに不正な値が入りました。');
  header("Location:index.php"); //トップページへ
}

// 表示件数
$listSpan = 15;
// 現在の表示レコードの先頭を算出
$currentMinNum = (($currentPageNum-1)*$listSpan); // 1ページ目なら0、2ページ目なら15になる
// DBから商品データを取得
$dbProductData = getProductList($currentMinNum, $category, $freeWord, $sort);
// カテゴリデータ
$dbCategoryData = getCategory();
// レビューデータ
// デバッグ
// debug('商品データ：'.print_r($dbProductData,true));
// debug('カテゴリデータ：'.print_r($dbCategoryData,true));

debug('画面表示処理終了 <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<');
?>
<?php
$siteTitle = 'Review | レビュー一覧ページ';
require('head.php');
?>

<body class="page-signup page-2colum">

<p id="js-show-msg" class="msg-slide">
     <?php echo getSessionFlash('msg_success'); ?>
</p>
  
  <!-- ヘッダー --> 
  <?php 
  require('header.php');
  ?>

  <div class="site-width-2" id="contents">

    <?php
      require('leftsidebar.php');
    ?>
    <!-- メインコンテンツ --> 
    <section id="main2">
      <div class="search-title">
        <div class="search-left">
          <span class="total-num"><?php echo sanitize($dbProductData['total']); ?></span>件の商品が見つかりました
        </div>
        <div class="search-right">
          <span class="num"><?php echo (!empty(sanitize($dbProductData['data']))) ? $currentMinNum + 1 : 0; ?></span> - <span class="num"><?php echo $currentMinNum + count($dbProductData['data']); ?></span>件 / <span class="num"><?php echo $dbProductData['total']; ?></span>件中
        </div>
      </div>
      <div class="panel-list">
        <?php foreach($dbProductData['data'] as $key => $val): ?>
          <a href="productDetail.php<?php echo (!empty(appendGetParam())) ? appendGetParam().'&p_id='.sanitize($val['id']) : '?p_id='.sanitize($val['id']); ?> " class="panel">
            <div class="panel-head">
              <!-- 商品名 -->
              <div class="head-right"><?php echo $val['name']; ?></div>
            </div>
            <div class="panel-left">
              <!-- 商品の画像と購入サイト -->
              <img src="<?php echo sanitize($val['pic']); ?>" alt="<?php echo sanitize($val['name']); ?>">
              <div class="head-left"><?php echo $val['purchasesite'];?></div>
            </div>
              <div class="panel-right">
                <span class="data">販売価格</span>
                <span class="data orange">￥ <?php echo $val['price']; ?>円</span>
                <span class="data">レビュー平均</span>
                <?php $dbProductInfo = getProductOne($val['id']); ?>
                <span class="data orange"><?php echo $dbProductInfo['average_review']; ?></span>
                <div class="wrap"><?php showReview($dbProductInfo['average_review']); ?></div>
              </div>  
            
          </a>
        <?php endforeach ?>
      </div>

      <?php pagination($currentPageNum,$dbProductData['total_page'],addParam(array('p')) ); ?>
    </section>
  </div>

  <?php 
  require('footer.php');
  ?>


