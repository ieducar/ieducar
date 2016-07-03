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
 * @package   iEd_Pessoa
 * @since     Arquivo dispon�vel desde a vers�o 1.0.0
 * @version   $Id$
 */

/**
 * clsCadastroDeficiencia class.
 *
 * @author    Prefeitura Municipal de Itaja� <ctima@itajai.sc.gov.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   iEd_Pessoa
 * @since     Classe dispon�vel desde a vers�o 1.0.0
 * @version   @@package_version@@
 */
class clsCadastroDeficiencia
{
  var $cod_deficiencia;
  var $nm_deficiencia;

  /**
   * Armazena o total de resultados obtidos na �ltima chamada ao m�todo lista().
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
   * Lista separada por v�rgula, com os campos que devem ser selecionados na
   * pr�xima chamado ao m�todo lista().
   * @var string
   */
  var $_campos_lista;

  /**
   * Lista com todos os campos da tabela separados por v�rgula, padr�o para
   * sele��o no m�todo lista.
   * @var string
   */
  var $_todos_campos;

  /**
   * Valor que define a quantidade de registros a ser retornada pelo m�todo lista().
   * @var int
   */
  var $_limite_quantidade;

  /**
   * Define o valor de offset no retorno dos registros no m�todo lista().
   * @var int
   */
  var $_limite_offset;

  /**
   * Define o campo para ser usado como padr�o de ordena��o no m�todo lista().
   * @var string
   */
  var $_campo_order_by;

  /**
   * Construtor.
   */
  function __construct($cod_deficiencia = NULL, $nm_deficiencia = NULL)
  {
    $db = new clsBanco();
    $this->_schema = 'cadastro.';
    $this->_tabela = "{$this->_schema}deficiencia";

    $this->_campos_lista = $this->_todos_campos = 'cod_deficiencia, nm_deficiencia';

    if (is_numeric($cod_deficiencia)) {
      $this->cod_deficiencia = $cod_deficiencia;
    }

    if (is_string($nm_deficiencia)) {
      $this->nm_deficiencia = $nm_deficiencia;
    }
  }

  /**
   * Cria um novo registro.
   * @return bool
   */
  function cadastra()
  {
    if (is_string($this->nm_deficiencia)) {
      $db = new clsBanco();

      $campos = '';
      $valores = '';
      $gruda = '';

      if (is_numeric($this->cod_deficiencia)) {
        $campos .= "{$gruda}cod_deficiencia";
        $valores .= "{$gruda}'{$this->cod_deficiencia}'";
        $gruda = ", ";
      }

      if (is_string($this->nm_deficiencia)) {
        $campos .= "{$gruda}nm_deficiencia";
        $valores .= "{$gruda}'{$this->nm_deficiencia}'";
        $gruda = ", ";
      }

      $db->Consulta( "INSERT INTO {$this->_tabela} ( $campos ) VALUES( $valores )" );
      return $db->InsertId( "{$this->_tabela}_cod_deficiencia_seq" );
    }
    return FALSE;
  }

  /**
   * Edita os dados de um registro.
   * @return bool
   */
  function edita()
  {
    if (is_numeric($this->cod_deficiencia)) {
      $db = new clsBanco();
      $set = '';

      if (is_string($this->nm_deficiencia)) {
        $set .= "{$gruda}nm_deficiencia = '{$this->nm_deficiencia}'";
        $gruda = ", ";
      }

      if ($set) {
        $db->Consulta("UPDATE {$this->_tabela} SET $set WHERE cod_deficiencia = '{$this->cod_deficiencia}'");
        return TRUE;
      }
    }

    return FALSE;
  }

  /**
   * Retorna uma lista de registros filtrados de acordo com os par�metros.
   * @return array
   */
  function lista($int_cod_deficiencia = NULL, $str_nm_deficiencia = NULL)
  {
    $sql = "SELECT {$this->_campos_lista} FROM {$this->_tabela}";
    $filtros = '';

    $whereAnd = ' WHERE ';

    if (is_numeric($int_cod_deficiencia)) {
      $filtros .= "{$whereAnd} cod_deficiencia = '{$int_cod_deficiencia}'";
      $whereAnd = " AND ";
    }

    if (is_string($str_nm_deficiencia)) {
      $filtros .= "{$whereAnd} nm_deficiencia ILIKE '%{$str_nm_deficiencia}%'";
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
    if (is_numeric($this->cod_deficiencia)) {
      $db = new clsBanco();
      $db->Consulta("SELECT {$this->_todos_campos} FROM {$this->_tabela} WHERE cod_deficiencia = '{$this->cod_deficiencia}'");
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
    if (is_numeric($this->cod_deficiencia)) {
    	$this->excluiVinculosDeficiencia($this->cod_deficiencia);
      $db = new clsBanco();
      $db->Consulta("DELETE FROM {$this->_tabela} WHERE cod_deficiencia = '{$this->cod_deficiencia}'");
      return TRUE;
    }

    return FALSE;
  }

  function excluiVinculosDeficiencia($deficienciaId){
    $db = new clsBanco();
    $db->Consulta("  UPDATE pmieducar.servidor SET ref_cod_deficiencia = NULL where ref_cod_deficiencia = {$deficienciaId};");
    $db->Consulta("  DELETE FROM cadastro.fisica_deficiencia WHERE ref_cod_deficiencia = {$deficienciaId};");
    return TRUE;
  }


  /**
   * Define quais campos da tabela ser�o selecionados no m�todo Lista().
   */
  function setCamposLista($str_campos)
  {
    $this->_campos_lista = $str_campos;
  }

  /**
   * Define que o m�todo Lista() deverpa retornar todos os campos da tabela.
   */
  function resetCamposLista()
  {
    $this->_campos_lista = $this->_todos_campos;
  }

	/**
	 * Define limites de retorno para o metodo lista
	 *
	 * @return null
	 */
	function setLimite( $intLimiteQtd, $intLimiteOffset = 0 )
	{
		$this->_limite_quantidade = $intLimiteQtd;
		if ($intLimiteOffset > 0)
			$this->_limite_offset = $intLimiteOffset;
	}

  /**
   * Retorna a string com o trecho da query respons�vel pelo limite de
   * registros retornados/afetados.
   *
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
   * Define o campo para ser utilizado como ordena��o no m�todo Lista().
   */
  function setOrderby( $strNomeCampo )
  {
    if (is_string($strNomeCampo) && $strNomeCampo) {
      $this->_campo_order_by = $strNomeCampo;
    }
  }

  /**
   * Retorna a string com o trecho da query respons�vel pela Ordena��o dos
   * registros.
   *
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