<?php theHeader() ?>
  <div id="main">
<?php theSideLeft() ?>
    <div id="contentsnoright">
      <h2>articleデフォルト</h2>
      <h3><?php theArticleTitle(); ?></h3>
      <div id="article">
<?php theArticleDescription(); ?>
        <ul>
<?php foreach(get_article_children() as $url => $c) : ?>
          <li><a href="<?php print $url; ?>"><?php print $c->record['title']; ?></a></li>
<?php endforeach; ?>
        </ul>
      </div>
      <div id="site">
        <h4><?php theSiteNameOfArticle(); ?></h4>
        <div class="address">
<?php theSiteContactOfArticle(); ?>
        </div>
      </div>
    </div>
  <div style="clear: both;"> </div>
  </div>
<?php theFooter() ?>
