<?php
/**
 * Class of MinCalendar.
 *
 * This class is to display calendar to the web server.
 *
 * @version $Id:$
 * @access public
 * @package MinCMS
 * @author $Author:$
 */
class MinCalendar extends MinCore
{
	/** instance of MinDB. @var MinDB */		var $db;
	/** the top URL of upload files. @var string */	var $url;
	/** the http query options. @var array */	var $opts;
	/** the week name array. @var array */		var $week_name;
	/** total second from UNIX time til today . @var int */	var $today;
	/** current second from UNIX time. @var int */	var $current;
	/** last day of each month. @var array */	var $mdays;
	/** previous year value. @var int */		var $prev_year;
	/** previous month value. @var int */		var $prev_month;
	/** next year value. @var int */		var $next_year;
	/** next month value. @var int */		var $next_month;

	/**
	 * The constructor
	 *
	 * invoke when an instance of this class is generated.
	 *
	 * @param MinDB $db the MinDB instance
	 * @param array $week_name the week name array.
	 * @param int $y year value.
	 * @param int $m month value.
	 */
	function __construct($db, $week_name = array(), $y = 0, $m = 0) {
		$this->db = $db;
		$this->url = $this->script_url();
		$this->opts = array();
		foreach (array_merge($_GET, $_POST) as $key => $val) {
			$this->opts[$this->sanitize($key)] = $this->sanitize($val);
		}
		$this->week_name = count($week_name) ? $week_name :
			array("Sunday" => "S",
				"Monday" => "M",
				"Tuesday" => "T",
				"Wednesday" => "W",
				"Thursday" => "T",
				"Friday" => "F",
				"Saturday" => "S");
		$this->today = getdate();
		$this->current = ($y == 0 && $m == 0) ?
			getdate(mktime(0, 0, 0,
				$this->today["mon"], 1, $this->today["year"])) :
			getdate(mktime(0, 0, 0, $m, 1, $y));

		$this->leap_year();
		$this->next_prev();
	}

	function leap_year() {
		$this->mdays =
			array(31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31);
		$this->mdays[1] =
			$this->current["year"] % 4 ? $this->mdays[1] : 29;
		$this->mdays[1] =
			$this->current["year"] % 100 ? $this->mdays[1] : 28;
		$this->mdays[1] =
			$this->current["year"] % 400 ? $this->mdays[1] : 29;
	}

	function next_prev() {
		$cy = $this->current["year"];
		$cm = $this->current["mon"];
		$this->prev_year = $cm == 1 ? $cy - 1 : $cy;
		$this->prev_month = $cm == 1 ? 12 : $cm - 1;
		$this->next_year = $cm == 12 ? $cy + 1 : $cy;
		$this->next_month = $cm == 12 ? 1 : $cm + 1;
	}

	function header($month = -1) {
		$this->opts['cal'] = sprintf("%04d-%02d",
				$this->prev_year, $this->prev_month);
		$prev = $this->generate_url($this->url, $this->opts);

		$this->opts['cal'] = sprintf("%04d-%02d",
				$this->next_year, $this->next_month);
		$next = $this->generate_url($this->url, $this->opts);

		$cur = $this->current["month"] . "&nbsp;" . $this->current["year"];

		$ret = $month == -1 ? "<caption>$cur</caption>\n" : <<<_EOF
<caption><a href="$prev"
  title="previous month" class="nav">&laquo;</a> $cur <a href="$next"
  title="next month" class="nav">&raquo;</a></caption>

_EOF;

 		$ret .= "<thead><tr>\n";
		foreach ($this->week_name as $key => $val) {
			$ret .= <<<_EOF
	<th scope="col" abbr="$key" title="$key">$val</th>

_EOF;
		}
 		$ret .= "</tr></thead>\n";

		return $ret;
	}

	function get_calendar_event($eventdata) {
		$ret = array();
		foreach ($eventdata as $a) {
			$ret[] = new MinCalendarEvent($a['sid'], $a);
		}

		return $ret;
	}

	function trtd($eventdata) {
		$current_year = $this->current["year"];
		$current_month = $this->current["mon"];
		$ret = '';
		$tds = array();
		$event = $this->get_calendar_event($eventdata);

		for ($td = 0; $td < $this->current["wday"]; $td++) {
			$tds[] = '<td>&nbsp;</td>';
		}
		for ($i = 1; $i <= $this->mdays[$current_month - 1]; $i++, $td++) {
			$desc = sprintf("%d", $i);
			foreach ($event as $ev) {
				if ($ev->is_current(
					$current_year, $current_month, $i)) {
					$opts = array(
						'site' => $ev->sid,
						'article' => $ev->article['id']
						);
					$desc = '<a href="' . $this->generate_url($this->url, $opts) . '">' . $desc . '</a>';
					break;
				}
			}

			$class = '';
			if ($this->today["year"] == $current_year &&
				$this->today["mon"] == $current_month &&
				$this->today["mday"] == $i) {
				$class = ' class="today"';
			} else if ($td % 7 == 6) {
				$class = ' class="saturday"';
			} else if ($td % 7 == 5) {
				$class = ' class="friday"';
			} else if ($td % 7 == 4) {
				$class = ' class="thursday"';
			} else if ($td % 7 == 3) {
				$class = ' class="wednesday"';
			} else if ($td % 7 == 2) {
				$class = ' class="tuesday"';
			} else if ($td % 7 == 1) {
				$class = ' class="monday"';
			} else if ($td % 7 == 0) {
				$class = ' class="sunday"';
			}
			$tds[] = "<td$class>$desc</td>";
		}
		for (; $td % 7; $td++) {
			$tds[] = '<td>&nbsp;</td>';
		}

		for ($i = 0; $i < count($tds); $i+=7) {
			$ret .= "<tr>\n";
			$ret .= "\t$tds[$i]\n";
			$ret .= "\t${tds[$i+1]}\n";
			$ret .= "\t${tds[$i+2]}\n";
			$ret .= "\t${tds[$i+3]}\n";
			$ret .= "\t${tds[$i+4]}\n";
			$ret .= "\t${tds[$i+5]}\n";
			$ret .= "\t${tds[$i+6]}\n";
			$ret .= "</tr>\n";
		}

		return $ret;
	}

	function month($eventdata, $year = 0, $month = 0,
			$id = "mincalendar", $sum = "This month's calendar") {

		if ($year != 0 && $month != 0) {
			$this->current =
				getdate(mktime(0, 0, 0, $month, 1, $year));
			$this->leap_year();
			$this->next_prev();
		}
		$ret = <<<_EOF
<table id="$id" cellspacing="0" cellpadding="0" summary="$sum">

_EOF;
		$ret .= $this->header($month);
		$ret .= '<tbody>' . $this->trtd($eventdata) . '</tbody>';
		$ret .= "</table>\n";

		return $ret;
	}

	function year($eventdata, $col, $year) {
		$y = $year == 0 ? $this->today["year"] : $year;

		$this->opts['cal'] = sprintf("%04d", ($y - 1));
		$prev = $this->generate_url($this->url, $this->opts);
		$this->opts['cal'] = sprintf("%04d", ($y + 1));
		$next = $this->generate_url($this->url, $this->opts);

		$cur = $y;

		$ret = "<div id=\"ecyear\">\n";
		$ret .= <<<_EOF
  <h1><a href="$prev" title="previous year">&laquo;</a> $cur <a href="$next" title="next year">&raquo;</a></h1>

_EOF;
		for ($i = 1; $i <= 12; $i++) {
			if ($i % $col == 1) {
				$ret .= "  <div class=\"ecline\">\n";
			}

			$divid = sprintf("mc%02d", $i);
			$id = sprintf("mincalendar%02d", $i);
			$sum = sprintf("%04d/02d calendar", $y, $i);

			$ret .= "    <div id=\"$divid\">\n";
			$ret .= $this->month($eventdata, $y, $i, $id, $sum);
			$ret .= "    </div>\n";

			if ($i % $col == 0) {
				$ret .= "  </div>\n";
			}
		}
		$ret .= "</div>\n";

		return $ret;
	}
}

/**
 * Class of MinCalendarEvent.
 *
 * This class is due to MinCalendar.
 *
 * @version $Id:$
 * @access public
 * @package MinCMS
 * @author $Author:$
 */
class MinCalendarEvent
{
	/** The site ID. @var int */		var $sid;
	/** The article array. @var array */	var $article;
	/** The year value. @var int */		var $year;
	/** The month value. @var int */	var $month;
	/** The day value. @var int */		var $day;

	/**
	 * The constructor
	 *
	 * invoke when an instance of this class is generated.
	 *
	 * @param int $sid a site ID.
	 * @param array $article an array of article that got from DB.
	 */
	function __construct($sid, $article) {
		$this->sid = $sid;
		$this->article = $article;
		$this->parse();
	}

	function parse() {
		if ($this->is_set_date('btime')) {
			return True;
		} else if ($this->is_set_date('etime')) {
			return True;
		} else if ($this->is_set_date('utime')) {
			return True;
		}

		return False;
	}

	function is_set_date($col) {
		if (ereg('^([0-9]{4})-([0-9]{2})-([0-9]{2}) ([0-9]{2}):([0-9]{2}):([0-9]{2})$', $this->article[$col], $m)) {
			$this->year = $m[1];
			$this->month = $m[2];
			$this->day = $m[3];
			return True;
		}

		return False;
	}

	function is_current($y, $m, $d) {
		return ($this->year == $y &&
			$this->month == $m &&
			$this->day == $d) ? True : False;
	}
}
?>
