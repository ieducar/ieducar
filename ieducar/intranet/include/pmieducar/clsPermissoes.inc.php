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

require_once 'include/pmieducar/geral.inc.php';
require_once 'include/clsMenuFuncionario.inc.php';

/**
 * clsPermissoes class.
 *
 * @author    Prefeitura Municipal de Itaja� <ctima@itajai.sc.gov.br>
 * @category  i-Educar
 * @license   http://creativecommons.org/licenses/GPL/2.0/legalcode.pt  CC GNU GPL
 * @package   iEd_Pmieducar
 * @since     Classe dispon�vel desde a vers�o 1.0.0
 * @todo      Eliminar a l�gica duplicada dos m�todos permissao_*
 * @version   arapiraca-r733
 */
class clsPermissoes
{
  function clsPermissoes()
  {
  }

  /**
   * Verifica se um usu�rio tem permiss�o para cadastrar baseado em um
   * identificador de processo.
   *
   * @param int $int_processo_ap Identificador de processo
   * @param int $int_idpes_usuario Identificador do usu�rio
   * @param int $int_soma_nivel_acesso
   * @param string $str_pagina_redirecionar Caminho para o qual a requisi��o ser�
   *   encaminhada caso o usu�rio n�o tenha privil�gios suficientes para a
   *   opera��o de cadastro
   * @param bool $super_usuario TRUE para verificar se o usu�rio � super usu�rio
   * @param bool $int_verifica_usuario_biblioteca TRUE para verificar se o
   *   usu�rio possui cadastro em alguma biblioteca
   * @return bool|void
   */
  function permissao_cadastra($int_processo_ap, $int_idpes_usuario,
    $int_soma_nivel_acesso, $str_pagina_redirecionar = NULL,
    $super_usuario = NULL, $int_verifica_usuario_biblioteca = FALSE)
  {
    $obj_usuario = new clsFuncionario($int_idpes_usuario);
    $detalhe_usuario = $obj_usuario->detalhe();

    // Verifica se � super usu�rio
    if ($super_usuario != NULL && $detalhe_usuario['ativo']) {
      $obj_menu_funcionario = new clsMenuFuncionario($int_idpes_usuario, FALSE, FALSE, 0);
      $detalhe_super_usuario = $obj_menu_funcionario->detalhe();
    }

    if (!$detalhe_super_usuario) {
      $obj_menu_funcionario = new clsMenuFuncionario($int_idpes_usuario,
        FALSE, FALSE, $int_processo_ap);
      $detalhe = $obj_menu_funcionario->detalhe();
    }

    $nivel = $this->nivel_acesso($int_idpes_usuario);
    $ok = FALSE;

    if (($super_usuario && $detalhe_super_usuario) || $nivel & $int_soma_nivel_acesso) {
      $ok = TRUE;
    }

    if ((!$detalhe['cadastra'] && !$detalhe_super_usuario)) {
      $ok = FALSE;
    }

    /*
     * Se for usuario tipo biblioteca ou escola
     * ($int_verifica_usuario_biblioteca = true), verifica se possui cadastro na
     * tabela usuario biblioteca
     */
    if (($nivel == 8 ||
        ($nivel == 4 && $int_verifica_usuario_biblioteca == TRUE)
      ) && $int_soma_nivel_acesso > 3 && !$detalhe_super_usuario
    ) {
      $ok = $this->getBiblioteca($int_idpes_usuario) == 0 ? FALSE : TRUE;

      if (!$ok && $nivel == 8) {
        header("Location: index.php?negado=1");
        die();
      }
    }

    if (!$ok) {
      if ($str_pagina_redirecionar) {
        header("Location: $str_pagina_redirecionar");
        die();
      }
      else {
        return FALSE;
      }
    }

    return  TRUE;
  }

  /**
   * Verifica se um usu�rio tem permiss�o para cadastrar baseado em um
   * identificador de processo.
   *
   * @param int $int_processo_ap Identificador de processo
   * @param int $int_idpes_usuario Identificador do usu�rio
   * @param int $int_soma_nivel_acesso
   * @param string $str_pagina_redirecionar Caminho para o qual a requisi��o ser�
   *   encaminhada caso o usu�rio n�o tenha privil�gios suficientes para a
   *   opera��o de cadastro
   * @param bool $super_usuario TRUE para verificar se o usu�rio � super usu�rio
   * @param bool $int_verifica_usuario_biblioteca TRUE para verificar se o
   *   usu�rio possui cadastro em alguma biblioteca
   * @return bool|void
   */
  function permissao_excluir($int_processo_ap, $int_idpes_usuario,
    $int_soma_nivel_acesso, $str_pagina_redirecionar = NULL,
    $super_usuario = NULL,$int_verifica_usuario_biblioteca = FALSE)
  {
    $obj_usuario = new clsFuncionario($int_idpes_usuario);
    $detalhe_usuario = $obj_usuario->detalhe();

    // Verifica se � super usu�rio
    if ($super_usuario != NULL && $detalhe_usuario['ativo']) {
      $obj_menu_funcionario = new clsMenuFuncionario($int_idpes_usuario, FALSE, FALSE, 0);
      $detalhe_super_usuario = $obj_menu_funcionario->detalhe();
    }

    if (!$detalhe_super_usuario) {
      $obj_menu_funcionario = new clsMenuFuncionario($int_idpes_usuario,
        FALSE, FALSE, $int_processo_ap);
      $detalhe = $obj_menu_funcionario->detalhe();
    }

    $nivel = $this->nivel_acesso($int_idpes_usuario);
    $ok = FALSE;

    if (($super_usuario && $detalhe_super_usuario) || $nivel & $int_soma_nivel_acesso) {
      $ok = TRUE;
    }

    if ((!$detalhe['exclui'] && ! $detalhe_super_usuario)) {
      $ok = FALSE;
    }

    /*
     * Se for usuario tipo biblioteca ou escola
     * ($int_verifica_usuario_biblioteca = true), verifica se possui cadastro na
     * tabela usuario biblioteca
     */
    if (($nivel == 8 ||
        ($nivel == 4 && $int_verifica_usuario_biblioteca == TRUE)
      ) && $int_soma_nivel_acesso > 3 && !$detalhe_super_usuario
    ) {
      $ok = $this->getBiblioteca($int_idpes_usuario) == 0 ? FALSE : TRUE;

      if (!$ok && $nivel == 8) {
        header("Location: index.php?negado=1");
        die();
      }
    }

    if (! $ok) {
      if($str_pagina_redirecionar) {
        header("Location: $str_pagina_redirecionar");
        die();
      }
      else {
        return FALSE;
      }
    }

    return TRUE;
  }

  /**
   * Retorna o n�vel de acesso do usu�rio, podendo ser:
   *
   * - 1: Poli-institucional
   * - 2: Institucional
   * - 4: Escola
   * - 8: Biblioteca
   *
   * @param int $int_idpes_usuario
   * @return bool|int Retorna FALSE caso o usu�rio n�o exista
   */
  function nivel_acesso($int_idpes_usuario)
  {
    $obj_usuario = new clsPmieducarUsuario($int_idpes_usuario);
    $detalhe_usuario = $obj_usuario->detalhe();

    if ($detalhe_usuario) {
      $obj_tipo_usuario = new clsPmieducarTipoUsuario($detalhe_usuario['ref_cod_tipo_usuario']);
      $detalhe_tipo_usuario = $obj_tipo_usuario->detalhe();
      return $detalhe_tipo_usuario['nivel'];
    }

    return FALSE;
  }

  /**
   * Retorna o c�digo identificador da institui��o ao qual o usu�rio est�
   * vinculado.
   *
   * @param int $int_idpes_usuario
   * @return bool|int Retorna FALSE caso o usu�rio n�o exista
   */
  function getInstituicao($int_idpes_usuario)
  {
    $obj_usuario = new clsPmieducarUsuario($int_idpes_usuario);
    $detalhe_usuario = $obj_usuario->detalhe();

    if ($detalhe_usuario) {
      return $detalhe_usuario['ref_cod_instituicao'];
    }

    return FALSE;
  }

  /**
   * Retorna o c�digo identificador da escola ao qual o usu�rio est� vinculado.
   *
   * @param int $int_idpes_usuario
   * @return bool|int Retorna FALSE caso o usu�rio n�o exista
   */
  function getEscola($int_idpes_usuario)
  {
    $obj_usuario = new clsPmieducarUsuario($int_idpes_usuario);
    $detalhe_usuario = $obj_usuario->detalhe();

    if ($detalhe_usuario) {
      return $detalhe_usuario['ref_cod_escola'];
    }

    return FALSE;
  }

  /**
   * Retorna um array associativo com os c�digos identificadores da escola e
   * da institui��o ao qual o usu�rio est� vinculado.
   *
   * @param $int_idpes_usuario
   * @return array|bool Retorna FALSE caso o usu�rio n�o exista
   */
  function getInstituicaoEscola($int_idpes_usuario)
  {
    $obj_usuario = new clsPmieducarUsuario($int_idpes_usuario);
    $detalhe_usuario = $obj_usuario->detalhe();

    if ($detalhe_usuario) {
      return array(
        "instituicao" => $detalhe_usuario['ref_cod_instituicao'],
        "escola" => $detalhe_usuario['ref_cod_escola']
      );
    }

    return FALSE;
  }

  /**
   * Retorna um array com os c�digos identificadores das bibliotecas aos quais
   * o usu�rio est� vinculado.
   *
   * @param int $int_idpes_usuario
   * @return array|int Retorna o inteiro "0" caso o usu�rio n�o esteja vinculado
   *   a uma biblioteca
   */
  function getBiblioteca($int_idpes_usuario)
  {
    $obj_usuario = new clsPmieducarBibliotecaUsuario();
    $lst_usuario_biblioteca = $obj_usuario->lista(NULL, $int_idpes_usuario);

    if ($lst_usuario_biblioteca) {
      return $lst_usuario_biblioteca;
    }
    else {
      return 0;
    }
  }
}