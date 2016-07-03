<?php
/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
*																	     *
*	@author Prefeitura Municipal de Itaja�								 *
*	@updated 29/03/2007													 *
*   Pacote: i-PLB Software P�blico Livre e Brasileiro					 *
*																		 *
*	Copyright (C) 2006	PMI - Prefeitura Municipal de Itaja�			 *
*						ctima@itajai.sc.gov.br					    	 *
*																		 *
*	Este  programa  �  software livre, voc� pode redistribu�-lo e/ou	 *
*	modific�-lo sob os termos da Licen�a P�blica Geral GNU, conforme	 *
*	publicada pela Free  Software  Foundation,  tanto  a vers�o 2 da	 *
*	Licen�a   como  (a  seu  crit�rio)  qualquer  vers�o  mais  nova.	 *
*																		 *
*	Este programa  � distribu�do na expectativa de ser �til, mas SEM	 *
*	QUALQUER GARANTIA. Sem mesmo a garantia impl�cita de COMERCIALI-	 *
*	ZA��O  ou  de ADEQUA��O A QUALQUER PROP�SITO EM PARTICULAR. Con-	 *
*	sulte  a  Licen�a  P�blica  Geral  GNU para obter mais detalhes.	 *
*																		 *
*	Voc�  deve  ter  recebido uma c�pia da Licen�a P�blica Geral GNU	 *
*	junto  com  este  programa. Se n�o, escreva para a Free Software	 *
*	Foundation,  Inc.,  59  Temple  Place,  Suite  330,  Boston,  MA	 *
*	02111-1307, USA.													 *
*																		 *
* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */
/**
* @author Prefeitura Municipal de Itaja�
*
* Criado em 22/11/2006 14:42 pelo gerador automatico de classes
*/

require_once( "include/pmidrh/geral.inc.php" );

class clsPmidrhPortariaCamposTabelaValor
{
	var $sequencial;
	var $ref_cod_campo_tabela;
	var $ref_cod_portaria;
	var $valor;

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
	 * @param integer sequencial
	 * @param integer ref_cod_campo_tabela
	 * @param integer ref_cod_portaria
	 * @param string valor
	 *
	 * @return object
	 */
	function clsPmidrhPortariaCamposTabelaValor( $sequencial = null, $ref_cod_campo_tabela = null, $ref_cod_portaria = null, $valor = null )
	{
		$db = new clsBanco();
		$this->_schema = "pmidrh.";
		$this->_tabela = "{$this->_schema}portaria_campos_tabela_valor";

		$this->_campos_lista = $this->_todos_campos = "sequencial, ref_cod_campo_tabela, ref_cod_portaria, valor";

		if( is_numeric( $ref_cod_portaria ) )
		{
			if( class_exists( "clsPmidrhPortaria" ) )
			{
				$tmp_obj = new clsPmidrhPortaria( $ref_cod_portaria );
				if( method_exists( $tmp_obj, "existe") )
				{
					if( $tmp_obj->existe() )
					{
						$this->ref_cod_portaria = $ref_cod_portaria;
					}
				}
				else if( method_exists( $tmp_obj, "detalhe") )
				{
					if( $tmp_obj->detalhe() )
					{
						$this->ref_cod_portaria = $ref_cod_portaria;
					}
				}
			}
			else
			{
				if( $db->CampoUnico( "SELECT 1 FROM pmidrh.portaria WHERE cod_portaria = '{$ref_cod_portaria}'" ) )
				{
					$this->ref_cod_portaria = $ref_cod_portaria;
				}
			}
		}
		if( is_numeric( $ref_cod_campo_tabela ) )
		{
			if( class_exists( "clsPmidrhPortariaCamposTabela" ) )
			{
				$tmp_obj = new clsPmidrhPortariaCamposTabela( $ref_cod_campo_tabela );
				if( method_exists( $tmp_obj, "existe") )
				{
					if( $tmp_obj->existe() )
					{
						$this->ref_cod_campo_tabela = $ref_cod_campo_tabela;
					}
				}
				else if( method_exists( $tmp_obj, "detalhe") )
				{
					if( $tmp_obj->detalhe() )
					{
						$this->ref_cod_campo_tabela = $ref_cod_campo_tabela;
					}
				}
			}
			else
			{
				if( $db->CampoUnico( "SELECT 1 FROM pmidrh.portaria_campos_tabela WHERE cod_campo_tabela = '{$ref_cod_campo_tabela}'" ) )
				{
					$this->ref_cod_campo_tabela = $ref_cod_campo_tabela;
				}
			}
		}


		if( is_numeric( $sequencial ) )
		{
			$this->sequencial = $sequencial;
		}
		if( is_string( $valor ) )
		{
			$this->valor = $valor;
		}

	}

	/**
	 * Cria um novo registro
	 *
	 * @return bool
	 */
	function cadastra()
	{
		if( is_numeric( $this->sequencial ) && is_numeric( $this->ref_cod_campo_tabela ) && is_numeric( $this->ref_cod_portaria ) && is_string( $this->valor ) )
		{
			$db = new clsBanco();
			$campos = "";
			$valores = "";
			$gruda = "";

			if( is_numeric( $this->sequencial ) )
			{
				$campos .= "{$gruda}sequencial";
				$valores .= "{$gruda}'{$this->sequencial}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->ref_cod_campo_tabela ) )
			{
				$campos .= "{$gruda}ref_cod_campo_tabela";
				$valores .= "{$gruda}'{$this->ref_cod_campo_tabela}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->ref_cod_portaria ) )
			{
				$campos .= "{$gruda}ref_cod_portaria";
				$valores .= "{$gruda}'{$this->ref_cod_portaria}'";
				$gruda = ", ";
			}
			if( is_string( $this->valor ) )
			{
				$campos .= "{$gruda}valor";
				$valores .= "{$gruda}'{$this->valor}'";
				$gruda = ", ";
			}


			$db->Consulta( "INSERT INTO {$this->_tabela} ( $campos ) VALUES( $valores )" );
			return true;
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
		if( is_numeric( $this->sequencial ) && is_numeric( $this->ref_cod_campo_tabela ) && is_numeric( $this->ref_cod_portaria ) )
		{

			$db = new clsBanco();
			$set = "";

			if( is_string( $this->valor ) )
			{
				$set .= "{$gruda}valor = '{$this->valor}'";
				$gruda = ", ";
			}


			if( $set )
			{
				$db->Consulta( "UPDATE {$this->_tabela} SET $set WHERE sequencial = '{$this->sequencial}' AND ref_cod_campo_tabela = '{$this->ref_cod_campo_tabela}' AND ref_cod_portaria = '{$this->ref_cod_portaria}'" );
				return true;
			}
		}
		return false;
	}

	/**
	 * Retorna uma lista filtrados de acordo com os parametros
	 * 
	 * @param string str_valor
	 *
	 * @return array
	 */
	function lista( $int_ref_cod_portaria = null, $str_valor = null, $int_ref_cod_campo_tabela = null )
	{
		$sql = "SELECT {$this->_campos_lista} FROM {$this->_tabela}";
		$filtros = "";

		$whereAnd = " WHERE ";

		if( is_numeric( $int_sequencial ) )
		{
			$filtros .= "{$whereAnd} sequencial = '{$int_sequencial}'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_ref_cod_campo_tabela ) )
		{
			$filtros .= "{$whereAnd} ref_cod_campo_tabela = '{$int_ref_cod_campo_tabela}'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_ref_cod_portaria ) )
		{
			$filtros .= "{$whereAnd} ref_cod_portaria = '{$int_ref_cod_portaria}'";
			$whereAnd = " AND ";
		}
		if( is_string( $str_valor ) )
		{
			$filtros .= "{$whereAnd} valor LIKE '%{$str_valor}%'";
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
		if( is_numeric( $this->sequencial ) && is_numeric( $this->ref_cod_campo_tabela ) && is_numeric( $this->ref_cod_portaria ) )
		{

			$db = new clsBanco();
			$db->Consulta( "SELECT {$this->_todos_campos} FROM {$this->_tabela} WHERE sequencial = '{$this->sequencial}' AND ref_cod_campo_tabela = '{$this->ref_cod_campo_tabela}' AND ref_cod_portaria = '{$this->ref_cod_portaria}'" );
			$db->ProximoRegistro();
			return $db->Tupla();
		}
		return false;
	}

	/**
	 * Retorna true se o registro existir. Caso contr�rio retorna false.
	 *
	 * @return bool
	 */
	function existe()
	{
		if( is_numeric( $this->sequencial ) && is_numeric( $this->ref_cod_campo_tabela ) && is_numeric( $this->ref_cod_portaria ) )
		{

			$db = new clsBanco();
			$db->Consulta( "SELECT 1 FROM {$this->_tabela} WHERE sequencial = '{$this->sequencial}' AND ref_cod_campo_tabela = '{$this->ref_cod_campo_tabela}' AND ref_cod_portaria = '{$this->ref_cod_portaria}'" );
			if( $db->ProximoRegistro() )
			{
				return true;
			}
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
		if( is_numeric( $this->ref_cod_portaria ) )
		{

		
		//	delete
		$db = new clsBanco();
		$db->Consulta( "DELETE FROM {$this->_tabela} WHERE ref_cod_portaria = '{$this->ref_cod_portaria}'" );
		return true;
		

		
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
	function setLimite( $intLimiteQtd, $intLimiteOffset = 0 )
	{
		$this->_limite_quantidade = $intLimiteQtd;
		if ($intLimiteOffset > 0)
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