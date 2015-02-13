<?php

class Tenant {

	private $name;
	private $configurations;

	public function __construct() {
		$this->name = '';
		$this->configurations = array();
	}

	public function getName() {
		return $this->name;
	}

	public function setName($tenant_name) {
		$this->name = (string) $tenant_name;
	}

	public function getConfigurations() {
		return $this->configurations;
	}

	public function setConfigurations($tenant_configurations) {
		$this->configurations = (array) $tenant_configurations;
	}

	public function to_json() {
		$tenant_title = $this->getConfigurations();
		return '{"tenantName" : "['.$this->getName().']", "tenantTitle" : "'.$this->print_tenant_title().'", "html" : "'.$this->print_tenant_details().'"}';
	}
	
	public function print_tenant_details() {

		$tenants_options = array("app.routes.redirect_to","app.template.loginpage","report.institution_logo_file_name");
		$configurations = $this->getConfigurations();
		
		$html = "";
		$html .= "<h3>[".$this->getName()."] - ".$configurations['app.entity.name']."</h3>";
		$html .= "<div class='tenant-config'>";
		$html .= "<table>";
		foreach ($configurations as $key => $val) {
			$html .= "<tr><td><span>".$key."</span></td><td><input type='text' value='".$val."' ";
			if (in_array($key, $tenants_options)) {
				$html .= " class='tenant-data' />";
				$html .= "<input type='button' value='- remover campo' />";
			} else { 
				$html .= "required='required' class='tenant-data' />";
				$html .= "<span class='required-field hidden-field'>*</span>";
			}
			$html .= "</td>";
		}
		$html .= "</table>";
		$html .= "<select class='select-tenant-options'>";
		$html .= "<option>+ adicionar campo</option>";
		foreach ($tenants_options as $option) {
			if (!key_exists($option, $configurations))
				$html .= "<option>".$option."</option>";
		}
		$html .= "</select>";
		$html .= "<input type='button' class='btn-save-config' value='Salvar Configura&ccedil;&atilde;o' />";
		$html .= "<input type='button' class='btn-remove-tenant' value='Excluir Tenant' />";
		$html .= "</div>";

		return $html;
	}
	
	public function print_tenant_title() {
		$configurations = $this->getConfigurations();
		$tenant_title = $configurations["app.entity.name"];
		return "<h2 class='tenant_title'>".$tenant_title."</h2>";
	}
}

?>