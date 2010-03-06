<?php
/**
 * Class of MinPage.
 *
 * This class is to accessing pages.
 *
 * @version $Id:$
 * @access public
 * @package MinCMS
 * @author $Author:$
 */
class MinPage
{
	/** instance of MinDB. @var MinDB */			var $db;
	/** an array of a page table line. @var array */	var $pages;
	/** an array of columns per a line. @var array */	var $line;

	/**
	 * The constructor
	 *
	 * invoke when an instance of this class is generated.
	 *
	 * @param MinDB $db the MinDB instance
	 */
	function __construct($db) {
		$this->db = $db;

		$this->read_pages();
		$this->line = array();
	}

	function read_pages() {
		$query = 'SELECT * FROM ' . MC_DB_TABLE_PAGE;
		$this->pages = $this->db->query($query);
	}

	function set_page($id) {
		$col = ereg("^[0-9]+$", $id) ? 'id' : 'path';
		foreach ($this->pages as $l) {
			if ($l[$col] == $id) {
				$this->line = $l;
				break;
			}
		}
	}

	function does_exist() {
		return count($this->line) == 0 ? False : True;
	}

	function get_page_column($col) {
		return $this->does_exist() ? $this->line[$col] : '';
	}

	function get_page_path() {
		return $this->get_page_column('path');
	}

	function get_page_title() {
		return $this->get_page_column('title');
	}

	function get_page_description() {
		return $this->get_page_column('description');
	}

	function exist_path($id, $path) {
		foreach ($this->pages as $l) {
			if ($l['id'] == $id && $l['path'] == $path) {
				return True;
			}
		}

		return False;
	}
}
?>
