<?php

/**
 * i-Educar - Sistema de gest�o escolar
 *
 * Copyright (C) 2006  Prefeitura Municipal de Itaja�
 *                     <ctima@itajai.sc.gov.br>
 *
 * Este programa � software livre; voc� pode redistribu�-lo e/ou modific�-lo
 * sob os termos da Licen�a P�blica Geral GNU conforme publicada pela Free
 * Software Foundation; tanto a vers�o 2 da Licen�a, como (a seu crit�rio)
 * qualquer vers�o posterior.
 *
 * Este programa � distribu�do na expectativa de que seja �til, por�m, SEM
 * NENHUMA GARANTIA; nem mesmo a garantia impl�cita de COMERCIABILIDADE OU
 * ADEQUA��O A UMA FINALIDADE ESPEC�FICA. Consulte a Licen�a P�blica Geral
 * do GNU para mais detalhes.
 *
 * Voc� deve ter recebido uma c�pia da Licen�a P�blica Geral do GNU junto
 * com este programa; se n�o, escreva para a Free Software Foundation, Inc., no
 * endere�o 59 Temple Street, Suite 330, Boston, MA 02111-1307 USA.
 *
 * @author    Prefeitura Municipal de Itaja� <ctima@itajai.sc.gov.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   iEd_Pmieducar
 * @since     Arquivo dispon�vel desde a vers�o 1.0.0
 * @version   $Id$
 */

require_once 'include/clsBase.inc.php';
require_once 'include/clsCadastro.inc.php';
require_once 'include/clsBanco.inc.php';
require_once 'include/pmieducar/geral.inc.php';
require_once 'include/clsPDF.inc.php';

/**
 * clsIndexBase class.
 *
 * @author    Prefeitura Municipal de Itaja� <ctima@itajai.sc.gov.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   iEd_Pmieducar
 * @since     Classe dispon�vel desde a vers�o 1.0.0
 * @version   @@package_version@@
 */
class clsIndexBase extends clsBase
{
  function Formular()
  {
    $this->SetTitulo($this->_instituicao . ' i-Educar - Di�rio de Classe - Avalia��es');
    $this->processoAp = 670;
  }
}

/**
 * indice class.
 *
 * @author    Prefeitura Municipal de Itaja� <ctima@itajai.sc.gov.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   iEd_Pmieducar
 * @since     Classe dispon�vel desde a vers�o 1.0.0
 * @version   @@package_version@@
 */
class indice extends clsCadastro
{
  var $pessoa_logada;

  var $ref_cod_instituicao;
  var $ref_cod_escola;
  var $ref_cod_serie;
  var $ref_cod_turma;

  var $ano;
  var $mes;

  var $nm_escola;
  var $nm_instituicao;
  var $ref_cod_curso;
  var $sequencial;
  var $pdf;
  var $pagina_atual = 1;
  var $total_paginas = 1;
  var $nm_professor;
  var $nm_turma;
  var $nm_serie;
  var $nm_disciplina;
  var $avaliacao_globalizada;

  var $page_y = 139;
  var $get_file;
  var $cursos = array();
  var $get_link;
  var $total;

  var $ref_cod_modulo;

  var $meses_do_ano = array(
    1  => 'JANEIRO',
    2  => 'FEVEREIRO',
    3  => 'MAR�O',
    4  => 'ABRIL',
    5  => 'MAIO',
    6  => 'JUNHO',
    7  => 'JULHO',
    8  => 'AGOSTO',
    9  => 'SETEMBRO',
    10 => 'OUTUBRO',
    11 => 'NOVEMBRO',
    12 => 'DEZEMBRO'
  );

  function Inicializar()
  {
    $retorno = 'Novo';

    @session_start();
    $this->pessoa_logada = $_SESSION['id_pessoa'];
    @session_write_close();

    return $retorno;
  }

  function Gerar()
  {
    $obj_permissoes = new clsPermissoes();
    $nivel_usuario = $obj_permissoes->nivel_acesso($this->pessoa_logada);

    @session_start();
    $this->pessoa_logada = $_SESSION['id_pessoa'];
    @session_write_close();

    if ($_POST){
      foreach ($_POST as $key => $value) {
        $this->$key = $value;
      }
    }

    $this->ano = $ano_atual = date('Y');
    $this->campoNumero('ano', 'Ano', $this->ano, 4, 4, TRUE);

    $this->campoCheck('em_branco', 'Relat�rio em branco', '');
    $this->campoNumero('numero_registros', 'N�mero de linhas', '', 3, 3);

    $get_escola             = TRUE;
    $obrigatorio            = TRUE;
    $exibe_nm_escola        = TRUE;
    $get_curso              = TRUE;
    $get_escola_curso_serie = TRUE;

    include 'include/pmieducar/educar_campo_lista.php';

    $opcoes_turma = array('' => 'Selecione');

    if (($this->ref_ref_cod_serie && $this->ref_cod_escola) || $this->ref_cod_curso) {
      $obj_turma = new clsPmieducarTurma();
      $obj_turma->setOrderby('nm_turma ASC');
      $lst_turma = $obj_turma->lista(NULL, NULL, NULL, $this->ref_ref_cod_serie,
        $this->ref_cod_escola, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL,
        NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL,
        $this->ref_cod_curso);

      if (is_array($lst_turma) && count($lst_turma)) {
        foreach ($lst_turma as $turma) {
          $opcoes_turma[$turma['cod_turma']] = $turma['nm_turma'];
        }
      }
    }

    $this->campoLista('ref_cod_turma','Turma',$opcoes_turma,$this->ref_cod_turma);
    $this->campoLista('ref_cod_modulo','M�dulo', array('' => 'Selecione'), '');

    if ($this->ref_cod_escola) {
      $this->ref_ref_cod_escola = $this->ref_cod_escola;
    }

    if ($this->get_link) {
      $this->campoRotulo('rotulo11', '-',
        sprintf('<a href="%s" id="tt" target="_blank">Baixar Relat�rio</a>', $this->get_link)
      );
    }

    $this->url_cancelar = 'educar_index.php';
    $this->nome_url_cancelar = 'Cancelar';

    $this->acao_enviar = 'acao2()';
    $this->acao_executa_submit = FALSE;
  }
}

// Instancia objeto de p�gina
$pagina = new clsIndexBase();

// Instancia objeto de conte�do
$miolo = new indice();

// Atribui o conte�do �  p�gina
$pagina->addForm($miolo);

// Gera o c�digo HTML
$pagina->MakeAll();
?>
<script type="text/javascript">
document.getElementById('ref_cod_turma').onchange = function()
{
  document.getElementById('ref_cod_modulo').length = 1;
  getModulos();
}

document.getElementById('ref_cod_escola').onchange = function()
{
  getEscolaCurso();
}

document.getElementById('ref_cod_curso').onchange = function()
{
  getEscolaCursoSerie();
  getModulos();
}

document.getElementById('ref_ref_cod_serie').onchange = function()
{
  var campoEscola = document.getElementById('ref_cod_escola').value;
  var campoSerie  = document.getElementById('ref_ref_cod_serie').value;

  var xml1 = new ajax(getTurma_XML);

  strURL = 'educar_turma_xml.php?esc=' + campoEscola + '&ser=' + campoSerie;
  xml1.envia(strURL);
}

function getTurma_XML(xml)
{
  var campoSerie = document.getElementById('ref_ref_cod_serie').value;
  var campoTurma = document.getElementById('ref_cod_turma');
  var turma = xml.getElementsByTagName('turma');

  campoTurma.length = 1;
  campoTurma.options[0] = new Option('Selecione uma Turma', '', false, false);

  for (var j = 0; j < turma.length; j++) {
    campoTurma.options[campoTurma.options.length] = new Option( turma[j].firstChild.nodeValue, turma[j].getAttribute('cod_turma'), false, false );
  }

  if (campoTurma.length == 1 && campoSerie != '') {
    campoTurma.options[0] = new Option('A s�rie n�o possui nenhuma turma', '', false, false);
  }
}

function acao2()
{
  if (!acao()) {
    return false;
  }

  showExpansivelImprimir(400, 200, '', [], 'Di�rio de Classe - Avalia��es');

  document.formcadastro.target = 'miolo_' + (DOM_divs.length - 1);

  document.formcadastro.submit();
}

function getModulos()
{
  var campoEscola = document.getElementById('ref_cod_escola').value;
  var campoCurso = document.getElementById('ref_cod_curso').value;
  var campoAno = document.getElementById('ano').value;
  var campoTurma = document.getElementById('ref_cod_turma').value;
  var xml1 = new ajax(getModulos_XML);

  strURL = 'educar_modulo_xml.php?esc=' + campoEscola + '&ano=' + campoAno + '&curso=' + campoCurso + '&turma=' + campoTurma;
  xml1.envia(strURL);
}

function getModulos_XML(xml)
{

  var modulos = xml.getElementsByTagName('ano_letivo_modulo');

  var campoEscola = document.getElementById('ref_cod_escola').value;
  var campoCurso= document.getElementById('ref_cod_curso').value;
  var campoModulo = document.getElementById('ref_cod_modulo');
  var campoAno = document.getElementById('ano').value;

  campoModulo.length = 1;
  campoModulo.options[0] = new Option('Selecione um m�dulo', '', false, false);

  for (var j = 0; j < modulos.length; j++) {
    campoModulo.options[campoModulo.options.length] = new Option(
      modulos[j].firstChild.nodeValue,
      modulos[j].getAttribute('cod_modulo') + '-' +modulos[j].getAttribute('sequencial'),
      false, false
    );
  }

  if (campoModulo.length == 1) {
    campoModulo.options[0] = new Option('O curso n�o possui m�dulos.', '', false, false);
  }
}

document.getElementById('ano').onchange = function()
{
  getModulos();
}

document.formcadastro.action = 'educar_relatorio_diario_classe_avaliacao_proc.php';
</script>