<?php
/**
 * MinCMS
 *
 * MinCMS is the Minimum Contents Management System.
 *
 * @version $Id:$
 * @package MinCMS
 * @author $Author:$
 */

include('../config.php');
include('../lib/init.php');

$mc = new MinCMSAdmin();
$mc->parse_arguments();
$mc->view();
?>
