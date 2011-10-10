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
 * @package   iEd_Imagem
 * @since     Arquivo dispon�vel desde a vers�o 1.0.0
 * @version   $Id$
 */

require_once 'include/clsBase.inc.php';
require_once 'include/clsCadastro.inc.php';
require_once 'include/imagem/clsPortalImagemTipo.inc.php';
require_once 'include/imagem/clsPortalImagem.inc.php';

/**
 * indice class.
 *
 * @author    Prefeitura Municipal de Itaja� <ctima@itajai.sc.gov.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   iEd_Imagem
 * @since     Classe dispon�vel desde a vers�o 1.0.0
 * @version   @@package_version@@
 */
class clsIndex extends clsBase
{
  function Formular()
  {
    $this->SetTitulo($this->_instituicao . ' Banco de Imagens');
    $this->processoAp = '473';
  }
}

/**
 * indice class.
 *
 * @author    Prefeitura Municipal de Itaja� <ctima@itajai.sc.gov.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   iEd_Imagem
 * @since     Classe dispon�vel desde a vers�o 1.0.0
 * @version   @@package_version@@
 */
class indice extends clsCadastro
{
  var $pessoa_logada;
  var $nome_reponsavel;

  var $cod_imagem;
  var $ref_cod_imagem_tipo;
  var $caminho;
  var $nm_imagem;
  var $extensao;
  var $img_altura;
  var $img_largura;
  var $data_cadastro;
  var $ref_cod_pessoa_cad;
  var $data_exclusao;
  var $ref_cod_pessoa_exc;

  function Inicializar()
  {
    $retorno = 'Novo';

    @session_start();
    $this->pessoa_logada = $_SESSION['id_pessoa'];
    @session_write_close();

    $this->cod_imagem = $_GET['cod_imagem'];

    if ($this->cod_imagem) {
      $obj = new clsPortalImagem($this->cod_imagem);

      $detalhe  = $obj->detalhe();

      $this->nm_tipo             = $detalhe['nm_tipo'];
      $this->ref_cod_imagem_tipo = $detalhe['ref_cod_imagem_tipo'];
      $this->caminho             = $detalhe['caminho'];
      $this->nm_imagem           = $detalhe['nm_imagem'];
      $this->extensao            = $detalhe['extensao'];
      $this->img_altura          = $detalhe['altura'];
      $this->img_largura         = $detalhe['largura'];
      $this->data_cadastro       = dataFromPgToBr($detalhe['data_cadastro']);
      $this->ref_cod_pessoa_cad  = $detalhe['ref_cod_pessoa_cad'];
      $this->data_exclusao       = dataFromPgToBr($detalhe['data_exclusao']);
      $this->ref_cod_pessoa_exc  = $detalhe['ref_cod_pessoa_exc'];
      $this->fexcluir = TRUE;
      $retorno = 'Editar';
    }

    $this->url_cancelar = $retorno == 'Editar' ?
      'imagem_det.php?cod_imagem=' . $this->cod_imagem : 'imagem_lst.php';

    $this->nome_url_cancelar = 'Cancelar';
    return $retorno;
  }

  function Gerar()
  {
    $this->campoOculto('cod_imagem', $this->cod_imagem_tipo);
    $ObjTImagem = new clsPortalImagemTipo();
    $TipoImagem = $ObjTImagem->lista();
    $listaTipo = array();

    if ($TipoImagem) {
      foreach ($TipoImagem as $dados) {
        $listaTipo[$dados['cod_imagem_tipo']] = $dados['nm_tipo'];
      }
    }

    $this->campoOculto('cod_imagem', $this->cod_imagem);
    $this->campoOculto('img_altura', $this->img_altura);
    $this->campoOculto('img_largura', $this->img_largura);
    $this->campoOculto('extensao', $this->extensao);
    $this->campoLista('ref_cod_imagem_tipo', 'Tipo da Imagem', $listaTipo, $this->ref_cod_imagem_tipo);
    $this->campoTexto('nm_imagem', 'Nome da Imagem', $this->nm_imagem, 30, 255, TRUE);
    $this->campoArquivo('caminho', 'Imagem', $this->caminho, 30);
  }

  function Novo()
  {
    @session_start();
    $this->pessoa_logada = $_SESSION['id_pessoa'];
    @session_write_close();

    $obj = new clsPortalImagem(FALSE, $this->ref_cod_imagem_tipo, 'caminho',
      $this->nm_imagem, FALSE, FALSE, FALSE, FALSE, $this->pessoa_logada,
      FALSE, FALSE);

    if($obj->cadastra()) {
      header("Location: imagem_lst.php");
    }

    return FALSE;
  }

  function Editar()
  {
    @session_start();
    $this->pessoa_logada = $_SESSION['id_pessoa'];
    session_write_close();

    $obj = new clsPortalImagem($this->cod_imagem, $this->ref_cod_imagem_tipo,
      'caminho', $this->nm_imagem, FALSE, FALSE, FALSE, FALSE, $this->pessoa_logada,
      FALSE, FALSE);

    if($obj->edita()) {
      header("Location: imagem_det.php?cod_imagem={$this->cod_imagem}");
    }

    return TRUE;
  }

  function Excluir()
  {
    $ObjImg = new clsPortalImagem($this->cod_imagem);
    $ObjImg->excluir();
    header('Location: imagem_lst.php');
  }
}

// Instancia objeto de p�gina
$pagina = new clsIndex();

// Instancia objeto de conte�do
$miolo = new indice();

// Atribui o conte�do � p�gina
$pagina->addForm($miolo);

// Gera o c�digo HTML
$pagina->MakeAll();