<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html>
	<head>
		<title>Intranet</title>

		<meta http-equiv='Content-Type' content='text/html; charset=iso-8859-1' />
		<meta http-equiv="Pragma" content="no-cache" />
		<meta http-equiv="Expires" content="-1" />

		<script language="JavaScript" type="text/javascript">
			function loginpage_onload()
			{
				loginObj = document.getElementById( "login" );
				if( loginObj.value == "" )
				{
					loginObj.focus();
				}
			}
		</script>
		<style type="text/css">
			BODY,TABLE,TD {
				color: #000000;
				font-size: 11px;
				margin: 0 0 0 0;
				font-family : sans-serif;
			}
			body {
				background-image: url("imagens/fundo_login.jpg");
				background-size : 100%;
			}
			A:link, A:visited, A:active, A:hover {
				color: #0033CC;
				font-family: verdana, arial, heveltica, sans;
				font-size: 11px;
			}
			LABEL{
				float: left;
				margin-right : 20px;
				font-family: sans;
				font-size : 150%;
				color: #092C34;
				min-width : 10em;
				text-align : right;
			}
			#login, #senha {
				-webkit-transition : 500ms;
				transition : 1s;
				margin-bottom:5px;
				width: 18em;
				font-size: 150%;
				color: #092C34;
				background-color : #C0E0EE;
				border : none;
				padding : 3px;
			}
			#login:focus, #senha:focus {
				background-color : white;
			}
			#login_form {
				margin-left : 10%;
				margin-top : 40px;
				max-width : 50%;
				
			}
			#botao {
				margin-top : 30px;
			}
			.logo_rodape {
				margin-top : 20px;
				margin-left : 16px;
				float : left;
				-webkit-transition : 1s;
				transition : 1s;
				border : thin solid transparent;
			}
			.logo_rodape:hover {
				background-color : #C0E0EE;
				border : thin solid white;
			}
			.error {
				padding : 1em;
				background-color : white;
				color : #BA0000;
				width : 80%;
				margin-left : 10%;
			}
		</style>
	</head>
	<body onload="loginpage_onload();">

		<span id="login_error">
		      <!-- #&ERROLOGIN&# -->
		</span>
	
		<form action="" method="post" id="login_form">
			<table>
				<tr>
					<td><label for="login">Login (Matr&iacute;cula ou CPF):</label></td>
					<td><input type="text" name="login" id="login" value="" size="15" /></td>
				</tr>
				<tr>
					<td><label for="senha">Senha:</label></td>
					<td><input type="password" name="senha" id="senha" size="15" /></td>
				</tr>
				<tr>
					<td colspan="2"><!-- #&RECAPTCHA&# --></td>
				</tr>
				<tr>
					<td>&nbsp;</td>
					<td><input type="image" id="botao" src="imagens/bot_login.png" value="Entrar" /></td>
				</tr>
			</table>
		</form>

		<a class="logo_rodape" href="http://pagina.softwarepublico.gov.br/ieducar/" target="_top">
			<img id="logo_ieducar" src="imagens/logo_ieducar.png" alt="i-Educar"> </img>
		</a>
		
		<a class="logo_rodape" href="http://serpro.gov.br" target="_top">
			<img id="logo_serpro" src="imagens/logo_serpro.png" alt="SERPRO"> </img>
		</a>
		
		<a class="logo_rodape" href="http://fazenda.gov.br" target="_top">
			<img id="logo_fazenda" src="imagens/logo_ministerio_fazenda.png" alt="Minist&eacute;rio da Fazenda"> </img>
		</a>
		
		<a class="logo_rodape" href="http://mc.gov.br" target="_top">
			<img id="logo_comunicacoes" src="imagens/logo_ministerio_comunicacoes.png" alt="Minist&eacute;rio das Comunica&ccedil;&otilde;es"> </img>
		</a>
		
	</body>
</html>
