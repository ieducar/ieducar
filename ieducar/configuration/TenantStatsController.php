<?php
include 'Tenant.php';

function returnErrorMessage($message) {
	return '{"errorMessage" : "'.htmlentities($message).'"}';
}

$ieducar_ini = "ieducar.ini";

if (isset($_POST['get_tenant_stats'])) {
	$json = json_decode($_POST['get_tenant_stats']);
} else {
	echo returnErrorMessage("Opчуo invсlida. Preencha corretamente os campos do fomulсrio.");
	exit();
}

if (!is_writable($ieducar_ini)) {
	echo returnErrorMessage("Permissуo negada. O arquivo nуo pode ser alterado.");
	exit();
}

$tenant = new Tenant();
$tenant->setName($json[0].' : production');

$ini_file_content = parse_ini_file($ieducar_ini, true);
$ini_file_keys = array_keys($ini_file_content);
$keys = preg_grep ("/\A([a-z\.])*ieducar([a-z\.])* : production\z/", $ini_file_keys);

// production configs
$prod_configurations = $ini_file_content['production'];
$prod_config = array();
foreach ($prod_configurations as $config => $value) {
	$prod_config[$config] = $value;
}

$tenant_exists = false;

foreach ($keys as $key => $tenant_name) {

	if ($tenant_name == $tenant->getName()) {
		$tenant_exists = true;
		$tenant_configurations = $ini_file_content[$tenant_name];
		$tenant_config = $prod_config;
		//preenche as configuraчѕes especэficas do tenant
		foreach ($tenant_configurations as $config => $value) {
			$tenant_config[$config] = $value;
		}
		$tenant->setConfigurations($tenant_config);
	}
}

if (!$tenant_exists) {
	echo returnErrorMessage("Nуo foi encontrado um tenant com este nome '[".$tenant->getName()."]'.");
	exit();
}

try {
	$stats .= $tenant->getTenantStats();
} catch (Exception $e) {
	$stats = returnErrorMessage($e->getMessage());
}


echo $stats;

exit();

?>