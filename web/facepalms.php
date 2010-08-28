<?php
require 'boot.php';
require 'i18n/' . $lang . '.php';

$order = 'facepalm_log.fecha DESC';
$facepalms = Facepalm::fetchLogs(isset($_GET['user_id']) ? $_GET['user_id'] : NULL, $order);
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
				<td><?php echo date('Y-m-d H:i:s', $facepalm->fecha); ?></td>
				<td><a href="facepalms.php?user_id=<?php echo $facepalm->user_id; ?> "><?php echo $facepalm->nombre; ?>:</td>
				<td><?php echo $facepalm->reason; ?></td>
			</tr>
		<?php } ?>
	</table>
<?php } ?>
</body>
</html>
