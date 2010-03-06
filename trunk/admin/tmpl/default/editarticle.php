<?php
/**
 * 記事作成トップテンプレート
 *
 * 記事作成トップのテンプレートです。
 * 一般的な記述方法はこのファイルをご覧ください。
 *
 * @author $Author:$
 * @version $Id:$
 */
?>
<?php theAdminHeader('editarticle-'); ?>
  <div id="main">
    <div id="contents">
      <h2><a href="<?php theURI(); print '?mode=editarticle'; ?>">新規作成</a></h2>
      <div id="articlemenu">
<?php if (canCreate()) : ?>
        <form action="<?php theURI(); ?>" method="post">
          <input type="hidden" name="mode" value="editarticle" />
          <input type="hidden" name="site" value="-1" />
          <input type="hidden" name="article" value="-1" />
          <select name="kind">
            <option value="C">カテゴリ</option>
            <option value="A">記事</option>
            <option value="U">URL</option>
          </select>
          <select name="parent">
<?php theParentOptions(); ?>
          </select>
          <input type="submit" value="新規作成" />
        </form>
<?php else : ?>
        <p>作成できません。</p>
<?php endif; ?>
      </div>

      <h2><a href="<?php theURI(); print '?mode=editarticle'; ?>">最新記事一覧</a></h2>
      <div id="adminlist">
        <table>
        <thead>
          <tr><th>&nbsp;</th><th>サイト名</th><th>名前</th><th>種別</th><th>状態</th><th>更新日付</th></tr>
        </thead>
        <tbody id="newarticles">
<?php foreach (get_new_articles() as $a) : ?>
          <tr>
            <td><a href="<?php theURI(); print '?mode=editarticle' . MC_PARAM_SEPARATOR . 'site=' . $a['siteid'] . MC_PARAM_SEPARATOR . 'article=' . $a['id']; ?>">修正</a></td>
            <td><?php print $a['sitename']; ?></td>
            <td><a href="<?php theViewURL($a['siteid'], $a['id']); ?>" target="_blank"><?php print $a['title']; ?></a></td>
            <td><?php theKindName($a['kind']); ?></td>
            <td><?php theStatusName($a['status']); ?></td>
            <td><?php print $a['utime']; ?></td>
          </tr>
<?php endforeach; ?>
        </tbody>
        </table>
        <ul class="pagination">
<?php thePaginationNewArticles(); ?>
        </ul>
      </div>
    </div>
    <div id="left">
      <h2>サイトと記事</h2>
      <div id="sitetree">
<?php theArticleTreeLeft(); ?>
      </div>
    </div>
  </div>
<?php theAdminFooter(); ?>
