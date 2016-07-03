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
 * @subpackage  pmieducar
 * @subpackage  ReservaVaga
 * @since       Arquivo dispon�vel desde a vers�o 1.0.0
 * @version     $Id$
 */

require_once 'include/pmieducar/geral.inc.php';

/**
 * clsPmieducarReservaVaga class.
 *
 * @author      Prefeitura Municipal de Itaja� <ctima@itajai.sc.gov.br>
 * @license     http://creativecommons.org/licenses/GPL/2.0/legalcode.pt  CC GNU GPL
 * @package     Core
 * @subpackage  pmieducar
 * @subpackage  ReservaVaga
 * @since       Classe dispon�vel desde a vers�o 1.0.0
 * @version     $Id$
 */
class clsPmieducarReservaVaga
{
  var $cod_reserva_vaga;
  var $ref_ref_cod_escola;
  var $ref_ref_cod_serie;
  var $ref_usuario_exc;
  var $ref_usuario_cad;
  var $ref_cod_aluno;
  var $data_cadastro;
  var $data_exclusao;
  var $ativo;
  var $nm_aluno;
  var $cpf_responsavel;

  /**
   * Armazena o total de resultados obtidos na ultima chamada ao metodo lista.
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
   * pr�xima chamado ao metodo lista.
   * @var string
   */
  var $_campos_lista;

  /**
   * Lista com todos os campos da tabela separados por v�rgula, padr�o para
   * sele�� no m�todo lista.
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
   * Define o campo padrao para ser usado como padr�o de ordena��o no m�todo lista.
   * @var string
   */
  var $_campo_order_by;

  /**
   * Construtor.
   *
   * @param int $cod_reserva_vaga
   * @param int $ref_ref_cod_escola
   * @param int $ref_ref_cod_serie
   * @param int $ref_usuario_exc
   * @param int $ref_usuario_cad
   * @param int $ref_cod_aluno
   * @param string $data_cadastro
   * @param string $data_exclusao
   * @param int $ativo
   * @param string $nm_aluno
   * @param int $cpf_responsavel
   */
  function clsPmieducarReservaVaga($cod_reserva_vaga = NULL,
    $ref_ref_cod_escola = NULL, $ref_ref_cod_serie = NULL, $ref_usuario_exc = NULL,
    $ref_usuario_cad = NULL, $ref_cod_aluno = NULL, $data_cadastro = NULL,
    $data_exclusao = NULL, $ativo = NULL, $nm_aluno = NULL, $cpf_responsavel = NULL)
  {

    $db = new clsBanco();
    $this->_schema = 'pmieducar.';
    $this->_tabela = $this->_schema . 'reserva_vaga';

    $this->_campos_lista = $this->_todos_campos = "rv.cod_reserva_vaga, rv.ref_ref_cod_escola, rv.ref_ref_cod_serie, rv.ref_usuario_exc, rv.ref_usuario_cad, rv.ref_cod_aluno, rv.data_cadastro, rv.data_exclusao, rv.ativo, rv.nm_aluno, rv.cpf_responsavel";

    if (is_numeric($ref_ref_cod_serie) && is_numeric($ref_ref_cod_escola)) {
      if (class_exists("clsPmieducarEscolaSerie")) {
        $tmp_obj = new clsPmieducarEscolaSerie($ref_ref_cod_escola, $ref_ref_cod_serie);

        if (method_exists($tmp_obj, 'existe')) {
          if ($tmp_obj->existe()) {
            $this->ref_ref_cod_serie = $ref_ref_cod_serie;
            $this->ref_ref_cod_escola = $ref_ref_cod_escola;
          }
        }
        elseif (method_exists($tmp_obj, 'detalhe')) {
          if ($tmp_obj->detalhe()) {
            $this->ref_ref_cod_serie  = $ref_ref_cod_serie;
            $this->ref_ref_cod_escola = $ref_ref_cod_escola;
          }
        }
      }
      else {
        if($db->CampoUnico("SELECT 1 FROM pmieducar.escola_serie WHERE ref_cod_serie = '{$ref_ref_cod_serie}' AND ref_cod_escola = '{$ref_ref_cod_escola}'")) {
          $this->ref_ref_cod_serie = $ref_ref_cod_serie;
          $this->ref_ref_cod_escola = $ref_ref_cod_escola;
        }
      }
    }

    if (is_numeric($ref_usuario_exc)) {
      if (class_exists('clsPmieducarUsuario')) {
        $tmp_obj = new clsPmieducarUsuario($ref_usuario_exc);

        if (method_exists($tmp_obj, 'existe')) {
          if ($tmp_obj->existe()) {
            $this->ref_usuario_exc = $ref_usuario_exc;
          }
        }
        elseif (method_exists($tmp_obj, 'detalhe')) {
          if ($tmp_obj->detalhe()) {
            $this->ref_usuario_exc = $ref_usuario_exc;
          }
        }
      }
      else {
        if ($db->CampoUnico("SELECT 1 FROM pmieducar.usuario WHERE cod_usuario = '{$ref_usuario_exc}'")) {
          $this->ref_usuario_exc = $ref_usuario_exc;
        }
      }
    }

    if (is_numeric($ref_usuario_cad)) {
      if (class_exists('clsPmieducarUsuario')) {
        $tmp_obj = new clsPmieducarUsuario( $ref_usuario_cad );

        if (method_exists($tmp_obj, 'existe')) {
          if ($tmp_obj->existe()) {
            $this->ref_usuario_cad = $ref_usuario_cad;
          }
        }
        elseif (method_exists($tmp_obj, 'detalhe')) {
          if ($tmp_obj->detalhe()) {
            $this->ref_usuario_cad = $ref_usuario_cad;
          }
        }
      }
      else {
        if ($db->CampoUnico( "SELECT 1 FROM pmieducar.usuario WHERE cod_usuario = '{$ref_usuario_cad}'")) {
          $this->ref_usuario_cad = $ref_usuario_cad;
        }
      }
    }

    if (is_numeric($ref_cod_aluno)) {
      if (class_exists('clsPmieducarAluno')) {
        $tmp_obj = new clsPmieducarAluno( $ref_cod_aluno );
        if (method_exists($tmp_obj, 'existe')) {
          if ($tmp_obj->existe()) {
            $this->ref_cod_aluno = $ref_cod_aluno;
          }
        }
        elseif (method_exists($tmp_obj, 'detalhe')) {
          if ($tmp_obj->detalhe()) {
            $this->ref_cod_aluno = $ref_cod_aluno;
          }
        }
      }
      else {
        if ($db->CampoUnico("SELECT 1 FROM pmieducar.aluno WHERE cod_aluno = '{$ref_cod_aluno}'")) {
          $this->ref_cod_aluno = $ref_cod_aluno;
        }
      }
    }

    if (is_numeric($cod_reserva_vaga)) {
      $this->cod_reserva_vaga = $cod_reserva_vaga;
    }

    if (is_string($data_cadastro)) {
      $this->data_cadastro = $data_cadastro;
    }

    if (is_string($data_exclusao)) {
      $this->data_exclusao = $data_exclusao;
    }

    if (is_numeric($ativo)) {
      $this->ativo = $ativo;
    }

    if (is_string($nm_aluno)) {
      $this->nm_aluno = $nm_aluno;
    }

    if (is_numeric($cpf_responsavel)) {
      $this->cpf_responsavel = $cpf_responsavel;
    }
  }

  /**
   * Cria um novo registro.
   * @return int|bool Retorna o valor da sequence ou FALSE em caso de erro.
   */
  function cadastra()
  {
    if (is_numeric($this->ref_ref_cod_escola) &&
      is_numeric($this->ref_ref_cod_serie) && is_numeric($this->ref_usuario_cad) &&
      (is_numeric($this->ref_cod_aluno) || (is_numeric($this->cpf_responsavel) && is_string($this->nm_aluno))))
    {
      $db = new clsBanco();

      $campos = "";
      $valores = "";
      $gruda = "";

      if (is_numeric($this->ref_ref_cod_escola)) {
        $campos .= "{$gruda}ref_ref_cod_escola";
        $valores .= "{$gruda}'{$this->ref_ref_cod_escola}'";
        $gruda = ", ";
      }

      if (is_numeric($this->ref_ref_cod_serie)) {
        $campos .= "{$gruda}ref_ref_cod_serie";
        $valores .= "{$gruda}'{$this->ref_ref_cod_serie}'";
        $gruda = ", ";
      }

      if (is_numeric($this->ref_usuario_cad)) {
        $campos .= "{$gruda}ref_usuario_cad";
        $valores .= "{$gruda}'{$this->ref_usuario_cad}'";
        $gruda = ", ";
      }

      if (is_numeric($this->ref_cod_aluno)) {
        $campos .= "{$gruda}ref_cod_aluno";
        $valores .= "{$gruda}'{$this->ref_cod_aluno}'";
        $gruda = ", ";
      }

      if (is_string($this->nm_aluno)) {
        $campos .= "{$gruda}nm_aluno";
        $valores .= "{$gruda}'{$this->nm_aluno}'";
        $gruda = ", ";
      }

      if (is_numeric($this->cpf_responsavel)) {
        $campos .= "{$gruda}cpf_responsavel";
        $valores .= "{$gruda}'{$this->cpf_responsavel}'";
        $gruda = ", ";
      }

      $campos .= "{$gruda}data_cadastro";
      $valores .= "{$gruda}NOW()";
      $gruda = ", ";

      $campos .= "{$gruda}ativo";
      $valores .= "{$gruda}'1'";
      $gruda = ", ";

      $db->Consulta("INSERT INTO {$this->_tabela} ( $campos ) VALUES( $valores )");
      return $db->InsertId("{$this->_tabela}_cod_reserva_vaga_seq");
    }

    return FALSE;
  }

  /**
   * Atualiza os dados de um registro.
   * @return bool
   */
  function edita()
  {
    if (is_numeric($this->cod_reserva_vaga)) {
      $db = new clsBanco();
      $set = "";

      if (is_numeric($this->ref_ref_cod_escola)) {
        $set .= "{$gruda}ref_ref_cod_escola = '{$this->ref_ref_cod_escola}'";
        $gruda = ", ";
      }

      if (is_numeric($this->ref_ref_cod_serie)) {
        $set .= "{$gruda}ref_ref_cod_serie = '{$this->ref_ref_cod_serie}'";
        $gruda = ", ";
      }

      if (is_numeric($this->ref_usuario_exc)) {
        $set .= "{$gruda}ref_usuario_exc = '{$this->ref_usuario_exc}'";
        $gruda = ", ";
      }

      if (is_numeric($this->ref_usuario_cad)) {
        $set .= "{$gruda}ref_usuario_cad = '{$this->ref_usuario_cad}'";
        $gruda = ", ";
      }

      if (is_numeric($this->ref_cod_aluno)) {
        $set .= "{$gruda}ref_cod_aluno = '{$this->ref_cod_aluno}'";
        $gruda = ", ";
      }

      if (is_string($this->data_cadastro)) {
        $set .= "{$gruda}data_cadastro = '{$this->data_cadastro}'";
        $gruda = ", ";
      }

      $set .= "{$gruda}data_exclusao = NOW()";
      $gruda = ", ";

      if (is_numeric($this->ativo)) {
        $set .= "{$gruda}ativo = '{$this->ativo}'";
        $gruda = ", ";
      }

      if (is_string($this->nm_aluno)) {
        $set .= "{$gruda}nm_aluno = '{$this->nm_aluno}'";
        $gruda = ", ";
      }

      if (is_numeric($this->cpf_responsavel)) {
        $set .= "{$gruda}cpf_responsavel = '{$this->cpf_responsavel}'";
        $gruda = ", ";
      }

      if ($set) {
        $db->Consulta("UPDATE {$this->_tabela} SET $set WHERE cod_reserva_vaga = '{$this->cod_reserva_vaga}'");
        return TRUE;
      }
    }

    return FALSE;
  }

  /**
   * Retorna uma lista de registros filtrados de acordo com os par�metros.
   *
   * @var int $int_cod_reserva_vaga
   * @var int $int_ref_ref_cod_escola
   * @var int $int_ref_ref_cod_serie
   * @var int $int_ref_usuario_exc
   * @var int $int_ref_usuario_cad
   * @var int $int_ref_cod_aluno
   * @var string $date_data_cadastro_ini
   * @var string $date_data_cadastro_fim
   * @var string $date_data_exclusao_ini
   * @var string $date_data_exclusao_fim
   * @var int $int_ativo
   * @var int $int_ref_cod_instituicao
   * @var int $int_ref_cod_curso
   * @var string $str_nm_aluno
   * @var int $int_cpf_responsavel
   * @return array|bool Retorna um array com registro(s) ou FALSE em caso de erro.
   */
  function lista($int_cod_reserva_vaga = NULL, $int_ref_ref_cod_escola = NULL,
    $int_ref_ref_cod_serie = NULL, $int_ref_usuario_exc = NULL,
    $int_ref_usuario_cad = NULL, $int_ref_cod_aluno = NULL,
    $date_data_cadastro_ini = NULL, $date_data_cadastro_fim = NULL,
    $date_data_exclusao_ini = NULL, $date_data_exclusao_fim = NULL,
    $int_ativo = NULL, $int_ref_cod_instituicao = NULL, $int_ref_cod_curso = NULL,
    $str_nm_aluno = NULL, $int_cpf_responsavel = NULL)
  {
    $sql = "SELECT {$this->_campos_lista}, c.ref_cod_instituicao, s.ref_cod_curso FROM {$this->_tabela} rv, {$this->_schema}serie s, {$this->_schema}curso c";

    $whereAnd = " AND ";
    $filtros = " WHERE rv.ref_ref_cod_serie = s.cod_serie AND s.ref_cod_curso = c.cod_curso ";

    if (is_numeric($int_cod_reserva_vaga)) {
      $filtros .= "{$whereAnd} rv.cod_reserva_vaga = '{$int_cod_reserva_vaga}'";
      $whereAnd = " AND ";
    }

    if (is_numeric($int_ref_ref_cod_escola)) {
      $filtros .= "{$whereAnd} rv.ref_ref_cod_escola = '{$int_ref_ref_cod_escola}'";
      $whereAnd = " AND ";
    }

    if (is_numeric($int_ref_ref_cod_serie)) {
      $filtros .= "{$whereAnd} rv.ref_ref_cod_serie = '{$int_ref_ref_cod_serie}'";
      $whereAnd = " AND ";
    }

    if (is_numeric($int_ref_usuario_exc)) {
      $filtros .= "{$whereAnd} rv.ref_usuario_exc = '{$int_ref_usuario_exc}'";
      $whereAnd = " AND ";
    }

    if (is_numeric($int_ref_usuario_cad)) {
      $filtros .= "{$whereAnd} rv.ref_usuario_cad = '{$int_ref_usuario_cad}'";
      $whereAnd = " AND ";
    }

    if (is_numeric($int_ref_cod_aluno)) {
      $filtros .= "{$whereAnd} rv.ref_cod_aluno = '{$int_ref_cod_aluno}'";
      $whereAnd = " AND ";
    }

    if (is_string($date_data_cadastro_ini)) {
      $filtros .= "{$whereAnd} rv.data_cadastro >= '{$date_data_cadastro_ini}'";
      $whereAnd = " AND ";
    }

    if (is_string($date_data_cadastro_fim)) {
      $filtros .= "{$whereAnd} rv.data_cadastro <= '{$date_data_cadastro_fim}'";
      $whereAnd = " AND ";
    }

    if (is_string($date_data_exclusao_ini)) {
      $filtros .= "{$whereAnd} rv.data_exclusao >= '{$date_data_exclusao_ini}'";
      $whereAnd = " AND ";
    }

    if (is_string($date_data_exclusao_fim)) {
      $filtros .= "{$whereAnd} rv.data_exclusao <= '{$date_data_exclusao_fim}'";
      $whereAnd = " AND ";
    }

    if (is_null($int_ativo) || $int_ativo) {
      $filtros .= "{$whereAnd} rv.ativo = '1'";
      $whereAnd = " AND ";
    }
    else {
      $filtros .= "{$whereAnd} rv.ativo = '0'";
      $whereAnd = " AND ";
    }

    if (is_numeric($int_ref_cod_instituicao)) {
      $filtros .= "{$whereAnd} c.ref_cod_instituicao = '{$int_ref_cod_instituicao}'";
      $whereAnd = " AND ";
    }

    if (is_numeric($int_ref_cod_curso)) {
      $filtros .= "{$whereAnd} s.ref_cod_curso = '{$int_ref_cod_curso}'";
      $whereAnd = " AND ";
    }

    if (is_string($str_nm_aluno)) {
      $filtros .= "{$whereAnd} rv.nm_aluno ilike '%{$str_nm_aluno}%'";
      $whereAnd = " AND ";
    }

    if (is_numeric($int_cpf_responsavel)) {
      $filtros .= "{$whereAnd} rv.cpf_responsavel = $int_cpf_responsavel";
      $whereAnd = " AND ";
    }

    $db = new clsBanco();
    $countCampos = count(explode(',', $this->_campos_lista));
    $resultado = array();

    $sql .= $filtros . $this->getOrderby() . $this->getLimite();
    $this->_total = $db->CampoUnico("SELECT COUNT(0) FROM {$this->_tabela} rv, {$this->_schema}serie s, {$this->_schema}curso c {$filtros}");

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
   * Retorna um array com os dados de um registro
   * @return array|bool
   */
  function detalhe()
  {
    if (is_numeric($this->cod_reserva_vaga)) {
      $db = new clsBanco();
      $db->Consulta("SELECT {$this->_todos_campos} FROM {$this->_tabela} rv WHERE rv.cod_reserva_vaga = '{$this->cod_reserva_vaga}'");
      $db->ProximoRegistro();

      return $db->Tupla();
    }

    return FALSE;
  }

  /**
   * Retorna um array com os dados de um registro
   * @return array|bool
   */
  function existe()
  {
    if (is_numeric($this->cod_reserva_vaga)) {
      $db = new clsBanco();
      $db->Consulta("SELECT 1 FROM {$this->_tabela} WHERE cod_reserva_vaga = '{$this->cod_reserva_vaga}'");
      $db->ProximoRegistro();

      return $db->Tupla();
    }

    return FALSE;
  }

  /**
   * Exclui um registro
   *
   * @return bool
   */
  function excluir()
  {
    if (is_numeric($this->cod_reserva_vaga)) {
      $this->ativo = 0;
      return $this->edita();
    }

    return FALSE;
  }

  /**
   * Define quais campos da tabela ser�o selecionados na invoca��o do m�todo lista.
   * @param string $str_campos
   */
  function setCamposLista($str_campos) {
    $this->_campos_lista = $str_campos;
  }

  /**
   * Define que o m�todo lista dever� retornar todos os campos da tabela.
   */
  function resetCamposLista() {
    $this->_campos_lista = $this->_todos_campos;
  }

  /**
   * Define limites de retorno para o m�todo lista.
   * @param int $intLimiteQtd
   * @param int $intLimiteOffset
   */
	function setLimite( $intLimiteQtd, $intLimiteOffset = 0 )
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
      $retorno = ' LIMIT ' . $this->_limite_quantidade;
      if (is_numeric($this->_limite_offset)) {
        $retorno .= ' OFFSET ' . $this->_limite_offset;
      }

      return $retorno;
    }

    return '';
  }

  /**
   * Define campo para ser utilizado como ordena��o no m�todo lista.
   * @param string $strNomeCampo
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
    if( is_string( $this->_campo_order_by ) )
    {
      return ' ORDER BY ' . $this->_campo_order_by;
    }

    return '';
  }

}