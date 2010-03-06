<?php theHeader() ?>
  <div id="main">
<?php theSideLeft() ?>
    <div id="contents">
      <h2>メインコンテンツ</h2>
      <div class="whatsnew">
        <ul>
<?php foreach (pick_up_at_site(4) as $a) : ?>
          <li><a href="<?php theArticlePath($a['sid'], $a['id']); ?>"><?php print $a['title']; ?></a>(<?php print $a['utime']; ?>)</li>
<?php endforeach; ?>
        </ul>
      </div>
      <div class="calendar">
      <?php theCalendarWithLabel(1); ?>
      </div>
    </div>
<?php theSideRight() ?>
  <div style="clear: both;"> </div>
  </div>
<?php theFooter() ?>
