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
    $this->SetTitulo($this->_instituicao . ' i-Educar - Boletim');
    $this->processoAp = 664;
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
  var $ano;

  function Inicializar()
  {
    $retorno = 'Novo';

    @session_start();
    $this->pessoa_logada = $_SESSION['id_pessoa'];
    @session_write_close();

    $obj_permissoes = new clsPermissoes();

    return $retorno;
  }

  function Gerar()
  {
    $obj_permissoes = new clsPermissoes();
    $nivel_usuario = $obj_permissoes->nivel_acesso($this->pessoa_logada);

    $this->ano = $ano_atual = date('Y');

    $this->campoNumero('ano', 'Ano', $this->ano, 4, 4, TRUE);

    $this->campoCheck('em_branco', 'Relat�rio em branco', '');
    $this->campoNumero('numero_registros', 'N�mero de linhas', '', 3, 3);

    $get_escola              = TRUE;
    $exibe_nm_escola         = TRUE;
    $get_curso               = TRUE;
    $get_escola_curso_serie  = TRUE;
    $escola_obrigatorio      = FALSE;
    $curso_obrigatorio       = FALSE;
    $instituicao_obrigatorio = TRUE;

    include 'include/pmieducar/educar_campo_lista.php';

    $this->campoLista('ref_cod_turma', 'Turma', array('' => 'Selecione'), '', '',
      FALSE, '', '', FALSE, FALSE);

    $this->campoLista('ref_cod_matricula', 'Aluno', array('' => 'Selecione'), '',
      '', FALSE, 'Campo n�o obrigat�rio', '', FALSE, FALSE);

    $this->url_cancelar      = 'educar_index.php';
    $this->nome_url_cancelar = 'Cancelar';

    $this->acao_enviar         = 'acao2()';
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
document.getElementById('ref_cod_escola').onchange = function()
{
  setMatVisibility();
  getEscolaCurso();
  var campoTurma = document.getElementById('ref_cod_turma');
  getTurmaCurso();
}

document.getElementById('ref_cod_curso').onchange = function()
{
  getEscolaCursoSerie();
  getTurmaCurso();
}

document.getElementById('ano').onkeyup = function()
{
  setMatVisibility();
  getAluno();
}

document.getElementById('ref_ref_cod_serie').onchange = function()
{
  var campoEscola = document.getElementById('ref_cod_escola').value;
  var campoSerie = document.getElementById('ref_ref_cod_serie').value;

  var xml1 = new ajax(getTurma_XML);
  strURL = 'educar_turma_xml.php?esc=' + campoEscola + '&ser=' + campoSerie;
  xml1.envia(strURL);
}

function getTurma_XML(xml)
{
  var campoSerie = document.getElementById('ref_ref_cod_serie').value;
  var campoTurma = document.getElementById('ref_cod_turma');
  var turma      = xml.getElementsByTagName('turma');

  campoTurma.length = 1;
  campoTurma.options[0] = new Option('Selecione uma Turma', '', false, false);

  for (var j = 0; j < turma.length; j++) {
    campoTurma.options[campoTurma.options.length] = new Option(
      turma[j].firstChild.nodeValue, turma[j].getAttribute('cod_turma'), false, false
    );
  }

  if (campoTurma.length == 1 && campoSerie != '') {
    campoTurma.options[0] = new Option('A s�rie n�o possui nenhuma turma', '', false, false);
  }

  setMatVisibility();
}

function getTurmaCurso()
{
  var campoCurso = document.getElementById('ref_cod_curso').value;
  var campoInstituicao = document.getElementById('ref_cod_instituicao').value;

  var xml1 = new ajax(getTurmaCurso_XML);
  strURL = 'educar_turma_xml.php?ins=' + campoInstituicao + '&cur=' + campoCurso;

  xml1.envia(strURL);
}

function getTurmaCurso_XML(xml)
{
  var turma = xml.getElementsByTagName('turma');
  var campoTurma = document.getElementById('ref_cod_turma');
  var campoCurso = document.getElementById('ref_cod_curso');

  campoTurma.length = 1;
  campoTurma.options[0] = new Option( 'Selecione uma Turma', '', false, false );

  for (var j = 0; j < turma.length; j++) {
    campoTurma.options[campoTurma.options.length] = new Option(
      turma[j].firstChild.nodeValue, turma[j].getAttribute('cod_turma'), false, false
    );
  }

  setMatVisibility();
}


document.getElementById('ref_cod_turma').onchange = function()
{
  getAluno();
  var This = this;
  setMatVisibility();

}

function setMatVisibility()
{
  var campoTurma = document.getElementById('ref_cod_turma');
  var campoAluno = document.getElementById('ref_cod_matricula');

  campoAluno.length = 1;

  if (campoTurma.value == '') {
    setVisibility('tr_ref_cod_matricula', false);
    setVisibility('ref_cod_matricula', false);
  }
  else {
    setVisibility('tr_ref_cod_matricula', true);
    setVisibility('ref_cod_matricula', true);
  }
}

function getAluno()
{
  var campoTurma = document.getElementById('ref_cod_turma').value;
  var campoAno = document.getElementById('ano').value;

  var xml1 = new ajax(getAluno_XML);
  strURL = 'educar_matricula_turma_xml.php?tur=' + campoTurma + '&ano=' + campoAno;

  xml1.envia(strURL);
}

function getAluno_XML(xml)
{
  var aluno = xml.getElementsByTagName('matricula');
  var campoTurma = document.getElementById('ref_cod_turma');
  var campoAluno = document.getElementById('ref_cod_matricula');

  campoAluno.length = 1;

  for (var j = 0; j < aluno.length; j++) {
    campoAluno.options[campoAluno.options.length] = new Option(
      aluno[j].firstChild.nodeValue, aluno[j].getAttribute('cod_matricula'),
      false, false
    );
  }

  if (campoTurma.length == 1 && campoCurso != '') {
    campoTurma.options[0] = new Option('O curso n�o possui nenhuma turma', '', false, false);
  }
}

setVisibility('tr_ref_cod_matricula',false);
var func = function()
{
  document.getElementById('btn_enviar').disabled= false;
};

if (window.addEventListener) {
  // mozilla
  document.getElementById('btn_enviar').addEventListener('click',func,false);
}
else if (window.attachEvent) {
  // ie
  document.getElementById('btn_enviar').attachEvent('onclick',func);
}

function acao2()
{
  if (!acao()) {
    return;
  }
  else {
    if (!(/[^ ]/.test(document.getElementById('ref_cod_instituicao').value))) {
      mudaClassName('formdestaque', 'obrigatorio');
      document.getElementById('ref_cod_instituicao').className = 'formdestaque';
      alert('Preencha o campo "Institui��o" corretamente!');
      document.getElementById('ref_cod_instituicao').focus();
      return false;
    }

    if (!(/[^ ]/.test(document.getElementById('ref_cod_curso').value))) {
      mudaClassName('formdestaque', 'obrigatorio');
      document.getElementById("ref_cod_curso").className = 'formdestaque';
      alert('Preencha o campo "Curso" corretamente!');
      document.getElementById('ref_cod_curso').focus();
      return false;
    }

    if (!(/[^ ]/.test( document.getElementById('ref_cod_turma').value))) {
      mudaClassName('formdestaque', 'obrigatorio');
      document.getElementById('ref_cod_turma').className = 'formdestaque';
      alert('Preencha o campo "Turma" corretamente!');
      document.getElementById('ref_cod_turma').focus();
      return false;
    }
  }

  showExpansivelImprimir(400, 200,'',[], 'Boletim');
  document.formcadastro.target = 'miolo_'+(DOM_divs.length-1);
  document.getElementById('btn_enviar').disabled = false;
  document.formcadastro.submit();
}

document.formcadastro.action = 'educar_relatorio_boletim_proc.php';
</script>