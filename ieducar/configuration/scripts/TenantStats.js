var tenantStatsController = "TenantStatsController.php";

function montaQuadroSemGrafico(mensagem) {
	return "<div style='text-align: center;'><img src='estilos/images/sinal-proibido.png' height='120' /><br /><br /><span>"+mensagem+"</span></div>";
}

$(document).ready(function(){

	// accessos
	$("#accordion_stats").accordion({
		heightStyle: "content",
		active: false,
		collapsible: true
    });
	
	
	$("#accordion_stats").on("accordionbeforeactivate", function(event, ui) {
		$(".stats-box").remove();
	});
	
	$("#accordion_stats").on("accordionactivate", function(event, ui) {
		var activeRow = $("#accordion_stats").accordion("option", "active");
		if ($.isNumeric(activeRow)) {
			var tenantName = $($("#accordion_config h3").get(activeRow)).text();
			tenantName = tenantName.substr(1,tenantName.indexOf(' ')-1);
			var configData = 'get_tenant_stats=["'+tenantName+'"]';

			$.ajax({url : tenantStatsController, type : "POST", data : configData, dataType : "json"})
			.done(function(json) {
				if (json.errorMessage) {
					ui.newPanel.prepend(showMessage(json.errorMessage, true));
					ui.newPanel.children('#error_message').fadeToggle('slow','linear');
				} else {
					// CACHE - HIT/MISS
					if (json.graph.cache.errorMessage) {
						ui.newPanel.children("div:first").prepend(showMessage(json.graph.cache.errorMessage, true));
					} else {
						ui.newPanel.children("div:first").prepend($("<div class='stats-box'><h2>Cache Hit/Miss ("+json.db_name+")</h2><canvas id='cache_hit_miss_canvas' width='200' height='200' /><div /></div>"));
						if (json.graph.cache.hit.value == 0 && json.graph.cache.miss.value == 0) {
							$("#cache_hit_miss_canvas").replaceWith(montaQuadroSemGrafico("Os valores de cache HIT/MISS estão zerados."));
						} else {
							var pieData = [{value: parseInt(json.graph.cache.hit.value), color: "#46BFBD", highlight: "#5AD3D1", label: "Hit"},
							               {value: parseInt(json.graph.cache.miss.value), color:"#F7464A", highlight: "#FF5A5E", label: "Miss"}];
							var ctx = $("#cache_hit_miss_canvas").get(0).getContext("2d");
							new Chart(ctx).Pie(pieData);
							legend($("#cache_hit_miss_canvas").next("div").get(0), pieData);
						}
					}

					// DATABASE SIZE
					if (json.graph.db_size.errorMessage) {
						ui.newPanel.children("div:first").prepend(showMessage(json.graph.db_size.errorMessage, true));
					} else {
						ui.newPanel.children("div:last").append($("<div class='stats-box' style='width:230px'><h2>DB Size (MB)</h2><canvas id='db_size_canvas' width='230' height='300' /><div /></div>"));
						if (json.graph.db_size.raw_data.value == 0 && json.graph.db_size.indexes.value == 0) {
							ui.newPanel.children("div:last").find("canvas").replaceWith(montaQuadroSemGrafico("Os valores do tamanho do banco estão zerados."));
						} else {
							var data = {
								labels: [json.db_name],
							    datasets: [
							        {
							            label: "Tamanho banco",
							            fillColor: "rgba(220,220,220,0.5)",
							            strokeColor: "rgba(220,220,220,0.8)",
							            highlightFill: "rgba(220,220,220,0.75)",
							            highlightStroke: "rgba(220,220,220,1)",
							            data: [json.graph.db_size.raw_data.value]
							        },
							        {
							            label: "Tamanho índices",
							            fillColor: "rgba(151,187,205,0.5)",
							            strokeColor: "rgba(151,187,205,0.8)",
							            highlightFill: "rgba(151,187,205,0.75)",
							            highlightStroke: "rgba(151,187,205,1)",
							            data: [json.graph.db_size.indexes.value]
							        }
							    ]
							};
							var ctx = $("#db_size_canvas").get(0).getContext("2d");
							new Chart(ctx).Bar(data);
							legend($("#db_size_canvas").next("div").get(0), data);
						}
					}
					
					// TABLE SIZE
					if (json.graph.table_size.errorMessage) {
						ui.newPanel.children("div:first").prepend(showMessage(json.graph.table_size.errorMessage, true));
					} else {
						
						var graph_labels = [];
						var graph_data_table_size = [];
						var graph_data_table_index_size = [];
						$(json.graph.table_size).each(function() {
							graph_labels[graph_labels.length] = $(this).get(0).table_name;
							graph_data_table_size[graph_data_table_size.length] = $(this).get(0).raw_data.value;
							graph_data_table_index_size[graph_data_table_index_size.length] = $(this).get(0).indexes.value;
						});
						
						ui.newPanel.children("div:last").append($("<div class='stats-box' style='width: 400px'><h2>Table Size (kB)</h2><canvas id='table_size_canvas' width='400' height='400' /><div /></div>"));
						if (json.graph.table_size.length) {
							var data = {
								labels: graph_labels,
							    datasets: [
							        {
							            label: "Tamanho tabelas",
							            fillColor: "rgba(220,220,220,0.5)",
							            strokeColor: "rgba(220,220,220,0.8)",
							            highlightFill: "rgba(220,220,220,0.75)",
							            highlightStroke: "rgba(220,220,220,1)",
							            data: graph_data_table_size
							        },
							        {
							            label: "Tamanho índices",
							            fillColor: "rgba(151,187,205,0.5)",
							            strokeColor: "rgba(151,187,205,0.8)",
							            highlightFill: "rgba(151,187,205,0.75)",
							            highlightStroke: "rgba(151,187,205,1)",
							            data: graph_data_table_index_size
							        }
							    ]
							};
							var ctx = $("#table_size_canvas").get(0).getContext("2d");
							new Chart(ctx).Bar(data);
							legend($("#table_size_canvas").next("div").get(0), data);
						} else {
							$("#table_size_canvas").replaceWith(montaQuadroSemGrafico("Os valores do tamanho do banco estão zerados."));
						}
					}
					
					// INSERT, UPDATE, DELETE, SELECT
					if (json.graph.db_transactions.errorMessage) {
						ui.newPanel.children("div:first").prepend(showMessage(json.graph.db_transactions.errorMessage, true));
					} else {
						ui.newPanel.children("div:last").append($("<div class='stats-box' style='width:230px'><h2>DB Transactions</h2><canvas id='db_transactions_canvas' width='230' height='200' /><div /></div>"));
						if (json.graph.db_transactions.tup_inserted.value == 0 && json.graph.db_transactions.tup_updated.value == 0
								&& json.graph.db_transactions.tup_deleted.value == 0 && json.graph.db_transactions.tup_fetched.value == 0) {
							$("#db_transactions_canvas").replaceWith(montaQuadroSemGrafico("Os valores do tamanho do banco estão zerados."));
						} else {
							var data = {
								labels: [json.db_name],
							    datasets: [
							        {
							            label: "Select",
							            fillColor: "rgba(220,220,220,0.5)",
							            strokeColor: "rgba(220,220,220,0.8)",
							            highlightFill: "rgba(220,220,220,0.75)",
							            highlightStroke: "rgba(220,220,220,1)",
							            data: [json.graph.db_transactions.tup_fetched.value]
							        },
							        {
							            label: "Update",
							            fillColor: "rgba(151,187,205,0.5)",
							            strokeColor: "rgba(151,187,205,0.8)",
							            highlightFill: "rgba(151,187,205,0.75)",
							            highlightStroke: "rgba(151,187,205,1)",
							            data: [json.graph.db_transactions.tup_updated.value]
							        },
							        {
							            label: "Insert",
							            fillColor: "rgba(70, 191, 189,0.5)",
							            strokeColor: "rgba(70, 191, 189,0.8)",
							            highlightFill: "rgba(70, 191, 189,0.75)",
							            highlightStroke: "rgba(70, 191, 189,1)",
							            data: [json.graph.db_transactions.tup_inserted.value]
							        },
							        {
							            label: "Delete",
							            fillColor: "rgba(247, 70, 74,0.5)",
							            strokeColor: "rgba(247, 70, 74,0.8)",
							            highlightFill: "rgba(247, 70, 74,0.75)",
							            highlightStroke: "rgba(247, 70, 74,1)",
							            data: [json.graph.db_transactions.tup_deleted.value]
							        }
							    ]
							};
							var ctx = $("#db_transactions_canvas").get(0).getContext("2d");
							new Chart(ctx).Bar(data);
							legend($("#db_transactions_canvas").next("div").get(0), data);
						}
					}
					
					// TABLE SCAN
					if (json.graph.table_scan.errorMessage) {
						ui.newPanel.children("div:first").prepend(showMessage(json.graph.table_scan.errorMessage, true));
					} else {
						if (json.graph.table_scan.length) {
							var graph_labels = [];
							var graph_data_table_seq_scan = [];
							var graph_data_table_index_scan = [];
							$(json.graph.table_scan).each(function() {
								graph_labels[graph_labels.length] = $(this).get(0).table_name;
								graph_data_table_seq_scan[graph_data_table_seq_scan.length] = $(this).get(0).seq_scan.value;
								graph_data_table_index_scan[graph_data_table_index_scan.length] = $(this).get(0).idx_scan.value;
							});
							
							ui.newPanel.children("div:last").append($("<div class='stats-box' style='width: 400px'><h2>Table Scans</h2><canvas id='table_scan_canvas' width='400' height='400' /><div /></div>"));
							if (json.graph.db_size.raw_data.value == 0 && json.graph.db_size.indexes.value == 0) {
								$("#table_scan_canvas").replaceWith(montaQuadroSemGrafico("Os valores do tamanho do banco estão zerados."));
							} else {
								var data = {
									labels: graph_labels,
								    datasets: [
								        {
								            label: "Index Scans",
								            fillColor: "rgba(220,220,220,0.5)",
								            strokeColor: "rgba(220,220,220,0.8)",
								            highlightFill: "rgba(220,220,220,0.75)",
								            highlightStroke: "rgba(220,220,220,1)",
								            data: graph_data_table_index_scan
								        },
								        {
								            label: "Sequential Scans",
								            fillColor: "rgba(151,187,205,0.5)",
								            strokeColor: "rgba(151,187,205,0.8)",
								            highlightFill: "rgba(151,187,205,0.75)",
								            highlightStroke: "rgba(151,187,205,1)",
								            data: graph_data_table_seq_scan
								        }
								    ]
								};
								var ctx = $("#table_scan_canvas").get(0).getContext("2d");
								new Chart(ctx).Bar(data);
								legend($("#table_scan_canvas").next("div").get(0), data);
							}
						} else {
							htmlScan = montaQuadroSemGrafico("Não foi realizado nenhum SCAN em nenhuma tabela até o momento.");
							ui.newPanel.children("div:last").append($("<div class='stats-box'><h2>Table Scans</h2>"+htmlScan+"</div>"));
						}
					}
					
					// INDEX NOT USED
					if (json.graph.indexes_not_used.errorMessage) {
						ui.newPanel.children("div:first").prepend(showMessage(json.graph.indexes_not_used.errorMessage, true));
					} else {
						if (json.graph.indexes_not_used.length) {
							var htmlTable = '<table class="stats-table">';
							htmlTable += '<thead><tr><td>Tabela</td><td>Índice</td></tr></thead>';
							$(json.graph.indexes_not_used).each(function() {
								htmlTable += '<tbody><tr><td>'+$(this).get(0).table_name+'</td><td>'+$(this).get(0).index_name+'</td></tr></tbody>';
							});
							htmlTable += '</table>';
						} else {
							htmlTable = montaQuadroSemGrafico("Não existe registro de índices não utilizados até o momento.");
						}
						ui.newPanel.children("div:last").append($("<div class='stats-box' style='width: 400px'><h2>Índices não utilizados</h2>"+htmlTable+"</div>"));
					}
					
					// DEADLOCKS, CONFLICTS
					if (json.graph.db_deadlocks.errorMessage && json.graph.db_conflicts.errorMessage){
						ui.newPanel.children("div:first").prepend(showMessage(json.graph.db_deadlocks.errorMessage, true));
						ui.newPanel.children("div:first").prepend(showMessage(json.graph.db_conflicts.errorMessage, true));
					} else {
						var htmlTable = '<table class="stats-table">';
						htmlTable += '<thead><tr><td>Conflitos</td><td>Quantidade</td></tr></thead>';
						if (json.graph.db_deadlocks.errorMessage)
							ui.newPanel.children("div:first").prepend(showMessage(json.graph.db_deadlocks.errorMessage, true));
						else {
							htmlTable += '<tbody><tr><td>Deadlocks</td><td>';
							htmlTable += json.graph.db_deadlocks.value;
						}	
						htmlTable += '</td></tr>';
						if (json.graph.db_conflicts.errorMessage)
							ui.newPanel.children("div:first").prepend(showMessage(json.graph.db_conflicts.errorMessage, true));
						else {
							htmlTable += '<tr><td>Conflitos</td><td>';
							htmlTable += json.graph.db_conflicts.value;
						}
						htmlTable += '</td></tr></tbody>';
						htmlTable += '</table>';
						ui.newPanel.children("div:last").append($("<div class='stats-box' style='width:300px;height:auto;'><h2>Deadlocks/Conflicts ("+json.db_name+")</h2>"+htmlTable+"</div>"));
					}
					
					// CHECKPOINTS
					if (json.graph.checkpoints.errorMessage) {
						ui.newPanel.children("div:first").prepend(showMessage(json.graph.checkpoints.errorMessage, true));
					} else {
						ui.newPanel.children("div:last").append($("<div class='stats-box' style='width:200px;height:auto;'><h2>Checkpoints</h2><canvas id='checkpoints_canvas' width='230' height='200' /><div /></div>"));
						if (json.graph.checkpoints.buffer.value == 0 && json.graph.checkpoints.backend.value == 0) {
							$("#checkpoints_canvas").replaceWith(montaQuadroSemGrafico("Os valores de checkpoint do banco estão zerados."));
						} else {
							var pieData = [{value: parseInt(json.graph.checkpoints.buffer.value), color: "#46BFBD", highlight: "#5AD3D1", label: "Buffer"},
							               {value: parseInt(json.graph.checkpoints.backend.value), color:"#F7464A", highlight: "#FF5A5E", label: "Backend"}];
							var ctx = $("#checkpoints_canvas").get(0).getContext("2d");
							new Chart(ctx).Pie(pieData);
							legend($("#checkpoints_canvas").next("div").get(0), pieData);
						}
					}
					
					// FUNCTIONS
					if (json.graph.db_functions.errorMessage) {
						ui.newPanel.children("div:first").prepend(showMessage(json.graph.db_functions.errorMessage, true));
					} else {
						var htmlTable = '';
						if (json.graph.db_functions.length) {
							htmlTable += '<table class="stats-table">';
							htmlTable += '<thead><tr><td>Schema</td><td>Nome da Função</td><td>Chamadas</td><td>Tempo Total</td></tr></thead>';
							$(json.graph.db_functions).each(function() {
								htmlTable += '<tbody><tr><td>'+$(this).get(0).schema_name+'</td><td>'+$(this).get(0).function_name+'</td><td>'+$(this).get(0).calls+'</td><td>'+$(this).get(0).total_time+'</td></tr></tbody>';
							});
							htmlTable += '</table>';
						} else {
							htmlTable = montaQuadroSemGrafico("Nenhuma função foi executada até o momento.");
						}
						ui.newPanel.children("div:last").append($("<div class='stats-box' style='width:450px;height:auto;'><h2>Execução de Funções</h2>"+htmlTable+"</div>"));
					}
					
					// VACCUM e ANALYZE
					if (json.graph.vacuum_analyze.errorMessage) {
						ui.newPanel.children("div:first").prepend(showMessage(json.graph.vacuum_analyze.errorMessage, true));
					} else {
						var htmlTable = '';
						if (json.graph.vacuum_analyze.length) {
							htmlTable += '<table class="stats-table" style="display:none;font-size:14px">';
							htmlTable += '<thead><tr><td>Tabela</td><td>Último Vacuum</td><td>Último Autovacuum</td>';
							htmlTable += '<td>Último Analyze</td><td>Último Autoanalyze</td><td>Vacuum</td>';
							htmlTable += '<td>Autovacuum</td><td>Analyze</td><td>Autoanalyze</td><td>Tamanho</td></tr></thead>';
							$(json.graph.vacuum_analyze).each(function() {
								htmlTable += '<tbody><tr><td title="Nome da tabela">'+$(this).get(0).table_name+'</td>';
								htmlTable += '<td title="Último Vacuum executado em '+$(this).get(0).table_name+'">'+$(this).get(0).last_vacuum+'</td>';
								htmlTable += '<td title="Último Autovacuum executado em '+$(this).get(0).table_name+'">'+$(this).get(0).last_autovacuum+'</td>';
								htmlTable += '<td title="Último Analyze executado em '+$(this).get(0).table_name+'">'+$(this).get(0).last_analyze+'</td>';
								htmlTable += '<td title="Último Autoanalyze executado em '+$(this).get(0).table_name+'">'+$(this).get(0).last_autoanalyze+'</td>';
								htmlTable += '<td title="Quantidade de Vacuum em '+$(this).get(0).table_name+'">'+$(this).get(0).vacuum_count+'</td>';
								htmlTable += '<td title="Quantidade de Autovacuum em '+$(this).get(0).table_name+'">'+$(this).get(0).autovacuum_count+'</td>';
								htmlTable += '<td title="Quantidade de Analyze em '+$(this).get(0).table_name+'">'+$(this).get(0).analyze_count+'</td>';
								htmlTable += '<td title="Quantidade de Autoanalyze em '+$(this).get(0).table_name+'">'+$(this).get(0).autoanalyze_count+'</td>';
								htmlTable += '<td title="Tamanho da tabela '+$(this).get(0).table_name+' em kB">'+$(this).get(0).table_size+'</td>';
								htmlTable += '</tr></tbody>';
							});
							htmlTable += '</table>';
							ui.newPanel.children("div:last").append($("<div class='stats-box' style='width:auto; height:auto;'><h2 class='vacuum_analyze_title' style='text-align: left;'>Vacuum e Analyze por tabela (clique para exibir/esconder)</h2>"+htmlTable+"</div>"));
							$(".vacuum_analyze_title").on("click", function() {
								$($(this).next("table")).fadeToggle();
							});
						} else {
							htmlTable = montaQuadroSemGrafico("Até o momento não foram realizadas operações de VACUUM ou ANALYZE nas tabelas do banco "+json.db_name+".");
							ui.newPanel.children("div:last").append($("<div class='stats-box' style='width:230px;height:auto;'><h2 id='vacuum_analyze_title'>Vacuum e Analyze por tabela</h2>"+htmlTable+"</div>"));
						}
						$(document).tooltip();
					}
					
					// exibe mensagens de erro
					ui.newPanel.find('.message').fadeToggle('slow','linear');
				}
			})
			.fail(function(jqxhr, textStatus, error) {
				var err = textStatus + ", " + error;
				console.log("Request Failed: " + err );
			});
			
		}
	});
	
});