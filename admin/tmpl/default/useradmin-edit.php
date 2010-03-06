<?php
/**
 * ユーザ情報修正テンプレート
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
      <h2>ユーザ情報修正</h2>
      <form action="<?php theURI(); ?>" method="post">
        <fieldset>
          <legend>入力項目</legend>
          <dl id="user">
            <dt>ユーザID</dt>
            <dd>
              <input type="text" name="id"
                value="<?php print $r['id']; ?>" <?php theReadOnly($r['id']); ?> />
            </dd>
            <dt>パスワード</dt>
            <dd>パスワードは変更する場合<em>のみ</em>入力してください。<br />
              <input type="password" name="password0" value="" /><br />
              <input type="password" name="password1" value="" />
              (確認のため再度入力してください)
            </dd>
            <dt>ユーザ名</dt>
            <dd>
              <input type="text"
                name="name" value="<?php print $r['name']; ?>" />
            </dd>
            <dt>メールアドレス</dt>
            <dd>
              <input type="text"
                name="email" value="<?php print $r['email']; ?>" />
            </dd>
	    <dt>権限</dt>
            <dd><?php theAuthorityCheckbox($r['id']); ?></dd>
	    <dt>登録/修正時刻</dt>
            <dd><?php theRegistUpdateDate($r); ?></dd>
	    <dt>&nbsp;</dt>
            <dd>
              <input type="hidden" name="mode" value="execute" />
              <input type="hidden" name="type" value="user" />
<?php theUserAdminEditType(); ?>
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
