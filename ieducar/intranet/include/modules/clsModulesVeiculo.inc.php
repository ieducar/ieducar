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
 * clsModulesVeiculo class.
 * 
 * @author    Lucas Schmoeller da Silva <lucas@portabilis.com.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   Module
 * @since     07/2013
 * @version   @@package_version@@
 */
class clsModulesVeiculo
{
  var $cod_veiculo;
  var $descricao;
  var $placa;
  var $renavam;
  var $chassi;
  var $marca;
  var $ano_fabricacao;
  var $ano_modelo;
  var $passageiros;
  var $malha;
  var $ref_cod_tipo_veiculo;
  var $exclusivo_transporte_escolar;
  var $adaptado_necessidades_especiais;
  var $ativo;
  var $descricao_inativo;
  var $ref_cod_empresa_transporte_escolar;
  var $ref_cod_motorista;
  var $observacao;

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
  function clsModulesVeiculo($cod_veiculo = NULL, $descricao = NULL, $placa = NULL, $renavam = NULL,
                             $chassi = NULL, $marca = NULL, $ano_fabricacao = NULL, 
                             $ano_modelo = NULL, $passageiros = NULL, $malha = NULL, $ref_cod_tipo_veiculo = NULL,
                             $exclusivo_transporte_escolar = NULL, $adaptado_necessidades_especiais = NULL,
                             $ativo = NULL, $descricao_inativo = NULL, $ref_cod_empresa_transporte_escolar = NULL,
                             $ref_cod_motorista = NULL, $observacao = NULL )
  {
    $db = new clsBanco();
    $this->_schema = "modules.";
    $this->_tabela = "{$this->_schema}veiculo";

    $this->_campos_lista = $this->_todos_campos = " cod_veiculo, descricao, placa, renavam, chassi, marca, ano_fabricacao, 
       ano_modelo, passageiros, malha, ref_cod_tipo_veiculo, exclusivo_transporte_escolar, 
       adaptado_necessidades_especiais, ativo, descricao_inativo, ref_cod_empresa_transporte_escolar, 
       ref_cod_motorista, observacao"; 

    if (is_numeric($cod_veiculo)) {
      $this->cod_veiculo = $cod_veiculo;
    }

    if (is_string($descricao)) {
      $this->descricao = $descricao;
    }

    if (is_string($placa)) {
      $this->placa = $placa;
    }

    if (is_string($renavam)) {
      $this->renavam = $renavam;
    }

    if (is_string($marca)) {
      $this->marca = $marca;
    }

    if (is_string($ano_fabricacao)) {
      $this->marca = $marca;
    }

   if (is_string($ano_modelo)) {
      $this->marca = $marca;
    }

    if (is_numeric($passageiros)) {
      $this->passageiros = $passageiros;
    }

    if (is_string($malha)) {
      $this->malha = $malha;
    }


    if (is_numeric($ref_cod_tipo_veiculo)) {
      $this->ref_cod_tipo_veiculo = $ref_cod_tipo_veiculo;
    }

    if (is_string($exclusivo_transporte_escolar)) {
      $this->exclusivo_transporte_escolar = $exclusivo_transporte_escolar;
    }

    if (is_string($adaptado_necessidades_especiais)) {
      $this->adaptado_necessidades_especiais = $adaptado_necessidades_especiais;
    }

    if (is_string($ativo)) {
      $this->ativo = $ativo;
    }

    if (is_string($descricao_inativo)) {
      $this->descricao_inativo = $descricao_inativo;
    }

    if (is_numeric($ref_cod_empresa_transporte_escolar)) {
      $this->ref_cod_empresa_transporte_escolar = $ref_cod_empresa_transporte_escolar;
    }

    if (is_numeric($ref_cod_motorista)) {
      $this->ref_cod_motorista = $ref_cod_motorista;
    }
        
    if (is_string($observacao)) {
      $this->observacao = $observacao;
    }

  }

  /**
   * Cria um novo registro.
   * @return bool
   */
  function cadastra()
  {


    if (is_string($this->descricao) && is_string($this->placa) && is_string($this->renavam)
      && is_numeric($this->passageiros) && is_string($this->malha) && is_string($this->adaptado_necessidades_especiais)
      && is_string($this->exclusivo_transporte_escolar) && is_numeric($this->ref_cod_empresa_transporte_escolar)
      && is_numeric($this->ref_cod_motorista))
    {

      $db = new clsBanco();

      $campos  = '';
      $valores = '';
      $gruda   = '';


    if (is_numeric($this->cod_veiculo)) {
        $campos .= "{$gruda}cod_veiculo";
        $valores .= "{$gruda}'{$this->cod_veiculo}'";
        $gruda = ", ";
    }

    if (is_string($this->descricao)) {
        $campos .= "{$gruda}descricao";
        $valores .= "{$gruda}'{$this->descricao}'";
        $gruda = ", ";
    }

    if (is_string($this->placa)) {
        $campos .= "{$gruda}placa";
        $valores .= "{$gruda}'{$this->placa}'";
        $gruda = ", ";
    }

    if (is_string($this->renavam)) {
        $campos .= "{$gruda}renavam";
        $valores .= "{$gruda}'{$this->renavam}'";
        $gruda = ", ";
    }

    if (is_string($this->marca)) {
        $campos .= "{$gruda}marca";
        $valores .= "{$gruda}'{$this->marca}'";
        $gruda = ", ";
    }

    if (is_string($this->chassi)) {
        $campos .= "{$gruda}chassi";
        $valores .= "{$gruda}'{$this->chassi}'";
        $gruda = ", ";
    }    

    if (is_numeric($this->ano_fabricacao)) {
        $campos .= "{$gruda}ano_fabricacao";
        $valores .= "{$gruda}'{$this->ano_fabricacao}'";
        $gruda = ", ";
    }

   if (is_numeric($this->ano_modelo)) {
        $campos .= "{$gruda}ano_modelo";
        $valores .= "{$gruda}'{$this->ano_modelo}'";
        $gruda = ", ";
    }

    if (is_numeric($this->passageiros)) {
        $campos .= "{$gruda}passageiros";
        $valores .= "{$gruda}'{$this->passageiros}'";
        $gruda = ", ";
    }

    if (is_string($this->malha)) {
        $campos .= "{$gruda}malha";
        $valores .= "{$gruda}'{$this->malha}'";
        $gruda = ", ";
    }


    if (is_numeric($this->ref_cod_tipo_veiculo)) {
        $campos .= "{$gruda}ref_cod_tipo_veiculo";
        $valores .= "{$gruda}'{$this->ref_cod_tipo_veiculo}'";
        $gruda = ", ";
    }

    if (is_string($this->exclusivo_transporte_escolar)) {
        $campos .= "{$gruda}exclusivo_transporte_escolar";
        $valores .= "{$gruda}'{$this->exclusivo_transporte_escolar}'";
        $gruda = ", ";
    }

    if (is_string($this->adaptado_necessidades_especiais)) {
        $campos .= "{$gruda}adaptado_necessidades_especiais";
        $valores .= "{$gruda}'{$this->adaptado_necessidades_especiais}'";
        $gruda = ", ";
    }

    if (is_string($this->ativo)) {
        $campos .= "{$gruda}ativo";
        $valores .= "{$gruda}'{$this->ativo}'";
        $gruda = ", ";
    }

    if (is_string($this->descricao_inativo)) {
        $campos .= "{$gruda}descricao_inativo";
        $valores .= "{$gruda}'{$this->descricao_inativo}'";
        $gruda = ", ";
    }

    if (is_numeric($this->ref_cod_empresa_transporte_escolar)) {
        $campos .= "{$gruda}ref_cod_empresa_transporte_escolar";
        $valores .= "{$gruda}'{$this->ref_cod_empresa_transporte_escolar}'";
        $gruda = ", ";
    }

    if (is_numeric($this->ref_cod_motorista)) {
        $campos .= "{$gruda}ref_cod_motorista";
        $valores .= "{$gruda}'{$this->ref_cod_motorista}'";
        $gruda = ", ";
    }
        
    if (is_string($this->observacao)) {
        $campos .= "{$gruda}observacao";
        $valores .= "{$gruda}'{$this->observacao}'";
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
    
    if (is_numeric($this->cod_veiculo)) {
      $db  = new clsBanco();
      $set = '';

    if (is_numeric($this->cod_veiculo)) {
        $set .= "{$gruda}cod_veiculo = '{$this->cod_veiculo}'";
        $gruda = ", ";
    }

    if (is_string($this->descricao)) {
        $set .= "{$gruda}descricao = '{$this->descricao}'";
        $gruda = ", ";
    }

    if (is_string($this->placa)) {
        $set .= "{$gruda}placa = '{$this->placa}'";
        $gruda = ", ";
    }

    if (is_string($this->renavam)) {
        $set .= "{$gruda}renavam = '{$this->renavam}'";
        $gruda = ", ";
    }

    if (is_string($this->marca)) {
        $set .= "{$gruda}marca = '{$this->marca}'";
        $gruda = ", ";
    }

    if (is_numeric($this->ano_fabricacao)) {
        $set .= "{$gruda}ano_fabricacao = '{$this->ano_fabricacao}'";
        $gruda = ", ";
    }

   if (is_numeric($this->ano_modelo)) {
        $set .= "{$gruda}ano_modelo = '{$this->ano_modelo}'";
        $gruda = ", ";
    }

    if (is_numeric($this->this->passageiros)) {
        $set .= "{$gruda}passageiros = '{$this->passageiros}'";
        $gruda = ", ";
    }

    if (is_string($this->malha)) {
        $set .= "{$gruda}malha = '{$this->malha}'";
        $gruda = ", ";
    }


    if (is_numeric($this->ref_cod_tipo_veiculo)) {
        $set .= "{$gruda}ref_cod_tipo_veiculo = '{$this->ref_cod_tipo_veiculo}'";
        $gruda = ", ";
    }

    if (is_string($this->exclusivo_transporte_escolar)) {
        $set .= "{$gruda}exclusivo_transporte_escolar = '{$this->exclusivo_transporte_escolar}'";
        $gruda = ", ";
    }

    if (is_string($this->adaptado_necessidades_especiais)) {
        $set .= "{$gruda}adaptado_necessidades_especiais = '{$this->adaptado_necessidades_especiais}'";
        $gruda = ", ";
    }

    if (is_string($this->ativo)) {
        $set .= "{$gruda}ativo = '{$this->ativo}'";
        $gruda = ", ";
    }

    if (is_string($this->descricao_inativo)) {
        $set .= "{$gruda}descricao_inativo = '{$this->descricao_inativo}'";
        $gruda = ", ";
    }

    if (is_numeric($this->ref_cod_empresa_transporte_escolar)) {
        $set .= "{$gruda}ref_cod_empresa_transporte_escolar = '{$this->ref_cod_empresa_transporte_escolar}'";
        $gruda = ", ";
    }

    if (is_numeric($this->ref_cod_motorista)) {
        $set .= "{$gruda}ref_cod_motorista = '{$this->ref_cod_motorista}'";
        $gruda = ", ";
    }
        
    if (is_string($this->observacao)) {
        $set .= "{$gruda}observacao = '{$this->observacao}'";
        $gruda = ", ";
    }
      if ($set) {
        $db->Consulta("UPDATE {$this->_tabela} SET $set WHERE cod_veiculo = '{$this->cod_veiculo}'");
        return TRUE;
      }
    }

    return FALSE;
  }

  /**
   * Retorna uma lista de registros filtrados de acordo com os par�metros.
   * @return array
   */
  function lista($cod_veiculo = NULL, $descricao = NULL,
    $placa = NULL, $renavam = NULL,
    $nome_motorista = NULL, $ref_cod_empresa_transporte_escolar = NULL, $marca = null, $ativo = null, $ref_cod_motorista = NULL)
  {
    $sql = "SELECT {$this->_campos_lista}, (
          SELECT
            nome
          FROM
            modules.empresa_transporte_escolar emp,cadastro.pessoa p
          WHERE
            ref_cod_empresa_transporte_escolar = cod_empresa_transporte_escolar AND p.idpes = emp.ref_idpes
         ) AS nome_empresa FROM {$this->_tabela}";
    $filtros = "";
    $whereNomes = '';
    $whereAnd = " WHERE ";

    if (is_numeric($cod_veiculo)) {
      $filtros .= "{$whereAnd} cod_veiculo = '{$cod_veiculo}'";
      $whereAnd = " AND ";
    }

    if (is_string($descricao)) {
      $filtros .= "
        {$whereAnd} TO_ASCII(LOWER(descricao)) LIKE TO_ASCII(LOWER('%{$descricao}%')) ";

      $whereAnd = ' AND ';
    }      

    if (is_string($placa)) {
      $filtros .= "{$whereAnd} placa = '{$placa}'";
      $whereAnd = " AND ";
    }

    if (is_string($renavam)) {
      $filtros .= "{$whereAnd} renavam = '{$renavam}'";
      $whereAnd = " AND ";
    }    

    if (is_string($nome_motorista)) {
      $whereNomes .= "
        {$whereAnd} TO_ASCII(LOWER((SELECT
            nome
          FROM
            modules.motorista m,cadastro.pessoa p
          WHERE
            ref_cod_motorista = cod_motorista AND p.idpes = m.ref_idpes))) LIKE TO_ASCII(LOWER('%{$nome_motorista}%')) ";

      $whereAnd = ' AND ';
    }

    if (is_numeric($ref_cod_empresa_transporte_escolar)) {
      $filtros .= "{$whereAnd} ref_cod_empresa_transporte_escolar = '{$ref_cod_empresa_transporte_escolar}'";
      $whereAnd = " AND ";
    } 

    if (is_numeric($ref_cod_motorista)) {
      $filtros .= "{$whereAnd} ref_cod_motorista = '{$ref_cod_motorista}'";
      $whereAnd = " AND ";
    } 

    if (is_string($ativo)) {
      $filtros .= "{$whereAnd} ativo = '{$ativo}'";
      $whereAnd = " AND ";
    }    

    if (is_string($marca)) {
        $filtros .= "
        {$whereAnd} TO_ASCII(LOWER(marca)) LIKE TO_ASCII(LOWER('%{$marca}%')) ";
      $whereAnd = " AND ";
    }       

    $db = new clsBanco();
    $countCampos = count(explode(',', $this->_campos_lista))+2;
    $resultado = array();

    $sql .= $filtros.$whereNomes.$this->getOrderby() . $this->getLimite();

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
    if (is_numeric($this->cod_veiculo)) {
      $db = new clsBanco();
      $db->Consulta("SELECT {$this->_todos_campos},(
          SELECT
            nome
          FROM
            modules.empresa_transporte_escolar emp,cadastro.pessoa p
          WHERE
            ref_cod_empresa_transporte_escolar = cod_empresa_transporte_escolar AND p.idpes = emp.ref_idpes
         ) AS nome_empresa, (
          SELECT
            nome
          FROM
            modules.motorista m,cadastro.pessoa p
          WHERE
            ref_cod_motorista = cod_motorista AND p.idpes = m.ref_idpes
         ) AS nome_motorista,(SELECT descricao FROM modules.tipo_veiculo where cod_tipo_veiculo = ref_cod_tipo_veiculo) AS descricao_tipo FROM {$this->_tabela} WHERE cod_veiculo = '{$this->cod_veiculo}'");
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
    if (is_numeric($this->cod_veiculo)) {
      $db = new clsBanco();
      $db->Consulta("SELECT 1 FROM {$this->_tabela} WHERE cod_veiculo = '{$this->cod_veiculo}'");
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
    if (is_numeric($this->cod_veiculo)) {
      $sql = "DELETE FROM {$this->_tabela} WHERE cod_veiculo = '{$this->cod_veiculo}'";
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