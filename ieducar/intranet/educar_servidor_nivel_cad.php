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
    $this->SetTitulo($this->_instituicao . ' i-Educar - Servidor N�vel');
    $this->processoAp         = 0;
    $this->renderBanner       = FALSE;
    $this->renderMenu         = FALSE;
    $this->renderMenuSuspenso = FALSE;
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

  var $cod_servidor;
  var $ref_cod_instituicao;
  var $data_cadastro;
  var $data_exclusao;
  var $ativo;
  var $ref_cod_nivel;
  var $ref_cod_subnivel;
  var $ref_cod_categoria;

  function Inicializar()
  {
    $retorno = 'Novo';

    @session_start();
    $this->pessoa_logada = $_SESSION['id_pessoa'];
    @session_write_close();

    $this->cod_servidor        = $_GET['ref_cod_servidor'];
    $this->ref_cod_instituicao = $_GET['ref_cod_instituicao'];

    $obj_permissoes = new clsPermissoes();

    $obj_permissoes->permissao_cadastra(635, $this->pessoa_logada, 7, 'educar_servidor_lst.php');

    if (is_numeric($this->cod_servidor) && is_numeric($this->ref_cod_instituicao)) {
      $obj = new clsPmieducarServidor($this->cod_servidor, NULL, NULL, NULL, NULL,
        NULL, NULL, $this->ref_cod_instituicao);

      $registro  = $obj->detalhe();
      if ($registro) {
        $this->ref_cod_subnivel = $registro['ref_cod_subnivel'];
        $obj_subnivel = new clsPmieducarSubnivel($this->ref_cod_subnivel);
        $det_subnivel = $obj_subnivel->detalhe();

        if ($det_subnivel) {
          $this->ref_cod_nivel = $det_subnivel['ref_cod_nivel'];
        }

        if ($this->ref_cod_nivel) {
          $obj_nivel = new clsPmieducarNivel($this->ref_cod_nivel);
          $det_nivel = $obj_nivel->detalhe();
          $this->ref_cod_categoria = $det_nivel['ref_cod_categoria_nivel'];
        }

        $retorno = 'Editar';
      }
    }
    else {
      echo sprintf('<script>window.parent.fechaExpansivel("%s");</script>', $_GET['div']);
      die();
    }

    return $retorno;
  }

  function Gerar()
  {
    $this->campoOculto('cod_servidor',$this->cod_servidor);
    $this->campoOculto('ref_cod_instituicao',$this->ref_cod_instituicao);

    $obj_categoria = new clsPmieducarCategoriaNivel();
    $lst_categoria = $obj_categoria->lista(NULL, NULL, NULL, NULL, NULL, NULL,
      NULL, NULL, 1);

    $opcoes = array('' => 'Selecione uma categoria');

    if ($lst_categoria) {
      foreach ($lst_categoria as $categoria) {
        $opcoes[$categoria['cod_categoria_nivel']] = $categoria['nm_categoria_nivel'];
      }
    }

    $this->campoLista('ref_cod_categoria', 'Categoria', $opcoes, $this->ref_cod_categoria);

    $opcoes = array('' => 'Selecione uma categoria');

    if ($this->ref_cod_categoria) {
      $obj_nivel = new clsPmieducarNivel();
      $lst_nivel = $obj_nivel->buscaSequenciaNivel($this->ref_cod_categoria);

      if ($lst_nivel) {
        foreach ($lst_nivel as $nivel) {
          $opcoes[$nivel['cod_nivel']] = $nivel['nm_nivel'];
        }
      }
    }

    $this->campoLista('ref_cod_nivel', 'N�vel', $opcoes, $this->ref_cod_nivel, '', TRUE);

    $opcoes = array('' => 'Selecione um n�vel');

    if ($this->ref_cod_nivel) {
      $obj_nivel = new clsPmieducarSubnivel();
      $lst_nivel = $obj_nivel->buscaSequenciaSubniveis($this->ref_cod_nivel);
      if ($lst_nivel) {
        foreach ($lst_nivel as $subnivel) {
          $opcoes[$subnivel['cod_subnivel']] = $subnivel['nm_subnivel'];
        }
      }
    }

    $this->campoLista('ref_cod_subnivel', 'Subn�vel', $opcoes,
      $this->ref_cod_subnivel, '', FALSE, '', '', FALSE, TRUE);
  }

  function Novo()
  {
    echo sprintf('<script>window.parent.fechaExpansivel("%s");</script>', $_GET['div']);
    die();
  }

  function Editar()
  {
    $obj_servidor = new clsPmieducarServidor($this->cod_servidor, NULL, NULL,
      NULL, NULL, NULL, NULL, $this->ref_cod_instituicao, $this->ref_cod_subnivel
    );

    $obj_servidor->edita();

    echo sprintf('<script>parent.fechaExpansivel("%s");window.parent.location.reload(true);</script>', $_GET['div']);
    die;
  }

  function Excluir()
  {
    return FALSE;
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
function trocaNiveis()
{
  var campoCategoria = document.getElementById('ref_cod_categoria').value;
  var campoNivel     = document.getElementById('ref_cod_nivel');
  var campoSubNivel  = document.getElementById('ref_cod_subnivel');

  campoNivel.length = 1;
  campoSubNivel.length = 1;

  if (campoCategoria) {
    campoNivel.disabled        = true;
    campoNivel.options[0].text = 'Carregando N�veis';
    var xml = new ajax(atualizaLstNiveis);
    xml.envia('educar_niveis_servidor_xml.php?cod_cat=' + campoCategoria);
  }
  else {
    campoNivel.options[0].text    = 'Selecione uma Categoria';
    campoNivel.disabled           = false;
    campoSubNivel.options[0].text = 'Selecione um N�vel';
    campoSubNivel.disabled        = false;
  }
}

function atualizaLstNiveis(xml)
{
  var campoNivel  = document.getElementById('ref_cod_nivel');

  campoNivel.length          = 1;
  campoNivel.options[0].text = 'Selecione uma Categoria';
  campoNivel.disabled        = false;

  var niveis = xml.getElementsByTagName('nivel');

  if (niveis.length) {
    for (var i = 0; i < niveis.length; i++) {
      campoNivel.options[campoNivel.options.length] = new Option( niveis[i].firstChild.data, niveis[i].getAttribute('cod_nivel'),false,false);
    }
  }
  else {
    campoNivel.options[0].text = 'Categoria n�o possui n�veis';
  }
}

function trocaSubniveis()
{
  var campoNivel    = document.getElementById('ref_cod_nivel').value;
  var campoSubNivel = document.getElementById('ref_cod_subnivel');

  campoSubNivel.length = 1;

  if (campoNivel) {
    campoSubNivel.disabled = true;
    campoSubNivel.options[0].text = 'Carregando Subn�veis';
    var xml = new ajax(atualizaLstSubiveis);
    xml.envia("educar_subniveis_servidor_xml.php?cod_nivel="+campoNivel);
  }
  else {
    campoSubNivel.options[0].text = 'Selecione uma N�vel';
    campoSubNivel.disabled = false;
  }
}

function atualizaLstSubiveis(xml)
{
  var campoSubNivel = document.getElementById('ref_cod_subnivel');

  campoSubNivel.length          = 1;
  campoSubNivel.options[0].text = 'Selecione um Subn�vel';
  campoSubNivel.disabled        = false;

  var subniveis = xml.getElementsByTagName('subnivel');

  if (subniveis.length) {
    for (var i = 0; i < subniveis.length; i++) {
      campoSubNivel.options[campoSubNivel.options.length] = new Option(
        subniveis[i].firstChild.data, subniveis[i].getAttribute('cod_subnivel'),
        false, false
      );
    }
  }
  else {
    campoNivel.options[0].text = 'N�vel n�o possui subn�veis';
  }
}

document.getElementById('ref_cod_categoria').onchange = function(){
  trocaNiveis();
}

document.getElementById('ref_cod_nivel').onchange = function(){
  trocaSubniveis();
}
</script>