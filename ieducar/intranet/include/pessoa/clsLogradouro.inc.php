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
require_once ("include/clsBanco.inc.php");
require_once ("include/Geral.inc.php");

class clsLogradouro
{
	var $idlog;
	var $idtlog;
	var $nome;
	var $idmun;
	var $geom;
	var $ident_oficial;
	var $idpes_cad;
	
	var $tabela;
	var $schema = "public";

	/**
	 * Construtor
	 *
	 * @return Object:clsLogradouro
	 */
	function clsLogradouro( $int_idlog = false, $str_idtlog=false, $str_nome=false, $int_idmun=false, $str_geom=false, $str_ident_oficial=false, $idpes_cad = null)
	{
		$this->idlog = $int_idlog;
		
		$objLog = new clsTipoLogradouro($str_idtlog);
		if($objLog->detalhe())
		{
			$this->idtlog = $str_idtlog;
		}
		
		$this->nome = $str_nome;
		$this->idmun = $int_idmun;
		$this->geom = $str_geom;
		$this->ident_oficial = $str_ident_oficial;
		$this->idpes_cad = $idpes_cad;
		
		$this->tabela = "logradouro";
	}
	
	/**
	 * Funcao que cadastra um novo registro com os valores atuais
	 *
	 * @return bool
	 */
	function cadastra()
	{
		$db = new clsBanco();
		// verificacoes de campos obrigatorios para insercao
		if( is_string( $this->idtlog ) && is_string( $this->nome ) && is_numeric( $this->idmun ) && is_string($this->ident_oficial) )
		{
			$campos = "";
			$values = "";
			
			if( is_string( $this->geom ) )
			{
				$campos .= ", geom";
				$values .= ", '{$this->geom}'";
			}

			if( is_string( $this->idpes_cad ) )
			{
				$campos .= ", idpes_cad";
				$values .= ", '{$this->idpes_cad}'";
			}

			$db->Consulta( "INSERT INTO {$this->schema}.{$this->tabela} ( idtlog, nome, idmun, origem_gravacao, ident_oficial,data_cad, OPERACAO, idsis_cad$campos ) VALUES ( '{$this->idtlog}', '{$this->nome}', '{$this->idmun}', 'U', '{$this->ident_oficial}', NOW(), 'I', '9' $values )" );

			return $db->InsertId("{$this->schema}.seq_logradouro");
		}
		return false;
	}
	
	/**
	 * Edita o registro atual
	 *
	 * @return bool
	 */
	function edita()
	{
		// verifica campos obrigatorios para edicao
		if( is_numeric( $this->idlog )  && is_string( $this->idtlog ) && is_string( $this->nome ) && is_numeric( $this->idmun ) && is_string($this->ident_oficial) )
		{
			$set = "SET idtlog = '{$this->idtlog}', nome = '{$this->nome}', idmun = '{$this->idmun}', ident_oficial = '{$this->ident_oficial}'";
			
			if( is_string( $this->geom ) )
			{
				$set .= ", geom = '{$this->geom}'";
			}
			else
			{
				$set .= ", geom = NULL";
			}
			
			$db = new clsBanco();
			$db->Consulta( "UPDATE {$this->schema}.{$this->tabela} $set WHERE idlog = '$this->idlog'" );
			return true;
		}
		return false;
	}
	
	/**
	 * Remove o registro atual
	 *
	 * @return bool
	 */
	function exclui()
	{
		if(is_numeric($this->idlog))
		{
			$objEndPessoa = new clsEnderecoPessoa();
			$listaEndPessoa = $objEndPessoa->lista(false, false, false, false, false, $this->idlog);
			
			$objCepLog = new clsCepLogradouro();
			$listaCepLog = $objCepLog->lista(false, $this->idlog);
			
			$objCepLogBai = new clsCepLogradouroBairro();
			$listaCepLogBai = $objCepLogBai->lista($this->idlog);
			
			if(!count($listaEndPessoa) && !count($listaCepLog) && !count($listaCepLogBai))
			{
				$db = new clsBanco();
				//$db->Consulta("DELETE FROM {$this->schema}.{$this->tabela} WHERE idlog={$this->idlog}");
				return true;
			}
			return false;
		}
		return false;
	}
	
	/**
	 * Exibe uma lista baseada nos parametros de filtragem passados
	 *
	 * @return Array
	 */
	function lista( $str_idtlog=false, $str_nome=false, $int_idnum=false, $str_geom=false, $str_ident_oficial=false, $int_limite_ini=0, $int_limite_qtd=20, $str_orderBy = false, $int_idlog=false )
	{
		// verificacoes de filtros a serem usados
		$whereAnd = "WHERE ";
		if( is_string( $str_idtlog ) )
		{
//			$str_idtlog = limpa_acentos( $str_idtlog );
			$where .= "{$whereAnd}fcn_upper_nrm( idtlog ) ILIKE fcn_upper_nrm('%$str_idtlog%')";
			$whereAnd = " AND ";
		}
		if( is_string( $int_idlog ) )
		{
			$where .= "{$whereAnd}idlog  = '$int_idlog'";
			$whereAnd = " AND ";
		}
		if( is_string( $str_nome ) )
		{
			$str_nome = limpa_acentos( $str_nome );
			$where .= "{$whereAnd}fcn_upper_nrm( nome ) ILIKE '%$str_nome%'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_idmun ) )
		{
			$where .= "{$whereAnd}idmun = '$int_idmun'";
			$whereAnd = " AND ";
		}
		if( is_string( $str_geom ) )
		{
			$where .= "{$whereAnd}geom LIKE '%$str_geom%'";
			$whereAnd = " AND ";
		}
		
		if( is_string( $str_ident_oficial ) )
		{
			$where .= "{$whereAnd}ident_oficial LIKE '%$str_ident_oficial%'";
			$whereAnd = " AND ";
		}
		
		if($str_orderBy)
		{
			$orderBy = "ORDER BY $str_orderBy";
		}
		
		$limit = "";
		if( is_numeric( $int_limite_ini ) && is_numeric( $int_limite_qtd ) )
		{
			$limit = " LIMIT $int_limite_qtd OFFSET $int_limite_ini";
		}
		
		$db = new clsBanco();
		$db->Consulta( "SELECT COUNT(0) AS total FROM {$this->schema}.{$this->tabela} $where" );
		$db->ProximoRegistro();
		$total = $db->Campo( "total" );
		
		$db->Consulta( "SELECT idlog, idtlog, nome, idmun, geom, ident_oficial FROM {$this->schema}.{$this->tabela} $where $orderBy $limit" );
		$resultado = array();
		while ( $db->ProximoRegistro() ) 
		{
			$tupla = $db->Tupla();
			$tupla["idtlog"] = new clsTipoLogradouro( $tupla["idtlog"] );

			$tupla["total"] = $total;
			$resultado[] = $tupla;
		}
		if( count( $resultado ) )
		{
			return $resultado;
		}
		return false;
	}
	/**
	 * Retorna um array com os detalhes do objeto
	 *
	 * @return Array
	 */
	function detalhe()
	{
		if($this->idlog)
		{
			$db = new clsBanco();
			$db->Consulta("SELECT idlog, idtlog, nome, idmun, geom, ident_oficial FROM {$this->schema}.{$this->tabela} WHERE idlog='{$this->idlog}'");
			if( $db->ProximoRegistro() )
			{
				$tupla = $db->Tupla();
				$this->idlog = $tupla["idlog"];
				$this->idtlog = $tupla["idtlog"];
				$this->nome = $tupla["nome"];
				$this->idmun = $tupla["idmun"];
				$this->geom = $tupla["geom"];
				$this->ident_oficial = $tupla["ident_oficial"];
				
				$tupla["idtlog"] = new clsTipoLogradouro( $tupla["idtlog"] );
				return $tupla;
			}
		}
		return false;
	}
}
?>
