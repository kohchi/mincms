<?php theHeader() ?>
  <div id="main">
<?php theSideLeft() ?>
    <div id="contentsnoright">
      <h2>siteデフォルト</h2>
      <h3><?php theSiteName(); ?></h3>
      <div id="site">
        <ul>
<?php foreach(get_site_children() as $url => $c) : ?>
          <li><a href="<?php print $url; ?>"><?php print $c->record['title']; ?></a></li>
<?php endforeach; ?>
        </ul>
        <div class="address">
<?php theSiteContact(); ?>
        </div>
      </div>
    </div>
    <div style="clear: both;"> </div>
  </div>
<?php theFooter() ?>
