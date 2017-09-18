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
require_once ("include/clsBanco.inc.php");
if( file_exists("include/juris/jurisGeral.inc.php") ) 
{
	require_once ("include/juris/jurisGeral.inc.php");
}

class clsAgenda
{
	var $cod_pessoa_dono;
	var $agenda;
	var $editor;
	var $compromissos;
	var $versoes;
	var $tabela;
	var $publica;
	var $envia_alerta;
	var $nome_agenda;
	var $err_msg;
	var $time_atual;
	var $time_amanha;
	var $time_ontem;
	var $time_real_atual;
	
	function  clsAgenda( $int_cod_editor = 0 , $int_cod_pessoa_dono=false, $int_cod_agenda=false, $time = false )
	{
		$db = new clsBanco();
		$this->cod_pessoa_dono = $int_cod_pessoa_dono;
		$this->agenda = $int_cod_agenda;
		$this->editor = $int_cod_editor;
		
		if( $time  )
		{
			$this->time_atual = $time;
		}
		else 
		{
			$this->time_atual = false;
		}
		if($this->time_atual)
		{
			$this->time_amanha = $this->time_atual + 86400;
			$this->time_ontem = $this->time_atual - 86400;
			
			$this->time_real_atual = time();
		}
		
		
		if( ! $this->agenda && $this->cod_pessoa_dono )
		{
			$db->Consulta( "SELECT cod_agenda, publica, envia_alerta, nm_agenda FROM agenda WHERE ref_ref_cod_pessoa_own = '{$this->cod_pessoa_dono}'" );
			if( $db->ProximoRegistro() )
			{
				list( $this->agenda, $this->publica, $this->envia_alerta, $this->nome_agenda ) = $db->Tupla();
			}
			else 
			{
				// essa pessoa nao possui uma agenda, vamos criar uma pra ela
				$this->cadastraAgenda();
			}
		}
		else 
		{
			if( ! $this->cod_pessoa_dono && $this->agenda )
			{
				$db->Consulta( "SELECT ref_ref_cod_pessoa_own, publica, envia_alerta, nm_agenda FROM agenda WHERE cod_agenda = '{$this->agenda}'" );
				if( $db->ProximoRegistro() )
				{	
					list( $this->cod_pessoa_dono, $this->publica, $this->envia_alerta, $this->nome_agenda ) = $db->Tupla();
				}
			}
		}
		$this->tabela = "portal.agenda";
		 
		if( ! $this->cod_pessoa_dono && ! $this->agenda )
		{
			unset( $this );
		}
	}

	function cadastraAgenda()
	{
		$db = new clsBanco();
		// o nome da agenda sera o nome da pessoa
		$nome = $db->CampoUnico( "SELECT nome FROM cadastro.pessoa WHERE idpes = '{$this->cod_pessoa_dono}'" );
		// trata o nome pra remover espacos no fim
		$nome = eregi_replace( " +\$", "", $nome );
		// usa apenas o primeiro e ultimo nome da pessoa
		$nomeArr = explode( " ", $nome );
		$nome = $nomeArr[0];
		if( count( $nomeArr ) > 1 )
		{
			$nome .= " " . $nomeArr[ count( $nomeArr ) - 1 ];
		}
		
		$db->Consulta( "INSERT INTO agenda( ref_ref_cod_pessoa_cad, ref_ref_cod_pessoa_own, nm_agenda, data_cad ) VALUES ( $this->cod_pessoa_dono, $this->cod_pessoa_dono, '{$nome}', NOW() )" );
		$this->agenda = $db->InsertId( "agenda_cod_agenda_seq" );
		
		$this->publica = 0;
		$this->envia_alerta = 0;
		$this->nome_agenda = $nome;
	}
	
	function detalheCompromisso( $cod_compromisso, $cod_agenda )
	{
		
		$db = new clsBanco();
		$db->Consulta( "SELECT data_inicio, cod_agenda_compromisso, versao, data_fim, ref_cod_agenda, titulo, descricao, importante, publico FROM agenda_compromisso WHERE ref_cod_agenda = '{$cod_agenda}' AND cod_agenda_compromisso = '$cod_compromisso' AND ativo = 1 AND data_fim IS NOT NULL" );
		if($db->ProximoRegistro())
		{
			return $db->Tupla();
		}
		return false;
		
	}
	
	function listaCompromissos( $data_inicio, $data_fim )
	{
		$db = new clsBanco();
		$this->compromissos = array();
		//echo "SELECT data_inicio, cod_agenda_compromisso, versao, data_fim, titulo, descricao, importante, publico FROM agenda_compromisso WHERE ref_cod_agenda = '{$this->agenda}' AND ativo = 1 AND data_fim IS NOT NULL AND data_inicio >= '{$data_inicio}' AND data_inicio <= '{$data_fim}' ORDER BY data_inicio ASC<br>";
		$db->Consulta( "SELECT data_inicio, cod_agenda_compromisso, versao, data_fim, titulo, descricao, importante, publico FROM agenda_compromisso WHERE ref_cod_agenda = '{$this->agenda}' AND ativo = 1 AND data_fim IS NOT NULL AND data_inicio >= '{$data_inicio}' AND data_inicio <= '{$data_fim}' ORDER BY data_inicio ASC" );
		while ( $db->ProximoRegistro() )
		{
			$temp_arr_compromisso = array();
			//list( $cod_agenda_compromisso, $versao, $data_inicio, $data_fim, $titulo, $descricao, $importante, $publico) = $db->Tupla();
			list( $temp_arr_compromisso["data_inicio"], $temp_arr_compromisso["cod_agenda_compromisso"], $temp_arr_compromisso["versao"], $temp_arr_compromisso["data_fim"], $temp_arr_compromisso["titulo"], $temp_arr_compromisso["descricao"], $temp_arr_compromisso["importante"], $temp_arr_compromisso["publico"] ) = $db->Tupla();
			
			$temp_arr_compromisso2["data_inicio"] = $temp_arr_compromisso["data_inicio"];
			$temp_arr_compromisso2["versao"] = $temp_arr_compromisso["versao"];
			$temp_arr_compromisso2["data_fim"] = $temp_arr_compromisso["data_fim"];
			$temp_arr_compromisso2["cod_agenda_compromisso"] = $temp_arr_compromisso["cod_agenda_compromisso"];
			$temp_arr_compromisso2["titulo"] = $temp_arr_compromisso["titulo"];
			$temp_arr_compromisso2["descricao"] = $temp_arr_compromisso["descricao"];
			$temp_arr_compromisso2["importante"] = $temp_arr_compromisso["importante"];
			$temp_arr_compromisso2["publico"] = $temp_arr_compromisso["publico"];
			
			$this->compromissos[] = $temp_arr_compromisso2;
		}
		
		$methodIndex = 0;
		while ( true )
		{
			$methodIndex++;
			$methodName = "add_compromisso_externo_{$methodIndex}";
			if( method_exists( $this, $methodName ) )
			{
				$this->$methodName();
			}
			else 
			{
				break;
			}
		}
		if( is_array( $this->compromissos ) && count( $this->compromissos ) )
		{
			asort( $this->compromissos );
			reset( $this->compromissos );
			
			return $this->compromissos;
		}
		return false;
	}
	
	function listaCompromissosDia( $data )
	{
		$edit_dataArr = explode( "/", $data );
		if( is_array( $edit_dataArr ) && count( $edit_dataArr ) == 3 && checkdate( $edit_dataArr[1], $edit_dataArr[0], $edit_dataArr[2] ) )
		{
			$edit_data = "{$edit_dataArr[2]}-{$edit_dataArr[1]}-{$edit_dataArr[0]}";
			
			return $this->listaCompromissos( "{$edit_data} 00:00:00", "{$edit_data} 23:59:59" );
		}
		
		return false;
	}
	
	function listaVersoes( $cod_compromisso )
	{
		$db = new clsBanco();
		if( $this->compromissoPertenceAgenda( $cod_compromisso ) )
		{
			$this->versoes = array();
			// seleciona as versoes desse compromisso
			$db->Consulta( "SELECT versao, ref_ref_cod_pessoa_cad, ativo, data_inicio, titulo, descricao, importante, publico, data_cadastro, data_fim FROM agenda_compromisso WHERE cod_agenda_compromisso = '{$_GET["versoes"]}' ORDER BY versao DESC" );
			while ( $db->ProximoRegistro() )
			{
				unset( $versao, $ref_ref_cod_pessoa_cad, $ativo, $data_inicio, $titulo, $descricao, $importante, $publico, $data_cadastro, $data_fim );
				list( $versao, $ref_ref_cod_pessoa_cad, $ativo, $data_inicio, $titulo, $descricao, $importante, $publico, $data_cadastro, $data_fim ) = $db->Tupla();
				
				$this->versoes[] = array( "versao" => $versao, "ref_ref_cod_pessoa_cad" => $ref_ref_cod_pessoa_cad, "ativo" => $ativo, "data_inicio" => $data_inicio, "titulo" => $titulo, "descricao" => $descricao, "importante" => $importante, "publico" => $publico, "data_cadastro" => $data_cadastro, "data_fim" => $data_fim );
			}
		}
	}
	
	function cadastraCompromisso( $cod_compromisso = false, $titulo, $descricao, $data, $hora_inicio, $hora_fim=false, $publico = false, $importante=false, $repetir_dias=false, $repetir_qtd=false, $tipo_compromisso = false )
	{
		$db = new clsBanco();
		$campos = "";
		$valores = "";
		
		if( $titulo || $descricao )
		{
			$edit_dataArr = explode( "/", $data );
			if( is_array( $edit_dataArr ) && count( $edit_dataArr ) == 3 && checkdate( $edit_dataArr[1], $edit_dataArr[0], $edit_dataArr[2] ) )
			{
				$edit_data = "{$edit_dataArr[2]}-{$edit_dataArr[1]}-{$edit_dataArr[0]}";
				if( ereg( "[0-9]{2}:[0-9]{2}", $hora_inicio ) ) 
				{
					$timeNovo = strtotime( $edit_data );
					if( isset( $publico ) && $publico )
					{
						$campos .= ", publico";
						$valores .= ", '1'";
					}
					if( isset( $importante ) && $importante )
					{
						$campos .= ", importante";
						$valores .= ", '1'";
					}
					$edit_titulo = ( $titulo ) ? "'{$titulo}'": "NULL";
					$edit_descricao = ( $descricao ) ? "'{$descricao}'": "NULL";
					
					if( is_numeric( $repetir_dias ) && is_numeric( $repetir_qtd ) && $repetir_dias && $repetir_qtd )
					{
						for ( $i = 0; $i < $repetir_qtd; $i++ )
						{
							$data_cad = date( "Y-m-d", $timeNovo );
							if( $hora_fim )
							{
								$campoDataFim = "";
								$valorDataFim = "";
								if( ereg( "[0-9]{2}:[0-9]{2}", $hora_fim ) ) 
								{
									$campoDataFim .= ", data_fim" ;
									$valorDataFim .= ", '{$data_cad} {$hora_fim}'";
								}
							}
							$maxCod = $db->CampoUnico( "SELECT MAX( cod_agenda_compromisso ) FROM agenda_compromisso" );
							$maxCod++;
							$db->Consulta( "INSERT INTO agenda_compromisso( cod_agenda_compromisso, versao, ref_cod_agenda, ref_ref_cod_pessoa_cad,data_inicio, titulo, descricao, data_cadastro {$campos} {$campoDataFim}) VALUES (  '{$maxCod}', '1', '{$this->agenda}', '{$this->editor}', '{$data_cad} {$hora_inicio}', $edit_titulo, $edit_descricao, NOW() {$valores} {$valorDataFim} )" );
							$timeNovo += 86400 * $repetir_dias;
						}
					}
					else 
					{
						$data_cad = date( "Y-m-d", $timeNovo );
						if( $hora_fim )
						{
							if( ereg( "[0-9]{2}:[0-9]{2}", $hora_fim ) ) 
							{
								$campos .= ", data_fim" ;
								$valores .= ", '{$data_cad} {$hora_fim}'";
							}
						}
						
						if( $cod_compromisso )
						{
							$maxCod = $cod_compromisso;
							$versao = $this->getCompromissoVersao( $cod_compromisso ) + 1;
						}
						else 
						{
							$maxCod = $db->CampoUnico( "SELECT MAX( cod_agenda_compromisso ) FROM agenda_compromisso" );
							$maxCod++;
							$versao = 1;
						}
						$db->Consulta( "INSERT INTO agenda_compromisso( cod_agenda_compromisso, versao, ref_cod_agenda, ref_ref_cod_pessoa_cad,data_inicio, titulo, descricao, data_cadastro {$campos}) VALUES (  '{$maxCod}', '{$versao}', '{$this->agenda}', '{$this->editor}', '{$data_cad} {$hora_inicio}', $edit_titulo, $edit_descricao, NOW() {$valores} )" );
					}
					if( $tipo_compromisso )
					{
						$objAgenda = new clsAgendaJuris( $maxCod, $versao,$this->agenda, $tipo_compromisso, $this->editor, "{$data_cad} {$hora_inicio}");
						$objAgenda->cadastra();
					}
				}
				else 
				{
					$this->erro_msg .= "Voc&ecirc; deve preencher o campo Hora de Inicio corretamente. Formato hora: hh:mm<br>";
				}
			}
			else 
			{
				$this->erro_msg .= "Voc&ecirc; deve preencher o campo Data corretamente. Formato data: dd/mm/aaaa<br>";
			}
		}
		else 
		{
			$this->erro_msg .= "Voc&ecirc; deve preencher o campo Titulo ou o campo Descricao<br>";
		}
	}
	
	function edita_compromisso( $cod_compromisso, $titulo = false, $conteudo = false, $data = false, $hora_inicio = false, $hora_fim = false, $publico = false, $importante = false )
	{
		$db = new clsBanco();
		if( $this->compromissoPertenceAgenda( $cod_compromisso ) )
		{
			$verifica = true;
			
			$versaoAtual = $db->CampoUnico( "SELECT MAX( versao ) FROM agenda_compromisso WHERE cod_agenda_compromisso = '{$cod_compromisso}'" );
			$versaoNova = $versaoAtual + 1;
			$campos = "";
			$valores = "";
			// faz as verificacoes dos campos postados
			if( $titulo || $conteudo )
			{
				$edit_dataArr = explode( "/", $data );
				if( is_array( $edit_dataArr ) && count( $edit_dataArr ) == 3 && checkdate( $edit_dataArr[1], $edit_dataArr[0], $edit_dataArr[2] ) )
				{
					$edit_data = "{$edit_dataArr[2]}-{$edit_dataArr[1]}-{$edit_dataArr[0]}";
					if( ereg( "[0-9]{2}:[0-9]{2}", $hora_inicio ) ) 
					{
						if( $hora_fim )
						{
							if( ereg( "[0-9]{2}:[0-9]{2}", $hora_fim ) ) 
							{
								$campos .= ", data_fim" ;
								$valores .= ", '{$edit_data} {$hora_fim}'";
							}
						}
					}
					else 
					{
						$this->erro_msg .= "Voc&ecirc; deve preencher o campo Hora de Inicio corretamente. Formato hora: hh:mm<br>";
						$verifica = false;
					}
				}
				else 
				{
					$this->erro_msg .= "Voc&ecirc; deve preencher o campo Data corretamente. Formato data: dd/mm/aaaa<br>";
					$verifica = false;
				}
			}
			else 
			{
				$this->erro_msg .= "Voc&ecirc; deve preencher o campo Titulo ou o campo Descricao<br>";
				$verifica = false;
			}
			if( isset( $publico ) )
			{
				$campos .= ", publico";
				$valores .= ", '1'";
			}
			if( isset( $importante ) )
			{
				$campos .= ", importante";
				$valores .= ", '1'";
			}
			
			if( $verifica )
			{
				$db->Consulta( "UPDATE agenda_compromisso SET ativo = 0 WHERE cod_agenda_compromisso = '{$cod_compromisso}'" );
				$this->cadastraCompromisso( $cod_compromisso, $titulo, $conteudo, $data, $hora_inicio, $hora_fim, $publico, $importante );
			}
		}
	}
	
	function edita_nota2compromisso( $cod_compromisso, $hora_fim )
	{
		$db = new clsBanco();
		if( $this->compromissoPertenceAgenda( $cod_compromisso ) )
		{
			if( ereg( "[0-9]{2}:[0-9]{2}", $hora_fim ) ) 
			{
				// pega a versao da nota
				$versaoAtual = $db->CampoUnico( "SELECT MAX( versao ) FROM agenda_compromisso WHERE cod_agenda_compromisso = '{$cod_compromisso}'" );
				$versaoNova = $versaoAtual + 1;
				
				// pega os dados da nota
				$db->Consulta( "SELECT data_inicio, titulo, descricao, importante, publico FROM agenda_compromisso WHERE cod_agenda_compromisso = '{$cod_compromisso}' AND versao = '{$versaoAtual}'" );
				$db->ProximoRegistro();
				list( $data, $titulo, $descricao, $importante, $publico ) = $db->Tupla();
				
				$data_inicio = date( "d/m/Y", strtotime( $data ) );
				$hora_inicio = date( "H:i", strtotime( $data ) );
				
				$this->edita_compromisso( $cod_compromisso, $titulo, $descricao, $data_inicio, $hora_inicio, $hora_fim, $publico, $importante );
			}
			else 
			{
				$this->erro_msg .= "Voc&ecirc; deve preencher o campo Hora de Fim corretamente. Formato hora: hh:mm<br>";
			}
		}
	}
	
	function restaura_versao( $cod_compromisso, $versao )
	{
		$db = new clsBanco();
		if( $this->compromissoPertenceAgenda( $cod_compromisso ) )
		{
			$db->Consulta( "UPDATE agenda_compromisso SET ativo = 0 WHERE cod_agenda_compromisso = '{$cod_compromisso}'" );
			$db->Consulta( "UPDATE agenda_compromisso SET ativo = 1 WHERE cod_agenda_compromisso = '{$cod_compromisso}' AND versao = '{$versao}'" );
			$this->erro_msg .= "Vers&atilde;o {$versao} restaurada com sucesso.<br>";
		}
	}
	
	function excluiCompromisso( $cod_compromisso )
	{
		$db = new clsBanco();
		if( $this->compromissoPertenceAgenda( $cod_compromisso ) )
		{
			$db->Consulta( "UPDATE agenda_compromisso SET ativo = 0 WHERE cod_agenda_compromisso = '{$cod_compromisso}'" );
		}
	}
	
	function permissao_agenda()
	{
		if( is_numeric( $this->editor ) && is_numeric( $this->agenda ) )
		{
			$db = new clsBanco();
			if($this->editor == 0)
			{
				return true;
			}
			$db->Consulta( "SELECT 1 FROM agenda WHERE ref_ref_cod_pessoa_own = '{$this->editor}' AND cod_agenda = '{$this->agenda}'" );
			if( $db->ProximoRegistro() )
			{
				return true;
			}
			
			$db->Consulta( "SELECT 1 FROM agenda_responsavel WHERE ref_ref_cod_pessoa_fj = '{$this->editor}' AND ref_cod_agenda = '{$this->agenda}'" );
			if( $db->ProximoRegistro() )
			{
				return true;
			}
		}
		return false;
	}
	
	function getPublica()
	{
		return $this->publica;
	}
	
	function getEnviaAlerta()
	{
		return $this->envia_alerta;
	}
	
	function getNome()
	{
		return $this->nome_agenda;
	}
	
	function getCodPessoaDono()
	{
		return $this->cod_pessoa_dono;
	}
	
	function getCodAgenda()
	{
		return $this->agenda;
	}
	
	function getCompromissoVersao( $cod_compromisso )
	{
		$db = new clsBanco();
		if( $this->compromissoPertenceAgenda( $cod_compromisso ) )
		{
			$maxVersao = $db->CampoUnico( "SELECT MAX( versao ) FROM agenda_compromisso WHERE cod_agenda_compromisso = '{$cod_compromisso}'" );
			return $maxVersao;
		}
		return 0;
	}
	
	function compromissoPertenceAgenda( $cod_compromisso )
	{
		$db = new clsBanco();
		if($cod_compromisso)
		{
			$db->Consulta( "SELECT 1 FROM agenda_compromisso WHERE cod_agenda_compromisso = '{$cod_compromisso}' AND ref_cod_agenda = '{$this->agenda}'" );
			if( $db->Num_Linhas() )
			{
				return true;
			}
		}

		return false;
	}
	
	/*
		COMPROMISSOS EXTERNOS
	*/
	
	
	// Busca Compromissos Relativos a encaminhamentos
	
	function add_compromisso_externo_1()
	{
		$db = new clsBanco();
		// seleciona os dados
		//$db->Consulta();
		if($this->time_atual)
		{
			$objEncaminha = new clsEncaminha();
			$listaEncaminha = $objEncaminha->lista(false,false,false,false,false,$this->cod_pessoa_dono,false,false,false,false,false,false,date("Y-m-d",$this->time_atual), date("Y-m-d",$this->time_atual)." 23:59:59",false,false);
			if($listaEncaminha)
			foreach ( $listaEncaminha	as $encaminha) 
			{
	
				if($encaminha['ref_cod_juris_processo'] && $encaminha['ref_versao_processo'])
				{
					$objProcesso = new clsProcesso($encaminha['ref_cod_juris_processo'],$encaminha['ref_versao_processo']);
					$detalheProcesso = $objProcesso->detalhe();
					if($detalheProcesso['ativo'] == 1 && !$detalheProcesso['ref_pessoa_finalizadora'] && $qtd<3)
					{
						$temp_arr_compromisso2["data_inicio"] = $detalheProcesso['data_envio'];
						$temp_arr_compromisso2["versao"] = "1";
						$temp_arr_compromisso2["data_fim"] =$detalheProcesso['data_envio'];
						$temp_arr_compromisso2["cod_agenda_compromisso"] = 0;
						$temp_arr_compromisso2["titulo"] = "Pasta Encaminhada";
						$temp_arr_compromisso2["descricao"] = "Nova Pasta foi Encaminhado nº {$encaminha['ref_cod_juris_processo']}";
						$temp_arr_compromisso2["importante"] = "1";
						$temp_arr_compromisso2["publico"] = "0";
						$this->compromissos[] = $temp_arr_compromisso2;
						unset( $temp_arr_compromisso, $temp_arr_compromisso2 );
					}
				}
				else 
				{
					$objTramite = new clsTramite($encaminha['ref_cod_juris_tramite'],$encaminha['ref_versao_tramite']);
					$detalheTramite = $objTramite->detalhe();
					$objProcesso = new clsProcesso($detalheTramite['ref_cod_juris_processo'],$detalheTramite['ref_versao_processo']);
					$detalheProcesso = $objProcesso->detalhe();					
					if($detalheTramite['ativo'] == 1 && !$detalheProcesso['ref_pessoa_finalizadora'] && $qtd<3)
					{
						$temp_arr_compromisso2["data_inicio"] = $detalheProcesso['data_envio'];
						$temp_arr_compromisso2["versao"] = "1";
						$temp_arr_compromisso2["data_fim"] =$detalheProcesso['data_envio'];
						$temp_arr_compromisso2["cod_agenda_compromisso"] = 0;
						$temp_arr_compromisso2["titulo"] = "Processo Encaminhado";
						$temp_arr_compromisso2["descricao"] = "Novo Processo foi Encaminhado nº {$encaminha['ref_cod_juris_tramite']}";
						$temp_arr_compromisso2["importante"] = "1";
						$temp_arr_compromisso2["publico"] = "0";
						$this->compromissos[] = $temp_arr_compromisso2;
						unset( $temp_arr_compromisso, $temp_arr_compromisso2 );
					}					
				}
			}
		}	
	}
	
	//Busca Compromissos marcados para o futuro e devem ser mostrados em dias anteriores
	
	function add_compromisso_externo_2()
	{
		$db = new clsBanco();
		
		// seleciona os dados
		if($this->time_atual)
		{
			$data_atual = date("Y-m-d");
			$obj = new clsAgenda(false,$this->cod_pessoa_dono);
			$cod_agenda = $obj->getCodAgenda();
			$obj = new clsAgendaJuris();
			$lista_avisos = $obj->lista(false,$cod_agenda,false,$data_atual,false,false,false,false,false);
			if($lista_avisos)
			{
				$db = new clsBanco();
				foreach ($lista_avisos as $aviso) {
					$obj = new clsTipoCompromisso($aviso['ref_cod_juris_tipo_compromisso']);
					$detalhe = $obj->detalhe();
					$data_aviso = date("d/m/Y",strtotime($aviso['data_aviso']) - $detalhe['avisa_intranet']*86400);
					if(date("d/m/Y",$this->time_atual) >= $data_aviso && date("d/m/Y",$this->time_atual) < date("d/m/Y",strtotime($aviso['data_aviso'])))
					{
						$db->Consulta( "SELECT data_inicio, data_fim, descricao FROM agenda_compromisso WHERE cod_agenda_compromisso = '{$aviso['ref_cod_agenda_compromisso']}' AND ativo = 1 ORDER BY data_inicio ASC" );
						if($db->ProximoRegistro())
						{
							$tupla = $db->Tupla();
							$temp_arr_compromisso2["data_inicio"] = "";
							$temp_arr_compromisso2["versao"] = "1";
							$temp_arr_compromisso2["data_fim"] ="";
							$temp_arr_compromisso2["cod_agenda_compromisso"] = 0;
							$temp_arr_compromisso2["titulo"] = "Lembrete de Compromisso";
							$data_comp_ini = date("d/m/Y",strtotime( $tupla['data_inicio']));
							$hora_comp_ini = date("H:i:s",strtotime( $tupla['data_inicio']));
							$data_comp_fim = date("d/m/Y",strtotime( $tupla['data_fim']));
							$hora_comp_fim = date("H:i:s",strtotime( $tupla['data_fim']));
							if($data_comp_ini == $data_comp_fim)
							{
								$data_compromisso = "$data_comp_ini $hora_comp_ini - $hora_comp_fim";
							}
							$temp_arr_compromisso2["descricao"] = "{$tupla['descricao']} <br> Data: $data_compromisso";
							$temp_arr_compromisso2["importante"] = "0";
							$temp_arr_compromisso2["publico"] = "0";
							// passa os valores para o array principal de compromissos
							$this->compromissos[] = $temp_arr_compromisso2;
							// libera as duas variaveis temporarias
							unset( $temp_arr_compromisso, $temp_arr_compromisso2 );
						}
						
					}
					
				}
			}
		} 
	}
	function add_compromisso_externo_3()
	{
		$db = new clsBanco();
		
		// seleciona os dados
		//$db->Consulta();
		while ( $db->ProximoRegistro() )
		{
			list( $temp_arr_compromisso["data_inicio"], $temp_arr_compromisso["cod_agenda_compromisso"], $temp_arr_compromisso["versao"], $temp_arr_compromisso["data_fim"], $temp_arr_compromisso["titulo"], $temp_arr_compromisso["descricao"], $temp_arr_compromisso["importante"], $temp_arr_compromisso["publico"] ) = $db->Tupla();
			// usa os dados recebidos para montar um segundo array temporario
			// adicionando os itens na ordem certa (data_inicio primeiro) para que ele faca a ordenacao por data de inicio
			
			$temp_arr_compromisso2["data_inicio"] = $temp_arr_compromisso["data_inicio"];
			$temp_arr_compromisso2["versao"] = $temp_arr_compromisso["versao"];
			$temp_arr_compromisso2["data_fim"] = $temp_arr_compromisso["data_fim"];
			$temp_arr_compromisso2["cod_agenda_compromisso"] = $temp_arr_compromisso["cod_agenda_compromisso"];
			$temp_arr_compromisso2["titulo"] = $temp_arr_compromisso["titulo"];
			$temp_arr_compromisso2["descricao"] = $temp_arr_compromisso["descricao"];
			$temp_arr_compromisso2["importante"] = $temp_arr_compromisso["importante"];
			$temp_arr_compromisso2["publico"] = $temp_arr_compromisso["publico"];
			
			// passa os valores para o array principal de compromissos
			$this->compromissos[] = $temp_arr_compromisso2;
			
			// libera as duas variaveis temporarias
			unset( $temp_arr_compromisso, $temp_arr_compromisso2 );
		}
	}
}
?>