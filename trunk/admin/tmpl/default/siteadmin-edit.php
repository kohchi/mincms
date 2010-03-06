<?php
/**
 * サイト情報修正テンプレート
 *
 * デフォルトのテンプレートです。
 * 一般的な記述方法はこのファイルをご覧ください。
 *
 * @author $Author:$
 * @version $Id:$
 */

list($r) = get_tmpl_result();
?>
<?php theAdminHeader(); ?>
  <div id="main">
    <div id="contents">
      <h2>サイト情報修正</h2>
      <form action="<?php theURI(); ?>" method="post">
        <fieldset>
          <legend>入力項目</legend>
          <dl id="site">
            <dt>サイトID</dt>
            <dd><?php print $r['id']; ?>
              <input type="hidden" name="id"
                value="<?php print $r['id']; ?>" />
            </dd>
            <dt>パス名</dt>
            <dd>
              <input type="text" name="path"
                value="<?php print $r['path']; ?>" /><br />
            </dd>
            <dt>サイト名</dt>
            <dd>
              <input type="text"
                name="name" value="<?php print $r['name']; ?>" />
            </dd>
            <dt>サイト連絡先</dt>
            <dd>
              <textarea name="contact"
                cols="48" rows="8"><?php print $r['contact']; ?></textarea>
            </dd>
	    <dt>サイト内ユーザ</dt>
            <dd><?php theEditSiteUsers($r['siteusers']); ?></dd>
	    <dt>登録/修正時刻</dt>
            <dd><?php theRegistUpdateDate($r); ?></dd>
	    <dt>&nbsp;</dt>
            <dd>
              <input type="hidden" name="mode" value="execute" />
              <input type="hidden" name="type" value="site" />
<?php theSiteAdminEditType(); ?>
              <input type="submit" value="実行" />
              <input type="reset" value="リセット" />
            </dd>
          </dl>
        </fieldset>
      </form>
    </div>
<?php theAdminSideLeft(); ?>
  </div>
<?php theAdminFooter(); ?>
