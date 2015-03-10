<?php

include 'Tenant.php';

/**
 * TODO
 * 
 * 
 * 
 * DOING
 *
 * - Dentro de cada item do accordion, exibir as estatísticas do tenant
 *  
 * DONE
 * 
 * - Criar aba para as estatísticas dos tenants
 * - Listar tenants com accordion
 * 
 */

// error_reporting(E_ALL);
// ini_set('display_errors', 1);

function print_tenants_stats($tenants) {
	$html = '';
	foreach ($tenants as $key => $tenant)
		$html .= $tenant->printTenantStats();
	return $html;
}

function print_tenants($tenants) {
	$html = '';
	foreach ($tenants as $key => $tenant)
		$html .= $tenant->printTenantDetails();
	return $html;
}

$ieducar_ini = "ieducar.ini";

if (!is_readable($ieducar_ini)) {
	echo "<h1 style='color:red;'>Permissão negada para leitura do arquivo '$ieducar_ini'. Não foi possível fazer a leitura do arquivo.</h1>";
	echo "<span>Para este módulo funcionar corretamente, o arquivo e a pasta onde ele se encontra devem possuir permissão de leitura/gravação.</span><br/>";
	echo "<span>A pasta deve possuir permissão de gravação porque a cada alteração do arquivo, um backup dele é realizado.</span>";
	exit();
}

if (!is_writable($ieducar_ini)) {
	echo "<h1 style='color:red;'>Permissão negada para escrita do arquivo '$ieducar_ini'. Não é possível fazer a gravação do arquivo.</h1>";
	echo "<span>Para este módulo funcionar corretamente, o arquivo ('$ieducar_ini') e a pasta onde ele se encontra devem possuir permissão de leitura/gravação.</span><br/>";
	echo "<span>A pasta deve possuir permissão de gravação porque a cada alteração do arquivo, um backup dele é realizado.</span>";
	exit();
}

if (!is_writable(".")) {
	echo "<h1 style='color:red;'>Permissão negada para escrita na pasta. Não é possível fazer a gravação de arquivos de backup na pasta.</h1>";
	echo "<span>Para este módulo funcionar corretamente, o arquivo ('$ieducar_ini') e a pasta onde ele se encontra devem possuir permissão de leitura/gravação.</span><br/>";
	echo "<span>A pasta deve possuir permissão de gravação porque a cada alteração do arquivo, um backup dele é realizado.</span>";
	exit();
}

// monta array com o arquivo ieducar.ini
$ini_file = parse_ini_file($ieducar_ini, true);

//ieducar.ini com problemas
if (!$ini_file) {
	echo "<h1 style='color:red;'>Existe um erro no arquivo '$ieducar_ini'. Não foi possível fazer a leitura e análise do arquivo.</h1>";
	echo "<span style='font-weight:bold;'>Conteúdo do arquivo '$ieducar_ini':</span><br/><br/>";
	
	$handle = fopen($ieducar_ini, "rb");
	$content = fread($handle, filesize($ieducar_ini));
	fclose($handle);
	
	echo nl2br($content, false);
	
	exit();
}

$ini_file_keys = array_keys($ini_file);

// busca as chaves com as configurações dos tenants
$keys = preg_grep ("/\A([a-z\.])*ieducar([a-z\.])* : production\z/", $ini_file_keys);

$tenants = array();

foreach ($keys as $key => $tenant_name) {
	
	try {
		
		$tenant_configurations = $ini_file[$tenant_name];
		$tenant = new Tenant();
		$tenant->setName($tenant_name);
		$tenant_config = array();		
		foreach ($tenant_configurations as $config => $value) {
			$tenant_config[$config] = $value;
		}
		$tenant->setConfigurations($tenant_config);
		array_push($tenants, $tenant);
		
	} catch (Exception $e) {
		echo $e->getMessage();
	}
}

sort($tenants);

?>
<!doctype html>
<html>
	<head>
		<title>Tenants Configuration</title>
		<link href="estilos/jquery-ui.css" rel="stylesheet" />
		<link href="estilos/tenantConfiguration.css" rel="stylesheet" />
		<link href="estilos/tenantStats.css" rel="stylesheet" />
		<link href="estilos/legend.css" rel="stylesheet" />
	</head>
	<body class="hidden-field">
		<h2 class="demoHeaders">Tenants</h2>
		<div id="tabs">
			<ul>
				<li><a href="#tabs-1">Estatísticas</a></li>
				<li><a href="#tabs-2">Configurações</a></li>
			</ul>
			<div id="tabs-1">
				<div id="accordion_stats">
					<?=print_tenants_stats($tenants);?>
				</div>
			</div>
			<div id="tabs-2">
				<div style="width:60%">
					<input type="button" id="btn_new_tenant" value="+ novo"/><br />
					<div class="ui-widget ui-widget-content ui-corner-all ui-front ui-dialog-buttons hidden-field" id="box_new_tenant" style="margin-top:1em; margin-bottom:2em;">
						<div class="ui-dialog-titlebar ui-widget-header ui-corner-all ui-helper-clearfix ui-draggable-handle" style="padding: .5em;">
							<span class="ui-dialog-title">Criar novo tenant</span>
						</div>
						<div id="new_tenant_config" class="ui-dialog-content ui-widget-content" style="border-left:0; border-top:0; border-right:0;">
							<div style="padding-top:1em; margin-left:1em; margin-right:1em;">
								<span style="font-size: 1.4em;">[<input type="text" size="1" />.ieducar : production]</span><span class="required-field hidden-field">*</span><br />
								<table>
									<tr><td><span>app.entity.name</span></td><td><input type="text" name="new.app.entity.name" required="required" /><span class="required-field hidden-field">*</span></td></tr>
									<tr><td><span>app.database.dbname</span></td><td><input type="text" name="new.app.database.dbname" required="required" /><span class="required-field hidden-field">*</span></td></tr>
									<tr><td><span>app.database.username</span></td><td><input type="text" name="new.app.database.username" required="required" /><span class="required-field hidden-field">*</span></td></tr>
									<tr><td><span>app.database.hostname</span></td><td><input type="text" name="new.app.database.hostname" required="required" /><span class="required-field hidden-field">*</span></td></tr>
									<tr><td><span>app.locale.province</span></td><td><input type="text" name="new.app.locale.province" required="required" /><span class="required-field hidden-field">*</span></td></tr>
								</table>
							</div>
						</div>
						<div class="ui-helper-clearfix" style="margin: .5em;">
							<div style="float:right;">
								<input type="button" class="ui-button ui-widget ui-state-default ui-corner-all ui-state-hover" name="btn_new_tenant_add" id="btn_new_tenant_add" value="Adicionar" />
								<input type="button" class="ui-button ui-widget ui-state-default ui-corner-all ui-state-hover" name="btn_new_tenant_cancel" id="btn_new_tenant_cancel" value="Cancelar" />
							</div>
						</div>
					</div>
					<div id="accordion_config">
						<?=print_tenants($tenants);?>
					</div>
				</div>
			</div>
		</div>
		<script type="text/javascript" src="scripts/jquery-1.11.2.min.js"></script>
		<script type="text/javascript" src="scripts/jquery-ui.min.js"></script>
		<script type="text/javascript" src="scripts/TenantConfiguration.js"></script>
		<script type="text/javascript" src="scripts/TenantStats.js"></script>
		<script type="text/javascript" src="scripts/Chart.js"></script>
		<script type="text/javascript" src="scripts/Legend.js"></script>
	</body>
</html>