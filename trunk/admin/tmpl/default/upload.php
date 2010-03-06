<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>アップロードファイル</title>
<link rel="stylesheet" type="text/css"
  href="<?php theCurrentAdminUri(); ?>upload.css" media="screen" />
<script type= "text/javascript">/*<![CDATA[*/

function delete_submit(myform, f) {
	if (confirm("delete " + f + " ?")) {
		myform.submit();
		return true;
	}

	return false;
}

/*]]>*/</script>
</head>
<body>
<div id="container">
  <div id="header">
    <h1>アップロードファイル</h1>
    <p>ファイルのアップロードとアップロード一覧です。</p>
  </div>

  <div id="main">
    <h2>アップロードの実行</h2>
    <p>同じファイル名の場合は上書きされますのでご注意願います。</p>
    <form action="<?php theURI(); ?>" method="post"
      enctype="multipart/form-data">
      <fieldset>
        <input type="hidden" name="mode" value="upload" />
        <input type="hidden" name="type" value="<?php theUploadType(); ?>" />
        <input type="hidden" name="id" value="<?php theUploadId(); ?>" />
        <input type="hidden" name="action" value="upload" />
        <input name="uploadfile" type="file" size="64" />
        <input type="submit" value="upload" />
      </fieldset>
    </form>

    <h2>アップロードファイル一覧</h2>
    <table class="files">
      <thead>
        <tr>
        <th class="kind">種別</th>
        <th>ファイル名</th>
        <th class="delete">削除</th>
        </tr>
      </thead>
      <tbody>
<?php theUploadedFiles(); ?>
      </tbody>
    </table>
    <div class="pagination">
<?php theUploadPagination(); ?>
    </div>
  </div>

  <div id="footer">
    <hr />
  </div>
</div>
</body>
</html>
