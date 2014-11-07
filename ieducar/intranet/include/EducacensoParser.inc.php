<?php

require_once('EducacensoFieldHelper.inc.php');
require_once('include/funcoes.inc.php');

class EducacensoParser {
    private $instituicao_id;
    private $filename;
    private $usuario_cad;
    private $year;

    public function __construct($instituicao_id, $filename, $usuario_cad, $year = 2014) {
        $this->instituicao_id = $instituicao_id;
        $this->filename = $filename;
        $this->usuario_cad = $usuario_cad;
        $this->aluno_data = array();
        $this->docente_data = array();
        $this->escola_data = array();
        $this->year = $year;
    }


    public function run() {
    	$logs = array ();
    	$contents = file_get_contents ( $this->filename );
    	$contents = explode ( "\n", $contents );
    
    	foreach ( $contents as $text_row ) {
    		if ($text_row) { // A última linha do arquivo é vazia.
    			$data = EducacensoFieldHelper::parse_row( explode ( "|", $text_row ) );
    			try {
        			$log = $this->parse_row($data);
        			if ($log) {
        				$logs[] = $log;
        			}
    			} catch (Exception $e) {
    			    $logs[] = "Erro ao processar linha:";
    			    $logs[] = print_r($e, true);
    			} 
    		}
    	}
    	return $logs;
    }
    
    protected function parse_row($data) {    	
        switch (EducacensoFieldHelper::row_type($data)) {
        	case "Escola":
        		$this->escola_data = array();
        		$this->escola_data = array_merge($data, $this->escola_data);
        		break;
        	case "Escola/Estrutura":
        		$this->escola_data = array_merge($data, $this->escola_data);
        		return $this->check_escola($this->escola_data);
        	case "Turma":
        		return $this->check_turma($data);
        	case "Profissional":
        		$this->docente_data = array();
        		$this->docente_data = array_merge($data, $this->docente_data);
        		break;
        	case "Profissional/Documentos":
        		$this->docente_data = array_merge($data, $this->docente_data);
        		break;
        	case "Profissional/Variaveis":
        		$this->docente_data = array_merge($data, $this->docente_data);
        		return $this->check_professor($this->docente_data);
        	case "Profissional/Docentes":
        		$d = array_merge($data, $this->docente_data);
        		return $this->check_turma_professor($d);
        	case "Alunos":
        		$this->aluno_data = array();
        		$this->aluno_data = array_merge($data, $this->aluno_data);
        		break;
        	case  "Alunos/Documentos":
        		$this->aluno_data = array_merge($data, $this->aluno_data);
        		return $this->check_aluno($this->aluno_data);
        	case "Alunos/Matricula":
        		return $this->check_matricula($data);
        }
    }
    
    protected function check_turma($d) {
        $logs = "";
        $id_turma_inep = intval($d['codigo_inep_turma']);
        $tipo_atendimento = intval($d['tipo_atendimento']);
        
        // Por enquanto, não tratamos turmas que não sejam padrão.
        if ($tipo_atendimento != 0) {
            $logs = "Turma $id_turma_inep não será importada (tipo: $tipo_atendimento)";
            return $logs;
        } 
        
        $id_turma = clsPmIeducarTurma::id_turma_inep($id_turma_inep);
        
        if ($id_turma) {
            $logs .= "Turma $id_turma_inep encontrada. Não será atualizada.\n";
        } else {
            $logs .= "Turma $id_turma_inep não encontrada. Criando o registro.\n";
            $this->add_turma($d);
        }
        return $logs;
    }
    
    protected function check_professor($d) {
        $logs = "";
        $id_professor_inep = $d['codigo_inep_profissional'];
        $id_servidor = clsPmIeducarServidor::id_servidor_inep($id_professor_inep);
        
        if ($id_servidor) {
            $logs .= "Servidor $id_professor_inep encontrado. Não será atualizado.\n";
        } else {
            $logs .= "Servidor $id_professor_inep não encontrado. Criando o registro.\n";
            $this->add_professor($d);
        }
        return $logs;                
    }
    
    protected function check_turma_professor($d) {
    	$id_turma_inep = $d['codigo_inep_turma'];
    	$id_professor_inep = $d['codigo_inep_profissional'];
    	// TODO: Turmas com mais de um professor regente não são aceitas.
    	// return $this->bind_turma_professor($id_turma_inep, $id_professor_inep);
    	return null;
    }
    
    protected function bind_turma_professor($id_turma_inep, $id_professor_inep) {
        $logs = "";
        $id_turma = clsPmieducarTurma::id_turma_inep($id_turma_inep);
        $id_servidor = clsPmieducarServidor::id_servidor_inep($id_professor_inep);
        
        if ((bool)$id_turma && (bool)$id_servidor) {
            $turma = new clsPmieducarTurma($id_turma);
            $turma->detalhe();
            $turma->ref_cod_regente = $id_servidor;
            if ($turma->edita()) {
                $logs .= "Adicionado servidor $id_professor_inep como regente da turma $id_turma_inep.\n";
            } else {
                $logs .= "Erro ao adicionar servidor $id_professor_inep como regente da turma $id_turma_inep.\n";
            }
        }
        return $logs;
    }
    
    protected function check_matricula($d) {
    	$id_turma_inep = $d['codigo_inep_turma'];
    	$id_aluno_inep = $d['codigo_inep_aluno'];
    	$id_escola_inep = $d['codigo_inep_escola'];
    	return $this->bind_turma_aluno($id_turma_inep, $id_escola_inep, $id_aluno_inep);
    }
    
    protected function bind_turma_aluno($id_turma_inep, $id_escola_inep, $id_aluno_inep) {
        $logs = "";
        $id_turma = clsPmIeducarTurma::id_turma_inep($id_turma_inep);
        $id_aluno = clsPmieducarAluno::id_aluno_inep($id_aluno_inep);
        $id_escola = clsPmieducarEscola::id_escola_inep($id_escola_inep);
        
        if ((bool)$id_turma && (bool)$id_aluno && (bool)$id_escola) {
            // Verifica se o aluno está matriculado na escola para este ano
            // Se sim, adiciona uma matrícula para esta turma
            // Se não, cria a matrícula antes de fazê-lo
            $matriculas = new clsPmieducarMatricula();
            $lista_matriculas = $matriculas->lista(NULL, NULL, NULL, NULL, NULL, NULL, $id_aluno, NULL, NULL, NULL, NULL, NULL, 1, $this->year);
            if ($lista_matriculas) {
                $logs .= "Aluno $id_aluno_inep já matriculado na escola $id_escola_inep. \n";
                $id_matricula = $lista_matriculas[0]['cod_matricula'];
            } else {
                $logs .= "Aluno $id_aluno_inep ainda não matriculado na escola $id_escola_inep. Iniciando matrícula. \n";
                $matricula = new clsPmieducarMatricula(
                        null, # $cod_matricula = NULL, 
                        null, # $ref_cod_reserva_vaga = NULL,
                        $id_escola, # $ref_ref_cod_escola = NULL, 
                        null, # $ref_ref_cod_serie = NULL, 
                        null, # $ref_usuario_exc = NULL,
                        $this->usuario_cad, # $ref_usuario_cad = NULL, 
                        $id_aluno, # $ref_cod_aluno = NULL, 
                        null, # $aprovado = NULL,
                        null, # $data_cadastro = NULL, 
                        null, # $data_exclusao = NULL, 
                        null, # $ativo = NULL, 
                        $this->year, # $ano = NULL,
                        null, # $ultima_matricula = NULL, 
                        null, # $modulo = NULL, 
                        null, # $formando = NULL,
                        null, # $descricao_reclassificacao = NULL, 
                        null, # $matricula_reclassificacao = NULL,
                        null, # $ref_cod_curso = NULL, 
                        null, # $matricula_transferencia = NULL, 
                        null # $semestre = NULL
                );
                $id_matricula = $matricula->cadastra();
            }
            // Com a matrícula do aluno na escola, matricula-se ele na turma.
            $matricula_turmas = new clsPmieducarMatriculaTurma();
            $matricula_turmas->lista($id_matricula, $id_turma, NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, NULL, 
                    NULL, NULL, null, NULL, NULL, NULL, NULL, $this->year);
            if ($matricula_turmas) {
                $logs .= "Aluno $id_aluno_inep já matriculado na turma $id_turma_inep da escola $id_escola_inep. \n";
            } else {
                $matricula_turma = new clsPmieducarMatriculaTurma(
                        $id_matricula, // $ref_cod_matricula = NULL,
                        $id_turma, # $ref_cod_turma = NULL, 
                        null, # $ref_usuario_exc = NULL, 
                        $this->usuario_cad, # $ref_usuario_cad = NULL,
                        null, # $data_cadastro = NULL, 
                        null, # $data_exclusao = NULL, 
                        null, # $ativo = NULL,
                        null, # $ref_cod_turma_transf = NULL,
                        null  # $sequencial = NULL
                );
                if ($matricula_turma->cadastra()) {
                    $logs .= "Aluno $id_aluno_inep matriculado na turma $id_turma_inep da escola $id_escola_inep.\n";
                } else {
                    $logs .= "Erro ao matricular aluno $id_aluno_inep na turma $id_turma_inep da escola $id_escola_inep. \n";
                }
            }
        }
        return $logs;
    }
    
    protected function check_aluno($d) {
        $logs = "";
        $id_inep = $d['codigo_inep_aluno'];
        $id_aluno = clsPmieducarAluno::id_aluno_inep($id_inep);        
        if ($id_aluno) {
            $logs .= "Aluno $id_inep encontrado. Não será atualizado.\n";
        } else {
            $logs .= "Aluno $id_inep não encontrado. Criando o registro.\n";
            $this->add_aluno($d);
        }
        return $logs;        
    }

    protected function check_escola($d) {
        $logs = "";
        $id_inep = $d['codigo_inep'];
        $id_escola = clsPmieducarEscola::id_escola_inep($id_inep);
        // Verificamos se a escola existe ...
        if ($id_escola) {
            // Se sim, atualizamos as informações.
            // Mas por enquanto não.
            $logs .= "Escola $id_inep encontrada. Não será atualizada.\n";
        } else {
            $logs .= "Escola $id_inep não encontrada. Criando o registro.\n";
            $this->add_escola($d);
        }
        return $logs;
    }
    
    protected function add_escola($d) {
        // Pessoa
        $pessoa = new clsPessoa_(
                null, # $int_idpes
                $d['nome'], # $str_nome
                null, # $int_idpes_cad
                null, # $str_url
                'J', # $int_tipo . How bad is this?
                null, # $int_idpes_rev
                null, # $str_data_rev
                $d['email'] ? $d['email'] : null # $str_email
        );
        $id_pessoa = $pessoa->cadastra();
        
        // O relacionamento do DB me obriga informar um CNPJ.
        // Eu não tenho o CNPJ.
        // Não me julgue.
        $cnpj = sprintf("%02d.%03d.%03d/%04d-%02d", rand(1, 99), rand(1, 999), rand(1, 999), rand(1, 9999), rand(1, 99));
        $juridica = new clsJuridica( 
                $id_pessoa, # $idpes = false, 
                idFederal2int($cnpj), # $cnpj = false, 
                $d['nome'], # $fantasia = false, 
                null, # $insc_estadual = false, 
                null, # $capital_social = false, 
                $this->usuario_cad, 
                null # $idpes_rev =false 
        );
        $juridica->cadastra();
        
        // A escola vai precisar de uma rede de ensino, que é específica
        // da instituição. Se tiver alguma, usa a primeira delas,
        // senão, cria uma e fica por isso.
        $rede_ensino_id = -1;
        $redes_ensino = new clsPmieducarEscolaRedeEnsino();
        $redes_ensino = $redes_ensino->lista( null,null,null,null,null,null,null,null,1,$this->instituicao_id );
        if ($redes_ensino) {
            $rede_ensino_id = $redes_ensino[0]['cod_escola_rede_ensino'];
        } else {
            // ,  , , , ,
            $rede_ensino = new clsPmieducarEscolaRedeEnsino(
                    null, # $cod_escola_rede_ensino = null,
                    null, # $ref_usuario_exc = null
                    $this->usuario_cad, # $ref_usuario_cad = null,
                    "Importação", # $nm_rede = null
                    null, # $data_cadastro = null
                    null, # $data_exclusao = null
                    1, # $ativo = null
                    $this->instituicao_id # $ref_cod_instituicao = null
            );
            $rede_ensino_id = $rede_ensino->cadastra();
        }
        // Outra coisa que a escola vai precisar é uma localização.
        // A instituição deve ter uma 'Urbana' ou 'Rural' criada.
        // Caso não, cria.
        $localizacao_id = 0;
        $localizacoes = new clsPmieducarEscolaLocalizacao();
        $localizacoes = $localizacoes->lista( null,null,null,null,null,null,null,null,1,$this->instituicao_id );
        $nome_localizacao = 'Urbana';
        if (intval($d['zona_localizacao']) == 2)
            $nome_localizacao = 'Rural';
        if ($localizacoes) {
            foreach ($localizacoes as $localizacao) {
                if (!strcmp($localizacao['nm_localizacao'], $nome_localizacao)) {
                    $localizacao_id = $localizacao['cod_escola_localizacao'];
                    break;
                }
            }
        }
        if (!$localizacao_id) {
            $localizacao = clsPmieducarEscolaLocalizacao(
                    null, # $cod_escola_localizacao = null,
                    null, # $ref_usuario_exc = null,
                    $this->usuario_cad, # $ref_usuario_cad = null,
                    $nome_localizacao, # $nm_localizacao = null,
                    null, # $data_cadastro = null,
                    null, # $data_exclusao = null,
                    1, # $ativo = null,
                    $this->instituicao_id # $ref_cod_instituicao = null
            );
            $localizacao_id = $localizacao->cadastra();
        }        
        // Gera uma sigla a partir do nome.
        $sigla = $this->sigla($d['nome']);
        
        $escola = new clsPmieducarEscola(
                null, # $cod_escola = NULL
                $this->usuario_cad, # $ref_usuario_cad = NULL,
                null, # $ref_usuario_exc = NULL,
                $this->instituicao_id, # $ref_cod_instituicao = NULL,
                $localizacao_id, # $ref_cod_escola_localizacao = NULL,
                $rede_ensino_id, # $ref_cod_escola_rede_ensino = NULL,
                $id_pessoa, # $ref_idpes = NULL,
                $sigla, # $sigla = NULL,
                null, # $data_cadastro = NULL,
                null, # $data_exclusao = NULL,
                1, # $ativo = NULL,
                null # $bloquear_lancamento_diario_anos_letivos_encerrados = NULL
        );
        $escola_id = $escola->cadastra();
        $escola->cod_escola = $escola_id;
        $escola->vincula_educacenso($d['codigo_inep'], 'Importador');
        
        $municipio = new clsMunicipio();
        $municipio = $municipio->by_id_IBGE($d['_municipio']);
        
        foreach (array(1 => 'telefone', 2 => 'telefone_publico', 3 => 'telefone_outro', 4 => 'fax') as $t => $f) {
            if ((bool)$d['_ddd'] && (bool)$d[$f]) {
                $telefone = new clsPessoaTelefone(
                        $id_pessoa, 
                        $t, 
                        str_replace( "-", "", $d[$f]), 
                        $d['_ddd'] 
                );
                $telefone->cadastra();
            }
        }
        
        $endereco = new clsEnderecoExterno( 
                $id_pessoa, 
                "1", 
                'QDA', 
                $d['endereco'], 
                null, // Endereço é campo numérico e tem letras. 
                null, // Letra é um campo text de length 1.
                $d['complemento'], 
                $d['bairro'], 
                idFederal2int($d['cep']), 
                $municipio->nome, 
                $municipio->sigla_uf, 
                false 
        );
        $endereco->cadastra();
        
        //TODO: Cadastro de cursos.
        //$curso_escola = new clsPmieducarEscolaCurso( $cadastrou, $campo, null, $this->pessoa_logada, null, null, 1 );
        //$cadastrou_ = $curso_escola->cadastra();
       
    } 

    protected function date_db($date) {
        return implode('-', array_reverse(explode('/', $date)));
    }
    
    protected function add_professor($d) {
        $id_professor_inep = $d['codigo_inep_profissional'];
        
        $municipio_nascimento = new clsMunicipio();
        $municipio_residencia = new clsMunicipio();
        try {
            $municipio_nascimento = $municipio_nascimento->by_id_IBGE($d['_municipio_nascimento']);
            $municipio_residencia = $municipio_residencia->by_id_IBGE($d['_municipio']);
        } catch (Exception $e) {
            $municipio_nascimento = null;
            $municipio_residencia = null;
        }
        
        // Cadastro de pessoas ...
        $pessoa = new clsPessoa_();
        $pessoa->nome  = $d['nome_profissional'];
        $pessoa->email = $d['email'];
        $pessoa->tipo = 'F';
        $idpes = $pessoa->cadastra();
        // Então pessoa física ...
        $fisica = new clsFisica();
        $fisica->idpes = $idpes;
        $fisica->data_nasc = $this->date_db($d['data_nascimento']);
        $fisica->sexo = intval($d['sexo']) == 1 ? 'M' : 'F';
        $fisica->ref_cod_sistema = null;
        $fisica->cpf =$d['cpf'];
        $fisica->nacionalidade = $d['nacionalidade'];
        $fisica->idmun_nascimento = $municipio_nascimento ? $municipio_nascimento->idmun : null;
        $fisica->nome_mae = $d['nome_mae'];
        $id_fisica = $fisica->cadastra();
        // Endereço ...
        $endereco = new clsEnderecoExterno(
                $idpes, # $idpes = FALSE, 
                '1', # $tipo = FALSE, 
                'QDA', # $idtlog = FALSE, //TODO: Encontrar uma forma de identificar o tipo.
                $d['endereco'], # $logradouro = FALSE, 
                null, # $numero = FALSE, 
                null, # $letra = FALSE, // Sim, o campo 'numero' é tipo Numeric. Geniuses. 
                $d['complemento'], # $complemento = FALSE,
                $d['bairro'], # $bairro = FALSE, 
                idFederal2int( $d['cep'] ), # $cep = FALSE, 
                $municipio_residencia ? $municipio_residencia->idmun : null, # $cidade = FALSE, 
                $municipio_residencia ? $municipio_residencia->uf : null, # $uf = FALSE,
                null, # $reside_desde = FALSE, 
                null, # $bloco = FALSE, 
                null, # $apartamento = FALSE, 
                null, # $andar = FALSE,
                null, # $idpes_cad = FALSE, 
                null, # $idpes_rev = FALSE, 
                1 # $zona_localizacao = 1
        );
        $endereco->cadastra();
        // Servidor
        $funcionario = new clsPortalFuncionario(
                $idpes, # $ref_cod_pessoa_fj = null, 
                $d['cpf'], # $matricula = null, 
                'ieducar@valparaiso', # $senha = null, 
                1, # $ativo = null, 
                null, # $ref_sec = null, 
                null, # $ramal = null, 
                null, # $sequencial = null, 
                null, # $opcao_menu = null, 
                null, # $ref_cod_administracao_secretaria = null, 
                null, # $ref_ref_cod_administracao_secretaria = null, 
                null, # $ref_cod_departamento = null, 
                null, # $ref_ref_ref_cod_administracao_secretaria = null, 
                null, # $ref_ref_cod_departamento = null, 
                null, # $ref_cod_setor = null, 
                null, # $ref_cod_funcionario_vinculo = null, 
                null, # $tempo_expira_senha = null, 
                null, # $tempo_expira_conta = null, 
                null, # $data_troca_senha = null, 
                null, # $data_reativa_conta = null, 
                null, # $ref_ref_cod_pessoa_fj = null, 
                null, # $proibido = null, 
                null, # $ref_cod_setor_new = null, 
                null, # $matricula_new = null, 
                1, # $matricula_permanente = null, 
                null, # $tipo_menu = null, 
                $d['email'] # $email = null
        );
        $id_funcionario = $funcionario->cadastra();
        $pmieducarservidor = new clsPmieducarServidor(
                $idpes, # $cod_servidor = NULL,
                null, # $ref_cod_deficiencia = NULL,
                null, # $ref_idesco = NULL,
                20.0, # $carga_horaria = NULL,
                null, # $data_cadastro = NULL,
                null, # $data_exclusao = NULL,
                1, # $ativo = NULL,
                $this->instituicao_id, # $ref_cod_instituicao = NULL,
                null #$ref_cod_subnivel = NULL
        );
        $pmieducarservidor->vincula_educacenso($id_professor_inep);
    }

    protected function add_turma($d) {
        $id_turma_inep = intval($d['codigo_inep_turma']);
        $id_escola_inep = intval($d['codigo_inep_escola']);
        $id_etapa_ensino = intval($d['_etapa_ensino']);
        
        $id_escola = clsPmieducarEscola::id_escola_inep($id_escola_inep);

        $id_tipo_turma = $this->tipo_turma($d);
        
        $id_curso = $this->curso($id_etapa_ensino, $id_escola);
        $id_serie = $this->serie($id_etapa_ensino, $id_curso, $id_escola); 
        
        $hora_inicio = sprintf("%02d:%02d:00", intval($d['horario_inicial_hora']), intval($d['horario_inicial_minuto']));
        $hora_fim = sprintf("%02d:%02d:00", intval($d['horario_final_hora']), intval($d['horario_final_minuto']));
        
        $turma = new clsPmieducarTurma();
        $turma->ref_cod_instituicao = $this->instituicao_id;
        $turma->ref_cod_instituicao_regente = $this->instituicao_id;
        $turma->ref_usuario_cad = $this->usuario_cad;
        $turma->ref_ref_cod_escola = $id_escola;
        $turma->ref_cod_curso = $id_curso;
        $turma->ref_ref_cod_serie = $id_serie;
        $turma->nm_turma = $d['nome_turma'];
        $turma->sgl_turma = '';
        $turma->max_aluno = 99;
        $turma->ativo = 1;
        $turma->visivel = 1;
        $turma->ref_cod_turma_tipo = $id_tipo_turma;
        $turma->hora_inicial = $hora_inicio;
        $turma->hora_final = $hora_fim; 
        $turma->ano = $this->year;
        $turma->tipo_boletim = 1; // Na falta de ...

        $id_turma = $turma->cadastra();
        $turma->cod_turma = $id_turma;
        $turma->vincula_educacenso($id_turma_inep, 'Importador');
        
        // TODO: Descobrir se módulos e dias da semana são realmente necessários.
    }

    protected function add_aluno($d) {
    	$municipio_nascimento = new clsMunicipio();
    	$municipio_residencia = new clsMunicipio();
    	try {
    		$municipio_nascimento = $municipio_nascimento->by_id_IBGE($d['_municipio_nascimento']);
    		$municipio_residencia = $municipio_residencia->by_id_IBGE($d['_municipio']);
    	} catch (Exception $e) {
    	   $municipio_nascimento = null;
    	   $municipio_residencia = null;
    	}
    	
    	$pessoa = new clsPessoa_();
    	$pessoa->nome  = $d['nome'];
    	$pessoa->tipo = 'F';
    	$idpes = $pessoa->cadastra();
    	
    	$fisica = new clsFisica();
    	$fisica->idpes = $idpes;
    	$fisica->data_nasc = $this->date_db($d['data_nascimento']);
    	$fisica->sexo = intval($d['sexo']) == 1 ? 'M' : 'F';
    	$fisica->ref_cod_sistema = null;
    	$fisica->cpf = $d['numero_cpf'] ? $d['numero_cpf'] : null;
    	$fisica->nacionalidade = $d['nacionalidade'];
    	$fisica->idmun_nascimento = $municipio_nascimento ? $municipio_nascimento->idmun : null;
    	$fisica->nome_mae = $d['nome_mae'];
    	$fisica->nome_pai = $d['nome_pai'];
    	$id_fisica = $fisica->cadastra();
    	// Endereço ...
    	$endereco = new clsEnderecoExterno(
    			$idpes, # $idpes = FALSE,
    			'1', # $tipo = FALSE,
    			'QDA', # $idtlog = FALSE, //TODO: Encontrar uma forma de identificar o tipo.
    			$d['endereco'], # $logradouro = FALSE,
    			null, # $numero = FALSE,
    			null, # $letra = FALSE, // Sim, o campo 'numero' é tipo Numeric. Geniuses.
    			$d['complemento'], # $complemento = FALSE,
    			$d['bairro'], # $bairro = FALSE,
    			idFederal2int( $d['cep'] ), # $cep = FALSE,
    			$municipio_residencia ? $municipio_residencia->idmun : null, # $cidade = FALSE,
    			$municipio_residencia ? $municipio_residencia->uf : null, # $uf = FALSE,
    			null, # $reside_desde = FALSE,
    			null, # $bloco = FALSE,
    			null, # $apartamento = FALSE,
    			null, # $andar = FALSE,
    			null, # $idpes_cad = FALSE,
    			null, # $idpes_rev = FALSE,
    			1 # $zona_localizacao = 1
    			);
    	$endereco->cadastra();
    	$aluno = new clsPmieducarAluno(
    			null, # $cod_aluno = NULL, 
    			null, # $ref_cod_aluno_beneficio = NULL,
    			null, # $ref_cod_religiao = NULL, 
    			null, # $ref_usuario_exc = NULL, 
    			$this->usuario_cad, # $ref_usuario_cad = NULL,
    			$idpes, # $ref_idpes = NULL, 
    			null, # $data_cadastro = NULL, 
    			null, # $data_exclusao = NULL, 
    			1, # $ativo = NULL,
    			null, # $caminho_foto = NULL,
    			null, # $analfabeto = NULL, 
    			$d['nome_pai'], # $nm_pai = NULL, 
    			$d['nome_mae'], # $nm_mae = NULL,
    			$tipo_responsavel = NULL, 
    			$aluno_estado_id = NULL
    		);
    	$id_aluno = $aluno->cadastra();
    	$aluno->cod_aluno = $id_aluno;
    	$aluno->vincula_educacenso($d['codigo_inep_aluno'], 'Importador'); 
    }
    
    protected function tipo_turma($d) {
        // Tipo de turma. Procura por tipos pré-criados e usa. Se não encontrar (pela sigla),
        // cria um novo tipo.
        $sigla_tipo_turma = null;
        $nome_tipo_turma = null;
        switch($d['tipo_atendimento']) {
            case '1':
                $sigla_tipo_turma = 'CH';
                $nome_tipo_turma = 'Classe hospitalar';
                break;
            case '2':
                $sigla_tipo_turma = 'UAS';
                $nome_tipo_turma = 'Unidade de atendimento socioeducativo';
                break;
            case '3':
                $sigla_tipo_turma = 'UP';
                $nome_tipo_turma = 'Unidade prisional';
                break;
            case '4':
                $sigla_tipo_turma = 'AC';
                $nome_tipo_turma = 'Atividade complementar';
                break;
            case '5':
                $sigla_tipo_turma = 'AEE';
                $nome_tipo_turma = 'Atendimento educacional especializado';
                break;
            default:
                $sigla_tipo_turma = 'N/A';
                $nome_tipo_turma =  'Não se aplica';
        }
        $tipos_turma = new clsPmieducarTurmaTipo();
        $tipos_turma = $tipos_turma->lista(null, null, null, null, null, null, null, null, null, 1, $this->instituicao_id);
        $id_turma_tipo = null;
        if ($tipos_turma) {
            foreach ($tipos_turma as $tipo) {
                if (!strcmp(strtoupper($tipo['sgl_tipo']), strtoupper($sigla_tipo_turma))) {
                    $id_turma_tipo = $tipo['cod_turma_tipo'];
                    break;
                }
            }
        }
        if (!(bool)$id_turma_tipo) {
            $tipo_turma = new clsPmieducarTurmaTipo(
                    null, # $cod_turma_tipo = null,
                    null, # $ref_usuario_exc = null,
                    $this->usuario_cad, # $ref_usuario_cad = null,
                    $nome_tipo_turma, # $nm_tipo = null,
                    $sigla_tipo_turma, # $sgl_tipo = null,
                    null, # $data_cadastro = null,
                    null, # $data_exclusao = null,
                    1, # $ativo = null,
                    $this->instituicao_id # $ref_cod_instituicao = null
            );
            $id_turma_tipo = $tipo_turma->cadastra();
        }
        
        return $id_turma_tipo;
    }
    
    protected function curso($id_etapa_ensino, $id_escola) {
        $curso_data = EducacensoFieldHelper::curso_serie_by_etapa_ensino($id_etapa_ensino);
        $id_curso = null;
        
        // Se houver um nível de ensino com o nome deste curso
        // o utiliza, senão criamos um novo.
        $id_nivel_ensino = null;
        $niveis_ensino = new clsPmieducarNivelEnsino();
        $niveis_ensino = $niveis_ensino->lista(
                null, # $int_cod_nivel_ensino = null, 
                null, # $int_ref_usuario_exc = null, 
                null, # $int_ref_usuario_cad = null, 
                $curso_data['nivel'], # $str_nm_nivel = null, 
                null, # $str_descricao = null, 
                null, # $date_data_cadastro_ini = null, 
                null, # $date_data_cadastro_fim = null, 
                null, # $date_data_exclusao_ini = null, 
                null, # $date_data_exclusao_fim = null, 
                1, # $int_ativo = null, 
                $this->instituicao_id # $int_ref_cod_instituicao = null 
         );
        if ($niveis_ensino) {
            $id_nivel_ensino = $niveis_ensino[0]['cod_nivel_ensino'];
        } else {
            $nivel_ensino = new clsPmieducarNivelEnsino();
            $nivel_ensino->nm_nivel = $curso_data['nivel'];
            $nivel_ensino->ref_cod_instituicao = $this->instituicao_id;
            $nivel_ensino->ativo = 1;
            $nivel_ensino->ref_usuario_cad = $this->usuario_cad;
            $id_nivel_ensino = $nivel_ensino->cadastra();
        }        
        
        // Tipo de ensino. Se já houver, usa o primeiro deles.
        // Senão, cria um tipo "Padrão"
        $id_tipo_ensino = null;
        $tipos_ensino = new clsPmieducarTipoEnsino();
        $tipos_ensino = $tipos_ensino->lista(null, null, null, null, null, null, 1, $this->instituicao_id);
        if ($tipos_ensino) {
            $id_tipo_ensino = $tipos_ensino[0]['cod_tipo_ensino'];
        } else {
            $tipo_ensino = new clsPmieducarTipoEnsino();
            $tipo_ensino->nm_tipo = "Padrão";
            $tipo_ensino->ativo = 1;
            $tipo_ensino->ref_cod_instituicao = $this->instituicao_id;
            $tipo_ensino->ref_usuario_cad = $this->usuario_cad;
            $id_tipo_ensino = $tipo_ensino->cadastra();
        }
        
        $cursos = new clsPmieducarCurso();
        $cursos = $cursos->lista(
                null, 
                null, 
                null, 
                $id_nivel_ensino, 
                $id_tipo_ensino, 
                null, 
                $curso_data['nome'],
                null, null, # $str_sgl_curso = NULL, $int_qtd_etapas = NULL,
                null, null, null, # $int_frequencia_minima = NULL, $int_media = NULL, $int_media_exame = NULL,
                null, null, # $int_falta_ch_globalizada = NULL, $int_carga_horaria = NULL,
                null, null, # $str_ato_poder_publico = NULL, $int_edicao_final = NULL,
                null, null, # $str_objetivo_curso = NULL, $str_publico_alvo = NULL,
                null, null, # $date_data_cadastro_ini = NULL, $date_data_cadastro_fim = NULL,
                null, null, # $date_data_exclusao_ini = NULL, $date_data_exclusao_fim = NULL,
                1, null, # $int_ativo = NULL, $int_ref_usuario_exc = NULL,
                $this->instituicao_id, null # $int_ref_cod_instituicao = NULL, $int_padrao_ano_escolar = NULL,
          );
        if ($cursos) {
            $id_curso = intval($cursos[0]['cod_curso']);
        } else {
            $curso = new clsPmieducarCurso();
            $curso->nm_curso = $curso_data['curso'];
            $curso->sgl_curso = $this->sigla($curso_data['curso'], 0);
            $curso->qtd_etapas = $curso_data['etapas'];
            $curso->carga_horaria = 800 * $curso_data['etapas'];
            $curso->ativo = 1;
            $curso->ref_cod_nivel_ensino = $id_nivel_ensino;
            $curso->ref_cod_tipo_ensino = $id_tipo_ensino;
            $curso->ref_cod_instituicao = $this->instituicao_id;
            $curso->ref_usuario_cad = $this->usuario_cad;
            $curso->padrao_ano_escolar = 1;
            $curso->multi_seriado = 1;
            $id_curso = $curso->cadastra();            
        }
        
        // Se houver escola, verifica o vínculo do curso.
        if ($id_escola) {
            $escola_curso = new clsPmieducarEscolaCurso();
            $escola_curso = $escola_curso->lista($id_escola, $id_curso);
            if (!$escola_curso) {
                $vinculo = new clsPmieducarEscolaCurso();
                $vinculo->ref_cod_curso = $id_curso;
                $vinculo->ref_cod_escola = $id_escola;
                $vinculo->ref_usuario_cad = $this->usuario_cad;
                $vinculo->ativo = 1;
                $vinculo->cadastra();
            }
        }
        
        return $id_curso;
    }
    
    protected function serie($id_etapa_ensino, $id_curso, $id_escola) {
        $serie_data = EducacensoFieldHelper::curso_serie_by_etapa_ensino($id_etapa_ensino);
        $id_serie = null;
        
        $series = new clsPmieducarSerie();
        $series = $series->lista(null, null, null, $id_curso, null, $serie_data['etapa'], null, null, null, null, null, null, 1, $this->instituicao_id);
        if ($series) {
            $id_serie = $series['cod_serie'];
        } else {
            $serie = new clsPmieducarSerie();
            $serie->ref_usuario_cad = $this->usuario_cad;
            $serie->ref_cod_curso = $id_curso;
            $serie->nm_serie = $serie_data['serie'];
            $serie->etapa_curso = $serie_data['etapa'];
            $serie->concluinte = ($serie_data['etapa'] == $serie_data['etapas']) ? 1 : 0;
            $serie->carga_horaria = 800;
            $serie->dias_letivos = 200;
            $serie->ativo = 1;
            $serie->intervalo = 1; // Não, não sei o que é isso também.
            $id_serie = $serie->cadastra();
        }
        
        // Verifica o vinculo da escola/serie
        if ($id_escola) {
            $escola_serie = new clsPmieducarEscolaSerie();
            $escola_serie = $escola_serie->lista($id_escola, $id_serie);
            if (!$escola_serie) {
                $vinculo = new clsPmieducarEscolaSerie();
                $vinculo->ref_cod_escola = $id_escola;
                $vinculo->ref_cod_serie = $id_serie;
                $vinculo->ref_usuario_cad = $this->usuario_cad;
                $vinculo->hora_inicial = "07:30:00";
                $vinculo->hora_final = "12:00:00";
                $vinculo->hora_inicio_intervalo = "09:50:00";
                $vinculo->hora_fim_intervalo = "10:20:00";
                $vinculo->cadastra();
            }
        }
        
        return $id_serie;
    }
    
    protected function sigla($s, $min_length = 2, $blacklist = array("de", "da", "do", "das", "dos")) {
        $result = "";
        foreach(explode(' ', $s) as $w) {
            if ((strlen($w) > $min_length) && (!in_array(strtolower($w), $blacklist))) {
                $result .= $w[0];
            }
        }
        return strtoupper($result);
    }
       
}

?>
