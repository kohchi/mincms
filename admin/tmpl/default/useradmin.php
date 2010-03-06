<?php
/**
 * ユーザ一覧テンプレート
 *
 * デフォルトのテンプレートです。
 * 一般的な記述方法はこのファイルをご覧ください。
 *
 * @author $Author:$
 * @version $Id:$
 */
?>
<?php theAdminHeader(); ?>
  <div id="main">
    <div id="contents">
      <h2><a href="<?php theURI(); print '?mode=useradmin&amp;id=-1'; ?>">新規</a></h2>
      <p>&nbsp;</p>
      <h2><a href="<?php theURI(); print '?mode=useradmin'; ?>">ユーザ一覧</a></h2>
      <div id="adminlist">
        <table>
        <thead>
          <tr><th>ユーザID</th><th>ユーザ名</th><th>権限</th></tr>
        </thead>
        <tbody>
<?php foreach (get_users() as $u) : ?>
        <tr>
          <td><a href="<?php theURI(); print '?mode=useradmin&amp;id=' . $u['id']; ?>"><?php print $u['id']; ?></a></td>
          <td><?php print $u['name']; ?></td>
          <td><?php theAuthority($u['id']); ?></td>
        </tr>
<?php endforeach; ?>
        </tbody>
        </table>
      </div>
      <ul class="pagination">
<?php thePaginationUsers(); ?>
      </ul>
    </div>
<?php theAdminSideLeft(); ?>
  </div>
<?php theAdminFooter(); ?>
