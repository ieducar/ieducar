<?php

include 'Tenant.php';

/**
 * TODO
 * 
 * 
 * DOING
 *
 *  
 * DONE
 *
 * - Incluir os novos tenants na lista à esquerda (ordenados)
 * - Criar novas configurações de tenants e salvar no ieducar.ini
 * - Salvar uma cópia do ieducar.ini antes de realizar as alterações
 * - Alterar as configurações de tenants existentes e salvar no ieducar.ini
 * - Criar select com opções pré-definidas contendo novas configurações para os tenants
 * - Possibilitar remover opções não obrigatórias dos tenants
 * - Ao salvar um novo tenant, inserí-lo na tela no local apropriado por ordem alfabética
 * - Ao incluir um novo tenant, levar a página com âncoras para local onde ele foi inserido.
 * - Fazer animação de 'loading' ou fade para salvar os novos tenants ou quando atualizar algum
 * - Criar accordion para os itens e adequar as funções para que continuem funcionando
 * - Remover lista da esquerda com os nomes dos tenants
 * - Depois de incluir com sucesso um novo tenant, apagar o valor dos campos inseridos no bloco 'new_tenant'
 * - Exibir mensagem de erro/confirmação em local apropriado para o usuário
 * - Aplicar estilos na página -> falta botão "+ novo" e combo
 * - Criar botão para remover tenant e excluir suas configurações do ieducar.ini
 * - Alertas para campos obrigatórios no box novo tenant
 * - Alertas para campos obrigatórios para atualização de tenants
 * - Ao alterar a chave app.entity.name e salvar, o nome não está sendo atualizado no h3
 * - Alertar quando o arquivo está mal formado ou não possuir permissão de leitura/escrita.
 * - Refatoração do código
 * - Separar os arquivos JS e CSS do código PHP
 * 
 */

// error_reporting(E_ALL);
// ini_set('display_errors', 1);

function print_tenant_titles($tenants) {
	$html = '';
	foreach ($tenants as $index => $tenant) {
		$html .= $tenant->print_tenant_title();
	}
	return $html;
}

function print_tenants($tenants) {
	$html = '';
	foreach ($tenants as $key => $tenant)
		$html .= $tenant->print_tenant_details();
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
	</head>
	<body class="hidden-field">
		<div style="width:60%">
			<h2 class="demoHeaders">Tenant Configurations</h2>
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
			<div id="accordion">
				<?=print_tenants($tenants);?>
			</div>
		</div>
		<script type="text/javascript" src="scripts/jquery-1.11.2.min.js"></script>
		<script type="text/javascript" src="scripts/jquery-ui.min.js"></script>
		<script type="text/javascript" src="scripts/TenantConfiguration.js"></script>
	</body>
</html>