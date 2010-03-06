<?php theHeader() ?>
  <div id="main">
<?php theSideLeft() ?>
    <div id="contents">
      <h2>検索結果</h2>
      <div class="result">
        <p>検索文字列：<?php print $this->string; ?></p>
<?php foreach (search_result(5) as $aa) : ?>
        <ul class="resultlist">
  <?php foreach ($aa as $a) : ?>
          <li><a href="<?php theArticlePath($a['sid'], $a['id']); ?>"><?php print $a['title']; ?></a>(<?php print $a['utime']; ?>)</li>
  <?php endforeach; ?>
        </ul>
<?php endforeach; ?>
      </div>
    </div>
<?php theSideRight() ?>
    <div style="clear: both;"> </div>
  </div>
<?php theFooter() ?>
