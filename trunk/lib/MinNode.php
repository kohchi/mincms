<?php
/**
 * Class of MinNode.
 *
 * This class is expression of an article.
 *
 * @version $Id:$
 * @access public
 * @package MinCMS
 * @author $Author:$
 */
class MinNode
{
	/** my id. @var int */				var $id;
	/** my table record array. @var array */	var $record;
	/** the parent node reference. @var &MinNode */	var $parent;
	/** my child's nodes. @var array */		var $children;

	/**
	 * The constructor
	 *
	 * invoke when an instance of this class is generated.
	 *
	 * @param array $r the table record array.
	 * @param &MinNode $p the parent node reference. If it is not specified,
	 * 		set to NULL that means site(root) node.
	 */
	function __construct($r, &$p = NULL) {
		$this->id = $p == NULL ? 0 : $r['id'];
		$this->record = $r;
		$this->parent = $p;
		$this->children = array();
	}

	function &search_node($id) {
		if ($this->id == $id) {
			return $this;
		}

		for ($i = 0; $i < count($this->children); $i++) {
			if ($c = $this->children[$i]->search_node($id)) {
				return $c;
			}
		}

		return NULL;
	}

	function push($r) {
		if ($this->id == $r['parent']) {
			$this->children[] = new MinNode($r, $this);
			return True;
		}

		for ($i = 0; $i < count($this->children); $i++) {
			if ($this->children[$i]->push($r)) {
				return True;
			}
		}

		return False;
	}

	function get_root_node() {
		return $this->parent == NULL ?
			$this : $this->parent->get_root_node();
	}

	function dump($tab = '') {
		print $tab . $this->id .
			" parent : " . $this->record['parent'] .
			" kind : " . $this->record['kind'] . "\n";
		for ($i = 0; $i < count($this->children); $i++) {
			$this->children[$i]->dump($tab . '  ');
		}
	}

	function set_to_array(&$a) {
		if ($this->parent != NULL) {
			$m = $this->parse_datetime('utime');
			$updatetime = mktime($m[4], $m[5], $m[6],
						$m[2], $m[3], $m[1]);
			$a[] = array_merge($this->record,
				array('updatetime' => $updatetime));
		}

		for ($i = 0; $i < count($this->children); $i++) {
			$this->children[$i]->set_to_array($a);
		}
	}

	function mylist($link, $current = array(), $tab = '') {
		$ret = array();

		if ($this->id == 0) {
//			$ret[] = $tab . '<ul>';
		} else {
			$u = $link;
			$u['article'] = $this->id;

			$curclass = ($current['site'] == $u['site'] &&
					$current['article'] == $u['article']) ?
				' class="currentarticle"' : '';

			$ret[] = $tab . '<li><a' . $curclass . ' href="' .
				generate_url($u) . '">' .
				$this->record['title'] .
				'</a>';
		}
		if (count($this->children) > 0) {
			$ret[] = $tab . '<ul>';
			for ($i = 0; $i < count($this->children); $i++) {
				$ret[] = $this->children[$i]->mylist($link,
						$current, $tab . '  ');
			}
			$ret[] = $tab . '</ul>';
		}
		if ($this->id == 0) {
//			$ret[] = $tab . '</ul>';
		} else {
			$ret[] = $tab . '</li>';
		}

		return implode("\n", $ret);
	}

	function current_nodes($id = 0) {
		if ($this->id == $id) {
			return $this->children;
		}

		for ($i = 0; $i < count($this->children); $i++) {
			$c = $this->children[$i]->current_category($id);
			if (count($c)) {
				return $c;
			}
		}

		return array();
	}

	function get_categories() {
		$ret = array();

		if ($this->record['kind'] == 'C') {
			$ret[] = $this;
		}

		for ($i = 0; $i < count($this->children); $i++) {
			if ($c = $this->children[$i]->get_categories()) {
				$ret = array_merge($ret, $c);
			}
		}

		return $ret;
	}

	function get_parent_id() {
		return ($this->parent && $this->parent->parent != NULL) ?
			$this->parent->record['id'] : 0;
	}

	function parse_datetime($key) {
		if (ereg("^([0-9]{4})-([0-9]{2})-([0-9]{2}) ([0-9]{2}):([0-9]{2}):([0-9]{2})$", $this->record[$key], $m)) {
			return $m;
		}

		return array();
	}

	function format_date($key) {
		$d = $this->parse_datetime($key);

		if (count($d)) {
			return "$d[1]-$d[2]-$d[3]";
		}

		return "";
	}

	function format_hms($key) {
		$d = $this->parse_datetime($key);

		if (count($d)) {
			return "$d[4]:$d[5]:$d[6]";
		}

		return "";
	}
}
?>
