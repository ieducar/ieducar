
// before page is ready

function hrefToCreateParent(parentType) {
  return '/intranet/atendidos_cad.php?parent_type=' + parentType;
}

function hrefToEditParent(parentType) {
  var id = $j(buildId(parentType + '_id')).val();
  return hrefToCreateParent(parentType) + '&cod_pessoa_fj=' + id;
}

var pessoaId      = $j('#cod_pessoa_fj').val();
var $form         = $j('#formcadastro');
var $submitButton = $j('#btn_enviar');
var $cpfField     = $j('#id_federal');
var $cpfNotice    = $j('<span>').html('')
                                .addClass('error resource-notice')
                                .hide()
                                .width($j('#nm_pessoa').outerWidth() - 12)
                                .appendTo($cpfField.parent());


// links pessoa pai, mãe

var $paiNomeField = $j('#pai_nome');
var $paiIdField   = $j('#pai_id');

var $maeNomeField = $j('#mae_nome');
var $maeIdField   = $j('#mae_id');


var $pessoaPaiActionBar  = $j('<span>').html('')
                                       .addClass('pessoa-links pessoa-pai-links')
                                       .width($paiNomeField.outerWidth() - 12)
                                       .appendTo($paiNomeField.parent());

var $pessoaMaeActionBar = $pessoaPaiActionBar.clone()
                                         .removeClass('pessoa-pai-links')
                                         .addClass('pessoa-mae-links')
                                         .appendTo($maeNomeField.parent());

var $linkToCreatePessoaPai = $j('<a>').addClass('cadastrar-pessoa-pai decorated')
                                      .attr('href', hrefToCreateParent('pai'))
                                      .attr('target', '_blank')
                                      .html('Cadastrar pessoa')
                                      .appendTo($pessoaPaiActionBar);

var $linkToEditPessoaPai = $j('<a>').hide()
                                    .addClass('editar-pessoa-pai decorated')
                                    .attr('href', hrefToEditParent('pai'))
                                    .attr('target', '_blank')
                                    .html('Editar pessoa')
                                    .appendTo($pessoaPaiActionBar);

var $linkToCreatePessoaMae = $linkToCreatePessoaPai.clone()
                                                   .removeClass('cadastrar-pessoa-pai')
                                                   .addClass('cadastrar-pessoa-mae')
                                                   .attr('href', hrefToCreateParent('mae'))
                                                   .appendTo($pessoaMaeActionBar);

var $linkToEditPessoaMae = $linkToEditPessoaPai.clone()
                                               .removeClass('editar-pessoa-pai')
                                               .addClass('editar-pessoa-mae')
                                               .attr('href', hrefToEditParent('mae'))
                                               .appendTo($pessoaMaeActionBar);

var handleGetPersonByCpf = function(dataResponse) {
  handleMessages(dataResponse.msgs);
  $cpfNotice.hide();

  var pessoaId = dataResponse.id;

  if (pessoaId && pessoaId != $j('#cod_pessoa_fj').val()) {
    $cpfNotice.html(stringUtils.toUtf8('CPF já utilizado pela pessoa código ' + pessoaId + ', ')).slideDown('fast');

    $j('<a>').addClass('decorated')
             .attr('href', '/intranet/atendidos_cad.php?cod_pessoa_fj=' + pessoaId)
             .attr('target', '_blank')
             .html('acessar cadastro.')
             .appendTo($cpfNotice);

    $j('body,html').animate({ scrollTop: $j('body').offset().top }, 'fast');
  }

  else if ($j(document).data('submit_form_after_ajax_validation'))
    formUtils.submit();
}


var getPersonByCpf = function(cpf) {
  var options = {
    url      : getResourceUrlBuilder.buildUrl('/module/Api/pessoa', 'pessoa'),
    dataType : 'json',
    data     : { cpf : cpf },
    success  : handleGetPersonByCpf,

    // forçado requisições sincronas, evitando erro com requisições ainda não concluidas,
    // como no caso, onde o usuário pressiona cancelar por exemplo.
    async    : false
  };

  getResource(options);
}


// hide or show #pais_origem_nome by #tipo_nacionalidade
var checkTipoNacionalidade = function() {
  if ($j.inArray($j('#tipo_nacionalidade').val(), ['2', '3']) > -1)
    $j('#pais_origem_nome').show();
  else
    $j('#pais_origem_nome').hide();
}

// hide or show *certidao* fields, by #tipo_certidao_civil
var checkTipoCertidaoCivil = function() {
  var $certidaoCivilFields     = $j('#termo_certidao_civil, #livro_certidao_civil, #folha_certidao_civil');
  var $certidaoNascimentoField = $j('#certidao_nascimento');
  var tipoCertidaoCivil        = $j('#tipo_certidao_civil').val();

  $certidaoCivilFields.hide();
  $certidaoNascimentoField.hide();

  if ($j.inArray(tipoCertidaoCivil, ['91', '92']) > -1) {
    $certidaoCivilFields.show();
    $j('#tr_tipo_certidao_civil td:first span').html(stringUtils.toUtf8('Tipo certidão civil / Termo / Livro / Folha'));
  }

  else if (tipoCertidaoCivil == 'certidao_nascimento_novo_formato') {
    $certidaoNascimentoField.show();
    $j('#tr_tipo_certidao_civil td:first span').html(stringUtils.toUtf8('Tipo certidão civil / Certidão nascimento'));
  }

}

var validatesCpf = function() {
  var valid = true;
  var cpf   = $cpfField.val();

  $cpfNotice.hide();

  if (cpf && ! validationUtils.validatesCpf(cpf)) {
    $cpfNotice.html(stringUtils.toUtf8('O CPF informado é inválido')).slideDown('fast');

    // não usado $cpfField.focus(), pois isto prenderia o usuário a página,
    // caso o mesmo tenha informado um cpf invalido e clique em cancelar
    $j('body,html').animate({ scrollTop: $j('body').offset().top }, 'fast');

    valid = false;
  }

  return valid;
}


var validatesUniquenessOfCpf = function() {
  var cpf = $cpfField.val();

  $cpfNotice.hide();

  if(cpf && validatesCpf())
    getPersonByCpf(cpf);
}


var submitForm = function(event) {
  if ($cpfField.val()) {
    $j(document).data('submit_form_after_ajax_validation', true);
    validatesUniquenessOfCpf();
  }

  else
    formUtils.submit();
}

// when page is ready

$j(document).ready(function() {
  $cpfField.focus();

  changeVisibilityOfLinksToPessoaPai();
  changeVisibilityOfLinksToPessoaMae();

  // style fixup

  // agrupado zebra por tipo documento, branco => .formlttd, colorido => .formmdtd

  $j('#tr_uf_emissao_certidao_civil td').removeClass('formmdtd');
  $j('#tr_carteira_trabalho td').removeClass('formlttd').addClass('formmdtd');

  // bind events

  checkTipoNacionalidade();
  $j('#tipo_nacionalidade').change(checkTipoNacionalidade);

  checkTipoCertidaoCivil();
  $j('#tipo_certidao_civil').change(checkTipoCertidaoCivil);

  $cpfField.focusout(function() {
    $j(document).removeData('submit_form_after_ajax_validation');
    validatesUniquenessOfCpf();
  });


  // ao clicar na lupa de pesquisa de cep, move página para cima,
  // pois (exceto no ie), a popup de pesquisa é exibida no topo da página.
  if (! $j.browser.msie) {
    $j('#cep_').siblings('img').click(function(){
      $j('body,html').animate({ scrollTop: $j('body').offset().top }, 'fast');
    });
  }

  $submitButton.removeAttr('onclick');
  $submitButton.click(submitForm);

}); // ready


// pessoa links callbacks

var changeVisibilityOfLinksToPessoaParent = function(parentType) {
  var $nomeField  = $j(buildId(parentType + '_nome'));
  var $idField    = $j(buildId(parentType + '_id'));
  var $linkToEdit = $j('.pessoa-' + parentType + '-links .editar-pessoa-' + parentType);

  if($nomeField.val() && $idField.val()) {
    $linkToEdit.attr('href', hrefToEditParent(parentType));
    $linkToEdit.show().css('display', 'inline');
  }
  else {
    $nomeField.val('')
    $idField.val('');

    $linkToEdit.hide();
  }
}

var changeVisibilityOfLinksToPessoaPai = function() {
  changeVisibilityOfLinksToPessoaParent('pai');
}

var changeVisibilityOfLinksToPessoaMae = function() {
  changeVisibilityOfLinksToPessoaParent('mae');
}


// children callbacks

var afterSetSearchFields = function() {
  $j('body,html').animate({ scrollTop: $j('#btn_enviar').offset().top }, 'fast');
  $j('#complemento').focus();
};

var afterUnsetSearchFields = function() {
  $j('body,html').animate({ scrollTop: $j('#btn_enviar').offset().top }, 'fast');
  $j('#cep_').focus();
};

function afterChangePessoa(targetWindow, parentType, parentId, parentName) {
  targetWindow.close();

  var $idField   = $j(buildId(parentType + '_id'));
  var $nomeField = $j(buildId(parentType + '_nome'));

  // timeout para usuario perceber mudança
  window.setTimeout(function() {
    messageUtils.success('Pessoa alterada com sucesso', $nomeField);

    $idField.val(parentId);
    $nomeField.val(parentId + ' - ' +parentName);
    $nomeField.focus();

    changeVisibilityOfLinksToPessoaParent(parentType);

  }, 500);
}


// simple search options

var simpleSearchPaiOptions = {
  autocompleteOptions : { close  : changeVisibilityOfLinksToPessoaPai }
};

var simpleSearchMaeOptions = {
  autocompleteOptions : { close : changeVisibilityOfLinksToPessoaMae }
};

$paiNomeField.focusout(changeVisibilityOfLinksToPessoaPai);
$maeNomeField.focusout(changeVisibilityOfLinksToPessoaMae);