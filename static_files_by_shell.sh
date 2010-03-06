#!/opt/php5/bin/php
<?php
/**
 * generate static files on MinCMS
 *
 * generate static files by shell command line.
 *
 * @version $Id:$
 * @package MinCMS
 * @author $Author:$
 */

include('config.php');
include('lib/init.php');

static_files_by_shell();
?>
