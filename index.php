<?php
require 'boot.php';

if (count($_POST) > 0 || isset($_GET['borrar']) || isset($_GET['touch']))
{
	if ($_GET['touch'] > 0)
	{
		$facepalm = new Facepalm($database, $_GET['touch']);
		$facepalm->touch();
		$_COOKIE['id-user'] = (int)$_GET['touch'].'-'.$facepalm->name();
		setcookie('id-user', $_COOKIE['id-user'], time() + 30 * 24 * 60 * 60);
	}

	if ($_GET['borrar'] > 0)
	{
		$facepalm = new Facepalm($database, $_GET['borrar']);
		$facepalm->remove();
		setcookie('id-user', '', time()-1);
	}
	if ($_POST['nombre_nuevo'])
	{
		$id = Facepalm::create($_POST['nombre_nuevo']);
		$_COOKIE['id-user'] = $id.'-'.$_POST['nombre_nuevo'];
		setcookie('id-user', $_COOKIE['id-user'], time() + 30 * 24 * 60 * 60);
	}

	header('location:index.php');die;
}

$users = Facepalm::fetchlist();
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
<?php } ?>
<br /><br />
<?php
echo '<form action="index.php" method="post"><table><tr><th>Nombre</th><th>Fecha</th></tr>';
foreach ($users as $usuario)
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

<div>
<img src="facepalm_palmera.jpg" /><br /><br />
<h2>The Discovery of Debugging</h2>

<p>Debugging was a surprise. When the early computer pioneers built the
first programmable computers, they assumed that writing programs would
be straightforward: think hard, write the program, done.</p>

<p>Maurice Wilkes, creator of the EDSAC, the first stored-program
computer, wrote what might be the first programming textbook in 1951,
	with David Wheeler (inventor of the subroutine call!) and Stanley
	Gill. It warns: "Experience has shown that such mistakes are much more
	difficult to avoid than might be expected. It is, in fact, rare for a
	program to work correctly the first time it is tried, and often
	several attempts must be made before all errors are eliminated."</p>

<p>	In his memoir, Wilkes recalled the exact moment he realized the
	importance of debugging: "By June 1949, people had begun to realize
	that it was not so easy to get a program right as had at one time
	appeared. It was on one of my journeys between the EDSAC room and the
	punching equipment that the realization came over me with full force
	that a good part of the remainder of my life was going to be spent in
	finding errors in my own programs."</p>
<img src="facepalm1.jpg" /><br /><br />
<object width="480" height="385"><param name="movie" value="http://www.youtube.com/v/wjLgekyOZA0&hl=en_US&fs=1&"></param><param name="allowFullScreen" value="true"></param><param name="allowscriptaccess" value="always"></param><embed src="http://www.youtube.com/v/wjLgekyOZA0&hl=en_US&fs=1&" type="application/x-shockwave-flash" allowscriptaccess="always" allowfullscreen="true" width="480" height="385"></embed></object>
