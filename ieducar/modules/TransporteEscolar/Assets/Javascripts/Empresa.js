// before page is ready

$deleteButton = $j('<input value=" Excluir " type="button" style="display: inline; margin-left: 6px;">').html('')
                              .addClass('botaolistagem').insertAfter('#btn_enviar');
                              
var $idField        = $j('#id');
var $nomeField      = $j('#pessoa_nome');

var $resourceNotice = $j('<span>').html('')
                                  .addClass('error resource-notice')
                                  .hide()
                                  .width($nomeField.outerWidth() - 12)
                                  .insertBefore($idField.parent());

var $pessoaNotice = $resourceNotice.clone()
                                   .appendTo($nomeField.parent());

// ajax

resourceOptions.handlePost = function(dataResponse) {
  $nomeField.attr('disabled', 'disabled');
  $j('.pessoa-links .cadastrar-pessoa').hide();

  if (! dataResponse.any_error_msg)
    window.setTimeout(function() { document.location = '/intranet/transporte_empresa_det.php?cod_empresa=' + resource.id(); }, 500);
  else
    $submitButton.removeAttr('disabled').val('Gravar');
}

resourceOptions.handlePut = function(dataResponse) {
  if (! dataResponse.any_error_msg)
    window.setTimeout(function() { document.location = '/intranet/transporte_empresa_det.php?cod_empresa=' + resource.id(); }, 500);
  else
    $submitButton.removeAttr('disabled').val('Gravar');
}

resourceOptions.handleGet = function(dataResponse) {
  handleMessages(dataResponse.msgs);
  $resourceNotice.hide();

  $deleteButton.removeAttr('disabled').show();

  if (dataResponse.pessoa)
    getPersonDetails(dataResponse.pessoa);

  $idField.val(dataResponse.id);
  $j('#observacao').val(dataResponse.observacao);

  $j('#pessoaj_pessoaj').val(dataResponse.pessoaj+' - '+dataResponse.pessoajnome);  
  $j('#pessoaj_id').val(dataResponse.pessoaj);  

};

var handleGetPersonDetails = function(dataResponse) {
  handleMessages(dataResponse.msgs);
  $pessoaNotice.hide();

  var alunoId = dataResponse.aluno_id;

    $j('.pessoa-links .editar-pessoa').attr('href', '/intranet/atendidos_cad.php?cod_pessoa_fj=' + dataResponse.id)
                                      .show().css('display', 'inline');

    $submitButton.removeAttr('disabled').show();
  

  $j('#pessoa_id').val(dataResponse.id);
  $nomeField.val(dataResponse.id + ' - ' + dataResponse.nome);


}

var getPersonDetails = function(personId) {
  var additionalVars = {
    id : personId
  };

  var options = {
    url      : getResourceUrlBuilder.buildUrl('/module/Api/pessoa', 'pessoa', additionalVars),
    dataType : 'json',
    data     : {},
    success  : handleGetPersonDetails
  };

  getResource(options);
}

var updatePersonDetails = function() {
  if ($j('#pessoa_nome').val() && $j('#pessoa_id').val())
    getPersonDetails($j('#pessoa_id').val());
  else
    clearPersonDetails();
}

var clearPersonDetails = function() {
  $j('#pessoa_id').val('');
  $j('.pessoa-links .editar-pessoa').hide();
}

// simple search options

var simpleSearchPessoaOptions = {
  autocompleteOptions : { close : updatePersonDetails /*, change : updatePersonDetails*/ }
};


// children callbacks

function afterChangePessoa(targetWindow, pessoaId) {
  targetWindow.close();

  // timeout para usuario perceber mudança
  window.setTimeout(function() {
    messageUtils.success('Pessoa alterada com sucesso', $nomeField);

    $j('#pessoa_id').val(pessoaId);
    getPersonDetails(pessoaId);

    if ($nomeField.is(':active'))
      $nomeField.focus();

  }, 500);
}


// when page is ready

(function($) {
  $(document).ready(function() {

    // pessoa

    var $pessoaActionBar  = $j('<span>').html('')
                                        .addClass('pessoa-links')
                                        .width($nomeField.outerWidth() - 12)
                                        .appendTo($nomeField.parent());

    $j('<a>').hide()
             .addClass('cadastrar-pessoa decorated')
             .attr('href', '/intranet/atendidos_cad.php')
             .attr('target', '_blank')
             .html('Cadastrar pessoa')
             .appendTo($pessoaActionBar);

    $j('<a>').hide()
             .addClass('editar-pessoa decorated')
             .attr('href', '#')
             .attr('target', '_blank')
             .html('Editar pessoa')
             .appendTo($pessoaActionBar);

    if (resource.isNew()) {
      $nomeField.focus();
      $j('.pessoa-links .cadastrar-pessoa').show().css('display', 'inline');
    }
    else
      $nomeField.attr('disabled', 'disabled');


  }); // ready
})(jQuery);
