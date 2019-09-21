<header>
  <div class="site-width">
    <h1 class="title"><a href="index.php"><i class="fa fa-star" aria-hidden="true"></i>Review</a></h1>
    <nav id="top-nav">
        <!-- セッションにユーザーIDが入っている、すなはちログインしている場合のヘッダーメニュー -->
        <?php if(!empty($_SESSION['user_id'])){ ?>
          <a href="mypage.php"><button class="form-modal">マイページ</button></a>
          <a href="logout.php"><button class="form-modal">ログアウト</button></a>
        <!-- ログインしていない場合のヘッダーメニュー -->
       <?php }else{ ?>
          <a href="login.php"><button class="form-modal" id="log-modal">ログイン</button></a>
          <a href="signup.php"><button class="form-modal" id="signup-modal">ユーザー登録</button></a>
       <?php } ?>
    </nav>
  </div>
</header>