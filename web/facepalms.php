<?php
require 'boot.php';

if (isset($_REQUEST['vote'])) {
	Facepalm::vote($_REQUEST['id'], $_REQUEST['vote']=='up');
	setcookie('voted_' . $_REQUEST['id'], 1);
	header('location:facepalms.php?user_id=' . $_REQUEST['user_id'] . '&order=' . $_GET['order']);
	exit;
}

require 'i18n/' . $lang . '.php';

if ($_REQUEST['order'] == 'popular') $order = 'facepalm_log.vote_up - facepalm_log.vote_down DESC';
else $order = 'facepalm_log.fecha DESC';
$facepalms = Facepalm::fetchLogs(!empty($_GET['user_id']) ? $_GET['user_id'] : NULL, $order);
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
<title>Facepalm!</title>
</head>
<body>
<a href="index.php"><?php echo htmlentities($i18n['back'], ENT_QUOTES, 'UTF-8'); ?></a>
<?php if (count($facepalms) > 0) { ?>
	<table>
		<?php foreach ($facepalms as $facepalm) { ?>
			<tr>
				<?php if (empty($_COOKIE['voted_'. $facepalm->id])) { ?>
					<td><a href="facepalms.php?order=<?php echo htmlentities($_GET['order']); ?>&user_id=<?php echo htmlentities($_GET['user_id']); ?>&vote=up&id=<?php echo $facepalm->id; ?>">+</a></td>
					<td><a href="facepalms.php?order=<?php echo htmlentities($_GET['order']); ?>&user_id=<?php echo htmlentities($_GET['user_id']); ?>&vote=down&id=<?php echo $facepalm->id; ?>">-</a></td>
				<?php } else { ?>
					<td></td>
					<td></td>
				<?php } ?>
				<td>+<?php echo $facepalm->vote_up; ?> -<?php echo $facepalm->vote_down; ?></td>
				<td><?php echo date('Y-m-d H:i:s', $facepalm->fecha); ?></td>
				<td><a href="facepalms.php?user_id=<?php echo $facepalm->user_id; ?> "><?php echo $facepalm->nombre; ?>:</a></td>
				<td><?php echo $facepalm->reason; ?></td>
			</tr>
		<?php } ?>
	</table>
<?php } ?>
</body>
</html>
