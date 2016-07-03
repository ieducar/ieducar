<?php

/*
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
 */

/**
 * Cadastro de nota da turma.
 *
 * @author   Adriano Erik Weiguert Nagasava <ctima@itajai.sc.gov.br>
 * @license  http://creativecommons.org/licenses/GPL/2.0/legalcode.pt  CC GNU GPL
 * @package  Core
 * @since    Arquivo dispon�vel desde a vers�o 1.0.0
 * @version  $Id$
 */

require_once 'include/clsBase.inc.php';
require_once 'include/clsCadastro.inc.php';
require_once 'include/clsBanco.inc.php';
require_once 'include/pmieducar/geral.inc.php';


class clsIndexBase extends clsBase
{
  function Formular() {
    $this->SetTitulo($this->_instituicao . 'Faltas/Notas Aluno');
    $this->processoAp = "650";
  }
}


class indice extends clsCadastro
{
	/**
	 * Referencia pega da session para o idpes do usuario atual
	 *
	 * @var int
	 */
	var $pessoa_logada;

	var $lst_matricula_turma;
	var $falta_ch_globalizada;
	var $lst_matriculas;

	var $nm_aluno;
	var $ref_cod_aluno;
	var $ref_cod_matricula;
	var $ref_cod_turma;
	var $ref_ref_cod_serie;
	var $ref_cod_curso;
	var $ref_ref_cod_escola;
	var $ref_cod_instituicao;
	var $modulo;
	var $ref_cod_disciplina;
	var $nota;
	var $faltas;
	var $disciplina_modulo;
	var $ref_cod_tipo_avaliacao;
	var $qtd_disciplinas;

	var $qtd_modulos;
	var $media;
	var $media_exame;
	var $aluno_exame;
	var $aprovado;
	var $conceitual;
	var $ano_letivo;
	var $padrao_ano_escolar;
	var $num_modulo;
	var $frequencia_minima;
	var $carga_horaria;
	var $hora_falta;
	var $cod_disciplinas;
	var $lst_apura_falta;
	var $exame;
	var $classifica;

	function Inicializar()
	{
		@session_start();
			$this->pessoa_logada = $_SESSION['id_pessoa'];
		@session_write_close();

		$this->ref_cod_turma 	  = $_GET["ref_cod_turma"];
		$this->ref_ref_cod_escola = $_GET["ref_ref_cod_escola"];
		$this->ref_ref_cod_serie  = $_GET["ref_ref_cod_serie"];
		$this->ref_cod_curso	  = $_GET["ref_cod_curso"];
		$this->ref_cod_disciplina = $_GET["ref_cod_disciplina"];
		$this->classifica		  = $_GET["classifica"];

		$obj_permissoes = new clsPermissoes();
		$obj_permissoes->permissao_cadastra( 650, $this->pessoa_logada, 7, "educar_turma_mvto_det.php?cod_turma={$this->ref_cod_turma}" );

		if ( is_numeric( $this->ref_cod_turma ) )
		{
			$obj_matricula_turma = new clsPmieducarMatriculaTurma();
			$lst_matricula_turma = $obj_matricula_turma->lista( null, $this->ref_cod_turma, null, null, null, null, null, null, 1 );
			if ( is_array( $lst_matricula_turma ) )
			{
//				Carrega todas as matr�culas da turma
				$this->lst_matricula_turma = $lst_matricula_turma;

				foreach ( $lst_matricula_turma as $matricula_turma )
				{
					$obj_matricula = new clsPmieducarMatricula( $matricula_turma["ref_cod_matricula"] );
					$det_matricula = $obj_matricula->detalhe();

//					Verifica se a matr�cula n�o est� aprovada nem reprovada
					if ( $det_matricula["aprovado"] != 1 && $det_matricula["aprovado"] != 2 )
					{
						$existe_matricula = "S";
					}
				}

//				Verifica se existe alguma matr�cula em exame ou em andamento
				if ( !( $existe_matricula == "S" ) )
				{
					header( "Location: educar_turma_mvto_det.php?cod_turma={$this->ref_cod_turma}" );
					die();
				}
			}
			else
			{
				$this->mensagem = "N&atilde;o existe nenhuma matr�cula cadastrada nesta turma.<br>";
			}
		}
		else
		{
			header( "Location: educar_turma_mvto_det.php?cod_turma={$this->ref_cod_turma}" );
			die();
		}
		$this->url_cancelar 	 = "educar_turma_mvto_det.php?cod_turma={$this->ref_cod_turma}";
		$this->nome_url_cancelar = "Cancelar";
		return $retorno;
	}

	function Gerar()
	{
		$obj_curso = new clsPmieducarCurso( $this->ref_cod_curso );
		$det_curso = $obj_curso->detalhe();

//		 Carrega as informa��es necess�rias do curso
		if ( $det_curso )
		{
			$this->ref_cod_tipo_avaliacao = $det_curso["ref_cod_tipo_avaliacao"];
			$this->media 				  = $det_curso["media"];
			$this->media_exame 			  = $det_curso["media_exame"];
			$this->frequencia_minima	  = $det_curso["frequencia_minima"];
			$this->falta_ch_globalizada	  = $det_curso["falta_ch_globalizada"];
			$this->padrao_ano_escolar 	  = $det_curso["padrao_ano_escolar"];
			$this->carga_horaria		  = $det_curso["carga_horaria"];
			$this->hora_falta			  = $det_curso["hora_falta"];
		}

//		Verifica se vai seguir o padr�o do ano escolar da escola
		if ( $this->padrao_ano_escolar == 1 )
		{
			$obj_escola_ano_letivo = new clsPmieducarEscolaAnoLetivo( $this->ref_ref_cod_escola, null, null, null, 1, null, null, 1 );
			$det_escola_ano_letivo = $obj_escola_ano_letivo->detalhe();

//			Carrega o ano letivo
			if ( is_array( $det_escola_ano_letivo ) )
				$this->ano_letivo = $det_escola_ano_letivo["ano"];


			$obj_ano_letivo_modulo = new clsPmieducarAnoLetivoModulo();
			$obj_ano_letivo_modulo->setOrderby( "data_fim" );
			$lst_ano_letivo_modulo = $obj_ano_letivo_modulo->lista( $this->ano_letivo, $this->ref_ref_cod_escola );

			if ( is_array( $lst_ano_letivo_modulo ) )
			{
				$obj_turma_disciplina = new clsPmieducarTurmaDisciplina();
				$lst_turma_disciplina = $obj_turma_disciplina->lista( $this->ref_cod_turma );

//				Carrega a quantidade de disciplinas da turma
				$this->qtd_disciplinas = count( $lst_turma_disciplina );

//				echo "<pre>";
//				print_r($lst_turma_disciplina);

				if ( $lst_turma_disciplina )
				{
					foreach ( $lst_turma_disciplina as $disciplina )
					{
//						Carrega o c�digo das disciplinas da turma
						$this->cod_disciplinas[] = $disciplina["ref_cod_disciplina"];
					}

//					Carrega a quantidade de m�dulos do ano letivo
					$this->qtd_modulos = count( $lst_ano_letivo_modulo );
					$cont = 1;

					if ( is_array( $lst_ano_letivo_modulo ) )
					{
//						Busca em qual m�dulo a turma est�
						//$resultado = $obj_turma_modulo->numModulo( $cont, $this->ref_ref_cod_serie, $this->ref_ref_cod_escola, $this->cod_disciplinas, $this->ref_cod_turma, $this->ref_cod_turma );
						$obj_matriculas = new clsPmieducarMatriculaTurma();
						$lst_matriculas = $obj_matriculas->lista( null, $this->ref_cod_turma, null, null, null, null, null, null, 1, $this->ref_ref_cod_serie, $this->ref_cod_curso, $this->ref_ref_cod_escola );

						$resultado = 0;

						if ( is_array( $lst_matriculas ) )
						{
							foreach ( $lst_matriculas as $registro )
							{
								$obj_nota_aluno = new clsPmieducarNotaAluno();
								$aux_min = $obj_nota_aluno->retornaModuloAluno( $this->ref_ref_cod_serie, $this->ref_ref_cod_escola, $this->ref_cod_turma, $this->ref_cod_turma, $registro["ref_cod_matricula"] );
								$aux_min = $aux_min + 1;

								if ( $resultado == 0 )
								{
									$resultado = $aux_min;
								}
								else if ( $resultado > $aux_min )
								{
									$resultado = $aux_min;
								}
							}
						}

						$this->num_modulo = $resultado;

						foreach ( $lst_ano_letivo_modulo as $registro )
						{
//							Verifica se a turma est� num m�dulo da turma
							if ( ( $resultado ) == $registro["sequencial"] )
							{
								$obj_modulo 	  = new clsPmieducarModulo( $registro["ref_cod_modulo"] );
								$det_modulo 	  = $obj_modulo->detalhe();

//								Carrega o nome do m�dulo no qual a turma se encontra
								$this->modulo 	  = $det_modulo["nm_tipo"];

								$obj_turma_disciplina = new clsPmieducarTurmaDisciplina();
								$obj_turma_disciplina->setOrderby( "ref_cod_disciplina" );

//								Carrega o c�digo das disciplinas da turma
								$lst_turma_disciplina = $obj_turma_disciplina->lista( $this->ref_cod_turma );

								if ( is_array( $lst_turma_disciplina ) )
								{
									$cont = 0;

									foreach ( $lst_turma_disciplina as $valores )
									{
										$obj_disciplina = new clsPmieducarDisciplina( $valores["ref_cod_disciplina"] );
										$det_disciplina = $obj_disciplina->detalhe();

										if ( $det_disciplina )
										{
//											Carrega a informa��o se a disciplina apura falta ou n�o
											$this->lst_apura_falta["{$det_disciplina["cod_disciplina"]}"] = $det_disciplina["apura_falta"];

											$obj_notas = new clsPmieducarNotaAluno();

//											Carrega a quantidade de notas por aluno de uma turma numa determinada disciplina
											$lst_notas = $obj_notas->retornaDiscMod( $this->ref_ref_cod_serie, $this->ref_ref_cod_escola, $det_disciplina["cod_disciplina"], $this->ref_cod_turma, $this->ref_cod_turma, null, true );

											if ( $cont == 0 )
											{
												$num_aux = $lst_notas;
											}
											else if ( $lst_notas < $num_aux )
											{
												$num_aux = $lst_notas;
											}
										}
										$cont++;
									}

									/*if ( is_numeric( $lst_notas ) )
									{
										$this->num_modulo = $lst_notas + 1;
									}
									else
									{
										$this->num_modulo = 1;
									}*/
								}

//								Carrega o n�mero do m�dulo em que a turma est�

//								$this->num_modulo = ( $resultado + 1 );
								break;
							}

//							Verifica se a turma est� no m�dulo de exame
							else if ( ( $resultado ) > $this->qtd_modulos )
							{
//								Carrega o nome do m�dulo no qual a turma se encontra como "Exame"
								$this->modulo 	  = "Exame";

//								Carrega o n�mero do m�dulo igual a quantidade de m�dulos da turma mais 1
								$this->num_modulo = ( $resultado + 1 );
								break;
							}
							$cont++;
						}
					}
				}
			}
		}

//		Escopo de instru��es a serem executadas, caso a turma n�o siga o padr�o ano letivo
		else
		{
			$obj_turma_modulo = new clsPmieducarTurmaModulo();
			$obj_turma_modulo->setOrderby( "data_fim" );
			$lst_turma_modulo = $obj_turma_modulo->lista( $this->ref_cod_turma );

			$obj_matriculas = new clsPmieducarMatriculaTurma();
			$lst_matriculas = $obj_matriculas->lista( null, $this->ref_cod_turma, null, null, null, null, null, null, 1, $this->ref_ref_cod_serie, $this->ref_cod_curso, $this->ref_ref_cod_escola );

			$resultado = 0;

			if ( is_array( $lst_matriculas ) )
			{
				foreach ( $lst_matriculas as $registro )
				{
					$obj_nota_aluno = new clsPmieducarNotaAluno();
					$aux_min = $obj_nota_aluno->retornaModuloAluno( $this->ref_ref_cod_serie, $this->ref_ref_cod_escola, $this->ref_cod_turma, $this->ref_cod_turma, $registro["ref_cod_matricula"] );
					$aux_min = $aux_min + 1;

					if ( $resultado == 0 )
					{
						$resultado = $aux_min;
					}
					else if ( $resultado > $aux_min )
					{
						$resultado = $aux_min;
					}
				}
			}

			$this->num_modulo = $resultado;

			if ( is_array($lst_turma_modulo) )
			{
				$obj_turma_disciplina = new clsPmieducarTurmaDisciplina();
				$lst_turma_disciplina = $obj_turma_disciplina->lista( $this->ref_cod_turma );

//				Carrega a quantidade de disciplinas da turma
				$this->qtd_disciplinas = count( $lst_turma_disciplina );

				if ( $lst_turma_disciplina )
				{
					foreach ( $lst_turma_disciplina as $disciplina )
					{
//						Carrega o c�digo das disciplinas da turma
						$this->cod_disciplinas[] = $disciplina["ref_cod_disciplina"];

						$obj_notas = new clsPmieducarNotaAluno();

//						Carrega a quantidade de notas por aluno de uma turma numa determinada disciplina
						$lst_notas = $obj_notas->retornaDiscMod( $this->ref_ref_cod_serie, $this->ref_ref_cod_escola, $disciplina["ref_cod_disciplina"], $this->ref_cod_turma, $this->ref_cod_turma, null, true );

						if ( $cont == 0 )
						{
							$num_aux = $lst_notas;
						}
						else if ( $lst_notas < $num_aux )
						{
							$num_aux = $lst_notas;
						}
					}

//					Carrega a quantidade de m�dulos da turma
					$this->qtd_modulos = count( $lst_turma_modulo );
					$cont = 1;

					foreach ( $lst_turma_modulo as $registro )
					{
						$obj_turma_modulo = new clsPmieducarTurmaModulo();

//						Busca em qual m�dulo a turma est�
//						$resultado = $obj_turma_modulo->numModulo( $cont, $this->ref_ref_cod_serie, $this->ref_ref_cod_escola, $this->cod_disciplinas, $this->ref_cod_turma, $this->ref_cod_turma );

//						Verifica se a turma est� num m�dulo da turma
						if ( ( $resultado ) == $cont )
						{
							$obj_modulo 	  = new clsPmieducarModulo( $registro["ref_cod_modulo"] );
							$det_modulo 	  = $obj_modulo->detalhe();

//							Carrega o nome do m�dulo no qual a turma se encontra
							$this->modulo 	  = $det_modulo["nm_tipo"];

//							Carrega o n�mero do m�dulo no qual a turma se encontra
							$this->num_modulo = ( $resultado );
							break;
						}

//						Verifica se a turma est� no m�dulo de exame
						else if ( ( $resultado ) > $this->qtd_modulos )
						{
//							Carrega o nome do m�dulo no qual a turma se encontra como "Exame"
							$this->modulo 	  = "Exame";

//							Carrega o n�mero do m�dulo no qual a turma se encontra igual ao n�mero de m�dulos da turma mais 1
							$this->num_modulo = ( $resultado + 1 );
							break;
						}
						$cont++;
					}
				}
			}
		}
//		echo "<pre>";
//		print_r( $this->cod_disciplinas );

		$obj_ano_letivo = new clsPmieducarEscolaAnoLetivo();
		$lst_ano_letivo = $obj_ano_letivo->lista( $this->ref_ref_cod_escola, null, null, null, 1, null, null, null, null, 1 );

		if ( is_array( $lst_ano_letivo ) )
		{
			$det_ano_letivo   = array_shift( $lst_ano_letivo );

//			Carrega o ano letivo em que a turma se encontra
			$this->ano_letivo = $det_ano_letivo["ano"];
		}

		$obj_tipo_avaliacao = new clsPmieducarTipoAvaliacao( $this->ref_cod_tipo_avaliacao );
		$det_tipo_avaliacao = $obj_tipo_avaliacao->detalhe();

// 		Carrega o tipo de avalia��o
		if ( $det_tipo_avaliacao )
			$this->conceitual = $det_tipo_avaliacao["conceitual"];

//		Carrega todos os valores do tipo de avalia��o do curso
		$obj_avaliacao_valores = new clsPmieducarTipoAvaliacaoValores();
		$obj_avaliacao_valores->setOrderby( "valor ASC" );
		$lst_avaliacao_valores = $obj_avaliacao_valores->lista( $this->ref_cod_tipo_avaliacao );

		if ( is_array( $lst_avaliacao_valores ) )
		{
			$opcoes_valores = array( "" => "Selecione" );

			foreach ( $lst_avaliacao_valores AS $valores )
				$opcoes_valores[$valores['sequencial']] = $valores["nome"];
		}

		$obj_turma_disciplina = new clsPmieducarTurmaDisciplina();
		$obj_turma_disciplina->setOrderby( "ref_cod_disciplina" );

//		Carrega o c�digo das disciplinas da turma
		$lst_turma_disciplina = $obj_turma_disciplina->lista( $this->ref_cod_turma );

//echo "<pre>";
//print_r( $lst_turma_disciplina );
		if ( is_array( $lst_turma_disciplina ) )
		{
//			Carrega a quantidade de disciplinas da turma
			$this->qtd_disciplinas = count( $lst_turma_disciplina );
			$opcoes_disciplinas = array( "" => "Selecione" );

			foreach ( $lst_turma_disciplina as $valores )
			{
				$obj_disciplina = new clsPmieducarDisciplina( $valores["ref_cod_disciplina"] );
				$det_disciplina = $obj_disciplina->detalhe();

				if ( $det_disciplina )
				{
//					Carrega a informa��o se a disciplina apura falta ou n�o
					$this->lst_apura_falta["{$det_disciplina["cod_disciplina"]}"] = $det_disciplina["apura_falta"];

					//$obj_notas = new clsPmieducarNotaAluno();

//					Carrega a quantidade de notas por aluno de uma turma numa determinada disciplina
					//$lst_notas = $obj_notas->retornaDiscMod( $this->ref_ref_cod_serie, $this->ref_ref_cod_escola, $det_disciplina["cod_disciplina"], $this->ref_cod_turma, $this->ref_cod_turma, null, true );

//					Verifica se a quantidade de notas por aluno � diferente do n�mero do m�dulo em que a turma se encontra
//echo "{$lst_notas} != {$this->num_modulo}<br>";
					//if ( $lst_notas != $this->num_modulo )
					//{
						$opcoes_disciplinas[$det_disciplina["cod_disciplina"]] = $det_disciplina["nm_disciplina"];
					//}
				}
			}
		}
		//echo "<pre>";
		//print_r( $opcoes_disciplinas );

//		Verifica se deve ser exibida a p�gina para classificar os alunos
		if ( $this->classifica == "S" )
		{
//			Verifica se a turma terminou o �ltimo m�dulo ou se est� no �ltimo m�dulo
			if ( $this->num_modulo >= $this->qtd_modulos )
			{
				$obj_nota_aluno = new clsPmieducarNotaAluno();

//				Carrega as m�dias de cada disciplina de cada aluno
				$lst_exame		= $obj_nota_aluno->listaMedias( $this->ref_ref_cod_serie, $this->ref_ref_cod_escola, $this->ref_cod_turma, $this->ref_cod_turma, $this->qtd_modulos, $this->ref_cod_curso, true, true, true );

				if ( is_array( $lst_exame ) )
				{
					$lst_disciplina_aprovacao = array( "" => "Selecione" );
					foreach ( $lst_exame as $exame )
					{
//						Verifica se o aluno possui freq��ncia abaixo da freq��ncia m�nima
						if ( ( 100 - $exame["faltas"] ) < $this->frequencia_minima )
						{
							$obj_disciplina = new clsPmieducarDisciplina( $exame["disc_ref_ref_cod_disciplina"] );
							$det_disciplina = $obj_disciplina->detalhe();

							if ( $det_disciplina )
							{
//								Carrega a disciplina que possui alunos com freq��ncia abaixo da freq��ncia m�nima
								$lst_disciplina_aprovacao["{$exame["disc_ref_ref_cod_disciplina"]}"] = $det_disciplina["nm_disciplina"];

//								Carrega as informa��es referentes a disciplina e ao aluno que possui freq��ncia abaixo da freq��ncia m�nima
								$lst_aprovacao[] 													 = $exame;
							}
						}
					}

					foreach ( $lst_aprovacao as $classificacao )
					{
						if ( $classificacao["media"] < $this->media )
						{
							$lst_classificacao[$classificacao["ref_ref_cod_matricula"]]["ref_ref_cod_matricula"] = $classificacao["ref_ref_cod_matricula"];
							$lst_classificacao[$classificacao["ref_ref_cod_matricula"]]["exibe"] = "S";
						}
						else
						{
							if( $lst_classificacao[$classificacao["ref_ref_cod_matricula"]] )
							{
								if ( $lst_classificacao[$classificacao["ref_ref_cod_matricula"]]["exibe"] != "S" )
								{
									$lst_classificacao[$classificacao["ref_ref_cod_matricula"]]["ref_ref_cod_matricula"] = $classificacao["ref_ref_cod_matricula"];
									$lst_classificacao[$classificacao["ref_ref_cod_matricula"]]["exibe"] = "N";
								}
							}
							else
							{
								$lst_classificacao[$classificacao["ref_ref_cod_matricula"]]["ref_ref_cod_matricula"] = $classificacao["ref_ref_cod_matricula"];
								$lst_classificacao[$classificacao["ref_ref_cod_matricula"]]["exibe"] = "N";
							}
						}
					}

					foreach ( $lst_classificacao as $registro )
					{
						foreach ( $lst_exame as $exame )
						{
							if ( $exame["ref_ref_cod_matricula"] == $registro["ref_ref_cod_matricula"] )
							{
								if ( $exame["media"] < $this->media )
								{
									if ( $registro["exibe"] == "N" )
									{
										$lst_classificacao[$registro["ref_ref_cod_matricula"]]["ref_ref_cod_matricula"] = $registro["ref_ref_cod_matricula"];
										$lst_classificacao[$registro["ref_ref_cod_matricula"]]["exibe"] = "S";
									}
								}
							}
						}
					}
					$opcoes_disciplinas = array_unique( $lst_disciplina_aprovacao );
				}

//				Verifica se existem alunos com a freq��ncia abaixo da freq��ncia m�nima
				if ( is_array( $lst_aprovacao ) )
				{
					foreach ( $lst_classificacao as $registro )
					{
						$obj_matricula = new clsPmieducarMatricula( $registro["ref_ref_cod_matricula"], null, null, null, null, null, null, null, null, null, 1, null, 1, $this->modulo );
						$det_matricula = $obj_matricula->detalhe();

						if ( $det_matricula )
						{
							$obj_aluno = new clsPmieducarAluno( $det_matricula["ref_cod_aluno"] );
							$det_aluno = $obj_aluno->detalhe();

							if ( $det_aluno )
							{
								$obj_pessoa = new clsPessoa_( $det_aluno["ref_idpes"] );
								$det_pessoa = $obj_pessoa->detalhe();

								$obj_dispensa = new clsPmieducarDispensaDisciplina( $this->ref_cod_turma, $det_matricula["cod_matricula"], $this->ref_cod_turma, $this->ref_ref_cod_serie, $this->ref_ref_cod_escola, $this->ref_cod_disciplina, null, null, null, null, null, 1 );
								$det_dispensa = $obj_dispensa->detalhe();

								if ( is_numeric( $this->ref_cod_tipo_avaliacao ) )
								{
									$obj_tipo_avaliacao = new clsPmieducarTipoAvaliacao( $this->ref_cod_tipo_avaliacao, null, null, null, null, null, 1, null, $this->ref_cod_instituicao );
									$det_tipo_avaliacao = $obj_tipo_avaliacao->detalhe();

//									Verifica se o tipo de avalia��o n�o � conceitual
									if ( $det_tipo_avaliacao["conceitual"] == 0 )
									{
										$this->campoOculto( "nm_aluno_{$det_pessoa["idpes"]}", $det_pessoa["nome"] );
										$this->campoTextoInv( "nm_aluno_{$det_pessoa["idpes"]}_", "Aluno", $det_pessoa["nome"], 30, 255, false, false, true );
										$this->lst_matriculas[] = array( "{$det_matricula["ref_cod_aluno"]}", "{$det_pessoa["idpes"]}", "{$det_matricula["cod_matricula"]}" );

//										Verifica se a falta n�o � globalizada e se n�o est� na �ltima disciplina
										if ( !( $this->falta_ch_globalizada == 1 && $this->qtd_disciplinas > 1 ) )
										{
											//$this->campoTextoInv( "faltas_{$det_pessoa["idpes"]}_", "Faltas", $registro["faltas"], 5, 5, false, false, true );

//											Verifica se a m�dia � maior ou igual a m�dia m�nima
											if ( $registro["exibe"] == "N" )
											{
												$this->campoCheck( "aprovacao_{$det_pessoa["idpes"]}_", " ", 0, "Aprovado" );
											}
											else
											{
												$this->campoCheck( "aprovacao_{$det_pessoa["idpes"]}_", " ", 0, "Exame" );
											}
										}
									}
									else
									{
										$this->campoTexto( "nm_aluno_{$det_pessoa["idpes"]}", "Aluno", $det_pessoa["nome"], 30, 255, false, false, false, "", "", "", "onKeyUp", true );
									}
								}
								else
								{
									$this->campoTexto( "nm_aluno_{$det_pessoa["idpes"]}", "Aluno", $det_pessoa["nome"], 30, 255, false, false, false, "", "", "", "onKeyUp", true );
								}
							}
						}
					}
				}
			}
		}
		else
		{
//			Verifica se a turma terminou o �ltimo m�dulo
			if ( $this->num_modulo > $this->qtd_modulos )
			{
				$obj_nota_aluno = new clsPmieducarNotaAluno();

//				Carrega as m�dias de cada disciplina de cada aluno
				$lst_exame		= $obj_nota_aluno->listaMedias( $this->ref_ref_cod_serie, $this->ref_ref_cod_escola, $this->ref_cod_turma, $this->ref_cod_turma, $this->qtd_modulos, $this->ref_cod_curso, true, true, false, true );

				if ( is_array( $lst_exame ) )
				{
					$lst_disciplina_aprovacao = array( "" => "Selecione" );
					foreach ( $lst_exame as $exame )
					{
						$obj_notas = new clsPmieducarNotaAluno();

//						Carrega a quantidade de notas por aluno de uma turma numa determinada disciplina
						$lst_notas = $obj_notas->retornaDiscMod( $this->ref_ref_cod_serie, $this->ref_ref_cod_escola, $exame["disc_ref_ref_cod_disciplina"], $this->ref_cod_turma, $this->ref_cod_turma, $exame["ref_ref_cod_matricula"] );

						$obj_dispensa = new clsPmieducarDispensaDisciplina( $this->ref_cod_turma, $exame["ref_ref_cod_matricula"], $this->ref_cod_turma, $this->ref_ref_cod_serie, $this->ref_ref_cod_escola, $exame["disc_ref_ref_cod_disciplina"], null, null, null, null, null, 1 );
						$det_dispensa = $obj_dispensa->detalhe();

//						Verifica se a quantiade de notas da disciplina � diferente da quantidade de m�dulos, se n�o foi dispensado da disciplina e se a m�dia � menor que a m�dia m�nima
						if ( $lst_notas != $this->num_modulo && !is_array( $det_dispensa ) && $exame["media"] < $this->media )
						{
							$obj_disciplina = new clsPmieducarDisciplina( $exame["disc_ref_ref_cod_disciplina"] );
							$det_disciplina = $obj_disciplina->detalhe();

							if ( $det_disciplina )
							{
//								Carrega a disciplina que possui alunos com m�dia abaixo da m�dia m�nima
								$lst_disciplina_aprovacao["{$exame["disc_ref_ref_cod_disciplina"]}"] = $det_disciplina["nm_disciplina"];
							}
						}
					}
					$opcoes_disciplinas = array_unique( $lst_disciplina_aprovacao );
				}
			}
			$this->campoRotulo( "tipo_modulo", "M�dulo", $this->modulo );
			$this->campoRotulo( "numero_modulo", "N�mero do M�dulo", $this->num_modulo );
//			$this->campoTexto( "tipo_modulo", "M�dulo", $this->modulo, 30, 255, false, false, false, "", "", "", "onKeyUp", true );
			$this->campoLista( "ref_cod_disciplina", "Disciplina", $opcoes_disciplinas, $this->ref_cod_disciplina );

			if ( is_numeric( $this->ref_cod_disciplina ) )
			{
				$this->tipoacao = "Novo";

//				Verifica se a turma terminou o �ltimo m�dulo
				if ( $this->qtd_modulos < $this->num_modulo )
				{
					$obj_nota_aluno = new clsPmieducarNotaAluno();

//					Carrega as m�dias dos alunos por disciplina
					$lst_exame		= $obj_nota_aluno->listaMedias( $this->ref_ref_cod_serie, $this->ref_ref_cod_escola, $this->ref_cod_turma, $this->ref_cod_turma, $this->qtd_modulos, $this->ref_cod_curso, true, true, false, true );

					if ( is_array( $lst_exame ) )
					{
						foreach ( $lst_exame as $registro )
						{
							$obj_matricula = new clsPmieducarMatricula( $registro["ref_ref_cod_matricula"], null, null, null, null, null, null, null, null, null, 1, null, 1, $this->modulo );
							$det_matricula = $obj_matricula->detalhe();

							if ( $registro["disc_ref_ref_cod_disciplina"] == $this->ref_cod_disciplina )
							{
//								Verifica se a m�dia do aluno em uma disciplina est� abaixo da m�dia m�nima
								if ( $registro["media"] < $this->media )
								{
									if ( $det_matricula )
									{
										$obj_aluno = new clsPmieducarAluno( $det_matricula["ref_cod_aluno"] );
										$det_aluno = $obj_aluno->detalhe();

										if ( $det_aluno )
										{
											$obj_pessoa = new clsPessoa_( $det_aluno["ref_idpes"] );
											$det_pessoa = $obj_pessoa->detalhe();

											$obj_dispensa = new clsPmieducarDispensaDisciplina( $this->ref_cod_turma, $det_matricula["cod_matricula"], $this->ref_cod_turma, $this->ref_ref_cod_serie, $this->ref_ref_cod_escola, $this->ref_cod_disciplina, null, null, null, null, null, 1 );
											$det_dispensa = $obj_dispensa->detalhe();

//											Verifica se o aluno n�o foi dispensado da disciplina
											if ( !$det_dispensa )
											{
												if ( is_numeric( $this->ref_cod_tipo_avaliacao ) )
												{
													$obj_tipo_avaliacao = new clsPmieducarTipoAvaliacao( $this->ref_cod_tipo_avaliacao, null, null, null, null, null, 1, null, $this->ref_cod_instituicao );
													$det_tipo_avaliacao = $obj_tipo_avaliacao->detalhe();

//													Verifica se o tipo de avalia��o n�o � conceitual
													if ( $det_tipo_avaliacao["conceitual"] == 0 )
													{
														$this->campoOculto( "nm_aluno_{$det_pessoa["idpes"]}", $det_pessoa["nome"] );
														$this->campoTextoInv( "nm_aluno_{$det_pessoa["idpes"]}_", "Aluno", $det_pessoa["nome"], 30, 255, false, false, true );
														$this->lst_matriculas[] = array( "{$det_matricula["ref_cod_aluno"]}", "{$det_pessoa["idpes"]}", "{$det_matricula["cod_matricula"]}" );
														$this->campoLista( "nota_{$det_pessoa["idpes"]}", " Nota", $opcoes_valores, "", "", true );
														$this->exame = "S";

//														Verifica se a falta n�o � globalizada e se n�o est� na �ltima disciplina
														if ( !( $this->falta_ch_globalizada == 1 && $this->qtd_disciplinas > 1 ) )
														{
//															Verifica se a disciplina apura faltas
															if ( $this->lst_apura_falta[$this->ref_cod_disciplina] == 1 )
															{
//																Verifica se o ano letivo ainda est� em andamento
																if ( !( $this->num_modulo > $this->qtd_modulos ) )
																{
																	$this->campoNumero( "faltas_{$det_pessoa["idpes"]}", " Faltas", "", 3, 3, true );
																}
																else
																{
																	$this->exame = "S";
																}
															}
															else
															{
																$this->campoLista( "nota_{$det_pessoa["idpes"]}", " Nota", $opcoes_valores, "", "", false );
															}
														}
													}
													else
													{
														$this->campoTexto( "nm_aluno_{$det_pessoa["idpes"]}", "Aluno", $det_pessoa["nome"], 30, 255, false, false, false, "", "", "", "onKeyUp", true );
													}
												}
												else
												{
													$this->campoTexto( "nm_aluno_{$det_pessoa["idpes"]}", "Aluno", $det_pessoa["nome"], 30, 255, false, false, false, "", "", "", "onKeyUp", true );
												}
											}
										}
									}
								}
							}
						}
					}
					else
					{
					}
				}
//				Executa o bloco de instru��es abaixo, caso o ano letivo n�o tenha encerrado
				else
				{
					if ( is_array( $this->lst_matricula_turma ) )
					{
						foreach ( $this->lst_matricula_turma as $registro )
						{
							$obj_notas = new clsPmieducarNotaAluno();

//							Carrega a quantidade de notas por aluno de uma turma numa determinada disciplina
							$lst_notas = $obj_notas->retornaDiscMod( $this->ref_ref_cod_serie, $this->ref_ref_cod_escola, $this->ref_cod_disciplina, $this->ref_cod_turma, $this->ref_cod_turma, $registro["ref_cod_matricula"] );
//echo "matrc.: {$registro["ref_cod_matricula"]}<br>";
//							echo "1 {$this->num_modulo} == ".( $lst_notas + 1 )."<br>";
							if ( $this->num_modulo == ( $lst_notas + 1 ) )
							{
								$obj_matricula = new clsPmieducarMatricula();
								$modulo_matric = $obj_matricula->numModulo( $this->ref_ref_cod_serie, $this->ref_ref_cod_escola, $this->ref_cod_turma, $this->ref_cod_turma, $registro["ref_cod_matricula"] );

								$obj_matricula = new clsPmieducarMatricula( $registro["ref_cod_matricula"], null, null, null, null, null, null, null, null, null, 1, null, 1, $this->modulo );
								$det_matricula = $obj_matricula->detalhe();

								if ( $det_matricula )
								{
//									echo "2 ".( $modulo_matric + 1 )." == {$this->num_modulo}<br>";
									if ( ( $modulo_matric + 1 ) == $this->num_modulo )
									{
										$obj_aluno = new clsPmieducarAluno( $det_matricula["ref_cod_aluno"] );
										$det_aluno = $obj_aluno->detalhe();

										if ( $det_aluno )
										{
											$obj_pessoa = new clsPessoa_( $det_aluno["ref_idpes"] );
											$det_pessoa = $obj_pessoa->detalhe();

											$obj_dispensa = new clsPmieducarDispensaDisciplina();
											$det_dispensa = $obj_dispensa->lista( $this->ref_cod_turma, $det_matricula["cod_matricula"], $this->ref_cod_turma, $this->ref_ref_cod_serie, $this->ref_ref_cod_escola, $this->ref_cod_disciplina, null, null, null, null, null, null, null, 1 );
											$det_disciplina = $det_disciplina[0];

	//										Verifica se o aluno n�o foi dispensado da disciplina
											if ( !$det_dispensa )
											{
												if ( is_numeric( $this->ref_cod_tipo_avaliacao ) )
												{
													$obj_tipo_avaliacao = new clsPmieducarTipoAvaliacao( $this->ref_cod_tipo_avaliacao, null, null, null, null, null, 1, null, $this->ref_cod_instituicao );
													$det_tipo_avaliacao = $obj_tipo_avaliacao->detalhe();

													$this->campoOculto( "nm_aluno_{$det_pessoa["idpes"]}", $det_pessoa["nome"] );
													$this->campoTextoInv( "nm_aluno_{$det_pessoa["idpes"]}_", "Aluno", $det_pessoa["nome"], 30, 255, false, false, true );
													$this->lst_matriculas[] = array( "{$det_matricula["ref_cod_aluno"]}", "{$det_pessoa["idpes"]}", "{$det_matricula["cod_matricula"]}" );

													$obj_nota_aluno		  = new clsPmieducarNotaAluno();
													$disc_nota 			  = $obj_nota_aluno->retornaDiscNota( $this->ref_ref_cod_serie, $this->ref_ref_cod_escola, $this->ref_cod_turma, $this->ref_cod_turma, $det_matricula["cod_matricula"], $this->num_modulo );
													$obj_disciplina_serie = new clsPmieducarDisciplinaSerie();
													$qtd_disc			  = $obj_disciplina_serie->retornaQtdDiscMat( $this->ref_ref_cod_serie, $this->ref_ref_cod_escola, $this->ref_cod_turma, $this->ref_cod_turma, $det_matricula["cod_matricula"] );

													if ( $this->falta_ch_globalizada == 1 && $disc_nota == ( $qtd_disc - 1 ) )
													{
														$this->campoLista( "nota_{$det_pessoa["idpes"]}", " Nota", $opcoes_valores, "", "", true );
													}
													else
													{
														$this->campoLista( "nota_{$det_pessoa["idpes"]}", " Nota", $opcoes_valores, "", "", false );
													}

	//												Verifica se a falta n�o � globalizada e se n�o est� na �ltima disciplina
													if ( !( $this->falta_ch_globalizada == 1 && $this->qtd_disciplinas > 1 ) )
													{
	//													Verifica se a disciplina apura faltas
														if ( $this->lst_apura_falta[$this->ref_cod_disciplina] == 1 )
														{
	//														Verifica se o ano letivo ainda est� em andamento
															if ( !( $this->num_modulo > $this->qtd_modulos ) )
															{
																$this->campoNumero( "faltas_{$det_pessoa["idpes"]}", " Faltas", "", 3, 3, true );
															}
															else
															{
																$this->exame = "S";
															}
														}
														else
														{
															$this->campoLista( "nota_{$det_pessoa["idpes"]}", " Nota", $opcoes_valores, "", "", false );
														}
													}
													else if ( $this->falta_ch_globalizada == 1 && $disc_nota == ( $qtd_disc - 1 ) )
													{
	//													Verifica se o ano letivo ainda est� em andamento
														if ( !( $this->num_modulo > $this->qtd_modulos ) )
														{
															if ( $this->num_modulo == $this->qtd_modulos )
															{
																$this->campoNumero( "faltas_{$det_pessoa["idpes"]}", " Faltas", "", 3, 3, true, "", "", false, false, true );
																$this->campoLista( "aprovacao_{$det_pessoa["idpes"]}", "", array( "" => "Selecione", "S" => "Aprovado", "N" => "Reprovado" ), "" );
															}
															else
															{
																$this->campoNumero( "faltas_{$det_pessoa["idpes"]}", " Faltas", "", 3, 3, true );
															}
														}
													}
												}
												else
												{
													$this->campoTexto( "nm_aluno_{$det_pessoa["idpes"]}", "Aluno", $det_pessoa["nome"], 30, 255, false, false, false, "", "", "", "onKeyUp", true );
												}
											}
										}
									}
								}
							}
						}
					}
				}
			}
		}

		$this->lst_matriculas  = serialize( $this->lst_matriculas  );
		$this->cod_disciplinas = serialize( $this->cod_disciplinas );
		$this->lst_apura_falta = serialize( $this->lst_apura_falta );

		$this->campoOculto( "ref_cod_turma"     	, $this->ref_cod_turma 			);
		$this->campoOculto( "ref_ref_cod_escola"	, $this->ref_ref_cod_escola 	);
		$this->campoOculto( "ref_ref_cod_serie" 	, $this->ref_ref_cod_serie 		);
		$this->campoOculto( "ref_cod_curso"     	, $this->ref_cod_curso 			);
		$this->campoOculto( "ref_cod_tipo_avaliacao", $this->ref_cod_tipo_avaliacao );
		$this->campoOculto( "media"					, $this->media 					);
		$this->campoOculto( "media_exame"			, $this->media_exame 			);
		$this->campoOculto( "ano_letivo"			, $this->ano_letivo 			);
		$this->campoOculto( "conceitual"			, $this->conceitual 			);
		$this->campoOculto( "lst_matriculas"		, $this->lst_matriculas			);
		$this->campoOculto( "falta_ch_globalizada"	, $this->falta_ch_globalizada	);
		$this->campoOculto( "qtd_modulos"			, $this->qtd_modulos			);
		$this->campoOculto( "num_modulo"			, $this->num_modulo				);
		$this->campoOculto( "frequencia_minima"		, $this->frequencia_minima		);
		$this->campoOculto( "carga_horaria"			, $this->carga_horaria			);
		$this->campoOculto( "cod_disciplinas"		, $this->cod_disciplinas		);
		$this->campoOculto( "lst_apura_falta"		, $this->lst_apura_falta		);
		$this->campoOculto( "qtd_disciplinas"		, $this->qtd_disciplinas		);
		$this->campoOculto( "exame"					, $this->exame					);
		$this->campoOculto( "classifica"			, $this->classifica				);
	}

	function Novo()
	{
		@session_start();
		 $this->pessoa_logada = $_SESSION['id_pessoa'];
		@session_write_close();

		$obj_permissoes = new clsPermissoes();
		$obj_permissoes->permissao_cadastra( 650, $this->pessoa_logada, 7,  "educar_turma_mvto_det.php" );

		$this->lst_matriculas  = unserialize( urldecode( $_POST["lst_matriculas"]  ) );
		$this->cod_disciplinas = unserialize( urldecode( $_POST["cod_disciplinas"] ) );
		$this->lst_apura_falta = unserialize( urldecode( $_POST["lst_apura_falta"] ) );

		if ( is_array( $this->lst_matriculas ) )
		{
//			Verifica se o professor aprovou ou reprovou algum aluno
			if ( $this->classifica == "S" )
			{
				$obj_nota_aluno = new clsPmieducarNotaAluno();
				$lst_exame = $obj_nota_aluno->listaMedias( $this->ref_ref_cod_serie, $this->ref_ref_cod_escola, $this->ref_cod_turma, $this->ref_cod_turma, $this->qtd_modulos, $this->ref_cod_curso, true, true, true );

				if ( $lst_exame )
				{
					foreach ( $lst_exame as $exame )
					{
						$obj_matricula = new clsPmieducarMatricula( $exame["ref_ref_cod_matricula"] );
						$det_matricula = $obj_matricula->detalhe();

						if ( $det_matricula )
						{
							$obj_aluno = new clsPmieducarAluno( $det_matricula["ref_cod_aluno"] );
							$det_aluno = $obj_aluno->detalhe();

							if ( $det_aluno )
							{
								$campo_aprovacao = "aprovacao_{$det_aluno["ref_idpes"]}_";
								$campo_aluno	 = "nm_aluno_{$det_aluno["ref_idpes"]}";
							}
						}

						if ( isset( $this->$campo_aluno ) )
						{
//							Verifica se a m�dia � maior ou igual a m�dia m�nima e se foi aprovado pelo professor
							if ( $exame["media"] >= $this->media && $this->$campo_aprovacao == "on" )
							{
//								Verifica se o aluno n�o foi reprovado ou deixado em exame
								if ( $aprovado[$exame["ref_ref_cod_matricula"]] != "N" && $aprovado[$exame["ref_ref_cod_matricula"]] != "R" )
								{
									$aprovado[$exame["ref_ref_cod_matricula"]] = "S";
								}
							}
//							Verifica se a m�dia � menor que a m�dia m�nima e se foi colocado em recupera��o pelo professor
							else if ( $exame["media"] < $this->media && $this->$campo_aprovacao == "on" )
							{
//								Verifica se o aluno n�o foi reprovado
								if ( $aprovado[$exame["ref_ref_cod_matricula"]] != "N" )
								{
									$aprovado[$exame["ref_ref_cod_matricula"]] = "R";
								}
							}
							else
							{
								$aprovado[$exame["ref_ref_cod_matricula"]] = "N";
							}
						}
					}
					if ( is_array( $aprovado ) )
					{
						foreach ( $aprovado as $matricula => $verificador )
						{

//							Verifica se o aluno foi aprovado ou deixado em exame
							if ( $verificador == "S" || $verificador == "R" )
							{
								$obj_matricula = new clsPmieducarMatricula( $matricula );
								$det_matricula = $obj_matricula->detalhe();

//								Verifica se a matr�cula aida est� em andamento
								if ( $det_matricula["aprovado"] == 3 )
								{
									$obj_historico = new clsPmieducarHistoricoEscolar();
									$lst_historico = $obj_historico->lista( $matricula );

									$seq = ( count( $lst_historico ) + 1 );

									$obj_ano_letivo = new clsPmieducarEscolaAnoLetivo( $this->ref_ref_cod_escola, null, null, null, 1, null, null, 1 );
									$det_ano_letivo = $obj_ano_letivo->detalhe();

									$obj_escola = new clsPmieducarEscolaComplemento( $this->ref_ref_cod_escola );
									$det_escola = $obj_escola->detalhe();

//									Verifica se o aluno foi aprovado
									if ( $verificador == "S" )
									{
										$obj_historico = new clsPmieducarHistoricoEscolar( $det_matricula["ref_cod_aluno"], $seq, null, $this->pessoa_logada, $this->ref_ref_cod_serie, $det_ano_letivo["ano"], $this->carga_horaria, null, $det_escola["nm_escola"], $det_escola["municipio"], null, null, 1, null, null, 1 );

										if ( $obj_historico->cadastra() )
										{
											$obj_matricula = new clsPmieducarMatricula( $det_matricula["cod_matricula"], null, null, null, $this->pessoa_logada, null, null, 1 );
											$obj_matricula->edita();
										}
										else
										{
											$this->mensagem = "Falha ao cadastrar o historico!<br>";
										}
									}

//									Verifica se o aluno foi deixado em recupera��o
									else if ( $verificador == "R" )
									{
										$obj_matricula = new clsPmieducarMatricula( $det_matricula["cod_matricula"], null, null, null, $this->pessoa_logada, null, null, 7 );

										if ( !( $obj_matricula->edita() ) )
										{
											$this->mensagem = "Falha ao editar a matricula!<br>";
										}
									}
								}
							}

//							Executa o bloco de instru��es abaixo, caso o aluno tenha sido reprovado
							else
							{
								$obj_matricula = new clsPmieducarMatricula( $matricula );
								$det_matricula = $obj_matricula->detalhe();

//								Verifica se a matricula ainda est� em andamento
								if ( $det_matricula["aprovado"] == 3 )
								{
									$obj_historico = new clsPmieducarHistoricoEscolar();
									$lst_historico = $obj_historico->lista( $matricula );

									$seq = ( count( $lst_historico ) + 1 );

									$obj_ano_letivo = new clsPmieducarEscolaAnoLetivo( $this->ref_ref_cod_escola, null, null, null, 1, null, null, 1 );
									$det_ano_letivo = $obj_ano_letivo->detalhe();

									$obj_escola = new clsPmieducarEscolaComplemento( $this->ref_ref_cod_escola );
									$det_escola = $obj_escola->detalhe();

//									Verifica se o aluno foi reprovado
									if ( $verificador == "N" )
									{
										$obj_historico = new clsPmieducarHistoricoEscolar( $det_matricula["ref_cod_aluno"], $seq, null, $this->pessoa_logada, $this->ref_ref_cod_serie, $det_ano_letivo["ano"], $this->carga_horaria, null, $det_escola["nm_escola"], $det_escola["municipio"], null, null, 2, null, null, 1 );

										if ( $obj_historico->cadastra() )
										{
											$obj_matricula = new clsPmieducarMatricula( $det_matricula["cod_matricula"], null, null, null, $this->pessoa_logada, null, null, 2 );
											if ( !( $obj_matricula->edita() ) )
											{
												$this->mensagem = "Falha ao alterar a matr�cula!<br>";
											}
										}
										else
										{
											$this->mensagem = "Falha ao cadastrar o hist�rico!<br>";
										}
									}
								}
							}
						}
					}
				}
				header( "location: educar_turma_mvto_det.php?cod_turma={$this->ref_cod_turma}" );
				die();
			}
//			Executa o bloco de instru��o abaixo, caso o servidor n�o tenha classificado nenhum aluno
			else
			{
				foreach ( $this->lst_matriculas as $matriculas )
				{

//					Verifica se o ano letivo ainda est� em andamento
					if ( $this->num_modulo <= $this->qtd_modulos )
					{
//						Verifica se a disciplina apura falta
						if ( $this->lst_apura_falta[$this->ref_cod_disciplina] == 1 )
						{
							$campo_falta 	 = "faltas_{$matriculas[1]}";
						}
						else if ( $this->falta_ch_globalizada == 1 )
						{
							$campo_falta 	 = "faltas_{$matriculas[1]}";
						}

						$campo_nota		= "nota_{$matriculas[1]}";
						$obj_mat_tur = new clsPmieducarMatriculaTurma();
						$lst_mat_tur = $obj_mat_tur->lista( $matriculas[2], $this->ref_cod_turma, null, null, null, null, null, null, 1 );
						$sequencial = 0;
//echo "<pre>";
//print_r( $lst_mat_tur );
						if ( is_array( $lst_mat_tur ) )
						{
							foreach( $lst_mat_tur as $registro )
							{
								if ( $sequencial == 0 )
								{
									$sequencial = $registro["sequencial"];
								}
								else if ( $sequencial < $registro["sequencial"] )
								{
									$sequencial = $registro["sequencial"];
								}
							}
							//$sequencial = ( count( $lst_mat_tur ) );
							//$sequencial = $lst_mat_tur["sequencial"];
						}
						else
						{
							$this->mensagem = "Erro no cadastro de nota!<br>";
						}

						$obj_nota_aluno = new clsPmieducarNotaAluno( null, $this->$campo_nota, $this->ref_cod_tipo_avaliacao, $this->ref_ref_cod_serie, $this->ref_ref_cod_escola, $this->ref_cod_disciplina, $this->ref_cod_turma, $matriculas[2], $this->ref_cod_turma, null, $this->pessoa_logada, null, null, 1, $sequencial );

						if ( !$obj_nota_aluno->cadastra() )
						{
							$this->mensagem = "Erro no cadastro de nota!<br>";
						}

//						Verifica se a falta n�o � globalizada
						if ( $this->falta_ch_globalizada == 0 )
						{
//							Verifica se a disciplina apura falta
							if ( $this->lst_apura_falta[$this->ref_cod_disciplina] == 1 )
							{
//								Verifica se o aluno possui alguma falta
								if ( $this->$campo_falta > 0 )
								{
									$obj_falta_aluno = new clsPmieducarFaltaAluno( null, null, $this->pessoa_logada, $this->ref_ref_cod_serie, $this->ref_ref_cod_escola, $this->ref_cod_disciplina, $this->ref_cod_turma, $this->ref_cod_turma, $matriculas[2], $this->$campo_falta, null, null, 1, $sequencial );

									if ( !$obj_falta_aluno->cadastra() )
									{
										$this->mensagem = "Erro no cadastro da falta!<br>";
									}
								}
							}
						}

//						Verifica se a falta � globalizada
						else if ( $this->falta_ch_globalizada == 1 )
						{
//							Verifica se o aluno possui alguma falta
							if ( $this->$campo_falta > 0 )
							{
								$obj_faltas = new clsPmieducarFaltas();
								$lst_faltas = $obj_faltas->lista( $matriculas[2] );
								$sequencial = count( $lst_faltas ) + 1;
								$obj_faltas = new clsPmieducarFaltas( $matriculas[2], $sequencial, $this->pessoa_logada, $this->$campo_falta );
								if ( !$obj_faltas->cadastra() )
								{
									$this->mensagem = "Erro no cadastro de falta!<br>";
								}
							}
						}
					}
				}

//				Verifica se a turma est� no �ltimo m�dulo
				if ( $this->qtd_modulos == $this->num_modulo )
				{
					$obj_matricula_turma = new clsPmieducarMatriculaTurma();
					$lst_matricula_turma = $obj_matricula_turma->lista( null, $this->ref_cod_turma, null, null, null, null, null, null, 1 );

					if ( $lst_matricula_turma )
					{
						foreach ( $lst_matricula_turma as $matricula )
						{
							$obj_nota_aluno = new clsPmieducarNotaAluno();

//							Busca se todas as notas j� foram dadas para o aluno
							$todas_notas	= $obj_nota_aluno->todasNotas( $this->ref_ref_cod_serie, $this->ref_ref_cod_escola, $this->ref_cod_turma, $this->ref_cod_turma, $this->num_modulo, $matricula["ref_cod_matricula"] );

//							Verifica se todas as notas j� foram dadas para o aluno
							if ( $todas_notas == 'S' )
							{
								if ( $this->conceitual == 0 )
								{
//									Carrega as m�dias de cada disciplina do aluno
									$lst_exame = $obj_nota_aluno->listaMedias( $this->ref_ref_cod_serie, $this->ref_ref_cod_escola, $this->ref_cod_turma, $this->ref_cod_turma, $this->qtd_modulos, $this->ref_cod_curso, true, true );

									if ( $lst_exame )
									{
										foreach ( $lst_exame as $exame )
										{
											if ( $exame["ref_ref_cod_matricula"] == $matricula["ref_cod_matricula"] )
											{
//												Verifica se a m�dia do aluno � igual ou superior a m�dia m�nima e se a freq��ncia do aluno � igual ou maior que a freq��ncia m�nima
												if ( $exame["media"] >= $this->media && ( 100 - $exame["faltas"] ) >= $this->frequencia_minima )
												{
//													Verifica se o aluno n�o foi reprovado ou deixado de recupera��o
													if ( $aprovado[$exame["ref_ref_cod_matricula"]] != "N" && $aprovado[$exame["ref_ref_cod_matricula"]] != "R" )
													{
														$aprovado[$exame["ref_ref_cod_matricula"]] = "S";
													}
												}

//												Verifica se a m�dia do aluno � inferior a m�dia m�nima e se a freq��ncia do aluno � maior ou igual a freq��ncia m�nima
												else if ( $exame["media"] < $this->media && ( 100 - $exame["faltas"] ) >= $this->frequencia_minima )
												{
//													Verifica se o aluno n�o foi reprovado
													if ( $aprovado[$exame["ref_ref_cod_matricula"]] != "N" )
													{
														$aprovado[$exame["ref_ref_cod_matricula"]] = "R";
													}
												}
												else
												{
													$aprovado[$exame["ref_ref_cod_matricula"]] = "N";
												}
											}
										}
									}
								}
								if ( $this->conceitual == 1 )
								{
									$obj_aluno = new clsPmieducarAluno( $matricula["ref_cod_aluno"] );
									$det_aluno = $obj_aluno->detalhe();
									$campo_aprovacao = "aprovacao_{$det_aluno["ref_idpes"]}";

									if ( $this->$campo_aprovacao == "S" )
									{
										$aprovado[$matricula["ref_cod_matricula"]] = "S";
									}
									elseif ( $this->$campo_aprovacao == "N" )
									{
										$aprovado[$matricula["ref_cod_matricula"]] = "N";
									}
								}
							}
						}
					}

					if ( is_array( $aprovado ) )
					{
						foreach ( $aprovado as $matricula => $verificador )
						{
//							Verifica se o aluno foi aprovado ou deixado em exame
							if ( $verificador == "S" || $verificador == "R" )
							{
								$obj_matricula = new clsPmieducarMatricula( $matricula );
								$det_matricula = $obj_matricula->detalhe();

//								Verifica se a matr�cula est� em andamento
								if ( $det_matricula["aprovado"] == 3 )
								{
									$obj_historico = new clsPmieducarHistoricoEscolar();
									$lst_historico = $obj_historico->lista( $matricula );

									$seq = ( count( $lst_historico ) + 1 );

									$obj_ano_letivo = new clsPmieducarEscolaAnoLetivo( $this->ref_ref_cod_escola, null, null, null, 1, null, null, 1 );
									$det_ano_letivo = $obj_ano_letivo->detalhe();

									$obj_escola = new clsPmieducarEscolaComplemento( $this->ref_ref_cod_escola );
									$det_escola = $obj_escola->detalhe();

//									Verifica se o aluno foi aprovado
									if ( $verificador == "S" )
									{
										if ( $this->falta_ch_globalizada == 1 )
										{
											$obj_historico = new clsPmieducarHistoricoEscolar( $det_matricula["ref_cod_aluno"], $seq, null, $this->pessoa_logada, $this->ref_ref_cod_serie, $det_ano_letivo["ano"], $this->carga_horaria, null, $det_escola["nm_escola"], $det_escola["municipio"], null, null, 1, null, null, 1, 1 );
										}
										else
										{
											$obj_historico = new clsPmieducarHistoricoEscolar( $det_matricula["ref_cod_aluno"], $seq, null, $this->pessoa_logada, $this->ref_ref_cod_serie, $det_ano_letivo["ano"], $this->carga_horaria, null, $det_escola["nm_escola"], $det_escola["municipio"], null, null, 1, null, null, 1, 0 );
										}

										if ( $obj_historico->cadastra() )
										{
											$obj_matricula = new clsPmieducarMatricula( $det_matricula["cod_matricula"], null, null, null, $this->pessoa_logada, null, null, 1 );
											if ( $obj_matricula->edita() )
											{
												$this->mensagem = "Falha ao editar a matr�cula!<br>";
											}
										}
										else
										{
											$this->mensagem = "Falha ao cadastrar o hist�rico!<br>";
										}
									}

//									Verifica se o aluno foi deixado em exame
									else if ( $verificador == "R" )
									{
										$obj_matricula = new clsPmieducarMatricula( $det_matricula["cod_matricula"], null, null, null, $this->pessoa_logada, null, null, 7 );

										if ( !$obj_matricula->edita() )
										{
											$this->mensagem = "Falha ao editar a matr�cula!<br>";
										}
									}
								}
							}
							else if ( $verificador == "N" && $this->conceitual == 1 )
							{
								$obj_matricula = new clsPmieducarMatricula( $matricula );
								$det_matricula = $obj_matricula->detalhe();

								if ( $det_matricula["aprovado"] == 3 )
								{
									$obj_historico = new clsPmieducarHistoricoEscolar();
									$lst_historico = $obj_historico->lista( $matricula );

									$seq = ( count( $lst_historico ) + 1 );

									$obj_ano_letivo = new clsPmieducarEscolaAnoLetivo( $this->ref_ref_cod_escola, null, null, null, 1, null, null, 1 );
									$det_ano_letivo = $obj_ano_letivo->detalhe();

									$obj_escola = new clsPmieducarEscolaComplemento( $this->ref_ref_cod_escola );
									$det_escola = $obj_escola->detalhe();

									if ( $this->falta_ch_globalizada == 1 )
									{
										$obj_historico = new clsPmieducarHistoricoEscolar( $det_matricula["ref_cod_aluno"], $seq, null, $this->pessoa_logada, $this->ref_ref_cod_serie, $det_ano_letivo["ano"], $this->carga_horaria, null, $det_escola["nm_escola"], $det_escola["municipio"], null, null, 2, null, null, 1, 1 );
									}
									else
									{
										$obj_historico = new clsPmieducarHistoricoEscolar( $det_matricula["ref_cod_aluno"], $seq, null, $this->pessoa_logada, $this->ref_ref_cod_serie, $det_ano_letivo["ano"], $this->carga_horaria, null, $det_escola["nm_escola"], $det_escola["municipio"], null, null, 2, null, null, 1, 0 );
									}

									if ( $obj_historico->cadastra() )
									{
										$obj_matricula = new clsPmieducarMatricula( $det_matricula["cod_matricula"], null, null, null, $this->pessoa_logada, null, null, 2 );
										if ( $obj_matricula->edita() )
										{
											$this->mensagem = "Falha ao editar a matr�cula!<br>";
										}
									}
									else
									{
										$this->mensagem = "Falha ao cadastrar o hist�rico!<br>";
									}
								}
							}
						}
					}
					$obj_turma_modulo = new clsPmieducarTurmaModulo();
					$fimAnoLetivo = $obj_turma_modulo->fimAno( $this->ref_cod_turma, $this->qtd_modulos );
//					$total_notas = $obj_nota_aluno->retornaTotalNotas( $this->ref_ref_cod_serie, $this->ref_ref_cod_escola, $this->ref_cod_turma, $this->ref_cod_turma );

//					if ( $total_notas == ( $this->qtd_modulos * $this->qtd_disciplinas ) )
					if ( $fimAnoLetivo == "S" )
					{
						header( "location: educar_turma_mvto_det.php?cod_turma={$this->ref_cod_turma}" );
						die();
					}
					else
					{
						header( "location: educar_turma_nota_cad.php?ref_cod_turma={$this->ref_cod_turma}&ref_ref_cod_escola={$this->ref_ref_cod_escola}&ref_ref_cod_serie={$this->ref_ref_cod_serie}&ref_cod_curso={$this->ref_cod_curso}" );
						die();
					}
				}

//				Verifica se o servidor adicionou as notas do exame
				else if ( $this->exame == "S" )
				{
					foreach ( $this->lst_matriculas as $matriculas )
					{
//						Verifica se todos os m�dulos j� terminaram
						if ( $this->num_modulo > $this->qtd_modulos )
						{
							$campo_nota		= "nota_{$matriculas[1]}";
							$obj_mat_tur = new clsPmieducarMatriculaTurma();
							$lst_mat_tur = $obj_mat_tur->lista( $matriculas[2], $this->ref_cod_turma, null, null, null, null, null, null, 1 );

							if ( is_array( $lst_mat_tur ) )
							{
								//$sequencial = ( count( $lst_mat_tur ) );
								$sequencial = $lst_mat_tur["sequencial"];
							}
							else
							{
								$this->mensagem = "Erro no cadastro de nota!<br>";
							}

							$obj_nota_aluno = new clsPmieducarNotaAluno( null, $this->$campo_nota, $this->ref_cod_tipo_avaliacao, $this->ref_ref_cod_serie, $this->ref_ref_cod_escola, $this->ref_cod_disciplina, $this->ref_cod_turma, $matriculas[2], $this->ref_cod_turma, null, $this->pessoa_logada, null, null, 1, $sequencial );

							if ( !$obj_nota_aluno->cadastra() )
							{
								$this->mensagem = "Erro no cadastro da nota!<br>";
							}

							$obj_nota_aluno = new clsPmieducarNotaAluno();
							$lst_exame = $obj_nota_aluno->listaMedias( $this->ref_ref_cod_serie, $this->ref_ref_cod_escola, $this->ref_cod_turma, $this->ref_cod_turma, $this->qtd_modulos, $this->ref_cod_curso, true, true, false, true );

							$obj_turma_disciplina = new clsPmieducarTurmaDisciplina();

//							Carrega o c�digo das disciplinas da turma
							$lst_turma_disciplina = $obj_turma_disciplina->lista( $this->ref_cod_turma );

							if ( is_array( $lst_turma_disciplina ) )
							{
//								Carrega a quantidade de disciplinas da turma
								$this->qtd_disciplinas = count( $lst_turma_disciplina );

								foreach ( $lst_turma_disciplina as $valores )
								{
									$obj_disciplina = new clsPmieducarDisciplina( $valores["ref_cod_disciplina"] );
									$det_disciplina = $obj_disciplina->detalhe();

									if ( $det_disciplina )
									{
										$obj_dispensa = new clsPmieducarDispensaDisciplina();
										$det_dispensa = $obj_dispensa->lista( $this->ref_cod_turma, $matriculas[2], $this->ref_cod_turma, $this->ref_ref_cod_serie, $this->ref_ref_cod_escola, $det_disciplina["cod_disciplina"], null, null, null, null, null, null, null, 1 );

//										Verifica se o aluno n�o foi dispensado da disciplina
										if ( !is_array( $det_dispensa ) )
										{
											$obj_notas = new clsPmieducarNotaAluno();

//											Carrega a quantidade de notas por aluno de uma turma numa determinada disciplina
											$lst_notas = $obj_notas->retornaDiscMod( $this->ref_ref_cod_serie, $this->ref_ref_cod_escola, $det_disciplina["cod_disciplina"], $this->ref_cod_turma, $this->ref_cod_turma, $matriculas[2] );

//											Carrega a m�dia da disciplina
											if ( $lst_exame )
											{
												foreach ( $lst_exame as $exame )
												{
													if ( $exame["disc_ref_ref_cod_disciplina"] == $det_disciplina["cod_disciplina"] && $exame["ref_ref_cod_matricula"] == $matriculas[2] )
													{
														$media_disciplina = $exame["media"];
													}
												}
											}

											if ( $lst_notas == $this->qtd_modulos )
											{
												if ( $media_disciplina >= $this->media )
												{
													$pula_disciplina = false;
												}
												else
												{
													$pula_disciplina = true;
												}
											}
											else
											{
												$pula_disciplina = true;
											}

											if ( $pula_disciplina )
											{
												if ( !is_array( $det_dispensa ) )
												{
//													Verifica se a quantidade de notas por aluno � diferente do n�mero do m�dulo em que a turma se encontra
													if ( $lst_notas == ( $this->qtd_modulos + 1 ) )
													{
														if ( $salva_historico != "N" )
														{
															$lst_disc_exame[$det_disciplina["cod_disciplina"]] = "S";
															$salva_historico = "S";
														}
													}
													else
													{
														$salva_historico = "N";
													}
												}
											}
										}
									}
								}
							}

							if ( $salva_historico == "S" )
							{
								$lst_exame = $obj_nota_aluno->listaMedias( $this->ref_ref_cod_serie, $this->ref_ref_cod_escola, $this->ref_cod_turma, $this->ref_cod_turma, ( $this->qtd_modulos + 1 ), $this->ref_cod_curso, true, true, false, true );

								if ( $lst_exame )
								{
									foreach ( $lst_exame as $exame )
									{
										if ( $exame["ref_ref_cod_matricula"] == $matriculas[2] && $lst_disc_exame[$exame["disc_ref_ref_cod_disciplina"]] == "S" )
										{
//											Verifica se a m�dia do aluno � igual ou superior a m�dia m�nima
											if ( $exame["media"] >= $this->media_exame )
											{
//												Verifica se o aluno n�o foi reprovado
												if ( $aprovado[$exame["ref_ref_cod_matricula"]] != "N" )
												{
													$aprovado[$exame["ref_ref_cod_matricula"]] = "S";
												}
											}
											else
											{
												$aprovado[$exame["ref_ref_cod_matricula"]] = "N";
											}
										}
									}
								}
							}
							if ( is_array( $aprovado ) )
							{
								foreach ( $aprovado as $matricula => $verificador )
								{
//									Verifica se o aluno foi aprovado
									if ( $verificador == "S" )
									{
										$obj_matricula = new clsPmieducarMatricula( $matricula );
										$det_matricula = $obj_matricula->detalhe();

//										Verifica se a matr�cula est� em exame
										if ( $det_matricula["aprovado"] == 7 )
										{
											$obj_historico = new clsPmieducarHistoricoEscolar();
											$lst_historico = $obj_historico->lista( $matricula );

											$seq = ( count( $lst_historico ) + 1 );

											$obj_ano_letivo = new clsPmieducarEscolaAnoLetivo( $this->ref_ref_cod_escola, null, null, null, 1, null, null, 1 );
											$det_ano_letivo = $obj_ano_letivo->detalhe();

											$obj_escola = new clsPmieducarEscolaComplemento( $this->ref_ref_cod_escola );
											$det_escola = $obj_escola->detalhe();

//											Verifica se o aluno foi aprovado
											if ( $verificador == "S" )
											{
												if ( $this->falta_ch_globalizada == 1 )
												{
													$obj_historico = new clsPmieducarHistoricoEscolar( $det_matricula["ref_cod_aluno"], $seq, null, $this->pessoa_logada, $this->ref_ref_cod_serie, $det_ano_letivo["ano"], $this->carga_horaria, null, $det_escola["nm_escola"], $det_escola["municipio"], null, null, 1, null, null, 1, 1 );
												}
												else
												{
													$obj_historico = new clsPmieducarHistoricoEscolar( $det_matricula["ref_cod_aluno"], $seq, null, $this->pessoa_logada, $this->ref_ref_cod_serie, $det_ano_letivo["ano"], $this->carga_horaria, null, $det_escola["nm_escola"], $det_escola["municipio"], null, null, 1, null, null, 1, 0 );
												}

												if ( $obj_historico->cadastra() )
												{
													$obj_matricula = new clsPmieducarMatricula( $det_matricula["cod_matricula"], null, null, null, $this->pessoa_logada, null, null, 1 );
													if ( $obj_matricula->edita() )
													{
														$this->mensagem = "Falha ao editar a matr�cula!<br>";
													}
												}
												else
												{
													$this->mensagem = "Falha ao cadastrar o hist�rico!<br>";
												}
											}
										}
									}
//									Verifica se o aluno foi reprovado
									if ( $verificador == "N" )
									{
										$obj_historico = new clsPmieducarHistoricoEscolar();
										$lst_historico = $obj_historico->lista( $matriculas[0] );

										$seq = ( count( $lst_historico ) + 1 );

										$obj_ano_letivo = new clsPmieducarEscolaAnoLetivo( $this->ref_ref_cod_escola, null, null, null, 1, null, null, 1 );
										$det_ano_letivo = $obj_ano_letivo->detalhe();

										$obj_escola = new clsPmieducarEscolaComplemento( $this->ref_ref_cod_escola );
										$det_escola = $obj_escola->detalhe();

										if ( $this->falta_ch_globalizada == 1 )
										{
											$obj_historico = new clsPmieducarHistoricoEscolar( $matriculas[0], $seq, null, $this->pessoa_logada, $this->ref_ref_cod_serie, $det_ano_letivo["ano"], $this->carga_horaria, null, $det_escola["nm_escola"], $det_escola["municipio"], null, null, 2, null, null, 1, 1 );
										}
										else
										{
											$obj_historico = new clsPmieducarHistoricoEscolar( $matriculas[0], $seq, null, $this->pessoa_logada, $this->ref_ref_cod_serie, $det_ano_letivo["ano"], $this->carga_horaria, null, $det_escola["nm_escola"], $det_escola["municipio"], null, null, 2, null, null, 1, 0 );
										}

										if ( $obj_historico->cadastra() )
										{
											$obj_matricula = new clsPmieducarMatricula( $matricula, null, null, null, $this->pessoa_logada, null, null, 2 );
											if ( !( $obj_matricula->edita() ) )
											{
												$this->mensagem = "Falha ao alterar a matr�cula!<br>";
											}
										}
										else
										{
											$this->mensagem = "Falha ao cadastrar o hist�rico!<br>";
										}
									}
								}
							}
							header( "location: educar_turma_nota_cad.php?ref_cod_turma={$this->ref_cod_turma}&ref_ref_cod_escola={$this->ref_ref_cod_escola}&ref_ref_cod_serie={$this->ref_ref_cod_serie}&ref_cod_curso={$this->ref_cod_curso}" );
							die();
						}
					}
				}
				header( "location: educar_turma_nota_cad.php?ref_cod_turma={$this->ref_cod_turma}&ref_ref_cod_escola={$this->ref_ref_cod_escola}&ref_ref_cod_serie={$this->ref_ref_cod_serie}&ref_cod_curso={$this->ref_cod_curso}" );
				die();
			}
		}
	}
}

// cria uma extensao da classe base
$pagina = new clsIndexBase();
// cria o conteudo
$miolo = new indice();
// adiciona o conteudo na clsBase
$pagina->addForm( $miolo );
// gera o html
$pagina->MakeAll();
?>
<script>
if ( document.getElementById( 'ref_cod_disciplina' ) )
{
	document.getElementById( 'ref_cod_disciplina' ).onchange = function()
	{
		if ( document.getElementById( 'tipoacao' ) )
		{
			document.getElementById( 'tipoacao' ).value = '';
		}
		document.formcadastro.action = 'educar_turma_nota_cad.php?ref_cod_turma=' + document.getElementById( 'ref_cod_turma' ).value + '&ref_ref_cod_escola=' + document.getElementById( 'ref_ref_cod_escola' ).value + '&ref_ref_cod_serie=' + document.getElementById( 'ref_ref_cod_serie' ).value + '&ref_cod_curso=' + document.getElementById( 'ref_cod_curso' ).value + '&ref_cod_disciplina=' + document.getElementById( 'ref_cod_disciplina' ).value;
		document.formcadastro.submit();
	}
}
if ( document.getElementById( 'btn_enviar' ) )
{
	document.getElementById( 'btn_enviar' ).onclick = function()
	{
		if ( document.getElementById( 'tipoacao' ) )
		{
			document.getElementById( 'tipoacao' ).value = 'Novo';
		}
		acao();
	}
}
</script>
