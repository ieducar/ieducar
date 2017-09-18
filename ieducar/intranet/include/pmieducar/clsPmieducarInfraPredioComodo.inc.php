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
* Criado em 28/06/2006 14:42 pelo gerador automatico de classes
*/

require_once( "include/pmieducar/geral.inc.php" );

class clsPmieducarInfraPredioComodo
{
	var $cod_infra_predio_comodo;
	var $ref_usuario_exc;
	var $ref_usuario_cad;
	var $ref_cod_infra_comodo_funcao;
	var $ref_cod_infra_predio;
	var $nm_comodo;
	var $desc_comodo;
	var $area;
	var $data_cadastro;
	var $data_exclusao;
	var $ativo;

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
	function clsPmieducarInfraPredioComodo( $cod_infra_predio_comodo = null, $ref_usuario_exc = null, $ref_usuario_cad = null, $ref_cod_infra_comodo_funcao = null, $ref_cod_infra_predio = null, $nm_comodo = null, $desc_comodo = null, $area = null, $data_cadastro = null, $data_exclusao = null, $ativo = null )
	{
		$db = new clsBanco();
		$this->_schema = "pmieducar.";
		$this->_tabela = "{$this->_schema}infra_predio_comodo";

		$this->_campos_lista = $this->_todos_campos = "ipc.cod_infra_predio_comodo, ipc.ref_usuario_exc, ipc.ref_usuario_cad, ipc.ref_cod_infra_comodo_funcao, ipc.ref_cod_infra_predio, ipc.nm_comodo, ipc.desc_comodo, ipc.area, ipc.data_cadastro, ipc.data_exclusao, ipc.ativo";

		if( is_numeric( $ref_cod_infra_comodo_funcao ) )
		{
			if( class_exists( "clsPmieducarInfraComodoFuncao" ) )
			{
				$tmp_obj = new clsPmieducarInfraComodoFuncao( $ref_cod_infra_comodo_funcao );
				if( method_exists( $tmp_obj, "existe") )
				{
					if( $tmp_obj->existe() )
					{
						$this->ref_cod_infra_comodo_funcao = $ref_cod_infra_comodo_funcao;
					}
				}
				else if( method_exists( $tmp_obj, "detalhe") )
				{
					if( $tmp_obj->detalhe() )
					{
						$this->ref_cod_infra_comodo_funcao = $ref_cod_infra_comodo_funcao;
					}
				}
			}
			else
			{
				if( $db->CampoUnico( "SELECT 1 FROM pmieducar.infra_comodo_funcao WHERE cod_infra_comodo_funcao = '{$ref_cod_infra_comodo_funcao}'" ) )
				{
					$this->ref_cod_infra_comodo_funcao = $ref_cod_infra_comodo_funcao;
				}
			}
		}
		if( is_numeric( $ref_usuario_exc ) )
		{
			if( class_exists( "clsPmieducarUsuario" ) )
			{
				$tmp_obj = new clsPmieducarUsuario( $ref_usuario_exc );
				if( method_exists( $tmp_obj, "existe") )
				{
					if( $tmp_obj->existe() )
					{
						$this->ref_usuario_exc = $ref_usuario_exc;
					}
				}
				else if( method_exists( $tmp_obj, "detalhe") )
				{
					if( $tmp_obj->detalhe() )
					{
						$this->ref_usuario_exc = $ref_usuario_exc;
					}
				}
			}
			else
			{
				if( $db->CampoUnico( "SELECT 1 FROM pmieducar.usuario WHERE cod_usuario = '{$ref_usuario_exc}'" ) )
				{
					$this->ref_usuario_exc = $ref_usuario_exc;
				}
			}
		}
		if( is_numeric( $ref_usuario_cad ) )
		{
			if( class_exists( "clsPmieducarUsuario" ) )
			{
				$tmp_obj = new clsPmieducarUsuario( $ref_usuario_cad );
				if( method_exists( $tmp_obj, "existe") )
				{
					if( $tmp_obj->existe() )
					{
						$this->ref_usuario_cad = $ref_usuario_cad;
					}
				}
				else if( method_exists( $tmp_obj, "detalhe") )
				{
					if( $tmp_obj->detalhe() )
					{
						$this->ref_usuario_cad = $ref_usuario_cad;
					}
				}
			}
			else
			{
				if( $db->CampoUnico( "SELECT 1 FROM pmieducar.usuario WHERE cod_usuario = '{$ref_usuario_cad}'" ) )
				{
					$this->ref_usuario_cad = $ref_usuario_cad;
				}
			}
		}
		if( is_numeric( $ref_cod_infra_predio ) )
		{
			if( class_exists( "clsPmieducarInfraPredio" ) )
			{
				$tmp_obj = new clsPmieducarInfraPredio( $ref_cod_infra_predio );
				if( method_exists( $tmp_obj, "existe") )
				{
					if( $tmp_obj->existe() )
					{
						$this->ref_cod_infra_predio = $ref_cod_infra_predio;
					}
				}
				else if( method_exists( $tmp_obj, "detalhe") )
				{
					if( $tmp_obj->detalhe() )
					{
						$this->ref_cod_infra_predio = $ref_cod_infra_predio;
					}
				}
			}
			else
			{
				if( $db->CampoUnico( "SELECT 1 FROM pmieducar.infra_predio WHERE cod_infra_predio = '{$ref_cod_infra_predio}'" ) )
				{
					$this->ref_cod_infra_predio = $ref_cod_infra_predio;
				}
			}
		}


		if( is_numeric( $cod_infra_predio_comodo ) )
		{
			$this->cod_infra_predio_comodo = $cod_infra_predio_comodo;
		}
		if( is_string( $nm_comodo ) )
		{
			$this->nm_comodo = $nm_comodo;
		}
		if( is_string( $desc_comodo ) )
		{
			$this->desc_comodo = $desc_comodo;
		}
		if( is_numeric( $area ) )
		{
			$this->area = $area;
		}
		if( is_string( $data_cadastro ) )
		{
			$this->data_cadastro = $data_cadastro;
		}
		if( is_string( $data_exclusao ) )
		{
			$this->data_exclusao = $data_exclusao;
		}
		if( is_numeric( $ativo ) )
		{
			$this->ativo = $ativo;
		}

	}

	/**
	 * Cria um novo registro
	 *
	 * @return bool
	 */
	function cadastra()
	{
		if( is_numeric( $this->ref_usuario_cad ) && is_numeric( $this->ref_cod_infra_comodo_funcao ) && is_numeric( $this->ref_cod_infra_predio ) && is_string( $this->nm_comodo ) && is_numeric( $this->area ) )
		{
			$db = new clsBanco();

			$campos = "";
			$valores = "";
			$gruda = "";

			if( is_numeric( $this->ref_usuario_cad ) )
			{
				$campos .= "{$gruda}ref_usuario_cad";
				$valores .= "{$gruda}'{$this->ref_usuario_cad}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->ref_cod_infra_comodo_funcao ) )
			{
				$campos .= "{$gruda}ref_cod_infra_comodo_funcao";
				$valores .= "{$gruda}'{$this->ref_cod_infra_comodo_funcao}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->ref_cod_infra_predio ) )
			{
				$campos .= "{$gruda}ref_cod_infra_predio";
				$valores .= "{$gruda}'{$this->ref_cod_infra_predio}'";
				$gruda = ", ";
			}
			if( is_string( $this->nm_comodo ) )
			{
				$campos .= "{$gruda}nm_comodo";
				$valores .= "{$gruda}'{$this->nm_comodo}'";
				$gruda = ", ";
			}
			if( is_string( $this->desc_comodo ) )
			{
				$campos .= "{$gruda}desc_comodo";
				$valores .= "{$gruda}'{$this->desc_comodo}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->area ) )
			{
				$campos .= "{$gruda}area";
				$valores .= "{$gruda}'{$this->area}'";
				$gruda = ", ";
			}
			$campos .= "{$gruda}data_cadastro";
			$valores .= "{$gruda}NOW()";
			$gruda = ", ";
			$campos .= "{$gruda}ativo";
			$valores .= "{$gruda}'1'";
			$gruda = ", ";


			$db->Consulta( "INSERT INTO {$this->_tabela} ( $campos ) VALUES( $valores )" );
			return $db->InsertId( "{$this->_tabela}_cod_infra_predio_comodo_seq");
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
		if( is_numeric( $this->cod_infra_predio_comodo ) && is_numeric( $this->ref_usuario_exc ) )
		{

			$db = new clsBanco();
			$set = "";

			if( is_numeric( $this->ref_usuario_exc ) )
			{
				$set .= "{$gruda}ref_usuario_exc = '{$this->ref_usuario_exc}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->ref_usuario_cad ) )
			{
				$set .= "{$gruda}ref_usuario_cad = '{$this->ref_usuario_cad}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->ref_cod_infra_comodo_funcao ) )
			{
				$set .= "{$gruda}ref_cod_infra_comodo_funcao = '{$this->ref_cod_infra_comodo_funcao}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->ref_cod_infra_predio ) )
			{
				$set .= "{$gruda}ref_cod_infra_predio = '{$this->ref_cod_infra_predio}'";
				$gruda = ", ";
			}
			if( is_string( $this->nm_comodo ) )
			{
				$set .= "{$gruda}nm_comodo = '{$this->nm_comodo}'";
				$gruda = ", ";
			}
			if( is_string( $this->desc_comodo ) )
			{
				$set .= "{$gruda}desc_comodo = '{$this->desc_comodo}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->area ) )
			{
				$set .= "{$gruda}area = '{$this->area}'";
				$gruda = ", ";
			}
			if( is_string( $this->data_cadastro ) )
			{
				$set .= "{$gruda}data_cadastro = '{$this->data_cadastro}'";
				$gruda = ", ";
			}
			$set .= "{$gruda}data_exclusao = NOW()";
			$gruda = ", ";
			if( is_numeric( $this->ativo ) )
			{
				$set .= "{$gruda}ativo = '{$this->ativo}'";
				$gruda = ", ";
			}


			if( $set )
			{
				$db->Consulta( "UPDATE {$this->_tabela} SET $set WHERE cod_infra_predio_comodo = '{$this->cod_infra_predio_comodo}'" );
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
	function lista( $int_cod_infra_predio_comodo = null, $int_ref_usuario_exc = null, $int_ref_usuario_cad = null, $int_ref_cod_infra_comodo_funcao = null, $int_ref_cod_infra_predio = null, $str_nm_comodo = null, $str_desc_comodo = null, $int_area = null, $date_data_cadastro_ini = null, $date_data_cadastro_fim = null, $date_data_exclusao_ini = null, $date_data_exclusao_fim = null, $int_ativo = null,$int_ref_cod_escola = null,$int_ref_cod_instituicao = null )
	{
		$sql = "SELECT {$this->_campos_lista}, ip.ref_cod_escola, e.ref_cod_instituicao FROM {$this->_tabela} ipc, {$this->_schema}infra_predio ip, {$this->_schema}escola e ";

		$whereAnd = " AND ";
		$filtros = " WHERE ipc.ref_cod_infra_predio = ip.cod_infra_predio AND ip.ref_cod_escola = e.cod_escola ";

		if( is_numeric( $int_cod_infra_predio_comodo ) )
		{
			$filtros .= "{$whereAnd} ipc.cod_infra_predio_comodo = '{$int_cod_infra_predio_comodo}'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_ref_usuario_exc ) )
		{
			$filtros .= "{$whereAnd} ipc.ref_usuario_exc = '{$int_ref_usuario_exc}'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_ref_usuario_cad ) )
		{
			$filtros .= "{$whereAnd} ipc.ref_usuario_cad = '{$int_ref_usuario_cad}'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_ref_cod_infra_comodo_funcao ) )
		{
			$filtros .= "{$whereAnd} ipc.ref_cod_infra_comodo_funcao = '{$int_ref_cod_infra_comodo_funcao}'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_ref_cod_infra_predio ) )
		{
			$filtros .= "{$whereAnd} ipc.ref_cod_infra_predio = '{$int_ref_cod_infra_predio}'";
			$whereAnd = " AND ";
		}
		if( is_string( $str_nm_comodo ) )
		{
			$filtros .= "{$whereAnd} ipc.nm_comodo LIKE '%{$str_nm_comodo}%'";
			$whereAnd = " AND ";
		}
		if( is_string( $str_desc_comodo ) )
		{
			$filtros .= "{$whereAnd} ipc.desc_comodo LIKE '%{$str_desc_comodo}%'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_area ) )
		{
			$filtros .= "{$whereAnd} ipc.area = '{$int_area}'";
			$whereAnd = " AND ";
		}
		if( is_string( $date_data_cadastro_ini ) )
		{
			$filtros .= "{$whereAnd} ipc.data_cadastro >= '{$date_data_cadastro_ini}'";
			$whereAnd = " AND ";
		}
		if( is_string( $date_data_cadastro_fim ) )
		{
			$filtros .= "{$whereAnd} ipc.data_cadastro <= '{$date_data_cadastro_fim}'";
			$whereAnd = " AND ";
		}
		if( is_string( $date_data_exclusao_ini ) )
		{
			$filtros .= "{$whereAnd} ipc.data_exclusao >= '{$date_data_exclusao_ini}'";
			$whereAnd = " AND ";
		}
		if( is_string( $date_data_exclusao_fim ) )
		{
			$filtros .= "{$whereAnd} ipc.data_exclusao <= '{$date_data_exclusao_fim}'";
			$whereAnd = " AND ";
		}
		if( is_null( $int_ativo ) || $int_ativo )
		{
			$filtros .= "{$whereAnd} ipc.ativo = '1'";
			$whereAnd = " AND ";
		}
		else
		{
			$filtros .= "{$whereAnd} ipc.ativo = '0'";
			$whereAnd = " AND ";
		}

		if( is_numeric( $int_ref_cod_escola ) )
		{
			$filtros .= "{$whereAnd} ip.ref_cod_escola = {$int_ref_cod_escola} ";
			$whereAnd = " AND ";
		}

		if( is_numeric( $int_ref_cod_instituicao ) )
		{
			$filtros .= "{$whereAnd} e.ref_cod_instituicao = {$int_ref_cod_instituicao} ";
			$whereAnd = " AND ";
		}
//		if( is_numeric( $int_ref_cod_escola ) )
//		{
//			$filtros .= "{$whereAnd} ref_cod_infra_predio in (SELECT cod_infra_predio from pmieducar.infra_predio where ref_cod_escola = {$int_ref_cod_escola}) ";
//			$whereAnd = " AND ";
//		}
//
//		if( is_numeric( $int_ref_cod_instituicao ) )
//		{
//			$filtros .= "{$whereAnd} ref_cod_infra_predio in (SELECT cod_infra_predio from pmieducar.infra_predio where ref_cod_escola in (SELECT cod_escola from pmieducar.escola where ref_cod_instituicao = $int_ref_cod_instituicao)) ";
//			$whereAnd = " AND ";
//		}

		$db = new clsBanco();
		$countCampos = count( explode( ",", $this->_campos_lista ) );
		$resultado = array();

		$sql .= $filtros . $this->getOrderby() . $this->getLimite();

		$this->_total = $db->CampoUnico( "SELECT COUNT(0) FROM {$this->_tabela} ipc, {$this->_schema}infra_predio ip, {$this->_schema}escola e {$filtros}" );

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
		if( is_numeric( $this->cod_infra_predio_comodo ) )
		{

		$db = new clsBanco();
		$db->Consulta( "SELECT {$this->_todos_campos} FROM {$this->_tabela} ipc WHERE ipc.cod_infra_predio_comodo = '{$this->cod_infra_predio_comodo}'" );
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
		if( is_numeric( $this->cod_infra_predio_comodo ) )
		{

		$db = new clsBanco();
		$db->Consulta( "SELECT 1 FROM {$this->_tabela} WHERE cod_infra_predio_comodo = '{$this->cod_infra_predio_comodo}'" );
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
		if( is_numeric( $this->cod_infra_predio_comodo ) && is_numeric( $this->ref_usuario_exc ) )
		{

		/*
			delete
		$db = new clsBanco();
		$db->Consulta( "DELETE FROM {$this->_tabela} WHERE cod_infra_predio_comodo = '{$this->cod_infra_predio_comodo}'" );
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