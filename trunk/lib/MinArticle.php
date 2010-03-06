<?php
/**
 * Class of MinArticle.
 *
 * This class is to accessing articles.
 *
 * @version $Id:$
 * @access public
 * @package MinCMS
 * @author $Author:$
 */
class MinArticle extends MinCore
{
	/** instance of MinDB. @var MinDB */	var $db;
	/** an array contains instances of MinNode. @var array */
						var $node;
	/** begin tag of site. @var string */	var $begin_site_tag;
	/** end tag of site. @var string */	var $end_site_tag;

	/**
	 * The constructor
	 *
	 * invoke when an instance of this class is generated.
	 *
	 * @param MinDB $db the MinDB instance
	 */
	function __construct($db) {
		$this->db = $db;
		$this->begin_site_tag = '<h3 class="site">';
		$this->end_site_tag = '</h3>';
	}

	function set_node($rows) {
		foreach ($rows as $row) {
			$article_table = MC_DB_TABLE_ARTICLE_PREFIX . $row['id'];
			$n = new MinNode($row);

			// select categories
			$aquery = 'SELECT * FROM ' . $article_table .
				' WHERE kind="C" ORDER BY parent,id';
			$arows = $this->db->query($aquery);
			foreach ($arows as $arow) {
				$n->push($arow);
			}

			// select articles and urls
			$aquery = 'SELECT * FROM ' . $article_table .
				' WHERE kind!="C" ORDER BY parent,id';
			$arows = $this->db->query($aquery);
			foreach ($arows as $arow) {
				$n->push($arow);
			}

			$this->node[$row['id']] = $n;
		}
	}

	function read_node() {
		$this->node = array();
		$query = 'SELECT * FROM ' . MC_DB_TABLE_SITE;
		$rows = $this->db->query($query);

		$this->set_node($rows);
	}

	function search_node($sid, $aid) {
		foreach ($this->node as $id => $n) {
			if ($sid != $id) {
				continue;
			}

			return $n->search_node($aid);
		}

		return NULL;
	}

	function set_article($sid, $aid) {
		$status_ok = MC_ADMIN_AUTH_CREATE | MC_ADMIN_AUTH_INSPECT |
				MC_ADMIN_AUTH_PUBLISH;
		$query = 'SELECT * FROM ' . MC_DB_TABLE_SITE .
			' WHERE id="' . $sid . '"';
		$site = $this->db->query($query);

		$article_table = MC_DB_TABLE_ARTICLE_PREFIX . $sid;
		$query = 'SELECT * FROM ' . $article_table .
				' WHERE id="' . $aid . '" AND (status & ' .
				$status_ok . ')=' . $status_ok . ' AND' .
				' ((btime IS NULL OR btime <= now())' .
				' AND (etime IS NULL OR now() <= etime))';
		$row = $this->db->query($query);
		$this->node = array();
		if (count($row) == 1) {
			$n = new MinNode($row[0], $site[0]);

			if ($row[0]['kind'] == 'C') {
				$query = 'SELECT * FROM ' . $article_table .
					' WHERE parent="' . $row[0]['id'] .
					'" AND (status & ' . $status_ok . ')=' .
					$status_ok . ' AND' .
					' ((btime IS NULL OR btime <= now())' .
					' AND (etime IS NULL OR now() <= etime))';
				$children = $this->db->query($query);
				foreach ($children as $child) {
					$n->push($child);
				}
			}

			$this->node[$sid] = $n;
		}
	}

	function set_site($sid) {
		$status_ok = MC_ADMIN_AUTH_CREATE | MC_ADMIN_AUTH_INSPECT |
				MC_ADMIN_AUTH_PUBLISH;
		$cond = (ereg("^[0-9]+$", $sid) ? 'id="' : 'path="') .
			$sid . '"';

		$query = 'SELECT * FROM ' . MC_DB_TABLE_SITE . " WHERE $cond";
		$site = $this->db->query($query);

		$this->node = array();
		if (count($site) != 0) {
			$n = new MinNode($site[0]);

			$article_table = MC_DB_TABLE_ARTICLE_PREFIX . $site[0]['id'];
			$query = 'SELECT * FROM ' . $article_table .
				' WHERE parent="0" AND (status & ' .
				$status_ok . ')=' . $status_ok . ' AND' .
				' ((btime IS NULL OR btime <= now())' .
				' AND (etime IS NULL OR now() <= etime))';
			$rows = $this->db->query($query);

			foreach ($rows as $row) {
				$n->push($row);
			}

			$this->node[$sid] = $n;
		}
	}

	function get_article($sid, $aid) {
		$status_ok = MC_ADMIN_AUTH_CREATE | MC_ADMIN_AUTH_INSPECT |
				MC_ADMIN_AUTH_PUBLISH;
		$query = 'SELECT * FROM ' . MC_DB_TABLE_SITE .
			' WHERE id="' . $sid . '"';
		$site = $this->db->query($query);

		$article_table = MC_DB_TABLE_ARTICLE_PREFIX . $sid;
		$query = 'SELECT * FROM ' . $article_table .
				' WHERE id="' . $aid . '" AND (status & ' .
				$status_ok . ')=' . $status_ok . ' AND' .
				' ((btime IS NULL OR btime <= now())' .
				' AND (etime IS NULL OR now() <= etime))';
		$row = $this->db->query($query);
		if (count($row) == 1) {
			return new MinNode($row[0], $site[0]);
		}

		return NULL;
	}

	function get_site($sid) {
		$this->set_site($sid);
		if ($this->node[$sid]) {
			return $this->node[$sid];
		}

		return NULL;
	}

	function get_categories() {
		$ret = array();
		foreach ($this->node as $id => $n) {
			$ret[] = array($n->record, NULL);
			foreach ($n->get_categories() as $nn) {
				$ret[] = array($n->record, $nn);
			}
		}

		return $ret;
	}

	function is_kind($sid, $kind) {
		return (isset($this->node[$sid]) &&
				$this->node[$sid]->record['kind'] == $kind) ?
					True : False;
	}

	function is_kind_category($sid) {
		return $this->is_kind($sid, 'C');
	}

	function is_kind_article($sid) {
		return $this->is_kind($sid, 'A');
	}

	function is_kind_url($sid) {
		return $this->is_kind($sid, 'U');
	}

	function htmltree($sid = 0, $aid = 0) {
		$ret = array();

		foreach ($this->node as $id => $n) {
			$ret[] = $this->begin_site_tag .
					$n->record['name'] .
					$this->end_site_tag;
			$ret[] = $n->mylist(array('site' => $n->record['id']),
				array('site' => $sid, 'article' => $aid));
		}

		return implode("\n", $ret);
	}

	function pick_up($key, $val) {
		$ret = array();

		switch ($key) {
		case 'label' :
			$ret = $this->pick_up_with_label($val);
			break;
		case 'search' :
			$ret = $this->pick_up_by_keyword($val);
			break;
		case 'site' :
			$ret = $this->pick_up_at_site($val);
			break;
		}

		return $ret;
	}

	function add_siteid($sid, $a) {
		return array_merge(array('sid' => $sid), $a);
	}

	function parse_datetime($time) {
		if (ereg("^([0-9]{4})-([0-9]{2})-([0-9]{2}) ([0-9]{2}):([0-9]{2}):([0-9]{2})$", $time, $m)) {
			return $m;
		}

		return array();
	}

	function _sort_articles($a, $b) {
		if ($a['btime'] && $b['btime']) {
			$am = $this->parse_datetime($a['btime']);
			$bm = $this->parse_datetime($b['btime']);
		} else {
			$am = $this->parse_datetime($a['utime']);
			$bm = $this->parse_datetime($b['utime']);
		}

		$a_time = mktime($am[4], $am[5], $am[6],
					$am[2], $am[3], $am[1]);
		$b_time = mktime($bm[4], $bm[5], $bm[6],
					$bm[2], $bm[3], $bm[1]);

		if ($a_time == $b_time) {
			return 0;
		}

		return $a_time > $b_time ? -1 : 1;
	}

	function pick_up_with_label($val) {
		$ret = array();
		$query = 'SELECT id FROM ' . MC_DB_TABLE_SITE;
		$rows = $this->db->query($query);
		foreach ($rows as $row) {
			$status_ok = MC_ADMIN_AUTH_CREATE |
					MC_ADMIN_AUTH_INSPECT |
					MC_ADMIN_AUTH_PUBLISH;
			$article_table = MC_DB_TABLE_ARTICLE_PREFIX . $row['id'];
			$query = 'SELECT * FROM ' . $article_table .
				' WHERE label="' . $val . '" AND (status & ' .
				$status_ok . ')=' . $status_ok . ' AND' .
				' ((btime IS NULL OR btime <= now())' .
				' AND (etime IS NULL OR now() <= etime))';
			$results = $this->db->query($query);
			if (count($results) > 0) {
				$ret = array_merge($ret,
					array_map(array($this, 'add_siteid'),
					array_fill(0, count($results), $row['id']),
					$results));
			}
		}

		usort($ret, array($this, '_sort_articles'));
		return $ret;
	}

	function pick_up_by_keyword($val) {
		$ret = array();
		$query = 'SELECT id FROM ' . MC_DB_TABLE_SITE;
		$rows = $this->db->query($query);
		foreach ($rows as $row) {
			$status_ok = MC_ADMIN_AUTH_CREATE |
					MC_ADMIN_AUTH_INSPECT |
					MC_ADMIN_AUTH_PUBLISH;
			$article_table = MC_DB_TABLE_ARTICLE_PREFIX . $row['id'];
			$query = 'SELECT * FROM ' . $article_table .
				' WHERE title like "%' . $val .
				'%" OR description like "%' . $val .
				'%" AND (status & ' . $status_ok . ')=' .
				$status_ok . ' AND' .
				' ((btime IS NULL OR btime <= now())' .
				' AND (etime IS NULL OR now() <= etime))';
			$results = $this->db->query($query);
			if (count($results) > 0) {
				$ret = array_merge($ret,
					array_map(array($this, 'add_siteid'),
					array_fill(0, count($results), $row['id']),
					$results));
			}
		}

		usort($ret, array($this, '_sort_articles'));
		return $ret;
	}

	function pick_up_at_site($val) {
		$status_ok = MC_ADMIN_AUTH_CREATE | MC_ADMIN_AUTH_INSPECT |
				MC_ADMIN_AUTH_PUBLISH;
		$article_table = MC_DB_TABLE_ARTICLE_PREFIX . $val;
		$query = 'SELECT * FROM ' . $article_table .
				' WHERE (status & ' .
				$status_ok . ')=' . $status_ok . ' AND' .
				' ((btime IS NULL OR btime <= now())' .
				' AND (etime IS NULL OR now() <= etime))';
		$results = $this->db->query($query);
		if (count($results) > 0) {
			$ret = array_map(array($this, 'add_siteid'),
				array_fill(0, count($results), $val), $results);

			usort($ret, array($this, '_sort_articles'));
			return $ret;
		}

		return array();
	}

	function generate_rss($v, $key, $val) {
		$ret = array();
		$ret['RSS_URL'] = $this->generate_url(MC_URL,
					array('mode' => 'rss',
						'v' => $v,
						'key' => $key,
						'val' => $val));
		if ($key == 'site') {
			$cond = (ereg("^[0-9]+$", $val) ? 'id="' : 'path="') .
				$val . '"';
			$query = 'SELECT * FROM ' . MC_DB_TABLE_SITE .
							" WHERE $cond";
			$site = $this->db->query($query);

			$ret['RSS_TITLE'] = $site[0]['name'];
			$ret['RSS_DESCRIPTION'] = $site[0]['contact'];
			$sitename = $site[0]['name'];
		} else {
			$ret['RSS_TITLE'] = _M($val . ' rss title');
			$ret['RSS_DESCRIPTION'] = _M($val . ' rss description');
			$sitename = get_title();
		}

		$rdf_li = array();
		$items = array();
		foreach ($this->pick_up($key, $val) as $a) {
			$url = get_article_path($a['sid'], $a['id']);
			$title = $a['title'];
			$description = $a['description'];
			$update = $v == 1 ? iso8601($a['utime']) :
						rfc822($a['utime']);
			if (!$ret['RSS_UPDATE']) {
				$ret['RSS_UPDATE'] = $update;
			}

			$rdf_li[] = '<rdf:li rdf:resource="' . $url . '" />';
			if ($v == 1) {
				$items[] = <<<_EOF
	<item rdf:about="$url">
		<title>$title</title>
		<link>$url</link>
		<description><![CDATA[$description]]></description>
		<dc:creator>$sitename</dc:creator>
		<dc:date>$update</dc:date>
        </item>
_EOF;
			} else {
				$items[] = <<<_EOF
	<item>
		<title>$title</title>
		<link>$url</link>
		<description><![CDATA[$description]]></description>
		<pubDate>$update<pubDate>
        </item>
_EOF;
			}
		}

		$ret['RDF_LI'] = implode("\n", $rdf_li);
		$ret['ITEMS'] = implode("\n", $items);

		return $ret;
	}
}

/**
 * Class of MinArticleAdmin.
 *
 * This class is for administration tool when you access articles.
 *
 * @version $Id:$
 * @access public
 * @package MinCMS
 * @author $Author:$
 */
class MinArticleAdmin extends MinArticle
{
	/** instance of MinLogin. @var MinLogin */	var $login;

	/**
	 * The constructor
	 *
	 * invoke when an instance of this class is generated.
	 *
	 * @param MinDB $db the MinDB instance
	 * @param MinLogin $login the MinLogin instance
	 */
	function __construct($db, $login) {
		$this->login = $login;
		parent::__construct($db);

		$this->read_node();
	}

	function read_node() {
		$this->node = array();
		$query = 'SELECT * FROM ' . MC_DB_TABLE_SITE;
		if (!$this->login->is_admin()) {
			$query .= ' LEFT JOIN ' . MC_DB_TABLE_SITEUSER .
				' ON ' . MC_DB_TABLE_SITE . '.id=' .
				MC_DB_TABLE_SITEUSER . '.siteid WHERE ' .
				MC_DB_TABLE_SITEUSER . '.userid="' .
				$this->login->id . '"';
		}
		$rows = $this->db->query($query);

		$this->set_node($rows);
	}

	function htmltree($sid = 0, $aid = 0) {
		$ret = array();

		foreach ($this->node as $id => $n) {
			$ret[] = $this->begin_site_tag .
					$n->record['name'] .
					$this->end_site_tag;
			$ret[] = $n->mylist(array('mode' => 'editarticle',
					'site' => $n->record['id']),
				array('site' => $sid, 'article' => $aid));
		}

		return implode("\n", $ret);
	}

	function get_new_articles() {
		$ret = $this->get_article_list();
		usort($ret, array($this, '_newer_articles'));

		return array_slice($ret, 0, MC_ADMIN_NEW_ARTICLE_MAX);
	}

	function _newer_articles($a, $b) {
		if ($a['updatetime'] == $b['updatetime']) {
			return 0;
		}

		return $a['updatetime'] > $b['updatetime'] ? -1 : 1;
	}

	function get_article_list() {
		$ret = array();

		foreach ($this->node as $id => $n) {
			$site = array('siteid' => $id,
				'sitename' => $n->record['name']);
			$articles = array();
			$n->set_to_array($articles);

			foreach ($articles as $a) {
				$ret[] = array_merge($site, $a);
			}
		}

		return $ret;
	}
}
?>
