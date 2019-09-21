<footer id="footer">
   <span>&copy;<a href="index.php">Review</a>. All Rights Reserved.</span>
</footer>
<script src="js/vendor/jquery-2.2.2.min.js"></script>



<script>
$(function(){

  //フッターを最下部に固定
  var $ftr = $('#footer');
  if ( window.innerHeight > $ftr.offset().top + $ftr.outerHeight() ){
    $ftr.attr({'style' : 'position:fixed; top:' + ( window.innerHeight - $ftr.outerHeight() ) + 'px;'});
  };


  //モーダルウィンドウを表示
  $("#modal-window,.modal-bg").fadeIn('slow');
  // 閉じるを押したらモーダルウィンドウが消える
  $('.modal-close, .modal-bg').on('click',function(e){
    $('#modal-window,.modal-bg').fadeOut('slow',function(){
      window.location.href = 'index.php';
    });
  });

  //画面の左上からmodal-windowの横幅・高さを引き、その値を2で割ると画面中央の位置が計算できます

  $(window).resize(modalResize());  //browserの高さや幅が変わったときに検知して処理してくれるメソッドがresizeメソッド。注意点は$(window)だけにしか使えない事
  
  function modalResize(){
 
    var w = $(window).width(); //browserのウィンドウサイズの横幅を取得
    var h = $(window).height();  //browserのウィンドウサイズの縦幅を取得
 
    var cw = $("#modal-window").outerWidth(); //モーダルウィンドウの横幅(padding,border含む)を取得
    var ch = $("#modal-window").outerHeight(); //モーダルウィンドウの縦幅(padding,border含む)を取得
 
    //取得した値をcssに追加する
    $("#modal-window").css({
      "left": ((w - cw)/2) + "px",
      "top": ((h - ch)/2) + "px"
    });
  };

  // メッセージ表示用
  var $jsShowMsg = $('#js-show-msg');
  var msg = $jsShowMsg.text();
  if(msg.replace(/^[\s ]+|[\s ]+$/g, '').length){ //msgのスペースをすべて空文字に変換して正確な文字列の長さを取得 
   $jsShowMsg.slideToggle('slow');
   setTimeout(function(){ $jsShowMsg.slideToggle(); },5000);
  }

  // マイページ一覧表示切替

  // 登録コンテンツの中から6コンテンツがこの中に入る
  var $6contents = $('.panel-list'),
  // 登録コンテンツの全てがこの中に入る
  $allshow = $('.panel-list2'),
  // 登録コンテンツ表示切り替えボタン
  $allcontents = $('#allcontents');
  $allcontents.on('click',function(e){
    $6contents.slideToggle('slow');
    $allshow.slideToggle('slow');
  });

  // 一覧表示を終えるボタン
  var $fewcontents = $('#fewcontents');

  $fewcontents.on('click',function(e){
    $allshow.slideToggle('slow');
    $6contents.slideToggle('slow');
  });

  // レビュー一覧表示ボタン
  var $show = $('#show'),
      // レビュー一覧表示終了ボタン
      $blind = $('#blind'),
      // 自分のした全レビューがこの中に入っている
      $allcontents2 = $('#allcontents2'),
      // 自分のした全レビューのうち6つがこの中に入っている
      $fewcontents2 = $('#fewcontents2');

  // 一覧をみるをクリックしたら全てのレビューを表示
  $allcontents2.on('click',function(e){
    $show.slideToggle('slow');
    $blind.slideToggle('slow');
  });

  // 一覧表示を終えるをクリックしたら6つのレビューのみを表示
  $fewcontents2.on('click',function(e){
    $blind.slideToggle('slow');
    $show.slideToggle('slow');
  });

  // レビュー表示用パネル
  var $panel = $('.pan2');
  // 自分がレビューをした商品パネルをクリックしたら自分のレビューが下にぬるっと表示される
  $panel.on('click',function(e){  
    $(this).find('.panel-bottom').slideToggle('slow');
  });

  // コンテンツ詳細画面でのレビュー一覧表示用パネル
  var $reviewModal = $('.review-modal');

  // 他のレビュー一覧をみるをクリックすると最新レビュー以外の全レビューがモーダル形式で表示される
  $('.allreview').on('click',function(e){
    // 背面用の紫（押したら消える）とレビュー一覧用のモーダルを表示
    $('.review-show,.modal-bg2,.review-modal').fadeIn('slow');
    // 背景の紫の部分をクリックしたらレビュー表示用モーダルが消える
    $('.modal-bg2').on('click',function(e){
      $('.review-show,.modal-bg2,.review-modal').fadeOut('slow');
    })
  })

  // 退会処理モーダル
  $('#withdraw-click').on('click',function(e){

    //画面の左上からmodal-windowの横幅・高さを引き、その値を2で割ると画面中央の位置が計算できます
    $(window).resize(modalResize2);  //browserの高さや幅が変わったときに検知して処理してくれるメソッドがresizeメソッド。注意点は$(window)だけにしか使えない事
      
    function modalResize2(){
      var w = $(window).width(); //browserのウィンドウサイズの横幅を取得
      var h = $(window).height();  //browserのウィンドウサイズの縦幅を取得
 
      var cw = $("#modal-window2").outerWidth(); //モーダルウィンドウの横幅(padding,border含む)を取得
      var ch = $("#modal-window2").outerHeight(); //モーダルウィンドウの縦幅(padding,border含む)を取得
 
      //取得した値をcssに追加する
      $("#modal-window2").css({
        "left": ((w - cw)/2) + "px",
        "top": ((h - ch)/2) + "px"
      });
    };
  

    // 背景用の紫（押したら消える）と退会処理用のフォームウィンドウを表示
    $('.modal-bg2,#modal-window2').fadeIn('slow');

    $('.withdraw-submit').on('click',function(){
      if(!confirm('本当に退会しますか？')){
      /* 退会を思いとどまる場合 */
      return false;
      }
    });

    // 背景の紫の部分をクリックしたら退会処理モーダルがフェードアウトする
    $('.modal-bg2').on('click',function(e){
      $('.modal-bg2,#modal-window2').fadeOut('slow');
    });
  });

 
  
 

  // 画像ライブプレビュー

  // 
  var $dropArea = $('.area-drop');
  var $fileInput = $('.input-file');
  $dropArea.on('dragover',function(e){
    e.stopPropagation();
    e.preventDefault();
    $(this).css('border','3px #fff dashed');
  });
  $dropArea.on('dragleave',function(e){
    e.stopPropagation();
    e.preventDefault();
    $(this).css('border','none');
  });
  $fileInput.on('change',function(e){
    $dropArea.css('border','none');
    var file = this.files[0], // 2. files配列にファイルが入っています。
        $img = $(this).siblings('.prev-img'), // 3. jQueryのsiblingsメソッドで兄弟のimgを取得
        fileReader = new FileReader();  // 4. ファイルを読み込むfileReaderオブジェクト

        

    
    // 5. 読み込みが完了した際のイベントハンドラ。imgのsrcにデータをセット
    fileReader.onload = function(event){
      // 読み込んだデータをimgに設定
      $img.attr('src',event.target.result).show();
    };

    // 6. 画像読み込み
    fileReader.readAsDataURL(file);
  });

});
</script>
<script>
  $(function(){
     // 登録コンテンツ削除
  var $delete,
      deleteProductId;

  $delete =  $('.js-click-delete') || null;
  deleteProductId = $delete.data('productid') || null;
  // 数値の0はfalseと判定されてしまう。productIdは0の場合もありうるので0もtrueとするためにはundefinedまたはnullを判定する
　if(deleteProductId !== undefined && deleteProductId !== null){
      $delete.on('click',function(){
        var $this = $(this);
        $.ajax({
          type: "POST",            // $_POST['productId'] = likeProductId, action="ajax.php"
          url: "ajaxDelete.php",
          dataType: 'json',
          data: { productid : deleteProductId}
        }).done(function(data){
          console.log('Ajax Success');
          alert(data.success);
          window.location.href = 'mypage.php';
        }).fail(function() {
          console.log('Ajax Error');
        });
      });
    }
  })
</script>
</body>
</html>