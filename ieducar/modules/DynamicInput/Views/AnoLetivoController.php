<?php

#error_reporting(E_ALL);
#ini_set("display_errors", 1);

/**
 * i-Educar - Sistema de gestão escolar
 *
 * Copyright (C) 2006  Prefeitura Municipal de Itajaí
 *     <ctima@itajai.sc.gov.br>
 *
 * Este programa é software livre; você pode redistribuí-lo e/ou modificá-lo
 * sob os termos da Licença Pública Geral GNU conforme publicada pela Free
 * Software Foundation; tanto a versão 2 da Licença, como (a seu critério)
 * qualquer versão posterior.
 *
 * Este programa é distribuí­do na expectativa de que seja útil, porém, SEM
 * NENHUMA GARANTIA; nem mesmo a garantia implí­cita de COMERCIABILIDADE OU
 * ADEQUAÇÃO A UMA FINALIDADE ESPECÍFICA. Consulte a Licença Pública Geral
 * do GNU para mais detalhes.
 *
 * Você deve ter recebido uma cópia da Licença Pública Geral do GNU junto
 * com este programa; se não, escreva para a Free Software Foundation, Inc., no
 * endereço 59 Temple Street, Suite 330, Boston, MA 02111-1307 USA.
 *
 * @author    Lucas D'Avila <lucasdavila@portabilis.com.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   Avaliacao
 * @subpackage  Modules
 * @since   Arquivo disponível desde a versão ?
 * @version   $Id$
 */

require_once 'lib/Portabilis/Controller/ApiCoreController.php';

/**
 * AnoLetivoController class.
 *
 * @author      Lucas D'Avila <lucasdavila@portabilis.com.br>
 * @category    i-Educar
 * @license     @@license@@
 * @package     Avaliacao
 * @subpackage  Modules
 * @since       Classe disponível desde a versão 1.1.0
 * @version     @@package_version@@
 */
class AnoLetivoController extends ApiCoreController
{

  protected function canGetAnosLetivos() {
    return $this->validatesId('escola');
  }

  protected function filtroSituacao() {
    $tiposSituacao  = array('nao_iniciado' => 0, 'em_andamento' => 1, 'finalizado' => 2);
    $situacaoIn     = array();

    foreach ($tiposSituacao as $nome => $flag) {
      if ($this->getRequest()->{"situacao_$nome"} == true)
        $situacaoIn[] = $flag;
    }

    return (empty($situacaoIn) ? '' : 'and andamento in ('. implode(',', $situacaoIn) . ')');
  }

  protected function getAnosLetivos() {
    if ($this->canGetAnosLetivos()) {
      $params       = array($this->getRequest()->escola_id);
      $sql          = "select ano from pmieducar.escola_ano_letivo as al where ref_cod_escola = $1
                       and ativo = 1 {$this->filtroSituacao()} order by ano desc";

      $records = $this->fetchPreparedQuery($sql, $params);
      $options = array();

      foreach ($records as $record)
        $options[$record['ano']] = $record['ano'];

      return array('options' => $options);
    }
  }

  public function Gerar() {
    if ($this->isRequestFor('get', 'anos_letivos'))
      $this->appendResponse($this->getAnosLetivos());
    else
      $this->notImplementedOperationError();
  }
}
