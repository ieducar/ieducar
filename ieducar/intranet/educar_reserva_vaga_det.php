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
 * @author      Prefeitura Municipal de Itaja� <ctima@itajai.sc.gov.br>
 * @license     http://creativecommons.org/licenses/GPL/2.0/legalcode.pt  CC GNU GPL
 * @package     Core
 * @subpackage  ReservaVaga
 * @since       Arquivo dispon�vel desde a vers�o 1.0.0
 * @version     $Id$
 */

require_once 'include/clsBase.inc.php';
require_once 'include/clsDetalhe.inc.php';
require_once 'include/clsBanco.inc.php';
require_once 'include/pmieducar/geral.inc.php';

class clsIndexBase extends clsBase {
  public function Formular() {
    $this->SetTitulo($this->_instituicao . ' i-Educar - Reserva Vaga');
    $this->processoAp = '639';
  }
}

class indice extends clsDetalhe
{
  /**
   * Refer�ncia a usu�rio da sess�o
   * @var int
   */
  var $pessoa_logada = NULL;

  /**
   * T�tulo no topo da p�gina
   * @var string
   */
  var $titulo = '';

  // Atributos de mapeamento da tabela pmieducar.reserva_vaga
  var
    $ref_cod_escola,
    $ref_cod_serie,
    $ref_usuario_exc,
    $ref_usuario_cad,
    $data_cadastro,
    $data_exclusao,
    $ativo;

  function Gerar()
  {
    session_start();
    $this->pessoa_logada = $_SESSION['id_pessoa'];
    session_write_close();

    $this->titulo = 'Reserva Vaga - Detalhe';
    $this->addBanner('imagens/nvp_top_intranet.jpg', 'imagens/nvp_vert_intranet.jpg', 'Intranet');

    $this->ref_cod_serie  = $_GET['ref_cod_serie'];
    $this->ref_cod_escola = $_GET['ref_cod_escola'];

    $tmp_obj = new clsPmieducarEscolaSerie();
    $lst_obj = $tmp_obj->lista($this->ref_cod_escola, $this->ref_cod_serie);
    $registro = array_shift($lst_obj);

    if (! $registro) {
      header('Location: educar_reserva_vaga_lst.php');
      die();
    }

    // Institui��o
    $obj_ref_cod_instituicao = new clsPmieducarInstituicao($registro['ref_cod_instituicao']);
    $det_ref_cod_instituicao = $obj_ref_cod_instituicao->detalhe();
    $registro['ref_cod_instituicao'] = $det_ref_cod_instituicao['nm_instituicao'];

    // Escola
    $obj_ref_cod_escola = new clsPmieducarEscola($registro['ref_cod_escola']);
    $det_ref_cod_escola = $obj_ref_cod_escola->detalhe();
    $nm_escola = $det_ref_cod_escola['nome'];

    // S�rie
    $obj_ref_cod_serie = new clsPmieducarSerie($registro['ref_cod_serie']);
    $det_ref_cod_serie = $obj_ref_cod_serie->detalhe();
    $nm_serie = $det_ref_cod_serie['nm_serie'];

    // Curso
    $obj_curso = new clsPmieducarCurso($registro['ref_cod_curso']);
    $det_curso = $obj_curso->detalhe();
    $registro['ref_cod_curso'] = $det_curso['nm_curso'];

    // Matr�cula
    $obj_matricula = new clsPmieducarMatricula();
    $lst_matricula = $obj_matricula->lista(NULL, NULL, $registro['ref_cod_escola'],
      $registro['ref_cod_serie'], NULL, NULL, NULL, 3, NULL, NULL, NULL, NULL, 1);

    if (is_array($lst_matricula)) {
      $matriculados = count($lst_matricula);
    }

    // Detalhes da reserva
    $obj_reserva_vaga = new clsPmieducarReservaVaga();
    $lst_reserva_vaga = $obj_reserva_vaga->lista(NULL, $registro['ref_cod_escola'],
      $registro['ref_cod_serie'], NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1);

    if (is_array($lst_reserva_vaga)) {
      $reservados = count($lst_reserva_vaga);
    }

    // Permiss�es
    $obj_permissao = new clsPermissoes();
    $nivel_usuario = $obj_permissao->nivel_acesso($this->pessoa_logada);

    if ($nivel_usuario == 1) {
      if ($registro['ref_cod_instituicao']) {
        $this->addDetalhe(array('Institui&ccedil;&atilde;o', $registro['ref_cod_instituicao']));
      }
    }

    if ($nivel_usuario == 1 || $nivel_usuario == 2) {
      if ($nm_escola) {
        $this->addDetalhe(array('Escola', $nm_escola));
      }
    }

    if ($registro['ref_cod_curso']) {
      $this->addDetalhe(array('Curso', $registro['ref_cod_curso']));
    }

    if ($nm_serie) {
      $this->addDetalhe(array('S&eacute;rie', $nm_serie));
    }

    $obj_ano_letivo = new clsPmieducarEscolaAnoLetivo();
    $obj_ano_letivo->ref_cod_escola = $this->ref_cod_escola;
    $obj_ano_letivo->andamento = 1;
    $ano_letivo = $obj_ano_letivo->detalhe();
    
    $obj_turmas = new clsPmieducarTurma();
    $lst_turmas = $obj_turmas->lista(NULL, NULL, NULL, $this->ref_cod_serie,
      $this->ref_cod_escola, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 
      NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 
      NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, $ano_letivo["ano"]);

    if (is_array($lst_turmas)) {
      $cont = 0;
      $total_vagas = 0;
      $html = "
        <table width='50%' cellspacing='0' cellpadding='0' border='0'>
          <tr>
            <td bgcolor=#A1B3BD>Nome</td>
            <td bgcolor=#A1B3BD>N&uacute;mero Vagas</td>
          </tr>";

      foreach ($lst_turmas as $turmas) {
        $total_vagas += $turmas['max_aluno'];
        if (($cont % 2) == 0) {
          $class = ' formmdtd ';
        }
        else {
          $class = ' formlttd ';
        }
        $cont++;

        $html .="
          <tr>
            <td class=$class width='35%'>{$turmas["nm_turma"]}</td>
            <td class=$class width='15%'>{$turmas["max_aluno"]}</td>
          </tr>";
      }

      $html .= '</tr></table>';
      $this->addDetalhe(array('Turma', $html));

      if ($total_vagas) {
        $this->addDetalhe(array('Total Vagas', $total_vagas));
      }

      if ($matriculados) {
        $this->addDetalhe(array('Matriculados', $matriculados));
      }

      if ($reservados) {
        $this->addDetalhe(array('Reservados', $reservados));
      }

      $vagas_restantes = $total_vagas - ($matriculados + $reservados);
      $this->addDetalhe(array('Vagas Restantes', $vagas_restantes));
    }

    if ($obj_permissao->permissao_cadastra(639, $this->pessoa_logada, 7)) {
      $this->array_botao = array('Reservar Vaga', 'Vagas Reservadas');
      $this->array_botao_url = array("educar_reserva_vaga_cad.php?ref_cod_escola={$registro["ref_cod_escola"]}&ref_cod_serie={$registro["ref_cod_serie"]}",
        'educar_reservada_vaga_lst.php?ref_cod_escola=' . $registro['ref_cod_escola'] .
        '&ref_cod_serie=' . $registro['ref_cod_serie']);
    }

    $this->url_cancelar = 'educar_reserva_vaga_lst.php';
    $this->largura = '100%';
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