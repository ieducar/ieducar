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
 * @package   iEd_Portal
 * @since     Arquivo dispon�vel desde a vers�o 1.0.0
 * @version   $Id$
 */

/**
 * clsPortalSmsProntuario class.
 *
 * @author    Prefeitura Municipal de Itaja� <ctima@itajai.sc.gov.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   iEd_Portal
 * @since     Classe dispon�vel desde a vers�o 1.0.0
 * @version   @@package_version@@
 */
class clsPortalSmsProntuario
{
  var $cod_prontuario;
  var $ref_pessoa_exc;
  var $ref_pessoa_cad;
  var $num_prontuario;
  var $nm_paciente;
  var $data_nascimento;
  var $data_cadastro;
  var $data_exclusao;
  var $ativo;

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
  function __construct($cod_prontuario = NULL, $ref_pessoa_exc = NULL,
    $ref_pessoa_cad = NULL, $num_prontuario = NULL, $nm_paciente = NULL,
    $data_nascimento = NULL, $data_cadastro = NULL, $data_exclusao = NULL, $ativo = NULL)
  {
    $db = new clsBanco();
    $this->_schema = 'portal.';
    $this->_tabela = "{$this->_schema}sms_prontuario";

    $this->_campos_lista = $this->_todos_campos = 'cod_prontuario, ref_pessoa_exc, ref_pessoa_cad, num_prontuario, nm_paciente, data_nascimento, data_cadastro, data_exclusao, ativo';

    if (is_numeric($cod_prontuario)) {
      $this->cod_prontuario = $cod_prontuario;
    }

    if (is_numeric($ref_pessoa_exc)) {
      $tmp_obj = new clsFuncionario($ref_pessoa_exc);
      if ($tmp_obj->detalhe()) {
        $this->ref_pessoa_exc = $ref_pessoa_exc;
      }
    }

    if (is_numeric($ref_pessoa_cad)) {
      $tmp_obj = new clsFuncionario($ref_pessoa_cad);
      if ($tmp_obj->detalhe()) {
        $this->ref_pessoa_cad = $ref_pessoa_cad;
      }
    }

    if (is_numeric($num_prontuario)) {
      $this->num_prontuario = $num_prontuario;
    }

    if (is_numeric($ativo)) {
      $this->ativo = $ativo;
    }

    if (is_string($nm_paciente)) {
      $this->nm_paciente = $nm_paciente;
    }

    if (is_string($data_nascimento)) {
      $this->data_nascimento = $data_nascimento;
    }

    if (is_string($data_cadastro)) {
      $this->data_cadastro = $data_cadastro;
    }

    if (is_string($data_exclusao)) {
      $this->data_exclusao = $data_exclusao;
    }
  }

  /**
   * Cria um novo registro.
   * @return bool
   */
  function cadastra()
  {
    if (is_numeric($this->ref_pessoa_cad) && is_numeric($this->num_prontuario) &&
      is_numeric($this->ativo) && is_string($this->nm_paciente)) {
      $db = new clsBanco();

      $campos  = '';
      $valores = '';
      $gruda   = '';

      if (is_numeric($this->cod_prontuario)) {
        $campos .= "{$gruda}cod_prontuario";
        $valores .= "{$gruda}'{$this->cod_prontuario}'";
        $gruda = ", ";
      }

      if (is_numeric($this->ref_pessoa_exc)) {
        $campos .= "{$gruda}ref_pessoa_exc";
        $valores .= "{$gruda}'{$this->ref_pessoa_exc}'";
        $gruda = ", ";
      }

      if (is_numeric($this->ref_pessoa_cad)) {
        $campos .= "{$gruda}ref_pessoa_cad";
        $valores .= "{$gruda}'{$this->ref_pessoa_cad}'";
        $gruda = ", ";
      }

      if (is_numeric($this->num_prontuario)) {
        $campos .= "{$gruda}num_prontuario";
        $valores .= "{$gruda}'{$this->num_prontuario}'";
        $gruda = ", ";
      }

      if (is_string($this->nm_paciente)) {
        $campos .= "{$gruda}nm_paciente";
        $valores .= "{$gruda}'{$this->nm_paciente}'";
        $gruda = ", ";
      }

      if (is_string($this->data_nascimento)) {
        $campos .= "{$gruda}data_nascimento";
        $valores .= "{$gruda}'{$this->data_nascimento}'";
        $gruda = ", ";
      }

      $campos  .= "{$gruda}data_cadastro";
      $valores .= "{$gruda}NOW()";
      $gruda    = ", ";

      $db->Consulta("INSERT INTO {$this->_tabela} ( $campos ) VALUES( $valores )");
      return $db->InsertId("{$this->_tabela}_cod_prontuario_seq");
    }

    return FALSE;
  }

  /**
   * Edita os dados de um registro.
   * @return bool
   */
  function edita()
  {
    if (is_numeric($this->cod_prontuario)) {
      $db  = new clsBanco();
      $set = '';

      if (is_numeric($this->ref_pessoa_exc)) {
        $set .= "{$gruda}ref_pessoa_exc = '{$this->ref_pessoa_exc}'";
        $gruda = ", ";
      }

      if (is_numeric($this->ref_pessoa_cad)) {
        $set .= "{$gruda}ref_pessoa_cad = '{$this->ref_pessoa_cad}'";
        $gruda = ", ";
      }

      if (is_numeric($this->num_prontuario)) {
        $set .= "{$gruda}num_prontuario = '{$this->num_prontuario}'";
        $gruda = ", ";
      }

      if (is_numeric($this->ativo)) {
        $set .= "{$gruda}ativo = '{$this->ativo}'";
        $gruda = ", ";
      }

      if (is_string($this->nm_paciente)) {
        $set .= "{$gruda}nm_paciente = '{$this->nm_paciente}'";
        $gruda = ", ";
      }

      if (is_string($this->data_nascimento)) {
        $set .= "{$gruda}data_nascimento = '{$this->data_nascimento}'";
        $gruda = ", ";
      }

      $set .= "{$gruda}data_exclusao = NOW()";
      $gruda = ", ";

      if ($set) {
        $db->Consulta("UPDATE {$this->_tabela} SET $set WHERE cod_prontuario = '{$this->cod_prontuario}'");
        return TRUE;
      }
    }

    return FALSE;
  }

  /**
   * Retorna uma lista de registros filtrados de acordo com os par�metros.
   * @return array
   */
  function lista($int_cod_prontuario = NULL, $int_ref_pessoa_exc = NULL,
    $int_ref_pessoa_cad = NULL, $int_num_prontuario = NULL, $int_ativo = NULL,
    $str_nm_paciente = NULL, $str_data_nascimento = NULL,
    $str_data_cadastro_inicio = NULL, $str_data_cadastro_fim = NULL,
    $str_data_exclusao_inicio = NULL, $str_data_exclusao_fim = NULL)
  {
    $db = new clsBanco();
    $sql = "SELECT {$this->_campos_lista} FROM {$this->_tabela}";
    $filtros = '';

    $whereAnd = ' WHERE ';

    if (is_numeric($int_cod_prontuario)) {
      $filtros .= "{$whereAnd} cod_prontuario = '{$int_cod_prontuario}'";
      $whereAnd = " AND ";
    }

    if (is_numeric($int_ref_pessoa_exc)) {
      $filtros .= "{$whereAnd} ref_pessoa_exc = '{$int_ref_pessoa_exc}'";
      $whereAnd = " AND ";
    }

    if (is_numeric($int_ref_pessoa_cad)) {
      $filtros .= "{$whereAnd} ref_pessoa_cad = '{$int_ref_pessoa_cad}'";
      $whereAnd = " AND ";
    }

    if (is_numeric($int_num_prontuario)) {
      $filtros .= "{$whereAnd} num_prontuario = '{$int_num_prontuario}'";
      $whereAnd = " AND ";
    }

    if (is_numeric($int_ativo)) {
      $filtros .= "{$whereAnd} ativo = '{$int_ativo}'";
      $whereAnd = " AND ";
    }

    if (is_string($str_nm_paciente)) {
      $filtros .= "{$whereAnd} to_ascii(nm_paciente) ILIKE  to_ascii('%{$str_nm_paciente}%')";
      $whereAnd = " AND ";
    }

    if (is_string($str_data_nascimento)) {
      $filtros .= "{$whereAnd} data_nascimento = '{$str_data_nascimento}'";
      $whereAnd = " AND ";
    }

    if (is_string($str_data_cadastro_inicio)) {
      $filtros .= "{$whereAnd} data_cadastro >= '{$str_data_cadastro_inicio}'";
      $whereAnd = " AND ";
    }

    if (is_string($str_data_cadastro_fim)) {
      $filtros .= "{$whereAnd} data_cadastro <= '{$str_data_cadastro_fim}'";
      $whereAnd = " AND ";
    }

    if (is_string($str_data_exclusao_inicio)) {
      $filtros .= "{$whereAnd} data_exclusao >= '{$str_data_exclusao_inicio}'";
      $whereAnd = " AND ";
    }

    if (is_string($str_data_exclusao_fim)) {
      $filtros .= "{$whereAnd} data_exclusao <= '{$str_data_exclusao_fim}'";
      $whereAnd = " AND ";
    }

    $countCampos = count(explode(',', $this->_campos_lista));
    $resultado = array();

    $sql .= $filtros . $this->getOrderby() . $this->getLimite();

    $this->_total = $db->CampoUnico("SELECT COUNT(0) FROM {$this->_tabela} {$filtros}");
    $db->Consulta($sql);

    if ($countCampos > 1) {
      while ($db->ProximoRegistro()) {
        $tupla = $db->Tupla();

        $tupla['_total'] = $this->_total;
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
    if (is_numeric($this->cod_prontuario)) {
      if($this->ativo != false) {
        $where = " and ativo = 1 ";
      }

      $db = new clsBanco();
      $db->Consulta("SELECT {$this->_todos_campos} FROM {$this->_tabela} WHERE cod_prontuario = '{$this->cod_prontuario}' {$where}");
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
    if (is_numeric($this->cod_prontuario)) {
      $this->ativo = 0;
      return $this->edita();
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