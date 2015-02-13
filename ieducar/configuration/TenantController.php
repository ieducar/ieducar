<?php
include 'Tenant.php';

function getNextTenantPosition($file_contents, $offset) {
	
	$next_tenant_pos = 0;
	
	if (strpos($file_contents, " : production]", $offset)) {
		$next_tenant_pos_ini = strpos($file_contents, "[", $offset);
		$next_tenant_name_length = strpos($file_contents, "]", $next_tenant_pos_ini) - $next_tenant_pos_ini;
		$next_tenant_name = substr($file_contents, $next_tenant_pos_ini + 1, $next_tenant_name_length - 1);
	
		if (preg_match("/\A([a-z\.])*ieducar([a-z\.])* : production\z/", $next_tenant_name))
			$next_tenant_pos = $next_tenant_pos_ini;
		else
			$next_tenant_pos = getNextTenantPosition($file_contents, strpos($file_contents, "]", $next_tenant_pos_ini));
	}
	
	return $next_tenant_pos;
}

function returnMessage($message, $errorMessage = false) {
	return '{"message" : "'.htmlentities($message).'", "errorMessage" : "'.$errorMessage.'"}';
}

$ieducar_ini = "ieducar.ini";

if (isset($_POST['update_tenant'])) {
	$new_tenant = false;
	$remove_tenant = false;
	$json = json_decode($_POST['update_tenant']);
} elseif (isset($_POST['new_tenant'])) {
	$new_tenant = true;
	$remove_tenant = false;
	$json = json_decode($_POST['new_tenant']);
} elseif (isset($_POST['remove_tenant'])) {
	$new_tenant = false;
	$remove_tenant = true;
	$json = json_decode($_POST['remove_tenant']);
} else {
	echo returnMessage("Opчуo invсlida. Preencha corretamente os campos do fomulсrio.", true);
	exit();
}

if (!is_writable($ieducar_ini)) {
	echo returnMessage("Permissуo negada. O arquivo nуo pode ser alterado.", true);
	exit();
}

$tenant = new Tenant();
$tenant->setName($json[0]);
if (!$remove_tenant)
	$tenant->setConfigurations($json[1]);

if (!is_writable(".")) {
	echo returnMessage("Operaчуo nуo realizada. Nуo foi possэvel realizar o backup do arquivo $ieducar_ini. Verifique a permissуo de gravaчуo na pasta.", true);
	exit();
}

$tenant->setName($tenant->getName().' : production');

$handle = fopen($ieducar_ini, "rb");
$original_content = stream_get_contents($handle);
fclose($handle);
$new_content = "";

$tenant_position = stripos($original_content, "[".$tenant->getName()."]");

if ($new_tenant) {
	
	if (!($tenant_position === false)) {
		echo returnMessage("Tenant nуo incluэdo. Jс existe um tenant com este nome '[".$tenant->getName()."]'.", true);
		exit();
	}
	
	$new_content = "\n";
	$new_content .= "[".$tenant->getName()."]\n";
	foreach ($tenant->getConfigurations() as $key => $value)
		$new_content .= $key." = ".utf8_decode($value)."\n";
	
	// insere a nova config no fim arquivo
	if (copy($ieducar_ini, $ieducar_ini.'.'.time())) {
		$handle = fopen($ieducar_ini, "ab");
		fwrite($handle, $new_content);
		fclose($handle);
	} else {
		echo returnMessage("Operaчуo cancelada. Nуo foi possэvel realizar o backup do arquivo $ieducar_ini.", true);
		exit();
	}
	
} else {
	//se existe tenant para atualizar/remover
	if($tenant_position) {
		//pega o conteњdo do arquivo atщ o tenant em $new_content
		$offset = strlen("[".$tenant->getName()."]") + $tenant_position;
		if ($remove_tenant)
			$new_content = substr($original_content, 0, $tenant_position-1);
		else {
			$new_content = substr($original_content, 0, $tenant_position);
			$new_content .= "[".$tenant->getName()."]\n";
	
			foreach ($tenant->getConfigurations() as $key => $value)
				$new_content .= $key." = ".utf8_decode($value)."\n";
		}
				
		//se tem mais tenants depois do atualizado/removido no arquivo, adiciona na var $new_content
		$next_tenant_position = getNextTenantPosition($original_content, $offset);
		if ($next_tenant_position) {
			$new_content .= "\n";
			$new_content .= substr($original_content, $next_tenant_position);
		}
		
		//sobrescreve o conteњdo do arquivo atualizado
		if (copy($ieducar_ini, $ieducar_ini.'.'.time())) {
			$handle = fopen($ieducar_ini, "wb");
			fwrite($handle, $new_content);
			fclose($handle);
		}  else {
			echo returnMessage("Operaчуo cancelada. Nуo foi possэvel realizar o backup do arquivo $ieducar_ini.", true);
			exit();
		}
		
		if ($remove_tenant) {
			echo returnMessage("Tenant removido com sucesso.");
			exit();
		}

	} else {
		echo returnMessage("Configuraчуo invсlida. Nуo existe tenant com este nome '[".$tenant->getName()."]'.", true);
		exit();
	}
}	

echo utf8_decode($tenant->to_json());
exit();

?>