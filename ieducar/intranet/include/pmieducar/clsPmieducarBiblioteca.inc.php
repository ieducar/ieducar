<?php
/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
*																	     *
*	@author Prefeitura Municipal de Itajaí								 *
*	@updated 29/03/2007													 *
*   Pacote: i-PLB Software Público Livre e Brasileiro					 *
*																		 *
*	Copyright (C) 2006	PMI - Prefeitura Municipal de Itajaí			 *
*						ctima@itajai.sc.gov.br					    	 *
*																		 *
*	Este  programa  é  software livre, você pode redistribuí-lo e/ou	 *
*	modificá-lo sob os termos da Licença Pública Geral GNU, conforme	 *
*	publicada pela Free  Software  Foundation,  tanto  a versão 2 da	 *
*	Licença   como  (a  seu  critério)  qualquer  versão  mais  nova.	 *
*																		 *
*	Este programa  é distribuído na expectativa de ser útil, mas SEM	 *
*	QUALQUER GARANTIA. Sem mesmo a garantia implícita de COMERCIALI-	 *
*	ZAÇÃO  ou  de ADEQUAÇÃO A QUALQUER PROPÓSITO EM PARTICULAR. Con-	 *
*	sulte  a  Licença  Pública  Geral  GNU para obter mais detalhes.	 *
*																		 *
*	Você  deve  ter  recebido uma cópia da Licença Pública Geral GNU	 *
*	junto  com  este  programa. Se não, escreva para a Free Software	 *
*	Foundation,  Inc.,  59  Temple  Place,  Suite  330,  Boston,  MA	 *
*	02111-1307, USA.													 *
*																		 *
* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */
/**
* @author Prefeitura Municipal de Itajaí
*
* Criado em 25/07/2006 15:19 pelo gerador automatico de classes
*/

require_once( "include/pmieducar/geral.inc.php" );

class clsPmieducarBiblioteca
{
	var $cod_biblioteca;
	var $ref_cod_instituicao;
	var $ref_cod_escola;
	var $nm_biblioteca;
	var $valor_multa;
	var $max_emprestimo;
	var $valor_maximo_multa;
	var $data_cadastro;
	var $data_exclusao;
	var $requisita_senha;
	var $ativo;
	var $dias_espera;
	var $tombo_automatico;

	// propriedades padrao

	/**
	 * Armazena o total de resultados obtidos na ultima chamada ao metodo lista
	 *
	 * @var int
	 */
	var $_total;

	/**
	 * Nome do schema
	 *
	 * @var string
	 */
	var $_schema;

	/**
	 * Nome da tabela
	 *
	 * @var string
	 */
	var $_tabela;

	/**
	 * Lista separada por virgula, com os campos que devem ser selecionados na proxima chamado ao metodo lista
	 *
	 * @var string
	 */
	var $_campos_lista;

	/**
	 * Lista com todos os campos da tabela separados por virgula, padrao para selecao no metodo lista
	 *
	 * @var string
	 */
	var $_todos_campos;

	/**
	 * Valor que define a quantidade de registros a ser retornada pelo metodo lista
	 *
	 * @var int
	 */
	var $_limite_quantidade;

	/**
	 * Define o valor de offset no retorno dos registros no metodo lista
	 *
	 * @var int
	 */
	var $_limite_offset;

	/**
	 * Define o campo padrao para ser usado como padrao de ordenacao no metodo lista
	 *
	 * @var string
	 */
	var $_campo_order_by;


	/**
	 * Construtor (PHP 4)
	 *
	 * @return object
	 */
	function clsPmieducarBiblioteca( $cod_biblioteca = null, $ref_cod_instituicao = null, $ref_cod_escola = null, $nm_biblioteca = null, $valor_multa = null, $max_emprestimo = null, $valor_maximo_multa = null, $data_cadastro = null, $data_exclusao = null, $requisita_senha = null, $ativo = null, $dias_espera = null, $tombo_automatico = null )
	{
		$db = new clsBanco();
		$this->_schema = "pmieducar.";
		$this->_tabela = "{$this->_schema}biblioteca";

		$this->_campos_lista = $this->_todos_campos = "cod_biblioteca, ref_cod_instituicao, ref_cod_escola, nm_biblioteca, valor_multa, max_emprestimo, valor_maximo_multa, data_cadastro, data_exclusao, requisita_senha, ativo, dias_espera, tombo_automatico";

		if( is_numeric( $ref_cod_instituicao ) )
		{
			if( class_exists( "clsPmieducarInstituicao" ) )
			{
				$tmp_obj = new clsPmieducarInstituicao( $ref_cod_instituicao );
				if( method_exists( $tmp_obj, "existe") )
				{
					if( $tmp_obj->existe() )
					{
						$this->ref_cod_instituicao = $ref_cod_instituicao;
					}
				}
				else if( method_exists( $tmp_obj, "detalhe") )
				{
					if( $tmp_obj->detalhe() )
					{
						$this->ref_cod_instituicao = $ref_cod_instituicao;
					}
				}
			}
			else
			{
				if( $db->CampoUnico( "SELECT 1 FROM pmieducar.instituicao WHERE cod_instituicao = '{$ref_cod_instituicao}'" ) )
				{
					$this->ref_cod_instituicao = $ref_cod_instituicao;
				}
			}
		}
		if( is_numeric( $ref_cod_escola ) )
		{
			if( class_exists( "clsPmieducarEscola" ) )
			{
				$tmp_obj = new clsPmieducarEscola( $ref_cod_escola );
				if( method_exists( $tmp_obj, "existe") )
				{
					if( $tmp_obj->existe() )
					{
						$this->ref_cod_escola = $ref_cod_escola;
					}
				}
				else if( method_exists( $tmp_obj, "detalhe") )
				{
					if( $tmp_obj->detalhe() )
					{
						$this->ref_cod_escola = $ref_cod_escola;
					}
				}
			}
			else
			{
				if( $db->CampoUnico( "SELECT 1 FROM pmieducar.escola WHERE cod_escola = '{$ref_cod_escola}'" ) )
				{
					$this->ref_cod_escola = $ref_cod_escola;
				}
			}
		}


		if( is_numeric( $cod_biblioteca ) )
		{
			$this->cod_biblioteca = $cod_biblioteca;
		}
		if( is_string( $nm_biblioteca ) )
		{
			$this->nm_biblioteca = $nm_biblioteca;
		}
		if( is_numeric( $valor_multa ) || $valor_multa == "NULL" )
		{
			$this->valor_multa = $valor_multa;
		}
		if( is_numeric( $max_emprestimo ) || $max_emprestimo == "NULL" )
		{
			$this->max_emprestimo = $max_emprestimo;
		}
		if( is_numeric( $valor_maximo_multa ) || $valor_maximo_multa == "NULL" )
		{
			$this->valor_maximo_multa = $valor_maximo_multa;
		}
		if( is_string( $data_cadastro ) )
		{
			$this->data_cadastro = $data_cadastro;
		}
		if( is_string( $data_exclusao ) )
		{
			$this->data_exclusao = $data_exclusao;
		}
		if( is_numeric( $requisita_senha ) || $requisita_senha == "NULL" )
		{
			$this->requisita_senha = $requisita_senha;
		}
		if( is_numeric( $ativo ) )
		{
			$this->ativo = $ativo;
		}
		if( is_numeric( $dias_espera ) || $dias_espera == "NULL" )
		{
			$this->dias_espera = $dias_espera;
		}
		if (!is_null($tombo_automatico))
		{
			$this->tombo_automatico = $tombo_automatico;
		}
	}

	/**
	 * Cria um novo registro
	 *
	 * @return bool
	 */
	function cadastra()
	{
		if( is_string( $this->nm_biblioteca ) )
		{
			$db = new clsBanco();

			$campos = "";
			$valores = "";
			$gruda = "";

			if( is_numeric( $this->ref_cod_instituicao ) )
			{
				$campos .= "{$gruda}ref_cod_instituicao";
				$valores .= "{$gruda}'{$this->ref_cod_instituicao}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->ref_cod_escola ) )
			{
				$campos .= "{$gruda}ref_cod_escola";
				$valores .= "{$gruda}'{$this->ref_cod_escola}'";
				$gruda = ", ";
			}
			if( is_string( $this->nm_biblioteca ) )
			{
				$campos .= "{$gruda}nm_biblioteca";
				$valores .= "{$gruda}'{$this->nm_biblioteca}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->valor_multa ) )
			{
				$campos .= "{$gruda}valor_multa";
				$valores .= "{$gruda}'{$this->valor_multa}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->max_emprestimo ) )
			{
				$campos .= "{$gruda}max_emprestimo";
				$valores .= "{$gruda}'{$this->max_emprestimo}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->valor_maximo_multa ) )
			{
				$campos .= "{$gruda}valor_maximo_multa";
				$valores .= "{$gruda}'{$this->valor_maximo_multa}'";
				$gruda = ", ";
			}
			$campos .= "{$gruda}data_cadastro";
			$valores .= "{$gruda}NOW()";
			$gruda = ", ";
			if( is_numeric( $this->requisita_senha ) )
			{
				$campos .= "{$gruda}requisita_senha";
				$valores .= "{$gruda}'{$this->requisita_senha}'";
				$gruda = ", ";
			}
			$campos .= "{$gruda}ativo";
			$valores .= "{$gruda}'1'";
			$gruda = ", ";
			if( is_numeric( $this->dias_espera ) )
			{
				$campos .= "{$gruda}dias_espera";
				$valores .= "{$gruda}'{$this->dias_espera}'";
				$gruda = ", ";
			}
			if (!is_null($this->tombo_automatico))
			{
				$campos .= "{$gruda}tombo_automatico";// = {$this->tombo_automatico}";
				$aux = dbBool($this->tombo_automatico) ? "TRUE" : "FALSE";
				$valores .= "{$gruda}{$aux}";
				$gruda = ", ";
			}

			$db->Consulta( "INSERT INTO {$this->_tabela} ( $campos ) VALUES( $valores )" );
			return $db->InsertId( "{$this->_tabela}_cod_biblioteca_seq");
		}
		return false;
	}

	/**
	 * Edita os dados de um registro
	 *
	 * @return bool
	 */
	function edita()
	{
		if( is_numeric( $this->cod_biblioteca ) )
		{

			$db = new clsBanco();
			$set = "";

			if( is_numeric( $this->ref_cod_instituicao ) )
			{
				$set .= "{$gruda}ref_cod_instituicao = '{$this->ref_cod_instituicao}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->ref_cod_escola ) )
			{
				$set .= "{$gruda}ref_cod_escola = '{$this->ref_cod_escola}'";
				$gruda = ", ";
			}
			if( is_string( $this->nm_biblioteca ) )
			{
				$set .= "{$gruda}nm_biblioteca = '{$this->nm_biblioteca}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->valor_multa ) )
			{
				$set .= "{$gruda}valor_multa = '{$this->valor_multa}'";
				$gruda = ", ";
			}
			else if( $this->valor_multa == "NULL" )
			{
				$set .= "{$gruda}valor_multa = {$this->valor_multa}";
				$gruda = ", ";
			}
			if( is_numeric( $this->max_emprestimo ) )
			{
				$set .= "{$gruda}max_emprestimo = '{$this->max_emprestimo}'";
				$gruda = ", ";
			}
			else if( $this->max_emprestimo == "NULL" )
			{
				$set .= "{$gruda}max_emprestimo = {$this->max_emprestimo}";
				$gruda = ", ";
			}
			if( is_numeric( $this->valor_maximo_multa ) )
			{
				$set .= "{$gruda}valor_maximo_multa = '{$this->valor_maximo_multa}'";
				$gruda = ", ";
			}
			else if( $this->valor_maximo_multa == "NULL" )
			{
				$set .= "{$gruda}valor_maximo_multa = {$this->valor_maximo_multa}";
				$gruda = ", ";
			}
			if( is_string( $this->data_cadastro ) )
			{
				$set .= "{$gruda}data_cadastro = '{$this->data_cadastro}'";
				$gruda = ", ";
			}
			$set .= "{$gruda}data_exclusao = NOW()";
			$gruda = ", ";
			if( is_numeric( $this->requisita_senha ) )
			{
				$set .= "{$gruda}requisita_senha = '{$this->requisita_senha}'";
				$gruda = ", ";
			}
			else if( $this->requisita_senha == "NULL" )
			{
				$set .= "{$gruda}requisita_senha = {$this->requisita_senha}";
				$gruda = ", ";
			}
			if( is_numeric( $this->ativo ) )
			{
				$set .= "{$gruda}ativo = '{$this->ativo}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->dias_espera ) )
			{
				$set .= "{$gruda}dias_espera = '{$this->dias_espera}'";
				$gruda = ", ";
			}
			else if( $this->dias_espera == "NULL" )
			{
				$set .= "{$gruda}dias_espera = {$this->dias_espera}";
				$gruda = ", ";
			}
			if (!is_null($this->tombo_automatico))
			{
				$aux = dbBool($this->tombo_automatico) ? "TRUE" : "FALSE";
				$set .= "{$gruda}tombo_automatico = {$aux}";
				$gruda = ", ";
			}
			if( $set )
			{
				$db->Consulta( "UPDATE {$this->_tabela} SET $set WHERE cod_biblioteca = '{$this->cod_biblioteca}'" );
				return true;
			}
		}
		return false;
	}

	/**
	 * Retorna uma lista filtrados de acordo com os parametros
	 *
	 * @return array
	 */
	function lista( $int_cod_biblioteca = null, $int_ref_cod_instituicao = null, $int_ref_cod_escola = null, $str_nm_biblioteca = null, $int_valor_multa = null, $int_max_emprestimo = null, $int_valor_maximo_multa = null, $date_data_cadastro_ini = null, $date_data_cadastro_fim = null, $date_data_exclusao_ini = null, $date_data_exclusao_fim = null, $int_requisita_senha = null, $int_ativo = null, $int_dias_espera = null, $in_biblioteca = null )
	{
		$sql = "SELECT {$this->_campos_lista} FROM {$this->_tabela}";
		$filtros = "";

		$whereAnd = " WHERE ";

		if( is_numeric( $int_cod_biblioteca ) )
		{
			$filtros .= "{$whereAnd} cod_biblioteca = '{$int_cod_biblioteca}'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_ref_cod_instituicao ) )
		{
			$filtros .= "{$whereAnd} ref_cod_instituicao = '{$int_ref_cod_instituicao}'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_ref_cod_escola ) )
		{
			$filtros .= "{$whereAnd} ref_cod_escola = '{$int_ref_cod_escola}'";
			$whereAnd = " AND ";
		}
		if( is_string( $str_nm_biblioteca ) )
		{
			$filtros .= "{$whereAnd} nm_biblioteca LIKE '%{$str_nm_biblioteca}%'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_valor_multa ) )
		{
			$filtros .= "{$whereAnd} valor_multa = '{$int_valor_multa}'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_max_emprestimo ) )
		{
			$filtros .= "{$whereAnd} max_emprestimo = '{$int_max_emprestimo}'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_valor_maximo_multa ) )
		{
			$filtros .= "{$whereAnd} valor_maximo_multa = '{$int_valor_maximo_multa}'";
			$whereAnd = " AND ";
		}
		if( is_string( $date_data_cadastro_ini ) )
		{
			$filtros .= "{$whereAnd} data_cadastro >= '{$date_data_cadastro_ini}'";
			$whereAnd = " AND ";
		}
		if( is_string( $date_data_cadastro_fim ) )
		{
			$filtros .= "{$whereAnd} data_cadastro <= '{$date_data_cadastro_fim}'";
			$whereAnd = " AND ";
		}
		if( is_string( $date_data_exclusao_ini ) )
		{
			$filtros .= "{$whereAnd} data_exclusao >= '{$date_data_exclusao_ini}'";
			$whereAnd = " AND ";
		}
		if( is_string( $date_data_exclusao_fim ) )
		{
			$filtros .= "{$whereAnd} data_exclusao <= '{$date_data_exclusao_fim}'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_requisita_senha ) )
		{
			$filtros .= "{$whereAnd} requisita_senha = '{$int_requisita_senha}'";
			$whereAnd = " AND ";
		}
		if( is_null( $int_ativo ) || $int_ativo )
		{
			$filtros .= "{$whereAnd} ativo = '1'";
			$whereAnd = " AND ";
		}
		else
		{
			$filtros .= "{$whereAnd} ativo = '0'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_dias_espera ) )
		{
			$filtros .= "{$whereAnd} dias_espera = '{$int_dias_espera}'";
			$whereAnd = " AND ";
		}

		if( !empty( $in_biblioteca ) )
		{
			$filtros .= "{$whereAnd} cod_biblioteca in ($in_biblioteca)";
			$whereAnd = " AND ";
		}


		$db = new clsBanco();
		$countCampos = count( explode( ",", $this->_campos_lista ) );
		$resultado = array();

		$sql .= $filtros . $this->getOrderby() . $this->getLimite();

		$this->_total = $db->CampoUnico( "SELECT COUNT(0) FROM {$this->_tabela} {$filtros}" );

		$db->Consulta( $sql );

		if( $countCampos > 1 )
		{
			while ( $db->ProximoRegistro() )
			{
				$tupla = $db->Tupla();

				$tupla["_total"] = $this->_total;
				$resultado[] = $tupla;
			}
		}
		else
		{
			while ( $db->ProximoRegistro() )
			{
				$tupla = $db->Tupla();
				$resultado[] = $tupla[$this->_campos_lista];
			}
		}
		if( count( $resultado ) )
		{
			return $resultado;
		}
		return false;
	}

	/**
	 * Retorna um array com os dados de um registro
	 *
	 * @return array
	 */
	function detalhe()
	{
		if( is_numeric( $this->cod_biblioteca ) )
		{

		$db = new clsBanco();
		$db->Consulta( "SELECT {$this->_todos_campos} FROM {$this->_tabela} WHERE cod_biblioteca = '{$this->cod_biblioteca}'" );
		$db->ProximoRegistro();
		return $db->Tupla();
		}
		return false;
	}

	/**
	 * Retorna um array com os dados de um registro
	 *
	 * @return array
	 */
	function existe()
	{
		if( is_numeric( $this->cod_biblioteca ) )
		{

		$db = new clsBanco();
		$db->Consulta( "SELECT 1 FROM {$this->_tabela} WHERE cod_biblioteca = '{$this->cod_biblioteca}'" );
		$db->ProximoRegistro();
		return $db->Tupla();
		}
		return false;
	}

	/**
	 * Exclui um registro
	 *
	 * @return bool
	 */
	function excluir()
	{
		if( is_numeric( $this->cod_biblioteca ) )
		{

		/*
			delete
		$db = new clsBanco();
		$db->Consulta( "DELETE FROM {$this->_tabela} WHERE cod_biblioteca = '{$this->cod_biblioteca}'" );
		return true;
		*/

		$this->ativo = 0;
			return $this->edita();
		}
		return false;
	}

	/**
	 * Define quais campos da tabela serao selecionados na invocacao do metodo lista
	 *
	 * @return null
	 */
	function setCamposLista( $str_campos )
	{
		$this->_campos_lista = $str_campos;
	}

	/**
	 * Define que o metodo Lista devera retornoar todos os campos da tabela
	 *
	 * @return null
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
	function setLimite( $intLimiteQtd, $intLimiteOffset = null )
	{
		$this->_limite_quantidade = $intLimiteQtd;
		$this->_limite_offset = $intLimiteOffset;
	}

	/**
	 * Retorna a string com o trecho da query resposavel pelo Limite de registros
	 *
	 * @return string
	 */
	function getLimite()
	{
		if( is_numeric( $this->_limite_quantidade ) )
		{
			$retorno = " LIMIT {$this->_limite_quantidade}";
			if( is_numeric( $this->_limite_offset ) )
			{
				$retorno .= " OFFSET {$this->_limite_offset} ";
			}
			return $retorno;
		}
		return "";
	}

	/**
	 * Define campo para ser utilizado como ordenacao no metolo lista
	 *
	 * @return null
	 */
	function setOrderby( $strNomeCampo )
	{
		// limpa a string de possiveis erros (delete, insert, etc)
		//$strNomeCampo = eregi_replace();

		if( is_string( $strNomeCampo ) && $strNomeCampo )
		{
			$this->_campo_order_by = $strNomeCampo;
		}
	}

	/**
	 * Retorna a string com o trecho da query resposavel pela Ordenacao dos registros
	 *
	 * @return string
	 */
	function getOrderby()
	{
		if( is_string( $this->_campo_order_by ) )
		{
			return " ORDER BY {$this->_campo_order_by} ";
		}
		return "";
	}

}
?>