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
 * @license   http://creativecommons.org/licenses/GPL/2.0/legalcode.pt  CC GNU GPL
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
 * @license   http://creativecommons.org/licenses/GPL/2.0/legalcode.pt  CC GNU GPL
 * @package   iEd_Pmieducar
 * @since     Classe dispon�vel desde a vers�o 1.0.0
 * @version   arapiraca-r735
 */
class clsIndexBase extends clsBase
{
  function Formular()
  {
    $this->SetTitulo($this->_instituicao . ' i-Educar - Relat�rio Professor Disciplina');
    $this->processoAp = 827;
  }
}

/**
 * indice class.
 *
 * @author    Prefeitura Municipal de Itaja� <ctima@itajai.sc.gov.br>
 * @category  i-Educar
 * @license   http://creativecommons.org/licenses/GPL/2.0/legalcode.pt  CC GNU GPL
 * @package   iEd_Pmieducar
 * @since     Classe dispon�vel desde a vers�o 1.0.0
 * @version   arapiraca-r735
 */
class indice extends clsCadastro
{
  var $pessoa_logada;

  var $ref_cod_instituicao;
  var $ref_cod_escola;
  var $ref_cod_serie;
  var $ref_cod_turma;
  var $ref_cod_disciplina;

  var $nm_escola;
  var $nm_instituicao;
  var $ref_cod_curso;

  var $pdf;

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

    $obj_permissoes = new clsPermissoes();

    return $retorno;
  }

  function Gerar()
  {
    $obj_permissoes = new clsPermissoes();
    $nivel_usuario = $obj_permissoes->nivel_acesso($this->pessoa_logada);

    if ($_POST){
      foreach ($_POST as $key => $value) {
        $this->$key = $value;
      }
    }

    $get_escola              = TRUE;
    $exibe_nm_escola         = TRUE;
    $get_curso               = TRUE;
    $escola_obrigatorio      = FALSE;
    $curso_obrigatorio       = TRUE;
    $instituicao_obrigatorio = TRUE;

    include 'include/pmieducar/educar_campo_lista.php';

    $this->campoLista('ref_cod_disciplina', 'Disciplina',
      array('' => 'Selecione'), $this->ref_cod_disciplina, '', FALSE, '', '',
      FALSE, FALSE
    );

    if ($this->ref_cod_escola) {
      $this->ref_ref_cod_escola = $this->ref_cod_escola;
    }

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
function getDisciplina()
{
  var campoInstituicao = document.getElementById('ref_cod_instituicao').value;
  var campoCurso = document.getElementById('ref_cod_curso').value;

  var xml1 = new ajax(getDisciplina_XML);

  strURL = 'educar_disciplina_xml.php?cur=' + campoCurso;

  xml1.envia(strURL);
}

function getDisciplina_XML(xml)
{
  var disciplinas = xml.getElementsByTagName('disciplina');
  var campoDisciplina = document.getElementById('ref_cod_disciplina');

  campoDisciplina.length = 1;

  for (var j = 0; j < disciplinas.length; j++) {
    campoDisciplina.options[campoDisciplina.options.length] =
      new Option(disciplinas[j].firstChild.nodeValue,
        disciplinas[j].getAttribute('cod_disciplina'), false, false
      );
  }

}

document.getElementById('ref_cod_curso').onchange = function()
{
  getDisciplina();
}

document.getElementById('ref_cod_escola').onchange = function()
{
  if (document.getElementById('ref_cod_escola').value) {
    getEscolaCurso();
  }
  else {
    getCurso();
  }
}

var func = function()
{
  document.getElementById('btn_enviar').disabled= false;
};

if (window.addEventListener) {
  // Mozilla
  document.getElementById('btn_enviar').addEventListener('click', func, false);
}
else if (window.attachEvent) {
  // IE
  document.getElementById('btn_enviar').attachEvent('onclick', func);
}

function acao2()
{
  if (!acao()) {
    return;
  }

  showExpansivelImprimir(400, 200, '', [], 'Relat�rio Professor por Disciplina');
  document.formcadastro.target = 'miolo_' + (DOM_divs.length-1);
  document.getElementById( 'btn_enviar' ).disabled = false;
  document.formcadastro.submit();
}

document.formcadastro.action = 'educar_relatorio_professor_disciplina_proc.php';
</script>
