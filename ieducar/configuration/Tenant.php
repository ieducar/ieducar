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

	public function toJson() {
		$tenant_title = $this->getConfigurations();
		return '{"tenantName" : "['.$this->getName().']", "tenantTitle" : "'.$this->printTenantTitle().'", "html" : "'.$this->printTenantDetails().'"}';
	}
	
	public function printTenantDetails() {

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
	
	public function printTenantTitle() {
		$configurations = $this->getConfigurations();
		$tenant_title = $configurations["app.entity.name"];
		return "<h2 class='tenant_title'>".$tenant_title."</h2>";
	}
	
	public function printTenantStats() {

		$configurations = $this->getConfigurations();
		
		$html = "";
		$html .= "<h3>[".$this->getName()."] - ".$configurations['app.entity.name']."</h3>";
		
		$db_id = substr($this->getName(), 0, strpos($this->getName(), " "));
		$html .= "<div>";
		$html .= "<div id='chart-area-$db_id' class='pie-chart-area'>";
		$html .= "<div id='chart-area-legend-$db_id'>";
		$html .= "</div>";
		$html .= "</div>";
		$html .= "</div>";
		return $html;
	}
	
	public function getTenantStats() {
		
		$configurations = $this->getConfigurations();
		
		$hostname = $configurations['app.database.hostname'];
		$dbname = $configurations['app.database.dbname'];
		$username = $configurations['app.database.username'];
		$password = $configurations['app.database.password'];
		$port = $configurations['app.database.port'];
		
		$db_id = substr($this->getName(), 0, strpos($this->getName(), " "));
		
		$dbconn = pg_connect("host=$hostname port=$port dbname=$dbname user=$username password=$password");
		
		$stats = array();

		$json = '{ "db_name" : "'.$dbname.'", ';
		
		if ($dbconn) {
			
			$json .= '"graph" : {'; 
			
			
			// CACHE - HIT/MISS
			$json .= '"cache" : { ';
			
			$result = pg_query($dbconn, "SELECT SUM(heap_blks_hit) AS hit, SUM(heap_blks_read) AS miss FROM pg_statio_user_tables");
			//$result = pg_query($dbconn, "SELECT blks_hit AS hit, blks_read AS miss FROM pg_stat_database WHERE datname = '".$dbname."'");
			if ($result) {
				while ($row = pg_fetch_row($result)) {
					$cache_hit_miss = array();
					$cache_hit_miss['hit'] = $row[0];
					$cache_hit_miss['miss'] = $row[1];
					$stats['cache'] = $cache_hit_miss;
				}
				$json .= '"hit" : { "value" : "'.$stats["cache"]["hit"].'"}, "miss" : { "value" : "'.$stats["cache"]["miss"].'" }';
			} else {
				$json .= '"errorMessage" : "Ocorreu um erro na consulta das estatísticas do cache (hit/miss)."';
			}
			$json .= '}';
			
			// DATABASE SIZE (RAW_DATA/INDEXES)
			$json .= ', "db_size" : {';
			$result = pg_query($dbconn, "SELECT SUM(pg_table_size(schemaname||'.'||tablename)) AS tamanho_banco, " .
										"SUM(pg_indexes_size(schemaname||'.'||tablename)) AS tamanho_indices_banco " .
										"FROM pg_tables " .
										"WHERE schemaname NOT IN ('pg_catalog', 'information_schema', 'pg_toast')");
			
			if ($result) {
				while ($row = pg_fetch_row($result)) {
					$db_size = array();
					$db_size['raw_data'] = $row[0];
					$db_size['indexes'] = $row[1];
					$stats['db_size'] = $db_size;
				}
				$json .= '"raw_data" : { "value" : "'.number_format($stats["db_size"]["raw_data"] / 1048576,2).'"}, "indexes" : { "value" : "'.number_format($stats["db_size"]["indexes"] / 1048576,2).'" }';
			} else {
				$json .= '"errorMessage" : "Ocorreu um erro na consulta do tamanho do banco."';
			}
			$json .= '}';

			// TABLES SIZE (RAW_DATA/INDEXES)
			$json .= ', "table_size" : ';
			$result = pg_query($dbconn, "SELECT schemaname||'.'||tablename AS nome_tabela, " .
					"pg_table_size(schemaname||'.'||tablename) AS tamanho_tabela, " .
					"pg_indexes_size(schemaname||'.'||tablename) AS tamanho_indices_tabela " .
					"FROM pg_tables " .
					"WHERE schemaname NOT IN ('pg_catalog', 'information_schema', 'pg_toast') " .
					"ORDER BY pg_table_size(schemaname||'.'||tablename) DESC " .
					"LIMIT 5");
				
			if ($result) {
				$tables = array();
				while ($row = pg_fetch_row($result)) {
					$table_size = array();
					$table_size['table_name'] = $row[0];
					$table_size['raw_data'] = $row[1];
					$table_size['indexes'] = $row[2];
					array_push($tables, $table_size);
				}
				$json .= '[ ';
				foreach ($tables as $key => $val) {
					$json .= '{ "table_name" : "'.$tables[$key]["table_name"].'", "raw_data" : { "value" : "'.number_format($tables[$key]["raw_data"] / 1024).'"}, "indexes" : { "value" : "'.number_format($tables[$key]["indexes"] / 1024).'" } },';
				}
				$json = rtrim($json, ",") . ' ]';
			} else {
				$json .= ' { "errorMessage" : "Ocorreu um erro na consulta do tamanho das tabelas." } ';
			}
			
			// INSERT, UPDATE, DELETE, SELECT
			$json .= ', "db_transactions" : { ';
			$result = pg_query($dbconn, "SELECT pg_stat_get_db_tuples_inserted(oid) AS tup_inserted, ".
					"pg_stat_get_db_tuples_updated(oid) AS tup_updated, ".
					"pg_stat_get_db_tuples_deleted(oid) AS tup_deleted, ".
					"pg_stat_get_db_tuples_fetched(oid) AS tup_fetched ".
					"FROM pg_database WHERE datname = '".$dbname."'");
			
			if ($result) {
				while ($row = pg_fetch_row($result)) {
					$db_transactions = array();
					$db_transactions['tup_inserted'] = $row[0];
					$db_transactions['tup_updated'] = $row[1];
					$db_transactions['tup_deleted'] = $row[2];
					$db_transactions['tup_fetched'] = $row[3];
					$stats['db_transactions'] = $db_transactions;
				}
				$json .= '"tup_inserted" : { "value" : "'.$stats["db_transactions"]["tup_inserted"].'"}, '.
						 '"tup_updated" : { "value" : "'.$stats["db_transactions"]["tup_updated"].'" }, '.
						 '"tup_deleted" : { "value" : "'.$stats["db_transactions"]["tup_deleted"].'" }, '.
						 '"tup_fetched" : { "value" : "'.$stats["db_transactions"]["tup_fetched"].'" }';
			} else {
				$json .= '"errorMessage" : "Ocorreu um erro na consulta de transações realizadas no banco."';
			}
			$json .= '}';
			
			// SEQ_SCAN x IDX_SCAN
			$json .= ', "table_scan" : ';
			$result = pg_query($dbconn, "SELECT schemaname||'.'||relname AS tabela_nome, seq_scan, idx_scan ".
					"FROM pg_stat_user_tables ".
					"WHERE idx_scan > 0 AND seq_scan > 0".
					"ORDER BY seq_scan DESC ".
					"LIMIT 5");
			
			if ($result) {
				$tables = array();
				while ($row = pg_fetch_row($result)) {
					$table_scan = array();
					$table_scan['table_name'] = $row[0];
					$table_scan['seq_scan'] = $row[1];
					$table_scan['idx_scan'] = $row[2];
					array_push($tables, $table_scan);
				}
				$json .= '[ ';
				foreach ($tables as $key => $val) {
					$json .= '{ "table_name" : "'.$tables[$key]["table_name"].'", "seq_scan" : { "value" : "'.$tables[$key]["seq_scan"].'"}, "idx_scan" : { "value" : "'.$tables[$key]["idx_scan"].'" } },';
				}
				$json = rtrim($json, ",") . ' ]';
			} else {
				$json .= ' { "errorMessage" : "Ocorreu um erro na consulta de scans por tabela (seq_scan x idx_scan)." } ';
			}
			
			// INDICES NAO UTILIZADOS
			$json .= ', "indexes_not_used" : ';
			$result = pg_query($dbconn, "SELECT t.schemaname||'.'||t.relname AS tabela_nome, i.indexrelname ".
						"FROM pg_stat_user_tables t ".
						"INNER JOIN pg_stat_user_indexes i ON i.relid = t.relid ".
						"WHERE t.seq_scan > 0 AND t.idx_scan = 0");
				
			if ($result) {
				$json .= '[ ';
				while ($row = pg_fetch_row($result)) {
					$json .= '{ "table_name" : "'.$row[0].'", "index_name" : "'.$row[1].'" },';
				}
				$json = rtrim($json, ",") . ' ]';
			} else {
				$json .= ' { "errorMessage" : "Ocorreu um erro na consulta que verifica os índices não utilizados." } ';
			}
			
			// CHECKPOINTS BUFFER x BACKEND
			$json .= ', "checkpoints" : { ';
				
			$result = pg_query($dbconn, "SELECT buffers_checkpoint, buffers_backend FROM pg_stat_bgwriter");
			if ($result) {
				while ($row = pg_fetch_row($result)) {
					$json .= '"buffer" : { "value" : "'.$row[0].'"}, "backend" : { "value" : "'.$row[1].'" }';
				}
			} else {
				$json .= '"errorMessage" : "Ocorreu um erro na consulta de checkpoints (buffer x backend)."';
			}
			$json .= '}';
			
			// DEADLOCKS
			$json .= ', "db_deadlocks" : { ';
				
			$result = pg_query($dbconn, "SELECT deadlocks FROM pg_stat_database WHERE datname = '".$dbname."'");
			if ($result) {
				while ($row = pg_fetch_row($result)) {
					$json .= '"value" : "'.$row[0].'"';
				}
			} else {
				$json .= '"errorMessage" : "Ocorreu um erro para retornar a quantidade de deadlocks do banco."';
			}
			$json .= '}';
					
			// CONFLICTS
			$json .= ', "db_conflicts" : { ';
			
			$result = pg_query($dbconn, "SELECT conflicts FROM pg_stat_database WHERE datname = '".$dbname."'");
			if ($result) {
				while ($row = pg_fetch_row($result)) {
					$json .= '"value" : "'.$row[0].'"';
				}
			} else {
				$json .= '"errorMessage" : "Ocorreu um erro para retornar a quantidade de consultas canceladas por algum conflito."';
			}
			$json .= '}';
			
			// FUNCTIONS
			$json .= ', "db_functions" : ';
			$result = pg_query($dbconn, "SELECT schemaname, funcname, calls, total_time FROM pg_stat_user_functions");
				
			if ($result) {
				$json .= '[ ';
				while ($row = pg_fetch_row($result)) {
					$json .= '{ "schema_name" : "'.$row[0].'", "function_name" : "'.$row[1].'", "calls" : "'.$row[2].'", "total_time" : "'.$row[3].'" },';
				}
				$json = rtrim($json, ",") . ' ]';
			} else {
				$json .= ' { "errorMessage" : "Ocorreu um erro na consulta de estatísticas das funções." } ';
			}
			
			// VACUUM e ANALYZE
			$json .= ', "vacuum_analyze" : ';
			$result = pg_query($dbconn, "SELECT at.schemaname||'.'||at.relname AS table_name, ".
					"to_char(at.last_vacuum, 'DD/MM/YYYY HH:MI:SS') AS last_vacuum, ".
					"to_char(at.last_autovacuum, 'DD/MM/YYYY HH:MI:SS') AS last_autovacuum, ".
					"to_char(at.last_analyze, 'DD/MM/YYYY HH:MI:SS') AS last_analyze, ".
					"to_char(at.last_autoanalyze, 'DD/MM/YYYY HH:MI:SS') AS last_autoanalyze, ".
					"at.vacuum_count, at.autovacuum_count, at.analyze_count, at.autoanalyze_count, ".
					"pg_table_size(t.schemaname||'.'||t.tablename) AS table_size ".
					"FROM pg_stat_all_tables at ".
					"INNER JOIN pg_tables t ON t.schemaname = at.schemaname AND t.tablename = at.relname ".
					"WHERE at.schemaname NOT IN ('pg_catalog', 'information_schema', 'pg_toast') ".
					"AND (at.vacuum_count+at.autovacuum_count+at.analyze_count+at.autoanalyze_count) > 0 ".
					"ORDER BY at.last_autovacuum DESC, at.last_vacuum DESC, at.last_autoanalyze DESC, at.last_analyze DESC");
			
			if ($result) {
				$json .= '[ ';
				while ($row = pg_fetch_row($result)) {
					$json .= '{ "table_name" : "'.$row[0].'", '.
							 '"last_vacuum" : "'.$row[1].'", '.
							 '"last_autovacuum" : "'.$row[2].'", '.
							 '"last_analyze" : "'.$row[3].'", '.
							 '"last_autoanalyze" : "'.$row[4].'", '.
							 '"vacuum_count" : "'.$row[5].'", '.
							 '"autovacuum_count" : "'.$row[6].'", '.
							 '"analyze_count" : "'.$row[7].'", '.
							 '"autoanalyze_count" : "'.$row[8].'", '.
							 '"table_size" : "'.number_format($row[9]/1024).'" },';
				}
				$json = rtrim($json, ",") . ' ]';
			} else {
				$json .= ' { "errorMessage" : "Ocorreu um erro na consulta de estatísticas de vacuum e analyze." } ';
			}
					
			$json .= ' }';
		} else {
			throw new Exception("Não foi possível realizar a conexão ao banco de dados.");
		}
		
		$json .= ' }';
		
		return $json;
	}
}

?>