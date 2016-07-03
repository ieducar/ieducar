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
 * @author    Eriksen Costa Paix�o <eriksen.paixao_bs@cobra.com.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   App_Model
 * @since     Arquivo dispon�vel desde a vers�o 1.1.0
 * @version   $Id$
 */

require_once 'CoreExt/Entity.php';
require_once 'App/Model/MatriculaSituacao.php';

/**
 * App_Model_Matricula class.
 *
 * @author    Eriksen Costa Paix�o <eriksen.paixao_bs@cobra.com.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   App_Model
 * @since     Classe dispon�vel desde a vers�o 1.1.0
 * @version   @@package_version@@
 */
class App_Model_Matricula
{
  /**
   * Atualiza os dados da matr�cula do aluno, promovendo-o ou retendo-o. Usa
   * uma inst�ncia da classe legada clsPmieducarMatricula para tal.
   *
   * @param int $matricula
   * @param int $usuario
   * @param bool $aprovado
   * @return bool
   */
  public static function atualizaMatricula($matricula, $usuario, $aprovado = TRUE)
  {
    $instance = CoreExt_Entity::addClassToStorage('clsPmieducarMatricula', NULL,
      'include/pmieducar/clsPmieducarMatricula.inc.php');

    $instance->cod_matricula   = $matricula;
    $instance->ref_usuario_cad = $usuario;
    $instance->ref_usuario_exc = $usuario;

    if (is_int($aprovado))
      $instance->aprovado = $aprovado;
    else
    {
      $instance->aprovado        = ($aprovado == TRUE) ?
        App_Model_MatriculaSituacao::APROVADO :
        App_Model_MatriculaSituacao::REPROVADO;
    }

    return $instance->edita();
  }
}
