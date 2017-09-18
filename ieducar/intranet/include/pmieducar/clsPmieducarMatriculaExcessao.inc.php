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
* Criado em 28/11/2006 14:26 pelo gerador automatico de classes
*/

require_once( "include/pmieducar/geral.inc.php" );

class clsPmieducarMatriculaExcessao
{
	var $cod_aluno_excessao;
	var $ref_cod_matricula;
	var $ref_cod_turma;
	var $ref_sequencial;
	var $ref_cod_serie;
	var $ref_cod_escola;
	var $ref_cod_disciplina;
	var $reprovado_faltas;
	var $precisa_exame;
	var $permite_exame;

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
	 * @param integer cod_aluno_excessao
	 * @param integer ref_cod_matricula
	 * @param integer ref_cod_turma
	 * @param integer ref_sequencial
	 * @param integer ref_cod_serie
	 * @param integer ref_cod_escola
	 * @param integer ref_cod_disciplina
	 * @param bool reprovado_faltas
	 * @param bool precisa_exame
	 * @param bool permite_exame
	 *
	 * @return object
	 */
	function clsPmieducarMatriculaExcessao( $cod_aluno_excessao = null, $ref_cod_matricula = null, $ref_cod_turma = null, $ref_sequencial = null, $ref_cod_serie = null, $ref_cod_escola = null, $ref_cod_disciplina = null, $reprovado_faltas = null, $precisa_exame = null, $permite_exame = null )
	{
		$db = new clsBanco();
		$this->_schema = "pmieducar.";
		$this->_tabela = "{$this->_schema}matricula_excessao";

		$this->_campos_lista = $this->_todos_campos = "cod_aluno_excessao, ref_cod_matricula, ref_cod_turma, ref_sequencial, ref_cod_serie, ref_cod_escola, ref_cod_disciplina, reprovado_faltas, precisa_exame, permite_exame";

		if( is_numeric( $ref_cod_serie ) && is_numeric( $ref_cod_escola ) && is_numeric( $ref_cod_disciplina ) )
		{
			if( class_exists( "clsPmieducarEscolaSerieDisciplina" ) )
			{
				$tmp_obj = new clsPmieducarEscolaSerieDisciplina( $ref_cod_serie, $ref_cod_escola, $ref_cod_disciplina );
				if( method_exists( $tmp_obj, "existe") )
				{
					if( $tmp_obj->existe() )
					{
						$this->ref_cod_serie = $ref_cod_serie;
						$this->ref_cod_escola = $ref_cod_escola;
						$this->ref_cod_disciplina = $ref_cod_disciplina;
					}
				}
				else if( method_exists( $tmp_obj, "detalhe") )
				{
					if( $tmp_obj->detalhe() )
					{
						$this->ref_cod_serie = $ref_cod_serie;
						$this->ref_cod_escola = $ref_cod_escola;
						$this->ref_cod_disciplina = $ref_cod_disciplina;
					}
				}
			}
			else
			{
				if( $db->CampoUnico( "SELECT 1 FROM pmieducar.escola_serie_disciplina WHERE ref_ref_cod_serie = '{$ref_cod_serie}' AND ref_ref_cod_escola = '{$ref_cod_escola}' AND ref_cod_disciplina = '{$ref_cod_disciplina}'" ) )
				{
					$this->ref_cod_serie = $ref_cod_serie;
					$this->ref_cod_escola = $ref_cod_escola;
					$this->ref_cod_disciplina = $ref_cod_disciplina;
				}
			}
		}
		if( is_numeric( $ref_cod_matricula ) && is_numeric( $ref_cod_turma ) && is_numeric( $ref_sequencial ) )
		{
			if( class_exists( "clsPmieducarMatriculaTurma" ) )
			{
				$tmp_obj = new clsPmieducarMatriculaTurma( $ref_cod_matricula, $ref_cod_turma, $ref_sequencial );
				if( method_exists( $tmp_obj, "existe") )
				{
					if( $tmp_obj->existe() )
					{
						$this->ref_cod_matricula = $ref_cod_matricula;
						$this->ref_cod_turma = $ref_cod_turma;
						$this->ref_sequencial = $ref_sequencial;
					}
				}
				else if( method_exists( $tmp_obj, "detalhe") )
				{
					if( $tmp_obj->detalhe() )
					{
						$this->ref_cod_matricula = $ref_cod_matricula;
						$this->ref_cod_turma = $ref_cod_turma;
						$this->ref_sequencial = $ref_sequencial;
					}
				}
			}
			else
			{
				if( $db->CampoUnico( "SELECT 1 FROM pmieducar.matricula_turma WHERE ref_cod_matricula = '{$ref_cod_matricula}' AND ref_cod_turma = '{$ref_cod_turma}' AND sequencial = '{$ref_sequencial}'" ) )
				{
					$this->ref_cod_matricula = $ref_cod_matricula;
					$this->ref_cod_turma = $ref_cod_turma;
					$this->ref_sequencial = $ref_sequencial;
				}
			}
		}


		if( is_numeric( $cod_aluno_excessao ) )
		{
			$this->cod_aluno_excessao = $cod_aluno_excessao;
		}
		if( ! is_null( $reprovado_faltas ) )
		{
			$this->reprovado_faltas = $reprovado_faltas;
		}
		if( ! is_null( $precisa_exame ) )
		{
			$this->precisa_exame = $precisa_exame;
		}
		if( ! is_null( $permite_exame ) )
		{
			$this->permite_exame = $permite_exame;
		}

	}

	/**
	 * Cria um novo registro
	 *
	 * @return bool
	 */
	function cadastra()
	{
		if( is_numeric( $this->ref_cod_matricula ) && is_numeric( $this->ref_cod_turma ) && is_numeric( $this->ref_sequencial ) && is_numeric( $this->ref_cod_serie ) && is_numeric( $this->ref_cod_escola ) && is_numeric( $this->ref_cod_disciplina ) && ! is_null( $this->reprovado_faltas ) && ! is_null( $this->precisa_exame ) )
		{
			$db = new clsBanco();

			$campos = "";
			$valores = "";
			$gruda = "";

			if( is_numeric( $this->ref_cod_matricula ) )
			{
				$campos .= "{$gruda}ref_cod_matricula";
				$valores .= "{$gruda}'{$this->ref_cod_matricula}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->ref_cod_turma ) )
			{
				$campos .= "{$gruda}ref_cod_turma";
				$valores .= "{$gruda}'{$this->ref_cod_turma}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->ref_sequencial ) )
			{
				$campos .= "{$gruda}ref_sequencial";
				$valores .= "{$gruda}'{$this->ref_sequencial}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->ref_cod_serie ) )
			{
				$campos .= "{$gruda}ref_cod_serie";
				$valores .= "{$gruda}'{$this->ref_cod_serie}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->ref_cod_escola ) )
			{
				$campos .= "{$gruda}ref_cod_escola";
				$valores .= "{$gruda}'{$this->ref_cod_escola}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->ref_cod_disciplina ) )
			{
				$campos .= "{$gruda}ref_cod_disciplina";
				$valores .= "{$gruda}'{$this->ref_cod_disciplina}'";
				$gruda = ", ";
			}
			if( ! is_null( $this->reprovado_faltas ) )
			{
				$campos .= "{$gruda}reprovado_faltas";
				if( dbBool( $this->reprovado_faltas ) )
				{
					$valores .= "{$gruda}TRUE";
				}
				else
				{
					$valores .= "{$gruda}FALSE";
				}
				$gruda = ", ";
			}
			if( ! is_null( $this->precisa_exame ) )
			{
				$campos .= "{$gruda}precisa_exame";
				if( dbBool( $this->precisa_exame ) )
				{
					$valores .= "{$gruda}TRUE";
				}
				else
				{
					$valores .= "{$gruda}FALSE";
				}
				$gruda = ", ";
			}
			if( ! is_null( $this->permite_exame ) )
			{
				$campos .= "{$gruda}permite_exame";
				if( dbBool( $this->permite_exame ) )
				{
					$valores .= "{$gruda}TRUE";
				}
				else
				{
					$valores .= "{$gruda}FALSE";
				}
				$gruda = ", ";
			}


			$db->Consulta( "INSERT INTO {$this->_tabela} ( $campos ) VALUES( $valores )" );
			return $db->InsertId( "{$this->_tabela}_cod_aluno_excessao_seq");
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
		if( is_numeric( $this->cod_aluno_excessao ) )
		{

			$db = new clsBanco();
			$set = "";

			if( is_numeric( $this->ref_cod_matricula ) )
			{
				$set .= "{$gruda}ref_cod_matricula = '{$this->ref_cod_matricula}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->ref_cod_turma ) )
			{
				$set .= "{$gruda}ref_cod_turma = '{$this->ref_cod_turma}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->ref_sequencial ) )
			{
				$set .= "{$gruda}ref_sequencial = '{$this->ref_sequencial}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->ref_cod_serie ) )
			{
				$set .= "{$gruda}ref_cod_serie = '{$this->ref_cod_serie}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->ref_cod_escola ) )
			{
				$set .= "{$gruda}ref_cod_escola = '{$this->ref_cod_escola}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->ref_cod_disciplina ) )
			{
				$set .= "{$gruda}ref_cod_disciplina = '{$this->ref_cod_disciplina}'";
				$gruda = ", ";
			}
			if( ! is_null( $this->reprovado_faltas ) )
			{
				$val = dbBool( $this->reprovado_faltas ) ? "TRUE": "FALSE";
				$set .= "{$gruda}reprovado_faltas = {$val}";
				$gruda = ", ";
			}
			if( ! is_null( $this->precisa_exame ) )
			{
				$val = dbBool( $this->precisa_exame ) ? "TRUE": "FALSE";
				$set .= "{$gruda}precisa_exame = {$val}";
				$gruda = ", ";
			}
			if( ! is_null( $this->permite_exame ) )
			{
				$val = dbBool( $this->permite_exame ) ? "TRUE": "FALSE";
				$set .= "{$gruda}permite_exame = {$val}";
				$gruda = ", ";
			}


			if( $set )
			{
				$db->Consulta( "UPDATE {$this->_tabela} SET $set WHERE cod_aluno_excessao = '{$this->cod_aluno_excessao}'" );
				return true;
			}
		}
		return false;
	}

	/**
	 * Retorna uma lista filtrados de acordo com os parametros
	 *
	 * @param integer int_ref_cod_matricula
	 * @param integer int_ref_cod_turma
	 * @param integer int_ref_sequencial
	 * @param integer int_ref_cod_serie
	 * @param integer int_ref_cod_escola
	 * @param integer int_ref_cod_disciplina
	 * @param bool bool_reprovado_faltas
	 * @param bool bool_precisa_exame
	 * @param bool bool_permite_exame
	 *
	 * @return array
	 */
	function lista( $int_ref_cod_matricula = null, $int_ref_cod_turma = null, $int_ref_sequencial = null, $int_ref_cod_serie = null, $int_ref_cod_escola = null, $int_ref_cod_disciplina = null, $bool_reprovado_faltas = null, $bool_precisa_exame = null, $bool_permite_exame = null )
	{
		$sql = "SELECT {$this->_campos_lista} FROM {$this->_tabela}";
		$filtros = "";

		$whereAnd = " WHERE ";

		if( is_numeric( $int_cod_aluno_excessao ) )
		{
			$filtros .= "{$whereAnd} cod_aluno_excessao = '{$int_cod_aluno_excessao}'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_ref_cod_matricula ) )
		{
			$filtros .= "{$whereAnd} ref_cod_matricula = '{$int_ref_cod_matricula}'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_ref_cod_turma ) )
		{
			$filtros .= "{$whereAnd} ref_cod_turma = '{$int_ref_cod_turma}'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_ref_sequencial ) )
		{
			$filtros .= "{$whereAnd} ref_sequencial = '{$int_ref_sequencial}'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_ref_cod_serie ) )
		{
			$filtros .= "{$whereAnd} ref_cod_serie = '{$int_ref_cod_serie}'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_ref_cod_escola ) )
		{
			$filtros .= "{$whereAnd} ref_cod_escola = '{$int_ref_cod_escola}'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_ref_cod_disciplina ) )
		{
			$filtros .= "{$whereAnd} ref_cod_disciplina = '{$int_ref_cod_disciplina}'";
			$whereAnd = " AND ";
		}
		if( ! is_null( $bool_reprovado_faltas ) )
		{
			if( dbBool( $bool_reprovado_faltas ) )
			{
				$filtros .= "{$whereAnd} reprovado_faltas = TRUE";
			}
			else
			{
				$filtros .= "{$whereAnd} reprovado_faltas = FALSE";
			}
			$whereAnd = " AND ";
		}
		if( ! is_null( $bool_precisa_exame ) )
		{
			if( dbBool( $bool_precisa_exame ) )
			{
				$filtros .= "{$whereAnd} precisa_exame = TRUE";
			}
			else
			{
				$filtros .= "{$whereAnd} precisa_exame = FALSE";
			}
			$whereAnd = " AND ";
		}
		if( ! is_null( $bool_permite_exame ) )
		{
			if( $bool_permite_exame == IS_NULL )
			{
				$filtros .= "{$whereAnd} permite_exame IS NULL";
			}
			else if( $bool_permite_exame == IS_NOT_NULL )
			{
				$filtros .= "{$whereAnd} permite_exame IS NOT NULL";
			}
			else if( dbBool( $bool_permite_exame ) )
			{
				$filtros .= "{$whereAnd} permite_exame = TRUE";
			}
			else
			{
				$filtros .= "{$whereAnd} permite_exame = FALSE";
			}
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
		if( is_numeric( $this->cod_aluno_excessao ) )
		{

			$db = new clsBanco();
			$db->Consulta( "SELECT {$this->_todos_campos} FROM {$this->_tabela} WHERE cod_aluno_excessao = '{$this->cod_aluno_excessao}'" );
			$db->ProximoRegistro();
			return $db->Tupla();
		}
		return false;
	}

	/**
	 * Retorna true se o registro existir. Caso contrário retorna false.
	 *
	 * @return bool
	 */
	function existe()
	{
		if( is_numeric( $this->cod_aluno_excessao ) )
		{

			$db = new clsBanco();
			$db->Consulta( "SELECT 1 FROM {$this->_tabela} WHERE cod_aluno_excessao = '{$this->cod_aluno_excessao}'" );
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
		if( is_numeric( $this->cod_aluno_excessao ) )
		{
			$db = new clsBanco();
			$db->Consulta( "DELETE FROM {$this->_tabela} WHERE cod_aluno_excessao = '{$this->cod_aluno_excessao}'" );
			return true;
		}
		return false;
	}

	/**
	 * Exclui todos os registros associados a matricula $cod_matricula
	 *
	 * @return bool
	 */
	function excluirPorMatricula($cod_matricula)
	{
		if( is_numeric( $cod_matricula ) )
		{
			$db = new clsBanco();
			$db->Consulta( "DELETE FROM {$this->_tabela} WHERE ref_cod_matricula = '{$cod_matricula}'" );
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