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
 * @package   iEd
 * @since     Arquivo dispon�vel desde a vers�o 1.0.0
 * @version   $Id$
 */

require_once '../includes/bootstrap.php';
require_once 'include/clsBancoPgSql.inc.php';

/**
 * clsBanco class.
 *
 * @author    Prefeitura Municipal de Itaja� <ctima@itajai.sc.gov.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   iEd
 * @since     Classe dispon�vel desde a vers�o 1.0.0
 * @version   @@package_version@@
 */
class clsBanco extends clsBancoSQL_
{
  /**
   * Construtor (PHP 4).
   */
  public function clsBanco($strDataBase = FALSE)
  {
    parent::__construct($strDataBase);

    global $coreExt;
    $config = $coreExt['Config']->app->database;

    $this->setHost($config->hostname);
    $this->setDbname($config->dbname);
    $this->setPassword($config->password);
    $this->setUser($config->username);
    $this->setPort($config->port);
  }

  /**
   * Retorna a quantidade de registros de uma tabela baseado no objeto que a
   * abstrai. Este deve ter um atributo p�blico Object->_tabela.
   *
   * @param   mixed   Objeto que abstrai a tabela
   * @param   string  Nome da coluna para c�lculo COUNT()
   * @return  int     Quantidade de registros da tabela
   */
  public function doCountFromObj($obj, $column = '*')
  {
    if ($obj->_tabela == NULL) {
      return FALSE;
    }

    $sql = sprintf('SELECT COUNT(%s) FROM %s', $column, $obj->_tabela);
    $this->Consulta($sql);

    return (int)$this->UnicoCampo($sql);
  }

  /**
   * Retorna os dados convertidos para a sintaxe SQL aceita por ext/pgsql.
   *
   * <code>
   * <?php
   * $data = array(
   *   'id' => 1,
   *   'hasChild' = FALSE
   * );
   *
   * $clsBanco->getDbValuesFromArray($data);
   * // array(
   * //   'id' => 1,
   * //   'hasChild' => 'f'
   * // );
   * </code>
   *
   * Apenas o tipo booleano � convertido.
   *
   * @param array $data Array associativo com os valores a serem convertidos.
   * @return array
   */
  public function formatValues(array $data)
  {
    $db = array();
    foreach ($data as $key => $val) {
      if (is_bool($val)) {
        $db[$key] = $this->_formatBool($val);
        continue;
      }
      $db[$key] = $val;
    }
    return $db;
  }

  /**
   * Retorna um valor formatado de acordo com o tipo output do tipo booleano
   * no PostgreSQL.
   *
   * @link   http://www.postgresql.org/docs/8.2/interactive/datatype-boolean.html
   * @link   http://www.php.net/manual/en/function.pg-query-params.php#78072
   * @param  mixed $val
   * @return string "t" para TRUE e "f" para false
   */
  protected function _formatBool($val)
  {
    return ($val == TRUE ? 't' : 'f');
  }
}