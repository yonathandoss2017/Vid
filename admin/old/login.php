<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Vidoomy - Admin Area</title>
<link rel="stylesheet" type="text/css" href="css/theme.css" />
<link rel="stylesheet" type="text/css" href="css/style.css" />
<script>
   var StyleFile = "theme" + document.cookie.charAt(6) + ".css";
   document.writeln('<link rel="stylesheet" type="text/css" href="css/' + StyleFile + '">');
</script>
<!--[if IE]>
<link rel="stylesheet" type="text/css" href="css/ie-sucks.css" />
<![endif]-->
</head>

<body>
	<div id="container">
    	<div id="header">
        	<h2>Vidoomy - Admin Login</h2>
        </div>
		<br/><br/><br/><br/><br/><br/>
        <div id="wrapper">
            <div id="content" style="float:none; margin:auto;">
                <div id="box">
					<form id="form" action="index.php" method="post">
					<fieldset id="ingreso">
					<legend>Ingreso</legend>
					<label for="precio">Usuario: </label>
					<input style="width:200px; maring-top:6px;" name="user" type="text" tabindex="2" />
					<br />
					<label for="precio">Contrase&ntilde;a: </label>
					<input style="width:200px; maring-top:6px;" name="pass" type="password" tabindex="2" />
					<br />
					</fieldset>
					<div align="center">
						  <input type="submit" value="Ingresar" name="ingresa" /> 
					</div>
					</form>
				</div>
            </div>
        </div>
        <div id="footer">

        <div id="styleswitcher">
            <ul>
                <li><a href="javascript: document.cookie='theme='; window.location.reload();" title="Default" id="defswitch">d</a></li>
                <li><a href="javascript: document.cookie='theme=1'; window.location.reload();" title="Blue" id="blueswitch">b</a></li>
                <li><a href="javascript: document.cookie='theme=2'; window.location.reload();" title="Green" id="greenswitch">g</a></li>
                <li><a href="javascript: document.cookie='theme=3'; window.location.reload();" title="Brown" id="brownswitch">b</a></li>
                <li><a href="javascript: document.cookie='theme=4'; window.location.reload();" title="Mix" id="mixswitch">m</a></li>
            </ul>
        </div><br />

        </div>
</div>
</body>
</html>
