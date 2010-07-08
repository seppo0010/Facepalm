<?php
$id = NULL;
error_reporting(E_NONE);
$filename='db.sql';
$is_new_database = !is_file($filename);
//$database = new SQLiteDatabase($filename);
$database = new PDO('sqlite:' . $filename);
if ($is_new_database)
{
        $database->query('CREATE TABLE facepalm ( id INTEGER PRIMARY KEY, nombre TEXT, fecha NUMERIC )');
}
if (count($_POST) > 0 || isset($_GET['borrar']) || isset($_GET['touch']))
{
	if ($_GET['touch'] > 0)
	{
		$database->query('UPDATE facepalm SET fecha = ' . time() . ' WHERe id = ' . (int)$_GET['touch']);
		$query = $database->query('SELECT nombre FROM facepalm WHERE id =' . (int)$_GET['touch']);
		$usuario = $query->fetchObject();
		$_COOKIE['id-user'] = (int)$_GET['touch'].'-'.$usuario->nombre;
		setcookie('id-user', $_COOKIE['id-user'], time() + 30 * 24 * 60 * 60);
	}

	if ($_GET['borrar'] > 0)
	{
		$database->query('DELETE FROM facepalm WHERe id = ' . (int)$_GET['borrar']);
		setcookie('id-user', '', time()-1);
	}
	if ($_POST['nombre_nuevo'])
	{
		$database->query('INSERT INTO facepalm (nombre, fecha) VALUES (' . $database->quote($_POST['nombre_nuevo']) . ', ' . time() . ')');
		$id = $database->lastInsertId();
		$_COOKIE['id-user'] = $id.'-'.$_POST['nombre_nuevo'];
		setcookie('id-user', $_COOKIE['id-user'], time() + 30 * 24 * 60 * 60);
	}

//	header('location:index.php');die;
}


$query = $database->query('SELECT * FROM facepalm ORDER BY nombre ASC, id ASC');
?>
<style type="text/css">
tr.self td { font-size: 20px; }
</style>
Una aplicaci&oacute;n que trackea cu&aacute;nto tiempo pasamos sin pensar "qu&eacute; boludo que soy" al son de una palmada en la frente.<br />Cada vez que uno se manda una boludez, debe venir a clickear "facepalm" para resetear el contador.
<?php if (isset($_COOKIE['id-user'])) { ?>
<?php list($id, $user) = explode('-', $_COOKIE['id-user']); ?>
<br /><br />
Bienvenido <?php echo $user; ?><br />
<a href="index.php?touch=<?php echo $id; ?>" style="text-align:center;border:2px solid #999; line-height:24px;text-decoration:none;display:block;background:#000; color:#fff; width:100px; height: 24px;">Facepalm</a><br />
<?php } ?>
<br /><br />
<?php
echo '<form action="index.php" method="post"><table><tr><th>Nombre</th><th>Fecha</th></tr>';
while ($usuario = $query->fetchObject())
{
        echo '<tr' . ($usuario->id != $id ? '' : ' class="self"') . '>
		<td>' , htmlentities($usuario->nombre, ENT_QUOTES) , '</td>
		<td'. ($usuario->fecha + 60 * 60 * 24 < time() ? ' style="color:#f00"' : '') . '>' .date('Y-m-d H:i:s', $usuario->fecha) .' </td>
		<td><a href="index.php?touch=' . $usuario->id . '" ' . ($usuario->id == $id ? '' : 'onclick="if(!confirm(\'tas seguro que queres facepalmear a ' . htmlentities($usuario->nombre, ENT_QUOTES) . '\')) return false"') . '>Facepalm!</a></td>
		<td><a href="index.php?borrar=' . $usuario->id . '" onclick="if(!confirm(\'tas seguro, flakito?\')) return false">Borrar</a></td>
		<td><input type="hidden" name="old_nombre['. $usuario->id .']" value="' , htmlentities($usuario->nombre, ENT_QUOTES) , '" /><input type="hidden" name="old_gustos['.$usuario->id .']" value="' , htmlentities($usuario->gustos, ENT_QUOTES) , '" /><input type="hidden" name="old_heladeria['.$usuario->id .']" value="' , htmlentities($usuario->heladeria, ENT_QUOTES) , '" /></td>
		<td width="60%"><div style="background:blue; width: '. round((time() - $usuario->fecha) / 60 / 60 / 24 * 100 / 30,2) . '%; height:10px;"</td>
	</tr>';
}
?>
<tr>
<td><input type="text" name="nombre_nuevo" /></td>
</tr>
<tr><td colspan="2"><input type="submit" value="Guardar" /></td></tr>
<?php
echo '</table></form>';
?>

<img src="facepalm1.jpg" /><br /><br />
<object width="480" height="385"><param name="movie" value="http://www.youtube.com/v/wjLgekyOZA0&hl=en_US&fs=1&"></param><param name="allowFullScreen" value="true"></param><param name="allowscriptaccess" value="always"></param><embed src="http://www.youtube.com/v/wjLgekyOZA0&hl=en_US&fs=1&" type="application/x-shockwave-flash" allowscriptaccess="always" allowfullscreen="true" width="480" height="385"></embed></object>
