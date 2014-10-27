<?php

require_once('EducacensoFieldParser.inc.php');
require_once('include/funcoes.inc.php');

class EducacensoParser {
    private $instituicao_id;
    private $filename;
    private $operations;

    public function __construct($instituicao_id, $filename) {
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
    
    protected function _aluno($d) {
        $logs = array();
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
            add_escola($d);
            $logs .= "Escola $id_escola não encontrada. Criando o registro.\n";
            $logs .= var_export($d, true) . "\n";
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

    protected function add_professor() {
    
    }

    protected function add_turma() {
        
    }

    protected function add_aluno() {
    
    }

    protected function add_matricula() {
        
    }

}

?>
