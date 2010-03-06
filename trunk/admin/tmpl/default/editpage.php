<?php
/**
 * ページ作成トップテンプレート
 *
 * ページ作成トップのテンプレートです。
 * 一般的な記述方法はこのファイルをご覧ください。
 *
 * @author $Author:$
 * @version $Id:$
 */
?>
<?php theAdminHeader('editpage-'); ?>
  <div id="main">
    <div id="contents">
      <h2><a href="<?php theURI(); ?>?mode=editpage">新規作成</a></h2>
      <div id="pagemenu">
        <form action="<?php theURI(); ?>" method="post">
          <input type="hidden" name="mode" value="editpage" />
          <input type="hidden" name="page" value="-1" />
          <input type="submit" value="新規作成" />
        </form>
      </div>

      <h2><a href="<?php theURI(); ?>?mode=editpage">ページ一覧</a></h2>
      <div id="adminlist">
        <table>
        <thead>
          <tr><th>&nbsp;</th><th>ページ名</th><th>パス名</th><th>更新日付</th></tr>
        </thead>
        <tbody id="pagelist">
<?php foreach (get_page_list() as $p) : ?>
          <tr>
            <td><a href="<?php theURI(); print "?mode=editpage&amp;page=" . $p['id']; ?>">編集</a></td>
            <td><?php print $p['title']; ?></td>
            <td><?php print $p['path']; ?></td>
            <td><?php print $p['utime']; ?></td>
          </tr>
<?php endforeach; ?>
        </tbody>
        </table>
        <ul class="pagination">
<?php thePaginationPages(); ?>
        </ul>
      </div>
    </div>
<?php theAdminSideLeft(); ?>
  </div>
<?php theAdminFooter(); ?>
