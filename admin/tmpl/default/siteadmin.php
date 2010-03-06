<?php
/**
 * サイト一覧テンプレート
 *
 * サイト一覧のテンプレートです。
 * 一般的な記述方法はこのファイルをご覧ください。
 *
 * @author $Author:$
 * @version $Id:$
 */
?>
<?php theAdminHeader(); ?>
  <div id="main">
    <div id="contents">
      <h2><a href="<?php theURI(); print '?mode=siteadmin&amp;id=-1'; ?>">新規</a></h2>
      <p>&nbsp;</p>
      <h2><a href="<?php theURI(); print '?mode=siteadmin'; ?>">サイト一覧</a></h2>
      <div id="adminlist">
        <table>
        <thead>
          <tr><th>サイトID</th><th>パス名</th><th>サイト名</th><th>所属ユーザ</th></tr>
        </thead>
        <tbody>
<?php foreach (get_sites() as $r) : ?>
        <tr>
          <td><a href="<?php theURI(); print '?mode=siteadmin&amp;id=' . $r['id']; ?>"><?php print $r['id']; ?></a></td>
          <td><?php print $r['path']; ?></td>
          <td><?php print $r['name']; ?></td>
          <td>
            <ul>
<?php foreach ($r['user'] as $u) : ?>
              <li><?php theSiteUser($u); ?></li>
<?php endforeach; ?>
            </ul>
          </td>
        </tr>
<?php endforeach; ?>
        </tbody>
        </table>
      </div>
      <ul class="pagination">
<?php thePaginationSites(); ?>
      </ul>
    </div>
<?php theAdminSideLeft(); ?>
  </div>
<?php theAdminFooter(); ?>
