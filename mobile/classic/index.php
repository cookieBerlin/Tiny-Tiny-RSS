<?php
	error_reporting(E_ERROR | E_WARNING | E_PARSE);

	define('MOBILE_VERSION', true);

	require_once "../../config.php";
	require_once "functions.php";
	require_once "../../functions.php";

	require_once "../../sessions.php";

	require_once "../../version.php";
	require_once "../../db-prefs.php";

	$link = db_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);

	init_connection($link);

	login_sequence($link, 2);

	/* perform various redirect-needing subops */

	$subop = db_escape_string($_GET["subop"]);
	$go = $_GET["go"];

	if ($subop == "tc" && !$go) {

		$cat_id = db_escape_string($_GET["id"]);
		toggle_collapse_cat($link, $cat_id);

		header("Location: index.php");
		return;
	}

	$ts_id = db_escape_string($_GET["ts"]);

	if ($go == "vf" && $ts_id) {

		toggleMarked($link, $ts_id);

		$query_string = preg_replace("/&ts=[0-9]*/", "", $_SERVER["QUERY_STRING"]);
		header("Location: index.php?$query_string");
		return;
	}

	$tp_id = db_escape_string($_GET["tp"]);

	if ($go == "vf" && $tp_id) {

		togglePublished($link, $tp_id);

		$query_string = preg_replace("/&tp=[0-9]*/", "", $_SERVER["QUERY_STRING"]);
		header("Location: index.php?$query_string");
		return;
	}

	$sop = db_escape_string($_GET["sop"]);

	if ($sop) {
		if ($go == "view") {
			$a_id = db_escape_string($_GET["id"]);
		} elseif ($go == "vf") {
			$a_id = db_escape_string($_GET["aid"]);
		}

		if ($a_id) {

			if ($sop == "tp") {
				togglePublished($link, $a_id);
			}

			if ($sop == "ts") {
				toggleMarked($link, $a_id);
			}

			if ($sop == "mu") {
				markUnread($link, $a_id);
			}

			$query_string = str_replace("&sop=$sop", "", $_SERVER["QUERY_STRING"]);
			header("Location: index.php?$query_string");
		}
	}

?>
<html>
<head>
	<title>Tiny Tiny RSS</title>
	<link rel="stylesheet" type="text/css" href="mobile.css">
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<script type="text/javascript" src="mobile.js"></script>
	<?php print_user_stylesheet($link) ?>
</head>
<body id="ttrssMobile">

<div id="content">
<?php
	if (!$go) {
		render_feeds_list($link);
	} else if ($go == "vf") {
		render_headlines($link);
	} else if ($go == "view") {
		render_article($link);
	} else if ($go == "sform") {
		render_search_form($link, $_GET["aid"], $_GET["ic"]);
	} else {
		print __("Internal error: Function not implemented");
	}

?>
</div>

</body>
</html>
