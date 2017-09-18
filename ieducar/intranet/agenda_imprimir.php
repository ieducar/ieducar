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

require_once ("include/clsBase.inc.php");
require_once ("include/clsBanco.inc.php");
require_once ("include/clsCadastro.inc.php");
require_once ("include/relatorio_pref_novo.inc.php");
require_once ("include/relatorio.inc.php");

class clsIndex extends clsBase
{

	function Formular()
	{
		$this->SetTitulo( "{$this->_instituicao} Agenda" );
		$this->processoAp = "345";
		$this->addEstilo('localizacaoSistema');
	}
}

class indice extends clsCadastro
{
	var $cod_agenda,
		$link;

		function indice()
	{
		foreach ($_GET as $nm => $var)
		{
			$this->$nm = $var;
		}
		foreach ($_POST as $nm => $var)
		{
			$this->$nm = $var;
		}
	}

	function semDesc($data_atual)
	{
		$diasSemana = array( "Domingo", "Segunda Feira", "Terça Feira", "Quarta Feira", "Quinta Feira", "Sexta Feira", "Sabado" );
		return $diasSemana[date('w', strtotime( $data_atual ) )];
	}

	function quebraLinha( $texto, $tamaho_max_caracteres )
	{
		if( strlen( $texto ) > $tamaho_max_caracteres )
		{
			$texto_array = explode( " ", $texto );
			$texto = "";
			$tamanho_linha = 0;
			foreach ( $texto_array AS $palavra )
			{
				$tamanho_palavra = strlen( $palavra );
				// se uma unica palavra for maior do que a linha quebra essa palavra no meio
				if( $tamanho_palavra > $tamaho_max_caracteres )
				{
					$texto .= substr( $palavra, 0, $tamaho_max_caracteres ) . "\n" . substr( $palavra, $tamaho_max_caracteres ) . " ";
				}
				else
				{
					// com essa palavra a linha vai passar do limite de caracteres?
					if( $tamanho_linha + $tamanho_palavra >= $tamaho_max_caracteres )
					{
						$texto .= "\n{$palavra} ";
						$tamanho_linha = $tamanho_palavra;
					}
					else
					{
						// apenas adiciona a palavra na linha e continua
						$texto .= "{$palavra} ";
						$tamanho_linha += $tamanho_palavra;
					}
				}
			}
		}
		return $texto;
	}

	function Inicializar()
	{
		$retorno = "Novo";

		if ( isset( $_GET['cod_agenda'] ) )
		{
			$this->cod_agenda = $_GET['cod_agenda'];
		}

    $localizacao = new LocalizacaoSistema();
    $localizacao->entradaCaminhos( array(
         $_SERVER['SERVER_NAME']."/intranet" => "In&iacute;cio",
         ""                                  => "Imprimir agenda"
    ));
    $this->enviaLocalizacao($localizacao->montar());		

		return $retorno;
	}

	function Gerar()
	{

		$this->campoData( "data_inicio", "Data Inicial", $this->data_inicio );

		$this->campoData( "data_fim", "Data Final", $this->data_fim );

		$this->campoRadio( "impressora", "Tipo de Impressora", array( "Laser", "Jato de Tinta" ), $this->impressora );

		if($this->link)
		{
			$this->campoRotulo("arquivo","Arquivo","<a href='$this->link'>Clique aqui para visualizar</a>" );
		}

		$this->url_cancelar = "agenda.php?cod_agenda={$this->cod_agenda}";

		$this->nome_url_cancelar = "Cancelar";

	}

	function Novo()
	{
		$db = new clsBanco();

		$where = "";
		// define as datas de limite dos compromissos
		if ( !empty($this->data_inicio ) )
		{
			$data_inicio = urldecode($this->data_inicio);
			$data_inicio = explode("/", $data_inicio);
			$data_inicio = "{$data_inicio[2]}-{$data_inicio[1]}-{$data_inicio[0]}";
			$where .= "'{$data_inicio} 00:00:00' <= data_inicio AND " ;
		}
		if ( !empty( $this->data_fim ) )
		{
			$data_fim = urldecode($this->data_fim);
			$data_fim = explode("/", $data_fim);
			$data_fim = "{$data_fim[2]}-{$data_fim[1]}-{$data_fim[0]}";
			$where .= "'{$data_fim} 23:59:59' >= data_fim AND " ;
		}

		$compromissos = array();
		$msg = "";

		//busca nome da agenda para titulo do relatorio
		$dba = new clsBanco();
		$nm_agenda = $dba->CampoUnico(" SELECT nm_agenda FROM agenda WHERE cod_agenda = {$this->cod_agenda} ");

		//verifica tipo de impressao
		if ($this->impressora == 1)
		{
			//impressao laser
			$relatorio = new relatoriosPref( false, 80, false, false, "A4", "Agenda: ".$nm_agenda);
		}
		else
		{
			//impressao jato de tinta
			$relatorio = new relatorios( "Agenda: ".$nm_agenda, 10 );
		}

		if ( ( $data_inicio > $data_fim) & ( isset( $data_fim ) ) )
		{
			$this->mensagem = "A data inicial não pode ser maior que a data final.";
		}

		$db = new clsBanco();
		$db->Consulta( "SELECT cod_agenda_compromisso, versao FROM agenda_compromisso WHERE ativo = 1 AND ref_cod_agenda = {$this->cod_agenda} AND $where data_fim IS NOT NULL ORDER BY data_inicio ASC ");

		while( $db->ProximoRegistro() )
		{
			list ( $cod_comp, $versao ) = $db->Tupla();
			$compromissos[] = array( "cod" => $cod_comp, "versao" => $versao );
		}

		$aux = 0;
		$qtd_pagina = 0;

		if( count( $compromissos ) )
		{
			$data_ant = "";
			foreach ( $compromissos as $compromisso )
			{
				$db->Consulta( "SELECT data_inicio, data_fim, titulo, descricao FROM agenda_compromisso WHERE cod_agenda_compromisso = '{$compromisso["cod"]}' AND ref_cod_agenda = {$this->cod_agenda} AND versao = '{$compromisso["versao"]}' " );

				if( $db->ProximoRegistro() )
				{
					// inicializacao de variaveis
					$qtd_tit_copia_desc = 5;

					list( $data_inicio, $data_fim, $titulo, $descricao ) = $db->Tupla();

					// TITULO
					if( $titulo )
					{
						$disp_titulo = $titulo;
					}
					else
					{
						// se nao tiver titulo pega as X primeiras palavras da descricao ( X = $qtd_tit_copia_desc )
						$disp_titulo = implode( " ", array_slice( explode( " ", $descricao ), 0, $qtd_tit_copia_desc ) );
					}

					// remove quebra de linha
//					$disp_titulo = str_replace("\r"," ", $disp_titulo );
//					$disp_titulo = str_replace("\n"," ", $disp_titulo );
//					$disp_titulo = str_replace("<br>"," ", $disp_titulo );
//
//					$titulo = str_replace("\r"," ", $titulo );
//					$titulo = str_replace("\n"," ", $titulo );
//					$titulo = str_replace("<br>"," ", $titulo );
//
//					$descricao = str_replace("\r"," ", $descricao );
//					$descricao = str_replace("\n"," ", $descricao );
//					$descricao = str_replace("<br>"," ", $descricao );

					// quebra o texto em linhas que caibam
//					$disp_titulo = $this->quebraLinha( $disp_titulo, 60 );
//					$titulo = $this->quebraLinha( $titulo, 60 );
//					$descricao = $this->quebraLinha( $descricao, 60 );

					$hora_comp = substr($data_inicio,11,5);
					$hora_fim = substr($data_fim,11,5);

					//verifica tipo da impressora 1 laser 0 jato de tinta
					if ($this->impressora == 0)
					{
						if( $data_ant != substr( $data_inicio, 0, 10 ) )
						{
							$relatorio->novalinha(array($this->semDesc($data_inicio).": ".date("d/m/Y", strtotime($data_inicio))),0,12,true);
							$relatorio->novalinha(array("{$hora_comp} as {$hora_fim} {$disp_titulo}"),0, 13 + 10 * ( strlen( $disp_titulo ) / 60 ) );

							$linhas = count( explode("\n",$descricao) );
							$relatorio->novalinha(array(false, $descricao),62, 13 + 10 * $linhas );

							$data_ant = substr( $data_inicio, 0, 10 );
						}
						else
						{
							if($hora_comp == "00:00")
							{
								$relatorio->novalinha(array( date("d/m/Y", strtotime($data_inicio) )." - $descricao"),0, 13 * ( count( explode( "\n", $descricao ) ) + 1), false, "arial", false, true );
							}
							else
							{
								$relatorio->novalinha(array("{$hora_comp} as {$hora_fim} {$disp_titulo}"),0, 13 * ( count( explode( "\n", $disp_titulo ) ) ) );
								$linhas = count( explode("\n",$descricao) );
								$relatorio->novalinha(array(false, $descricao),62, 13 + 10 * $linhas );
							}
						}
					}
					else
					{
						// laser
						if( $data_ant != substr( $data_inicio, 0, 10 ) )
						{
							$relatorio->novalinha(array($this->semDesc($data_inicio).": ".date("d/m/Y", strtotime($data_inicio))),0,13,true,"arial",false,false, 10, 3 );
							$data_ant = substr( $data_inicio, 0, 10 );
						}
						if( $hora_comp == "00:00" )
						{
							$relatorio->novalinha(array( date("d/m/Y", strtotime($data_inicio) )." - $descricao"),0, 13 + 10 * ( strlen( $descricao ) / 60 ),false,"arial",false,true );
						}
						else
						{
							if( $titulo || $descricao )
							{
								$textoLinha = "";
								if( $titulo )
								{
									$textoLinha = $titulo;
								}
								if( $descricao )
								{
									if( $textoLinha )
									{
										$textoLinha .= "\n\n";
									}
									$textoLinha .= $descricao;
								}

								if( $textoLinha )
								{
									$linhas = ceil( strlen($textoLinha) / 90 );
									$linhas += count( explode("\n",$textoLinha) );
									$relatorio->novalinha(array("{$hora_comp} as {$hora_fim}", $textoLinha),0, 13 + 10 * $linhas );
								}
//								$relatorio->altura += 30;
							}
						}
					}
				}
			}
			$this->link = $relatorio->fechaPdf();
		}
		return  true;

	}
}



$pagina = new clsIndex();

$miolo = new indice();
$pagina->addForm( $miolo );

$pagina->MakeAll();

?>