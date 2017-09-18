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

require_once 'CoreExt/Enum.php';

/**
 * App_Model_NivelAcesso class.
 *
 * Define os valores inteiros usados nas compara��es das verifica��es de
 * acesso da classe clsPermissoes.
 *
 * Esses valores s�o verificados com o uso do operador bin�rio &, resultando
 * na seguinte tabela verdade:
 *
 * <code>
 * +------------------------+---+---+---+----+
 * | N�vel acessos          | 1 | 3 | 7 | 11 |
 * +------------------------+---+---+---+----+
 * | Poli-institucional (1) | T | T | T |  T |
 * +------------------------+---+---+---+----+
 * | Institucional      (2) | F | T | T |  T |
 * +------------------------+---+---+---+----+
 * | Escola             (4) | F | F | T |  F |
 * +------------------------+---+---+---+----+
 * | Biblioteca         (8) | F | F | F |  T |
 * +------------------------+---+---+---+----+
 *
 * Onde, T = TRUE; F = FALSE
 * </code>
 *
 * @author    Eriksen Costa Paix�o <eriksen.paixao_bs@cobra.com.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   App_Model
 * @see       clsPermissoes#permissao_cadastra
 * @see       clsPermissoes#permissao_excluir
 * @since     Classe dispon�vel desde a vers�o 1.1.0
 * @version   @@package_version@@
 */
class App_Model_NivelAcesso extends CoreExt_Enum
{
  const POLI_INSTITUCIONAL = 1;
  const INSTITUCIONAL      = 3;
  const SOMENTE_ESCOLA     = 7;
  const SOMENTE_BIBLIOTECA = 11;

  protected $_data = array(
    self::POLI_INSTITUCIONAL => 'Poli-institucional',
    self::INSTITUCIONAL      => 'Institucional',
    self::SOMENTE_ESCOLA     => 'Somente escola',
    self::SOMENTE_BIBLIOTECA => 'Somente biblioteca'
  );

  public static function getInstance()
  {
    return self::_getInstance(__CLASS__);
  }
}