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
				margin-left : 200px;
			}
			#cidades_digitais {
				position : absolute;
				bottom : 50px;
				left : 50px;
			}
		</style>
	</head>
	<body onload="loginpage_onload();">
	
		<form action="" method="post" id="login_form">
			<label for="login">Usu&aacute;rio:</label>
			<input type="text" name="login" id="login" value="" size="15" /><br />
				
			<label for="senha">Senha:</label>
			<input type="password" name="senha" id="senha" size="15" /><br />

			<input type="image" id="botao" src="imagens/bot_login.png" value="Entrar" />
		</form>
		
		<a href="http://www.cidadesdigitais.gov.br" target="_top">
			<img id="cidades_digitais" src="imagens/logo_cidades_digitais.jpg" alt="Cidades Digitais"> </img>
		</a>
	</body>
</html>
