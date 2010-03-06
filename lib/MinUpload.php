<?php
/**
 * Class of MinUpload.
 *
 * This class is to uploading local files to the web server.
 *
 * @version $Id:$
 * @access public
 * @package MinCMS
 * @author $Author:$
 */
class MinUpload
{
	/** the directory for upload. @var string */	var $dir;
	/** the top URL of upload files. @var string */	var $url;
	/** type of upload(site or page). @var string */	var $type;
	/** site id if type is site, otherwise none. @var string */	var $id;
	/** the page offset value. @var int */		var $offset;
	/** the pagination information. @var array */	var $page_info;

	/**
	 * The constructor
	 *
	 * invoke when an instance of this class is generated.
	 *
	 * @param string $dir the directory for upload.
	 * @param string $url the top URL of upload files.
	 * @param string $type type of upload(site or page).
	 * @param string $id site id if type is site, otherwise none.
	 * @param string $offset the page offset value.
	 */
	function __construct($dir, $url, $type, $id, $offset = 0) {
		$this->dir = $dir;
		$this->url = $url;
		$this->type = $type;
		$this->id = $id;
		$this->offset = $offset;
		$this->page_info = array(-1, 0, -1, 0);
	}

	function is_image($f) {
		global $UploadImageFormat;

		$info = getimagesize($f);	// x = $info[0], y = $info[1];

		if (!$info) {
			return False;
		}

		// Is the type of this image supported on this PHP(GD) ?
		if (imagetypes() & $UploadImageFormat[$info[2] - 1]) {
			return $UploadImageFormat[$info[2] - 1];
		}

		return False;
	}

	function create_thumbnail_filename($f) {
		if (eregi("\.(gif|jpe?g|png)$", $f)) {
			return eregi_replace("(\.(gif|jpe?g|png))$",
				MC_UPLOAD_THUMBNAIL_SUFFIX . "\\1", $f);
		}

		return "";
	}

	/**
	 * create a thumbnail image
	 *
	 * required GD2 library.
	 */
	function create_thumbnail($f, $type) {
		switch ($type) {
		case IMG_GIF :
			$srcim = imagecreatefromgif($f);
			break;
		case IMG_JPG :
			$srcim = imagecreatefromjpeg($f);
			break;
		case IMG_PNG :
			$srcim = imagecreatefrompng($f);
			break;
		default :
			return;
		}

		$srcx = imagesx($srcim);
		$srcy = imagesy($srcim);
		$dstx = MC_UPLOAD_THUMBNAIL_WIDTH;
		$dsty = round($srcy * $dstx / $srcx);
		$dstim = imagecreatetruecolor($dstx, $dsty);	// GD 2.0 later
		imagecopyresampled($dstim, $srcim,		// GD 2.0 later
			0, 0, 0, 0, $dstx, $dsty, $srcx, $srcy);
		$dstname = $this->create_thumbnail_filename($f);

		switch ($type) {
		case IMG_GIF :
			imagegif($dstim, $dstname);
			break;
		case IMG_JPG :
			imagejpeg($dstim, $dstname);
			break;
		case IMG_PNG :
			imagepng($dstim, $dstname);
			break;
		}
	}

	function upload() {
		$tmpfile = $_FILES[MC_UPLOAD_INPUT_NAME]['tmp_name'];
		$uploadfile = $this->dir . ereg_replace('%', '',
			urlencode($_FILES[MC_UPLOAD_INPUT_NAME]['name']));

		if (move_uploaded_file($tmpfile, $uploadfile)) {
			if ($type = $this->is_image($uploadfile)) {
				$this->create_thumbnail($uploadfile, $type);
			}
		}
	}

	function icon_image($type, $f = '') {
		if (is_file($this->dir . $f)) {
			return '<img src="' . $this->url . $f .
				'" alt="uploaded image file" />';
		}

		return '<img src="' .
			MC_UPLOAD_ICON_URL . $type . MC_UPLOAD_ICON_SUFFIX .
			'" alt="' . $type . ' file" />';
	}

	function delete_form($f) {
		$script = MC_UPLOAD_SCRIPT_URL;
		$type = $this->type;
		$id = $this->id;
		return <<<_EOF
<form action="$script" method="post"
  onsubmit="return delete_submit(this.form, '$f');">
  <input type="hidden" name="mode" value="upload" />
  <input type="hidden" name="type" value="$type" />
  <input type="hidden" name="id" value="$id" />
  <input type="hidden" name="action" value="delete" />
  <input type="hidden" name="deletefile" value="$f" />
  <input type="submit" value="Delete" />
</form>
_EOF;
	}

	function uploaded_files() {
		global $UploadFileType;

		if (!ereg("^[0-9]*$", $this->offset)) {
			$this->offset = 0;
		}

		if ($dh = opendir($this->dir)) {
			$upload_files = array();
			while (($f = readdir($dh)) !== False) {
				if ($f == '.' || $f == '..' ||
					eregi(MC_UPLOAD_THUMBNAIL_SUFFIX, $f)) {
					continue;
				}
				$upload_files[] = $f;
			}
			closedir($dh);

			$prev_offset = $this->offset - 1;
			$next_offset = $this->offset + 1;
			$lmin = $this->offset * MC_UPLOAD_LIST_MAX;
			$lmax = $next_offset * MC_UPLOAD_LIST_MAX;
			$max = count($upload_files);

			sort($upload_files);
			$tags = array();
			$i = 0;
			for ($i = 0; $i < $max; $i++) {
				if (!($lmin <= $i && $i < $lmax)) {
					continue;
				}

				$f = $upload_files[$i];
				$iimg = $this->icon_image('default');
				foreach ($UploadFileType as $key => $val) {
					if (eregi($val, $f)) {
						$iimg = $this->icon_image($key,
							$this->create_thumbnail_filename($f));
						break;
					}
				}
				$tags[] = '<tr><td>' . $iimg .
					'</td><td><a href="' . $this->url . $f .
					'">' . $f . '</a></td><td>' .
					$this->delete_form($f) . '</td></tr>';
			}

			print implode("\n", $tags);

			$this->page_info = array($prev_offset, $this->offset,
				$lmax > $max - 1 ? -1 : $next_offset, $max);
		}

		return $this->page_info;
	}

	function delete_file($f) {
		if (is_file($this->dir . $f)) {
			unlink($this->dir . $f);
			$thumb = $this->create_thumbnail_filename($f);
			if (is_file($this->dir . $thumb)) {
				unlink($this->dir . $thumb);
			}
		}
	}

	function pagination() {
		$po = htmlspecialchars($this->page_info[0]); // previrous offset
		$co = htmlspecialchars($this->page_info[1]); // current offset
		$no = htmlspecialchars($this->page_info[2]); // next offset
		$cnt = ceil($this->page_info[3] / MC_UPLOAD_LIST_MAX);

		$link = array('<ul>');
		$link[] = $po == -1 ? '<li>&laquo;</li>' :
			'<li><a href="' . MC_UPLOAD_SCRIPT_URL .
			'?mode=upload&amp;type=' . $this->type .
			'&amp;id=' . $this->id . '&amp;offset=' .
			$po . '">&laquo;</a></li>';
		for ($i = 0; $i < $cnt; $i++) {
			$n = $i + 1;
			$num = $i == $co ? "<span>$n</span>" : $n;
			$link[] = '<li><a href="' . MC_UPLOAD_SCRIPT_URL .
				'?mode=upload&amp;type=' . $this->type .
				'&amp;id=' . $this->id . '&amp;offset=' .
				$i . '">' . $num . '</a></li>';
		}
		$link[] = $no == -1 ? '<li class="last">&raquo;</li>' :
			'<li class="last"><a href="' . MC_UPLOAD_SCRIPT_URL .
			'?mode=upload&amp;type=' . $this->type .
			'&amp;id=' . $this->id . '&amp;offset=' .
			$no . '">&raquo;</a></li>';
		$link[] = '</ul>';

		print implode("\n", $link);
	}
}

/**
 * Class of MinUploadedList.
 *
 * This class is to listing the uploaded files on the web server.
 * The list is for tinyMCE javascript editor.
 *
 * @version $Id:$
 * @access public
 * @package MinCMS
 * @author $Author:$
 */
class MinUploadedList
{
	/** The URL absolute path. @var string */	var $ap;
	/** The relate path. @var string */		var $dir;
	/** The directory handle that the relate path opened. @var resource */
							var $h;
	/** The uploaded files list for tinyMCE. @var array */
							var $js_list;

	/**
	 * The constructor
	 *
	 * invoke when an instance of this class is generated.
	 *
	 * @param string $ap the URL absolute path of directory for upload.
	 * @param string $dir the relate path of directory for upload.
	 */
	function __construct($ap, $d) {
		$this->ap = $ap;
		$this->dir = $d;
		$this->h = opendir($this->dir);
		$this->js_list = array();
	}

	function __destruct() {
		closedir($this->h);
	}

	function target($f) {
		return ($f != '.' && $f != '..' && is_file("$this->dir/$f"));
	}

	function set_listname() {
		$this->js_list[] = 'var tinyMCEList = new Array(';
	}

	function make_list() {
		$this->js_list = array();
		$this->set_listname();

		$d = array();
		while ($f = readdir($this->h)) {
			if ($this->target($f)) {
				$d[] = '  ["' . utf8_encode($f) . '", "' .
					utf8_encode("$this->ap/$f") . '"]';
			}
		}
		$this->js_list[] = implode(",\n", $d);
		$this->js_list[] = ');';
	}

	function javascript_list() {
		$this->make_list();
		return implode("\n", $this->js_list);
	}
}

/**
 * Class of MinUploadedImageList
 *
 * This class is to listing the uploaded image files on the web server.
 * The list is for tinyMCE javascript editor.
 * This class inherit MinUploadedList class.
 *
 * @version $Id:$
 * @access public
 * @package MinCMS
 * @author $Author:$
 */
class MinUploadedImageList extends MinUploadedList
{
	function target($f) {
		return (eregi("(\.jpe?g|\.gif|.png|\.bmp)$", $f) &&
			!eregi("_thumb\.", $f) && is_file("$this->dir/$f"));
	}

	function set_listname() {
		$this->js_list[] = 'var tinyMCEImageList = new Array(';
	}
}

/**
 * Class of MinUploadedLinkList
 *
 * This class is to listing the uploaded files excepting images
 * on the web server.
 * The list is for tinyMCE javascript editor.
 * This class inherit MinUploadedList class.
 *
 * @version $Id:$
 * @access public
 * @package MinCMS
 * @author $Author:$
 */
class MinUploadedLinkList extends MinUploadedList
{
	function target($f) {
		return (eregi("\.(docx?|xlsx?|pptx?|pdf|txt|zip|lzh)$", $f) &&
			is_file("$this->dir/$f"));
	}

	function set_listname() {
		$this->js_list[] = 'var tinyMCELinkList = new Array(';
	}
}
?>
