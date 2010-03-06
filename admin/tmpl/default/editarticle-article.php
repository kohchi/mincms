<?php
/**
 * 記事作成(記事専用)テンプレート
 *
 * 記事作成(記事専用)のテンプレートです。
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

<?php if (doesEditArticle()) : ?>
      <h2><a href="<?php theURI(); print '?mode=editarticle'; ?>">カテゴリ変更</a></h2>
      <div id="articlecategory">
        <form action="<?php theURI(); ?>" method="post">
          <input type="hidden" name="mode" value="execute" />
          <input type="hidden" name="type" value="changecategory" />
          <input type="hidden" name="article" value="<?php theArticleID(); ?>" />
          <select name="parent">
<?php theOurCategories(); ?>
          </select>
          <input type="submit" value="変更" />
        </form>
      </div>
<?php endif; ?>

      <h2><a href="<?php theURI(); print '?mode=editarticle'; ?>">記事の作成</a></h2>
      <div id="article">
        <h3>ファイルアップロード</h3>
        <form action="<?php theURI(); ?>" method="post">
          <input type="hidden" name="mode" value="upload" />
          <input type="hidden" name="type" value="site" />
          <input type="hidden" name="id" value="<?php theArticleSiteID(); ?>" />
          <input type="submit" value="アップロード画面を開く" />
        </form>
        <a href="<?php theURI(); ?>?mode=upload&amp;type=site&amp;id=<?php theArticleSiteID(); ?>&amp;keepThis=true&amp;TB_iframe=true"
          title="ファイルアップロード"
          class="thickbox">ファイルアップロード画面</a>
        <h3>記事</h3>
        <form action="<?php theURI(); ?>" method="post">
          <input type="hidden" name="mode" value="execute" />
          <input type="hidden" name="type" value="article" />
          <input type="hidden" name="kind" value="article" />
<?php theArticleHiddenID(); ?>
          <input type="text" name="title" value="<?php theArticleTitle(); ?>" size="64" <?php theArticleReadOnly(); ?>/><br />
          <textarea name="description" class="tinymce" <?php theArticleReadOnly(); ?>><?php theArticleDescription(); ?></textarea><br />
          開始日時<input type="text" name="bdate" id="bdatepicker"
            value="<?php theArticleBeginDate(); ?>" size="14" <?php theArticleReadOnly(); ?>/>
          <input type="text" name="bhms" id="btimePicker"
            value="<?php theArticleBeginHMS(); ?>" size="10" <?php theArticleReadOnly(); ?>/>
          終了日時<input type="text" name="edate" id="edatepicker"
            value="<?php theArticleEndDate(); ?>" size="14" <?php theArticleReadOnly(); ?>/>
          <input type="text" name="ehms" id="etimePicker"
            value="<?php theArticleEndHMS(); ?>" size="10" <?php theArticleReadOnly(); ?>/><br />
<?php theArticleEditModeButton(); ?><br />
<?php theArticleStatusBox(); ?><br />
<?php theArticleLabelOption(); ?><br />
          <input type="submit" value="submit" />
          <input type="reset" value="reset" />
        </form>
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
