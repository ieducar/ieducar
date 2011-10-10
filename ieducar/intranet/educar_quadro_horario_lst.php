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
 * @author    Adriano Erik Weiguert Nagasava <ctima@itajai.sc.gov.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   iEd_Pmieducar
 * @since     Arquivo dispon�vel desde a vers�o 1.0.0
 * @version   $Id$
 */

require_once 'include/clsBase.inc.php';
require_once 'include/clsListagem.inc.php';
require_once 'include/clsBanco.inc.php';
require_once 'include/pmieducar/geral.inc.php';
require_once 'ComponenteCurricular/Model/ComponenteDataMapper.php';

/**
 * clsIndexBase class.
 *
 * @author    Adriano Erik Weiguert Nagasava <ctima@itajai.sc.gov.br>
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
    $this->SetTitulo($this->_instituicao . ' i-Educar - Quadro de Hor�rio');
    $this->processoAp = "641";
  }
}

/**
 * indice class.
 *
 * @author    Adriano Erik Weiguert Nagasava <ctima@itajai.sc.gov.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   iEd_Pmieducar
 * @since     Classe dispon�vel desde a vers�o 1.0.0
 * @version   @@package_version@@
 */
class indice extends clsConfig
{
  var $pessoa_logada;
  var $titulo;
  var $limite;
  var $offset;

  var $cod_calendario_ano_letivo;
  var $ref_cod_escola;
  var $ref_cod_curso;
  var $ref_cod_serie;
  var $ref_cod_turma;
  var $ref_usuario_exc;
  var $ref_usuario_cad;
  var $ano;
  var $data_cadastra;
  var $data_exclusao;
  var $ativo;
  var $ref_cod_instituicao;
  var $busca;

  function renderHTML()
  {
    @session_start();
    $this->pessoa_logada = $_SESSION['id_pessoa'];
    session_write_close();

    $obj_permissoes = new clsPermissoes();

    if ($obj_permissoes->nivel_acesso($this->pessoa_logada) > 7)
    {
      $retorno .= '
        <table width="100%" height="40%" cellspacing="1" cellpadding="2" border="0" class="tablelistagem">
          <tbody>
            <tr>
              <td colspan="2" valig="center" height="50">
                <center class="formdktd">Usu�rio sem permiss�o para acessar esta p�gina</center>
              </td>
            </tr>
          </tbody>
        </table>';

      return $retorno;
    }

    $retorno .= '
      <table width="100%" cellspacing="1" cellpadding="2" border="0" class="tablelistagem">
        <tbody>';

    if ($_POST) {
      $this->ref_cod_turma       = $_POST['ref_cod_turma'] ? $_POST['ref_cod_turma'] : NULL;
      $this->ref_cod_serie       = $_POST['ref_cod_serie'] ? $_POST['ref_cod_serie'] : NULL;
      $this->ref_cod_curso       = $_POST['ref_cod_curso'] ? $_POST['ref_cod_curso'] : NULL;
      $this->ref_cod_escola      = $_POST['ref_cod_escola'] ? $_POST['ref_cod_escola'] : NULL;
      $this->ref_cod_instituicao = $_POST['ref_cod_instituicao'] ? $_POST['ref_cod_instituicao'] : NULL;
      $this->busca               = $_GET['busca'] ? $_GET['busca'] : NULL;
    }
    else {
      if ($_GET) {
        // Passa todos os valores obtidos no GET para atributos do objeto
        foreach( $_GET as $var => $val) {
          $this->$var = $val === '' ? NULL : $val;
        }
      }
    }

    $nivel_usuario = $obj_permissoes->nivel_acesso($this->pessoa_logada);

    if (!$this->ref_cod_escola) {
      $this->ref_cod_escola = $obj_permissoes->getEscola($this->pessoa_logada);
    }

    if (!is_numeric($this->ref_cod_instituicao)) {
      $this->ref_cod_instituicao = $obj_permissoes->getInstituicao($this->pessoa_logada);
    }

    // Componente curricular
    $componenteMapper = new ComponenteCurricular_Model_ComponenteDataMapper();

    $obrigatorio     = FALSE;
    $get_instituicao = TRUE;
    $get_escola      = TRUE;
    $get_curso       = TRUE;
    $get_serie       = TRUE;
    $get_turma       = TRUE;
    include 'educar_quadro_horarios_pesquisas.php';

    if ($this->busca == 'S') {
      if (is_numeric( $this->ref_cod_turma)) {
        $obj_turma = new clsPmieducarTurma($this->ref_cod_turma);
        $det_turma = $obj_turma->detalhe();

        $obj_quadro = new clsPmieducarQuadroHorario(NULL, NULL, NULL,
          $this->ref_cod_turma, NULL, NULL, 1);
        $det_quadro = $obj_quadro->detalhe();

        if (is_array($det_quadro)) {
          $quadro_horario = "<table class='calendar' cellspacing='0' cellpadding='0' border='0'><tr><td class='cal_esq' >&nbsp;</td><td background='imagens/i-educar/cal_bg.gif' width='100%' class='mes'>{$det_turma["nm_turma"]}</td><td align='right' class='cal_dir'>&nbsp;</td></tr><tr><td colspan='3' class='bordaM' style='border-bottom: 1px solid #8A959B;'  align='center'><table cellspacing='0' cellpadding='0'  border='0' ><tr class='header'><td style='border-right: 1px dotted #FFFFFF;width: 100px;'>DOM</td><td style='border-right: 1px dotted #FFFFFF;width: 100px;'>SEG</td><td style='border-right: 1px dotted #FFFFFF;width: 100px;'>TER</td><td style='border-right: 1px dotted #FFFFFF;width: 100px;'>QUA</td><td style='border-right: 1px dotted #FFFFFF;width: 100px;'>QUI</td><td style='border-right: 1px dotted #FFFFFF;width: 100px;'>SEX</td><td style='width: 100px;'>SAB</td></tr>";
          $texto = '<tr>';

          for ($c = 1; $c <= 7; $c++) {
            $obj_horarios = new clsPmieducarQuadroHorarioHorarios();
            $resultado    = $obj_horarios->retornaHorario($this->ref_cod_instituicao,
              $this->ref_cod_escola, $this->ref_cod_serie, $this->ref_cod_turma, $c);

            $texto .= "<td valign=top align='center' width='100' style='cursor: pointer; ' onclick='envia( this, {$this->ref_cod_turma}, {$this->ref_cod_serie}, {$this->ref_cod_curso}, {$this->ref_cod_escola}, {$this->ref_cod_instituicao}, {$det_quadro["cod_quadro_horario"]}, {$c} );'>";

            if (is_array($resultado)) {
              foreach ($resultado as $registro) {
                // Componente curricular
                $componente = $componenteMapper->find($registro['ref_cod_disciplina']);

                // Servidor
                $obj_servidor = new clsPmieducarServidor();

                $det_servidor = array_shift($obj_servidor->lista(
                  $registro['ref_servidor'], NULL, NULL, NULL, NULL, NULL, NULL,
                  NULL, NULL, NULL, NULL,�NULL, NULL, NULL, NULL, NULL, TRUE));

                $det_servidor['nome'] = array_shift(explode(' ',$det_servidor['nome']));

                //$texto .= "<div  style='text-align: center;background-color: #F6F6F6;font-size: 11px; width: 100px; margin: 3px; border: 1px solid #CCCCCC; padding:5px; '>". substr($registro['hora_inicial'], 0, 5) . ' - ' . substr($registro['hora_final'], 0, 5) . " <br> {$componente->abreviatura} <br> {$det_servidor["nome"]}</div>";
                $detalhes = sprintf("%s - %s<br />%s<br />%s",
                  substr($registro['hora_inicial'], 0, 5), substr($registro['hora_final'], 0, 5),
                  $componente->abreviatura, $det_servidor['nome']);

                $texto .= sprintf('<div style="text-align: center; background-color: #F6F6F6; font-size: 11px; width: 100px; margin: 3px; border: 1px solid #CCCCCC; padding:5px;">%s</div>',
                  $detalhes);
              }
            }
            else {
              $texto .= "<div  style='text-align: center;background-color: #F6F6F6;font-size: 11px; width: 100px; margin: 3px; border: 1px solid #CCCCCC; padding:5px; height: 85%;'></div>";
            }

            $texto .= '</td>';
          }

          $texto .= '<tr><td colspan="7">&nbsp;</td></tr>';
          $quadro_horario .= $texto;

          $quadro_horario .= '</table></td></tr></table>';
          $retorno .= "<tr><td colspan='2' ><center><b></b>{$quadro_horario}</center></td></tr>";
        }
        else {
          $retorno .= "<tr><td colspan='2' ><b><center>N&atilde;o existe nenhum quadro de hor&aacute;rio cadastrado para esta turma.</center></b></td></tr>";
        }
      }
    }

    if ($obj_permissoes->permissao_cadastra(641, $this->pessoa_logada, 7)) {
      $retorno .= "<tr><td>&nbsp;</td></tr><tr>
            <td align=\"center\" colspan=\"2\">";

      if (!$det_quadro) {
        $retorno .= "<input type=\"button\" value=\"Novo Quadro de Hor&aacute;rios\" onclick=\"window.location='educar_quadro_horario_cad.php?ref_cod_turma={$this->ref_cod_turma}&ref_cod_serie={$this->ref_cod_serie}&ref_cod_curso={$this->ref_cod_curso}&ref_cod_escola={$this->ref_cod_escola}&ref_cod_instituicao={$this->ref_cod_instituicao};'\" class=\"botaolistagem\"/>";
      }
      else {
        if ($obj_permissoes->permissao_excluir(641, $this->pessoa_logada, 7))
          $retorno .= "<input type=\"button\" value=\"Excluir Quadro de Hor&aacute;rios\" onclick=\"window.location='educar_quadro_horario_cad.php?ref_cod_turma={$this->ref_cod_turma}&ref_cod_serie={$this->ref_cod_serie}&ref_cod_curso={$this->ref_cod_curso}&ref_cod_escola={$this->ref_cod_escola}&ref_cod_instituicao={$this->ref_cod_instituicao}&ref_cod_quadro_horario={$det_quadro["cod_quadro_horario"]}'\" class=\"botaolistagem\"/>";
      }

      $retorno .= "</td>
            </tr>";
    }

    $retorno .='</tbody>
      </table>';

    return $retorno;
  }
}

// Instancia objeto de p�gina
$pagina = new clsIndexBase();

// Instancia objeto de conte�do
$miolo = new indice();

// Atribui o conte�do � p�gina
$pagina->addForm($miolo);

// Gera o c�digo HTML
$pagina->MakeAll();
?>
<script type="text/javascript">
var campoInstituicao = document.getElementById('ref_cod_instituicao');
var campoEscola = document.getElementById('ref_cod_escola');
var campoCurso = document.getElementById('ref_cod_curso');
var campoSerie = document.getElementById('ref_cod_serie');
var campoTurma = document.getElementById('ref_cod_turma');

campoInstituicao.onchange = function()
{
  var campoInstituicao_ = document.getElementById('ref_cod_instituicao').value;

  campoEscola.length = 1;
  campoEscola.disabled = true;
  campoEscola.options[0].text = 'Carregando escola';

  campoCurso.length = 1;
  campoCurso.disabled = true;
  campoCurso.options[0].text = 'Selecione uma escola antes';

  campoSerie.length = 1;
  campoSerie.disabled = true;
  campoSerie.options[0].text = 'Selecione um curso antes';

  campoTurma.length = 1;
  campoTurma.disabled = true;
  campoTurma.options[0].text = 'Selecione uma S�rie antes';

  var xml_escola = new ajax(getEscola);
  xml_escola.envia('educar_escola_xml2.php?ins=' + campoInstituicao_);
};

campoEscola.onchange = function()
{
  var campoEscola_ = document.getElementById( 'ref_cod_escola' ).value;

  campoCurso.length = 1;
  campoCurso.disabled = true;
  campoCurso.options[0].text = 'Carregando curso';

  campoSerie.length = 1;
  campoSerie.disabled = true;
  campoSerie.options[0].text = 'Selecione um curso antes';

  campoTurma.length = 1;
  campoTurma.disabled = true;
  campoTurma.options[0].text = 'Selecione uma s�rie antes';

  var xml_curso = new ajax(getCurso);
  xml_curso.envia('educar_curso_xml.php?esc=' + campoEscola_);
};

campoCurso.onchange = function()
{
  var campoEscola_ = document.getElementById('ref_cod_escola').value;
  var campoCurso_ = document.getElementById('ref_cod_curso').value;

  campoSerie.length = 1;
  campoSerie.disabled = true;
  campoSerie.options[0].text = 'Carregando s�rie';

  campoTurma.length = 1;
  campoTurma.disabled = true;
  campoTurma.options[0].text = 'Selecione uma S�rie antes';

  var xml_serie = ajax(getSerie);
  xml_serie.envia('educar_escola_curso_serie_xml.php?esc=' + campoEscola_ + '&cur=' + campoCurso_);
};

campoSerie.onchange = function()
{
  var campoEscola_ = document.getElementById('ref_cod_escola').value;
  var campoSerie_ = document.getElementById('ref_cod_serie').value;

  campoTurma.length = 1;
  campoTurma.disabled = true;
  campoTurma.options[0].text = 'Carregando turma';

  var xml_turma = new ajax(getTurma);
  xml_turma.envia('educar_turma_xml.php?esc=' + campoEscola_ + '&ser=' + campoSerie_);
};

if (document.getElementById('botao_busca')) {
  obj_botao_busca = document.getElementById('botao_busca');
  obj_botao_busca.onclick = function()
  {
    document.formcadastro.action = 'educar_quadro_horario_lst.php?busca=S';
    acao();
  };
}

function envia(obj, var1, var2, var3, var4, var5, var6, var7)
{
  var identificador = Math.round(1000000000 * Math.random());

  if (obj.innerHTML) {
    document.formcadastro.action = 'educar_quadro_horario_horarios_cad.php?ref_cod_turma=' + var1 + '&ref_cod_serie=' + var2 + '&ref_cod_curso=' + var3 + '&ref_cod_escola=' + var4 + '&ref_cod_instituicao=' + var5 + '&ref_cod_quadro_horario=' + var6 + '&dia_semana=' + var7 + '&identificador=' + identificador;
    document.formcadastro.submit();
  }
  else {
    document.formcadastro.action = 'educar_quadro_horario_horarios_cad.php?ref_cod_turma=' + var1 + '&ref_cod_serie=' + var2 + '&ref_cod_curso=' + var3 + '&ref_cod_escola=' + var4 + '&ref_cod_instituicao=' + var5 + '&ref_cod_quadro_horario=' + var6 + '&dia_semana=' + var7 + '&identificador=' + identificador;
    document.formcadastro.submit();
  }
}

if (document.createStyleSheet) {
  document.createStyleSheet('styles/calendario.css');
}
else {
  var objHead = document.getElementsByTagName('head');
  var objCSS = objHead[0].appendChild(document.createElement('link'));
  objCSS.rel = 'stylesheet';
  objCSS.href = 'styles/calendario.css';
  objCSS.type = 'text/css';
}
</script>