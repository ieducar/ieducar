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

/**
 * clsIndexBase class.
 *
 * @author    Prefeitura Municipal de Itaja�
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
    $this->SetTitulo($this->_instituicao . ' i-Educar - Escola Ano Letivo');
    $this->processoAp = 561;
  }
}

/**
 * indice class.
 *
 * @author    Prefeitura Municipal de Itaja�
 * @category  i-Educar
 * @license   @@license@@
 * @package   iEd_Pmieducar
 * @since     Classe dispon�vel desde a vers�o 1.0.0
 * @version   @@package_version@@
 */
class indice extends clsCadastro
{
  var $pessoa_logada;

  var $ref_cod_escola;
  var $ano;
  var $ref_usuario_cad;
  var $ref_usuario_exc;
  var $andamento;
  var $data_cadastro;
  var $data_exclusao;
  var $ativo;

  function Inicializar()
  {
    $retorno = 'Novo';

    @session_start();
    $this->pessoa_logada = $_SESSION['id_pessoa'];
    @session_write_close();

    $this->ano            = $_GET['ano'];
    $this->ref_cod_escola = $_GET['cod_escola'];

    $obj_permissoes = new clsPermissoes();
    $obj_permissoes->permissao_cadastra(561, $this->pessoa_logada, 7,
      'educar_escola_lst.php');

    $this->nome_url_sucesso  = 'Continuar';
    $this->url_cancelar      = 'educar_escola_det.php?cod_escola=' . $this->ref_cod_escola;
    $this->nome_url_cancelar = 'Cancelar';

    return $retorno;
  }

  function Gerar()
  {
    // Primary keys
    $this->campoOculto('ref_cod_escola', $this->ref_cod_escola);
    $this->campoOculto('ano', $this->ano);

    $obj_anos = new clsPmieducarEscolaAnoLetivo();
    $lista_ano = $obj_anos->lista($this->ref_cod_escola, NULL, NULL, NULL, 2,
      NULL, NULL, NULL, NULL, 1);

    $ano_array = array();

    if ($lista_ano) {
      foreach ($lista_ano as $ano) {
        $ano_array[$ano['ano']] = $ano['ano'];
      }
    }

    $ano_atual = date('Y') + 1;
    

    // Foreign keys
    $opcoes = array('' => 'Selecione');
    $lim = 25;

    for ($i = 0; $i < $lim; $i++) {
      $ano = $ano_atual - $i;

      if (! key_exists($ano,$ano_array)) {
        $opcoes[$ano] = $ano;
      }
      else {
        $lim++;
      }
    }

    $this->campoLista('ano', 'Ano', $opcoes, $this->ano);
  }

  function Novo()
  {
    @session_start();
    $this->pessoa_logada = $_SESSION['id_pessoa'];
    @session_write_close();

    $obj_permissoes = new clsPermissoes();
    $obj_permissoes->permissao_cadastra(561, $this->pessoa_logada, 7,
      'educar_escola_lst.php');

    $url = sprintf(
      'educar_ano_letivo_modulo_cad.php?ref_cod_escola=%s&ano=%s',
      $this->ref_cod_escola, $this->ano
    );

    header('Location: ' . $url);
    die();
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