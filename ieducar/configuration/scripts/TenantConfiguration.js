var tenantController = "TenantController.php";

function defineSelectBehavior() {
	$("body").on('change', 'select', function() {
        var selectedOption = $(this).find("option:selected");
        var keyLabel = selectedOption.text();
        if (keyLabel != $(this).find("option:first").text() &&
        		!$(this).prev("table").find('tr td span').filter(function(){ 
						return $(this).text() == keyLabel; }).length) {
			var removeButton = '<input type="button" class="ui-button-text" value="- remover campo" />';
	        var item = '<tr><td><span>'+keyLabel+'</span></td><td><input type="text" value="" class="tenant-data" />'+removeButton+'</td></tr>';
			$(this).prev("table").append(item);
			selectedOption.remove();
			$("input[type='button']").button();
        }
    });
}

function defineSaveConfigBehavior() {
	$("body").on('click', '.btn-save-config', function() {

		$('#info_message').remove();
		$('#error_message').remove();
		
		var tenantName = $(this).parent().prev().text();
		tenantName = tenantName.substr(1,tenantName.indexOf(' ')-1);
		var configData = 'update_tenant=["'+tenantName+'",{';
		var tableLines = $(this).parent().find("tr");
		configData += getTenantConfiguration(tableLines) + '}]';
		var div = $(this).parent();

		if (!filledRequiredFields(false, tableLines)) {
			div.prepend($(showMessage("Todos os campos obrigatórios devem ser preenchidos.", true)));
			$('#error_message').fadeToggle('slow','linear').delay(3000).fadeToggle('slow','linear');
			return false;
		}
		
		$.ajax({url : tenantController, type : "POST", data : configData, dataType : "json"})
		.done(function(json) {
			var tenantName = json.tenantName;
			var html = json.html;
			var errorMessage = json.errorMessage;

			$('#info_message').remove();
			$('#error_message').remove();

			if (errorMessage) {
				div.prepend($(showMessage(json.message, errorMessage)));
				$('#error_message').fadeToggle('slow','linear').delay(3000).fadeToggle('slow','linear');
			} else {
				$('#accordion h3:contains("'+tenantName+'")').text($(html).closest('h3').text());
				div.prepend($(showMessage("Configuração salva com sucesso.", false)));
				$('#info_message').fadeToggle('slow','linear').delay(3000).fadeToggle('slow','linear');
			}
		})
		.fail(function(jqxhr, textStatus, error) {
			var err = textStatus + ", " + error;
			console.log("Request Failed: " + err );
		});
	});
}

function defineNewTenantBehavior() {
	$("body").on('click', '#btn_new_tenant_add', function() {
    	
    	
		var tenantName = $("#new_tenant_config span input");
		var tableLines = $("#new_tenant_config table tr");

		$('#info_message').remove();
		$('#error_message').remove();
		
		if (!filledRequiredFields(tenantName, tableLines)) {
			$("#new_tenant_config div").prepend($(showMessage("Todos os campos obrigatórios devem ser preenchidos.", true)));
			$('#error_message').fadeToggle('slow','linear').delay(3000).fadeToggle('slow','linear');
			return false;
		}
		
		var configData = 'new_tenant=["'+tenantName.val()+'.ieducar",{';
		configData += getTenantConfiguration(tableLines) + '}]';
		
		$.ajax({url : tenantController, type : "POST", data : configData, dataType : "json"})
		.done(function(json) {
			var message = json.message;
			if (message) {
				$("#error_message").remove();
				$("#info_message").remove();
				$("#new_tenant_config div").prepend($(showMessage(message, true)));
				$('#error_message').fadeToggle('slow','linear').delay(3000).fadeToggle('slow','linear');
			} else {
				$("#box_new_tenant").hide();
				var tenantConfig = json.html;
				var tenantName = json.tenantName;
				var tenantTitle = json.tenantTitle;
				if (tenantName && tenantConfig && tenantTitle) {
					var tenantDivs = $("#accordion div").not("#info_message").not("#error_message");
					if (tenantDivs.length) {
						tenantDivs.each(function(index) {
							divTitle = $(this).prev("h3").text();
							//se é menor que o primeiro
							if ($(this).is(tenantDivs.first()) && tenantName < divTitle) {
									$(tenantConfig).insertBefore($(this).prev("h3"));
									return false;
							} else {
								// ou se é maior que o atual e menor que o próximo ou é o último
								nextDivTitle = $(this).next("h3").text();
								if (tenantName > divTitle && ((tenantName < nextDivTitle) || $(this).is(tenantDivs.last()))) {
									$(tenantConfig).insertAfter($(this));
									return false;
								}
							}
						});
					} else {
						$("#accordion").prepend(tenantConfig);
					}
					//aplica estilos e exibe o novo tenant criado com mensagem de sucesso
					$("input[type='button']").button();
					$("#accordion").accordion("refresh");
					$('#info_message').remove();
					$("#error_message").remove();
					$("#accordion h3:contains('"+tenantName+"')").next().prepend($(showMessage("Novo tenant criado com sucesso.", false)));
					var index = $("#accordion").find(":contains('"+tenantName+"')").index();
					if (index >= 0)
						$("#accordion").accordion("option","active",index/2);
					$("body").scrollTop($('#accordion').find(':contains("'+tenantName+'")').position().top);
					$('#info_message').fadeToggle('slow','linear').delay(3000).fadeToggle('slow','linear');
					//limpa configurações do form #new_tenant_box
					$("#box_new_tenant input[type='text']").each(function() { 
						$(this).val('');
					});
				} else {
					alert("Ocorreu um erro inesperado. Para verificar se a nova configuração foi incluída, recarregue a página.");
				}
			}
		})
		.fail(function(jqxhr, textStatus, error) {
			var err = textStatus + ", " + error;
			console.log("Request Failed: " + err );
		});
    });
}

function defineRemoveConfigurationBehavior() {
	$("body").on("click", ".tenant-config table input[type='button']", function() {
		var config = $(this).closest("tr").find("td span");
		var select = $(this).closest("table").next().find("option");
		//insere config no select de volta no lugar correto
		select.each(function() {
			if (((config.text() > $(this).text() || $(this).is(select.first())) && config.text() < $(this).next().text()) ||
					config.text() > $(this).text() && $(this).is(select.last())) {
				$("<option>"+config.text()+"</option>").insertAfter($(this));
				config.closest("tr").remove();
				return false;
			}
		});
	});
}

function getTenantConfiguration(tableLines) {
	var configLength = tableLines.length;
	var configData = '';
	var key = '';
	var value = '';
	tableLines.each(function(i) {
		key = $(this).find("td:first").text();
		value = $(this).find("td:last input").val().replace(/['"]+/g, '');
		configData += '"'+key+'":';
		configData += '"'+value+'"';
		if (i+1 != configLength)
			configData += ',';
	});
	return configData;
}

function showMessage(message, error) {
	var label = "";
	var css = new Array();
	var divId = "";
	if (error) {
		label = "Erro:";
		css = ["ui-state-error", "ui-icon-alert"];
		divId = "error_message";
	} else {
		label = "Informação:";
		css = ["ui-state-highlight", "ui-icon-info"];
		divId = "info_message";
	}
	var html = '<div id="'+divId+'" class="ui-widget message" style="display: none;">';
	html += '<div class="'+css[0]+' ui-corner-all message-container">';
	html += '<p><span class="ui-icon '+css[1]+' message-text"></span>';
	html += '<strong>'+label+'</strong> '+message+'</p>';
	html += '</div>';
	html += '</div>';
	return html;
}

function defineRemoveTenantBehavior() {
	$('#accordion').on('click', '.btn-remove-tenant', function() {
		var btnRemoveTenant = $(this);
		var tenantName = btnRemoveTenant.parent().prev("h3").text(); 
		if (confirm('Você tem certeza que deseja remover a configuração do Tenant - "'+tenantName+'"?')) {
			tenantName = tenantName.substr(1,tenantName.indexOf(' ')-1);
			var configData = 'remove_tenant=["'+tenantName+'"]';
			$('#info_message').remove();
			$('#error_message').remove();
			
			$.ajax({url : tenantController, type : "POST", data : configData, dataType : "json"})
			.done(function(json) {
				var message = json.message;
				var errorMessage = json.errorMessage;

				if (errorMessage) {
					btnRemoveTenant.parent().prepend($(showMessage(message, errorMessage)));
					$('#error_message').fadeToggle('slow','linear').delay(3000).fadeToggle('slow','linear');
				} else {
					$(showMessage(message)).insertAfter($(".demoHeaders"));
					$("#accordion").accordion({heightStyle: "content",active: false,collapsible: true});
					$('body').scrollTop($('#info_message').position().top);
					$('#info_message').fadeToggle('slow','linear').delay(3000).fadeToggle('slow','linear');
					btnRemoveTenant.parent().prev('h3').fadeOut('slow', function(){ $(this).next('div').remove(); $(this).remove(); });
				}
			})
			.fail(function(jqxhr, textStatus, error) {
				var err = textStatus + ", " + error;
				console.log("Request Failed: " + err );
			});	
		}
	});
}

function filledRequiredFields(tenantName, tableLines) {
	var filledAll = true;
	if (tenantName) {
		if (tenantName.val() == '') {
			tenantName.parent().next().removeClass('hidden-field');
			filledAll = false;
		} else {
			tenantName.parent().next().addClass('hidden-field');
		}
	}
	tableLines.each(function() {
		var input = $(this).find("td:last input");
		if (input.attr("required") == "required") {
			if (input.val() == '') {
				filledAll = false;
				input.next().removeClass('hidden-field');
			} else 
				input.next().addClass('hidden-field');
		}
	});
	return filledAll;
}

$(document).ready(function(){

	var newTenantBox = $("#box_new_tenant"); 
			
    $("#btn_new_tenant").click(function(){
		newTenantBox.show();
		newTenantBox.find("input[type='text']:first").focus();
    });

    $("#btn_new_tenant_cancel").click(function(){
        newTenantBox.hide();
    });

    $("#accordion").accordion({
		heightStyle: "content",
		active: false,
		collapsible: true
    });
	
    defineNewTenantBehavior();
    defineSaveConfigBehavior();
	defineSelectBehavior();
	defineRemoveConfigurationBehavior();
	defineRemoveTenantBehavior();

	$('input[type="button"]').button();

	$('body').show();
});