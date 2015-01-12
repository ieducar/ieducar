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
				color: #092C34;
				font-size: 11px;
				margin: 0 0 0 0;
				font-family : sans-serif;
			}
			A:link, A:visited  {
				transition : 1s;
				color: #0033CC;
			}
			A:hover, A:active {
				text-decoration : none;
				color : black;
			}
			label {
				float: right;
				margin-right : 20px;
				font-family: sans;
				font-size : 150%;
				color: #092C34;
				min-width : 10em;
				text-align : right;
			}
			#login, #senha, #botao {
				-webkit-transition : 500ms;
				transition : 500ms;
				margin-bottom:5px;
				width: 18em;
				font-size: 150%;
				background-color : #C0E0EE;
				border : 1px solid #092C34;
				padding : 3px;
			}
			#login:focus, #senha:focus, #botao:focus, #botao:hover {
				background-color : white;
			}
			#botao:active {
				background-color : #092C34;
				color : #C0E0EE;
				border-color : #C0E0EE; 
			}
			#login_form {
				margin-left : 10%;
				margin-top : 40px;
				max-width : 50%;
			}
			#botao {
				margin-left : 12em;
				padding-top : 10px;
				padding-bottom : 10px;
				border-radius : 10px;
				float: right;
			}
			.logo_rodape {
				margin-top : 20px;
				margin-left : 16px;
				min-width : 160px;
				min-height : 80px;
				text-align : center;
				-webkit-transition : 1s;
				transition : 1s;
				border : thin solid transparent;
				float : left;
			}
			.logo_rodape img {
				max-width : 160px;
				max-height : 80px;
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
			#recommendations {
				width : 90%;
				font-size : 120%;
				background-color : white;
				padding : 2em;
			}
			hr {
				border : thin solid #092C34;
				margin : 2em;
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
				<tr><td colspan="2"><!-- #&RECAPTCHA&# --><input type="submit" id="botao" value="Entrar" /></td></tr>
			</table>
		</form>
		
		<hr>

		<div id="recommendations">
			<p> Para melhor uso do sistema, recomendamos: </p>
			<ul>
				<li> Navegador <a href="http://getfirefox.com"> Mozilla Firefox </a> </li>
				<li> Leitor de relat&oacute;rios em formato PDF <a href="http://get.adobe.com/br/reader/"> Adobe Reader</a> ou <a href="http://www.foxitsoftware.com/portuguese/products/reader/"> Foxit </a> </li> 
			</ul>
		</div>
		
		<hr>

		<a class="logo_rodape" href="http://pagina.softwarepublico.gov.br/ieducar/" target="_top">
			<img id="logo_ieducar" src="imagens/logo_ieducar.png" alt="i-Educar - Software de Gest&atilde;o Escolar"> </img>
		</a>
		<a class="logo_rodape" href="http://serpro.gov.br" target="_top">
			<img id="logo_serpro" src="imagens/logo_serpro.png" alt="Servi&ccedil;o Federal de Processamento de Dados - SERPRO"> </img>
		</a>
		<a class="logo_rodape" href="http://www.valparaisodegoias.go.gov.br/" target="_top">
			<img id="logo_valparaiso" src="imagens/logo_valparaiso.png" alt="Prefeitura Municipal de Valpara&iacute;so de Goi&aacute;s"> </img>
		</a>
		
	</body>
</html>
