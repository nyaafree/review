<section id="leftside">
    <form action="" method="get" >
      <label>
        カテゴリ<br>
        <select name="c_id" id="">
          <option value="0" <?php if(getFormData('c_id',true) == 0 ) echo 'selected'; ?> >選択してください</option>
          <?php foreach ($dbCategoryData as $key => $val) : ?>
            <option value="<?php echo $val['id']; ?>"><?php echo $val['name']; ?></option>
          <?php endforeach ?>
        </select>
      </label>
      <label>
        表示順<br>
        <select name="sort" id="">
          <option value="0" <?php if(getFormData('sort',true) == 0) echo 'selected'; ?>>選択してください</option>
          <option value="1" <?php if(getFormData('sort',true) == 1) echo 'selected'; ?>>レビューが高い順に並べる</option>
          <option value="2" <?php if(getFormData('sort',true) == 2) echo 'selected'; ?>>レビューが低い順に並べる</option>
        </select>
      </label>
      <label>
         フリーワードで検索
         <input type="text" name="free">
      </label>
      <input type="submit" value="送信する">
    </form>
</section>