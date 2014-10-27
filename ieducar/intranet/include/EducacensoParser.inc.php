<?php

require_once('EducacensoFieldParser.inc.php');
require_once('include/funcoes.inc.php');

class EducacensoParser {
    private $instituicao_id;
    private $filename;
    private $operations;
    private $year;  

    public function __construct($instituicao_id, $filename, $year = 2014) {
        $this->instituicao_id = $instituicao_id;
        $this->filename = $filename;
        $this->operations = array ();
    }

    protected function parse_data($data) {
        $ready = false;
        $result = null;
        switch ($data ['tipo_registro']) {
            case '00' :
                break;
            case '10' :
                break;
            case '20' :
                break;
            case '30' :
                break;
            case '40' :
                break;
            case '50' :
                break;
            case '51' :
                break;
            case '60' :
                break;
            case '70' :
                break;
            case '80' :
                break;
        }
    }

    public function run() {
        $logs = array ();
        $contents = file_get_contents ( $this->filename );
        $contents = explode ( '\n', $contents );
        
        foreach ( $contents as $text_row ) {
            $log = parse_row ( explode ( '|', $text_row ) );
            if ($log) {
                $logs [] = $log;
            }
        }
        return $logs;
    }
    
    protected function _turma($d) {
        $logs = "";
        $id_turma_inep = int($d['codigo_inep_turma']);
        $id_escola_inep = int($d['codigo_inep_escola']);
        $id_turma = (new clsPmieducarTurma())->id_turma_inep($id_turma_inep);
        $id_escola = (new clsPmieducarEscola())->id_escola_inep($id_escola_inep);
        
        if ($id_turma) {
            $logs .= "Turma $id_turma_inep encontrada. Não será atualizada.\n";
        } else {
            $logs .= "Turma $id_turma_inep não encontrada. Criando o registro.\n";
            $logs .= var_export($d, true) . "\n";
            add_turma($d);
        }
        
        return $logs;
    }
    
    protected function _aluno($d) {
    }

    protected function _escola($d) {
        
        $logs = "";
        $id_inep = int($d['codigo_inep']);
        $escola = new clsPmieducarEscola();
        $id_escola = $escola->id_escola_inep($id_inep);
        
        // Verificamos se a escola existe ...
        if ($id_escola) {
            // Se sim, atualizamos as informações.
            // Mas por enquanto não.
            $logs .= "Escola $id_escola encontrada. Não será atualizada.\n";
        } else {
            $logs .= "Escola $id_escola não encontrada. Criando o registro.\n";
            $logs .= var_export($d, true) . "\n";
            add_escola($d);
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
        
        // A escola vai precisar de uma rede de ensino, que é específica
        // da instituição. Se tiver alguma, usa a primeira delas,
        // senão, cria uma e fica por isso.
        $rede_ensino_id = -1;
        $redes_ensino = (new clsPmieducarEscolaRedeEnsino())->lista( null,null,null,null,null,null,null,null,1,$this->instituicao_id );
        if ($redes_ensino) {
            $rede_ensino_id = $redes_ensino[0]['cod_escola_rede_ensino'];
        } else {
            // ,  , , , ,
            $rede_ensino = new clsPmieducarEscolaRedeEnsino(
                    null, # $cod_escola_rede_ensino = null,
                    null, # $ref_usuario_exc = null
                    null, # $ref_usuario_cad = null,
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
        $localizacoes = (new clsPmieducarEscolaLocalizacao())->lista( null,null,null,null,null,null,null,null,1,$this->instituicao_id );
        $nome_localizacao = 'Urbana';
        if ($d['zona_localizacao'] == '2')
            $nome_localizacao == 'Rural';
        if ($localizacoes) {
            foreach ($localizacoes as $localizacao) {
                if ($localizacao['nm_localizacao'] == $nome_localizacao) {
                    $localizacao_id = $localizacao['cod_escola_localizacao'];
                    break;
                }
            }
        }
        if (!$localizacao_id) {
            $localizacao = clsPmieducarEscolaLocalizacao(
                    null, # $cod_escola_localizacao = null,
                    null, # $ref_usuario_exc = null,
                    null, # $ref_usuario_cad = null,
                    $nome_localizacao, # $nm_localizacao = null,
                    null, # $data_cadastro = null,
                    null, # $data_exclusao = null,
                    1, # $ativo = null,
                    $this->instituicao_id # $ref_cod_instituicao = null
            );
            $localizacao_id = $localizacao->cadastra();
        }        
        // Gera uma sigla a partir do nome.
        $sigla = "";
        foreach(explode(' ', $d['nome']) as $w) {
            if (strlen($w) > 2) {
                $sigla .= $w[0];
            }
        }
        
        $escola = new clsPmieducarEscola(
                null, # $cod_escola = NULL
                null, # $ref_usuario_cad = NULL,
                null, # $ref_usuario_exc = NULL,
                $this->instituicao_id, # $ref_cod_instituicao = NULL,
                $localizacao_id, # $ref_cod_escola_localizacao = NULL,
                $rede_ensino_id, # $ref_cod_escola_rede_ensino = NULL,
                null, # $ref_idpes = NULL,
                $sigla, # $sigla = NULL,
                null, # $data_cadastro = NULL,
                null, # $data_exclusao = NULL,
                1, # $ativo = NULL,
                null # $bloquear_lancamento_diario_anos_letivos_encerrados = NULL
        );
        $escola_id = $escola->cadastra();
        $escola->vincula_educacenso(int($d['codigo_inep']), 'Importador');
        
        
        $municipio = (new clsMunicipio())->by_id_IBGE(int($d['_municipio']));
        
        // Complemento do cadastro escolar
        clsPmieducarEscolaComplemento( 
                $escola_id, # $ref_cod_escola = null
                null,   # $ref_usuario_exc = null
                null,  # $ref_usuario_cad = null  
                idFederal2int( $d['cep'] ), # $cep = null, 
                $d['endereco_numero'], # $numero = null,  
                $d['complemento'], #$complemento = null,
                $d['email'], # $email = null
                null, # $nm_escola = null
                $municipio ? $municipio->nome : null, # $municipio = null 
                $d['bairro'], # $bairro = null,
                $d['endereco'], # $logradouro = null
                $d['ddd'], # $ddd_telefone = null  
                $d['telefone'], # $telefone = null
                $d['ddd'], # $ddd_fax = null 
                $d['fax'], # $fax = null
                null, # $data_cadastro = null
                null, # $data_exclusao = null
                1 # $ativo = null 
        );
        
        //TODO: Cadastro de cursos.
        //$curso_escola = new clsPmieducarEscolaCurso( $cadastrou, $campo, null, $this->pessoa_logada, null, null, 1 );
        //$cadastrou_ = $curso_escola->cadastra();
       
    } 

    protected function add_professor($d) {
    }

    protected function add_turma($d) {
        $id_turma_inep = int($d['codigo_inep_turma']);
        $id_escola_inep = int($d['codigo_inep_escola']);
        
        $id_escola = (new clsPmieducarEscola())->id_escola_inep($id_escola_inep);

        // Tipo de turma. Procura por tipos pré-criados e usa o primeiro.
        // Não encontrando nenhum, cria um tipo 'Não se aplica'.
        $tipos_turma = (new clsPmieducarTurmaTipo())->lista(null, null, null, null, null, null, null, null, null, 1, $this->instituicao_id);
        $id_turma_tipo = null;
        if ($tipos_turma) {
            $id_tipo_turma = $tipos_turma[0]['cod_turma_tipo'];
        } else {
            $tipo_turma = clsPmieducarTurmaTipo( 
                    null, # $cod_turma_tipo = null, 
                    null, # $ref_usuario_exc = null, 
                    null, # $ref_usuario_cad = null, 
                    "Não se aplica", # $nm_tipo = null, 
                    "N/A", # $sgl_tipo = null, 
                    null, # $data_cadastro = null, 
                    null, # $data_exclusao = null, 
                    1, # $ativo = null, 
                    $this->instituicao_id # $ref_cod_instituicao = null 
            );
            $id_turma_tipo = $tipo_turma->cadastra();
        }
        
        $hora_inicio = sprintf("%02d:%02d:00", int($d['horario_inicial_hora']), int($d['horario_inicial_minuto']));
        $hora_fim = sprintf("%02d:%02d:00", int($d['horario_final_hora']), int($d['horario_final_minuto']));
        
        $turma = clsPmieducarTurma(
                null, # $cod_turma = null
                null, # $ref_usuario_exc = null
                null, # $ref_usuario_cad = null
                null, # $ref_ref_cod_serie = null
                $id_escola, # $ref_ref_cod_escola = null
                null, # $ref_cod_infra_predio_comodo = null
                $d['nome_turma'], # $nm_turma = null
                '', # $sgl_turma = null
                99, # $max_aluno = null
                null, # $multiseriada = null
                null, # $data_cadastro = null
                null, # $data_exclusao = null
                1, # $ativo = null
                $id_turma_tipo, # $ref_cod_turma_tipo = null
                $hora_inicio, # $hora_inicial = null
                $hora_fim, # $hora_final = null
                null, # $hora_inicio_intervalo = null
                null, # $hora_fim_intervalo = null  
                null, # $ref_cod_regente = null
                null, # $ref_cod_instituicao_regente = null
                $this->instituicao_id, # $ref_cod_instituicao = null
                null, # $ref_cod_curso = null
                null, # $ref_ref_cod_serie_mult = null
                null, # $ref_ref_cod_escola_mult = null
                null, # $visivel = null
                null, # $turma_turno_id = null
                null, # $tipo_boletim = null
                $this->year # $ano = null
        );
        $id_turma = $turma->cadastra();
        $turma->vincula_educacenso($id_turma_inep, 'Importador');
        
        // TODO: Descobrir se módulos e dias da semana são realmente necessários.
    }

    protected function add_aluno() {
    
    }

    protected function add_matricula() {
        
    }

}

?>
