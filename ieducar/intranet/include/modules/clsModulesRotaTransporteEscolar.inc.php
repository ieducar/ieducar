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
 * @author    Lucas Schmoeller da Silva <lucas@portabilis.com.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   Module
 * @since     07/2013
 * @version   $Id$
 */

require_once 'include/pmieducar/geral.inc.php';

/**
 * clsModulesRotaTransporteEscolar class.
 * 
 * @author    Lucas Schmoeller da Silva <lucas@portabilis.com.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   Module
 * @since     07/2013
 * @version   @@package_version@@
 */
class clsModulesRotaTransporteEscolar
{
  var $cod_rota_transporte_escolar;
  var $ref_idpes_destino;
  var $descricao;
  var $ano;
  var $tipo_rota;
  var $km_pav;
  var $km_npav;
  var $ref_cod_empresa_transporte_escolar;
  var $tercerizado;

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
  function clsModulesRotaTransporteEscolar($cod_rota_transporte_escolar = NULL, $ref_idpes_destino = NULL, $descricao = NULL,  $ano = NULL, $tipo_rota = NULL, $km_pav = NULL, $km_npav = NULL, $ref_cod_empresa_transporte_escolar=NULL, $tercerizado = NULL)
  {

    $db = new clsBanco();
    $this->_schema = "modules.";
    $this->_tabela = "{$this->_schema}rota_transporte_escolar";

    $this->_campos_lista = $this->_todos_campos = " cod_rota_transporte_escolar, ref_idpes_destino, descricao, ano, tipo_rota, km_pav, km_npav, ref_cod_empresa_transporte_escolar, tercerizado"; 

    if (is_numeric($cod_rota_transporte_escolar)) {
      $this->cod_rota_transporte_escolar = $cod_rota_transporte_escolar;
    }

    if (is_numeric($ref_idpes_destino)) {
      $this->ref_idpes_destino = $ref_idpes_destino;
    }    

    if (is_string($descricao)) {
      $this->descricao = $descricao;
    }

    if (is_numeric($ano)) {
      $this->ano = $ano;
    }

    if (is_string($tipo_rota)) {
      $this->tipo_rota = $tipo_rota;
    }    

    if (is_numeric($km_pav)) {
      $this->km_pav = $km_pav;
    }

    if (is_numeric($km_npav)) {
      $this->km_npav = $km_npav;
    }    

    if (is_numeric($ref_cod_empresa_transporte_escolar)) {
      $this->ref_cod_empresa_transporte_escolar = $ref_cod_empresa_transporte_escolar;
    }    

    if (is_string($tercerizado)) {
      $this->tercerizado = $tercerizado;
    }    
  }

  /**
   * Cria um novo registro.
   * @return bool
   */
  function cadastra()
  {
    
    if (is_numeric($this->ref_idpes_destino) && is_numeric($this->ano) && is_string($this->descricao) 
      && is_string($this->tipo_rota) && is_numeric($this->ref_cod_empresa_transporte_escolar) 
      && is_string($this->tercerizado))
    {
      $db = new clsBanco();

      $campos  = '';
      $valores = '';
      $gruda   = '';
      
    if (is_numeric($this->ref_idpes_destino)) {
      $campos .= "{$gruda}ref_idpes_destino";
      $valores .= "{$gruda}'{$this->ref_idpes_destino}'";
      $gruda = ", ";
    }    

    if (is_string($this->descricao)) {
      $campos .= "{$gruda}descricao";
      $valores .= "{$gruda}'{$this->descricao}'";
      $gruda = ", ";
    }

    if (is_numeric($this->ano)) {
      $campos .= "{$gruda}ano";
      $valores .= "{$gruda}'{$this->ano}'";
      $gruda = ", ";
    }

    if (is_string($this->tipo_rota)) {
      $campos .= "{$gruda}tipo_rota";
      $valores .= "{$gruda}'{$this->tipo_rota}'";
      $gruda = ", ";
    }    

    if (is_numeric($this->km_pav)) {
      $campos .= "{$gruda}km_pav";
      $valores .= "{$gruda}'{$this->km_pav}'";
      $gruda = ", ";
    }

    if (is_numeric($this->km_npav)) {
      $campos .= "{$gruda}km_npav";
      $valores .= "{$gruda}'{$this->km_npav}'";
      $gruda = ", ";
    }    

    if (is_numeric($this->ref_cod_empresa_transporte_escolar)) {
      $campos .= "{$gruda}ref_cod_empresa_transporte_escolar";
      $valores .= "{$gruda}'{$this->ref_cod_empresa_transporte_escolar}'";
      $gruda = ", ";
    }    

    if (is_string($this->tercerizado)) {
      $campos .= "{$gruda}tercerizado";
      $valores .= "{$gruda}'{$this->tercerizado}'";
      $gruda = ", ";
    }    


      $db->Consulta("INSERT INTO {$this->_tabela} ( $campos ) VALUES( $valores )");
      return $db->InsertId("{$this->_tabela}_seq");
    }

    return FALSE;
  }

  /**
   * Edita os dados de um registro.
   * @return bool
   */
  function edita()
  {

    if (is_string($this->cod_rota_transporte_escolar)) {
      $db  = new clsBanco();
      $set = '';
      $gruda = '';

    if (is_numeric($this->ref_idpes_destino)) {
        $set .= "{$gruda}ref_idpes_destino = '{$this->ref_idpes_destino}'";
        $gruda = ", ";
    }    

    if (is_string($this->descricao)) {
        $set .= "{$gruda}descricao = '{$this->descricao}'";
        $gruda = ", ";
    }

    if (is_numeric($this->ano)) {
        $set .= "{$gruda}ano = '{$this->ano}'";
        $gruda = ", ";
    }

    if (is_string($this->tipo_rota)) {
        $set .= "{$gruda}tipo_rota = '{$this->tipo_rota}'";
        $gruda = ", ";
    }    

    if (is_numeric($this->km_pav)) {
        $set .= "{$gruda}km_pav = '{$this->km_pav}'";
        $gruda = ", ";
    }

    if (is_numeric($this->km_npav)) {
        $set .= "{$gruda}km_npav = '{$this->km_npav}'";
        $gruda = ", ";
    }    

    if (is_numeric($this->ref_cod_empresa_transporte_escolar)) {
        $set .= "{$gruda}ref_cod_empresa_transporte_escolar = '{$this->ref_cod_empresa_transporte_escolar}'";
        $gruda = ", ";
    }    

    if (is_string($this->tercerizado)) {
        $set .= "{$gruda}tercerizado = '{$this->tercerizado}'";
        $gruda = ", ";
    }   

      if ($set) {
        $db->Consulta("UPDATE {$this->_tabela} SET $set WHERE cod_rota_transporte_escolar = '{$this->cod_rota_transporte_escolar}'");
        return TRUE;
      }
    }

    return FALSE;
  }

  /**
   * Retorna uma lista de registros filtrados de acordo com os par�metros.
   * @return array
   */
  function lista($cod_rota_transporte_escolar = NULL, $descricao = NULL, $ref_idpes_destino = NULL , $nome_destino = NULL,
   $ano = NULL, $ref_cod_empresa_transporte_escolar = NULL, $nome_empresa = NULL, $tercerizado = NULL)
  {
    
    $sql = "SELECT {$this->_campos_lista}, (
          SELECT
            nome
          FROM
            cadastro.pessoa
          WHERE
            idpes = ref_idpes_destino
         ) AS nome_destino , (
          SELECT
            nome
          FROM
            cadastro.pessoa, modules.empresa_transporte_escolar
          WHERE
            idpes = ref_idpes and cod_empresa_transporte_escolar = ref_cod_empresa_transporte_escolar
         ) AS nome_empresa FROM {$this->_tabela}";
    $filtros = "";

    $whereAnd = " WHERE ";

    if (is_numeric($cod_rota_transporte_escolar)) {
      $filtros .= "{$whereAnd} cod_rota_transporte_escolar = '{$cod_rota_transporte_escolar}'";
      $whereAnd = " AND ";
    }

    if (is_string($descricao)) {
      $filtros .= "{$whereAnd} TO_ASCII(LOWER(descricao)) LIKE TO_ASCII(LOWER('%{$descricao}%'))";
      $whereAnd = " AND ";
    }

    if (is_numeric($ref_idpes_destino)) {
      $filtros .= "{$whereAnd} ref_idpes_destino = '{$ref_idpes_destino}'";
      $whereAnd = " AND ";
    }
    if (is_string($nome_destino)) {
      $filtros .= "
        {$whereAnd} exists (
          SELECT
            1
          FROM
            cadastro.pessoa
          WHERE
            cadastro.pessoa.idpes = ref_idpes_destino
            AND TO_ASCII(LOWER(nome)) LIKE TO_ASCII(LOWER('%{$nome_destino}%'))
        )";

      $whereAnd = ' AND ';
    }  

    if (is_numeric($ano)) {
      $filtros .= "{$whereAnd} ano = '{$ano}'";
      $whereAnd = " AND ";
    }
    if (is_string($ref_cod_empresa_transporte_escolar)){
      $filtros .= "{$whereAnd} ref_cod_empresa_transporte_escolar = '{$ref_cod_empresa_transporte_escolar}'";
      $whereAnd = " AND ";
    }

    if (is_string($nome_empresa)) {
      $filtros .= "
        {$whereAnd} exists (
          SELECT
            nome
          FROM
            cadastro.pessoa, modules.empresa_transporte_escolar
          WHERE
            idpes = ref_idpes and cod_empresa_transporte_escolar = ref_cod_empresa_transporte_escolar 
            AND TO_ASCII(LOWER(nome)) LIKE TO_ASCII(LOWER('%{$nome_empresa}%'))
        )";

      $whereAnd = ' AND ';
    }  

    if (is_string($tercerizado)){
      $filtros .= "{$whereAnd} tercerizado = '{$tercerizado}'";
      $whereAnd = " AND ";
    }

    $db = new clsBanco();
    $countCampos = count(explode(',', $this->_campos_lista))+2;
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
    if (is_numeric($this->cod_rota_transporte_escolar)) {
      $db = new clsBanco();
      $db->Consulta("SELECT {$this->_todos_campos}, (
          SELECT
            nome
          FROM
            cadastro.pessoa
          WHERE
            idpes = ref_idpes_destino
         ) AS nome_destino , (
          SELECT
            nome
          FROM
            cadastro.pessoa, modules.empresa_transporte_escolar
          WHERE
            idpes = ref_idpes and cod_empresa_transporte_escolar = ref_cod_empresa_transporte_escolar
         ) AS nome_empresa FROM {$this->_tabela} WHERE cod_rota_transporte_escolar = '{$this->cod_rota_transporte_escolar}'");
      $db->ProximoRegistro();
      return $db->Tupla();
    }
    return FALSE;
  }

  /**
   * Retorna um array com os dados de um registro.
   * @return array
   */
  function existe()
  {
    if (is_numeric($this->cod_rota_transporte_escolar)) {
      $db = new clsBanco();
      $db->Consulta("SELECT 1 FROM {$this->_tabela} WHERE cod_rota_transporte_escolar = '{$this->cod_rota_transporte_escolar}'");
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
    if (is_numeric($this->cod_rota_transporte_escolar)) {
      $sql = "DELETE FROM {$this->_tabela} WHERE cod_rota_transporte_escolar = '{$this->cod_rota_transporte_escolar}'";
      $db = new clsBanco();
      $db->Consulta($sql);
      return true;
    }

    return FALSE;
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
   * Define limites de retorno para o m�todo Lista().
   */
  function setLimite($intLimiteQtd, $intLimiteOffset = NULL)
  {
    $this->_limite_quantidade = $intLimiteQtd;
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
  function setOrderby($strNomeCampo)
  {
    if (is_string($strNomeCampo) && $strNomeCampo ) {
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