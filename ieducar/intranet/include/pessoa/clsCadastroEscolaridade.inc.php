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
 * @subpackage  pessoa
 * @subpackage  Escolaridade
 * @since       Arquivo dispon�vel desde a vers�o 1.0.0
 * @version     $Id$
 */

/**
 * clsCadastroEscolaridade class.
 *
 * @author      Prefeitura Municipal de Itaja� <ctima@itajai.sc.gov.br>
 * @license     http://creativecommons.org/licenses/GPL/2.0/legalcode.pt  CC GNU GPL
 * @package     Core
 * @subpackage  pessoa
 * @subpackage  Escolaridade
 * @since       Classe dispon�vel desde a vers�o 1.0.0
 * @version     $Id$
 */
class clsCadastroEscolaridade
{
  var $idesco;
  var $descricao;

  /**
   * Armazena o total de resultados obtidos na �ltima chamada ao m�todo lista.
   * @var int
   */
  var $_total;

  /**
   * Nome do schema.
   * @var string
   */
  var $_schema;

  /**
   * Nome da tabela.
   * @var string
   */
  var $_tabela;

  /**
   * Lista separada por virgula, com os campos que devem ser selecionados na pr�xima chamado ao m�todo lista.
   * @var string
   */
  var $_campos_lista;

  /**
   * Lista com todos os campos da tabela separados por v�rgula, padr�o para sele��o no m�todo lista.
   * @var string
   */
  var $_todos_campos;

  /**
   * Valor que define a quantidade de registros a ser retornada pelo m�todo lista.
   * @var int
   */
  var $_limite_quantidade;

  /**
   * Define o valor de offset no retorno dos registros no m�todo lista.
   * @var int
   */
  var $_limite_offset;

  /**
   * Define o campo padr�o para ser usado como padr�o de ordena��o no m�todo lista.
   * @var string
   */
  var $_campo_order_by;

  /**
   * Construtor (PHP 4).
   */
  function clsCadastroEscolaridade($idesco = NULL, $descricao = NULL)
  {
    $db = new clsBanco();
    $this->_schema = "cadastro.";
    $this->_tabela = "{$this->_schema}escolaridade";

    $this->_campos_lista = $this->_todos_campos = "idesco, descricao";

    if (is_numeric($idesco)) {
      $this->idesco = $idesco;
    }
    if (is_string($descricao)) {
      $this->descricao = $descricao;
    }
  }

  /**
   * Cria um novo registro.
   * @return bool
   */
  function cadastra()
  {
    if (is_string($this->descricao))
    {
      $db = new clsBanco();

      $campos  = '';
      $valores = '';
      $gruda   = '';

      $this->idesco = $db->CampoUnico('SELECT MAX(idesco) + 1
                      FROM cadastro.escolaridade');

      // Se for nulo, � o primeiro registro da tabela
      if (is_null($this->idesco)) {
        $this->idesco = 1;
      }

      if (is_numeric($this->idesco)) {
        $campos  .= "{$gruda}idesco";
        $valores .= "{$gruda}'{$this->idesco}'";
        $gruda = ", ";
      }
      if (is_string($this->descricao)) {
        $campos  .= "{$gruda}descricao";
        $valores .= "{$gruda}'{$this->descricao}'";
        $gruda = ", ";
      }

      $db->Consulta("INSERT INTO {$this->_tabela} ($campos) VALUES ($valores)");
      return $this->idesco;
    }

    return FALSE;
  }

  /**
   * Edita os dados de um registro.
   * @return bool
   */
  function edita()
  {
    if (is_numeric($this->idesco)) {
      $db  = new clsBanco();
      $set = "";

      if (is_string($this->descricao)) {
        $set  .= "{$gruda}descricao = '{$this->descricao}'";
        $gruda = ", ";
      }

      if ($set) {
        $db->Consulta("UPDATE {$this->_tabela} SET $set WHERE idesco = '{$this->idesco}'");
        return TRUE;
      }
    }

    return FALSE;
  }

  /**
   * Retorna uma lista de registros filtrados de acordo com os par�metros.
   * @return array
   */
  function lista($int_idesco = NULL, $str_descricao = NULL) {
    $sql = "SELECT {$this->_campos_lista} FROM {$this->_tabela}";
    $filtros = "";

    $whereAnd = " WHERE ";

    if (is_numeric($int_idesco)) {
      $filtros .= "{$whereAnd} idesco = '{$int_idesco}'";
      $whereAnd = " AND ";
    }
    if (is_string($str_descricao)) {
      $filtros .= "{$whereAnd} descricao ILIKE '%{$str_descricao}%'";
      $whereAnd = " AND ";
    }

    $db = new clsBanco();
    $countCampos = count(explode(",", $this->_campos_lista));
    $resultado = array();

    $sql .= $filtros . $this->getOrderby() . $this->getLimite();

    $this->_total = $db->CampoUnico("SELECT COUNT(0) FROM {$this->_tabela} {$filtros}");

    $db->Consulta($sql);

    if ($countCampos > 1) {
      while ($db->ProximoRegistro()) {
        $tupla = $db->Tupla();

        $tupla["_total"] = $this->_total;
        $resultado[] = $tupla;
      }
    }
    else {
      while ($db->ProximoRegistro()) {
        $tupla = $db->Tupla();
        $resultado[] = $tupla[$this->_campos_lista];
      }
    }
    if (count($resultado)) {
      return $resultado;
    }

    return FALSE;
  }

  /**
   * Retorna um array com os dados de um registro.
   * @return array
   */
  function detalhe()
  {
    if (is_numeric($this->idesco)) {
      $db = new clsBanco();
      $db->Consulta("SELECT {$this->_todos_campos} FROM {$this->_tabela} WHERE idesco = '{$this->idesco}'");
      $db->ProximoRegistro();

      return $db->Tupla();
    }

    return FALSE;
  }

  /**
   * Exclui um registro.
   * @return bool
   */
  function excluir()
  {
    if (is_numeric($this->idesco)) {
      $db = new clsBanco();
      $db->Consulta("DELETE FROM {$this->_tabela} WHERE idesco = '{$this->idesco}'");

      return TRUE;
    }

    return FALSE;
  }

  /**
   * Define quais campos da tabela ser�o selecionados na invoca��o do m�todo lista.
   * @return null
   */
  function setCamposLista($str_campos) {
    $this->_campos_lista = $str_campos;
  }

  /**
   * Define que o m�todo Lista dever� retornoar todos os campos da tabela.
   * @return null
   */
  function resetCamposLista() {
    $this->_campos_lista = $this->_todos_campos;
  }

  /**
   * Define limites de retorno para o m�todo lista.
   * @return null
   */
  function setLimite($intLimiteQtd, $intLimiteOffset = 0)
  {
    $this->_limite_quantidade = $intLimiteQtd;
	if ($intLimiteOffset > 0)
			$this->_limite_offset = $intLimiteOffset;
  }

  /**
   * Retorna a string com o trecho da query respos�vel pelo limite de registros.
   * @return string
   */
  function getLimite()
  {
    if (is_numeric($this->_limite_quantidade)) {
      $retorno = " LIMIT {$this->_limite_quantidade}";

      if (is_numeric($this->_limite_offset)) {
        $retorno .= " OFFSET {$this->_limite_offset} ";
      }

      return $retorno;
    }

    return '';
  }

  /**
   * Define campo para ser utilizado como ordena��o no m�todo lista.
   * @return null
   */
  function setOrderby($strNomeCampo)
  {
    if (is_string($strNomeCampo) && $strNomeCampo) {
      $this->_campo_order_by = $strNomeCampo;
    }
  }

  /**
   * Retorna a string com o trecho da query respos�vel pela ordena��o dos registros.
   * @return string
   */
  function getOrderby()
  {
    if (is_string($this->_campo_order_by)) {
      return " ORDER BY {$this->_campo_order_by} ";
    }

    return '';
  }
}