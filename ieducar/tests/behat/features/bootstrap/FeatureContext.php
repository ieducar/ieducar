<?php

use Behat\Behat\Context\ClosuredContextInterface,
    Behat\Behat\Context\TranslatedContextInterface,
    Behat\Behat\Context\BehatContext,
    Behat\Behat\Exception\PendingException;
use Behat\Gherkin\Node\PyStringNode,
    Behat\Gherkin\Node\TableNode;

//
// Require 3rd-party libraries here:
//
//   require_once 'PHPUnit/Autoload.php';
//   require_once 'PHPUnit/Framework/Assert/Functions.php';
//

use Behat\MinkExtension\Context\MinkContext;
use Behat\Mink\Driver;

/**
 * Features context.
 */
class FeatureContext extends MinkContext
{
    /**
     * Initializes context.
     * Every scenario gets it's own context object.
     *
     * @param array $parameters context parameters (set them up through behat.yml)
     */
    public function __construct(array $parameters)
    {
        // Initialize your context here
    }
    
    /**
     * @Then /^I wait for the suggestion box to appear$/
     */
    public function iWaitForTheSuggestionBoxToAppear()
    {
    	$this->getSession()->wait(5000,
    			"$('.suggestions-results').children().length > 0"
    	);
    }
    
    /**
     * @Given /^(?:|Eu )estou navegando no frame "([^"]*)"$/
     */
    public function euEstouNavegandoNoFrame($frameName)
    {
    	$this->getSession()->switchToIFrame($frameName);
    }
    
    /**
     * @Given /^(?:|Eu )foco na janela principal$/
     */
    public function euFocoNaJanelaPrincipal()
    {
    	$this->getSession()->switchToWindow(null);
    }
    
    /**
     * @Given /^(?:|Que eu )estou autenticado como Administrador$/
     */
    public function euEstouAutenticadoComoAdministrador()
    {
    	$this->visit('/intranet/logof.php');
    	$this->visit('/intranet/index.php');
    	$this->fillField('login', 'admin');
    	$this->fillField('senha', 'ieducar@serpro');
    	$this->pressButton('Entrar');
    	$this->assertPageContainsText(utf8_encode('Usuário atual: Administrador'));
    }
    
    /**
     * @Given /^(?:|Eu )confirmo o popup$/
     */
    public function confirmoOPopup()
    {
    	$this->getSession()->getDriver()->getWebDriverSession()->accept_alert();
    }
    
    /**
     * @When /^(?:|Eu )cancelo o popup$/
     */
    public function canceloOPopup()
    {
    	$this->getSession()->getDriver()->getWebDriverSession()->dismiss_alert();
    }
    
    /**
     * @When /^(?:|Eu )deveria ver "([^"]*)" no popup$/
     *
     * @param string $message Texto da mensagem
     *
     * @return bool
     */
    public function verificaMensagemPopup($message)
    {
    	return $message == $this->getSession()->getDriver()->getWebDriverSession()->getAlert_text();
    }
    
    /**
     * @When /^(?:|Eu )preencho "([^"]*)" no popup$/
     *
     * @param string $message Texto da mensagem.
     */
    public function setPopupText($message)
    {
    	$this->getSession()->getDriver()->getWebDriverSession()->postAlert_text($message);
    }
    
    /**
     * @Given /^(?:|Que eu )estou autenticado como Usuario de Teste$/
     */
    public function euEstouAutenticadoComoUsuarioDeTeste()
    {
    	$this->visit('/intranet/logof.php');
    	$this->visit('/intranet/index.php');
    	$this->fillField('login', '35189775208');
    	$this->fillField('senha', '12345678');
    	$this->pressButton('Entrar');
    	$this->assertPageContainsText(utf8_encode('Usuário atual: Usuario de Teste'));
    }
    
    /**
     * @Given /^(?:|Que )existe a Escola de Teste$/
     */
    public function criaEscolaTeste()
    {
    	$this->euEstouAutenticadoComoAdministrador();

    	//bairro
    	//TODO criar passo para testar criação de bairro
    	$this->visit('/intranet/public_bairro_lst.php');
    	$this->pressButton('Novo');
    	$this->selectOption('idpais', 'Brasil');
    	$this->selectOption('sigla_uf', 'Rio Grande do Sul');
    	$this->selectOption('idmun', 'Porto Alegre');
    	$this->selectOption('zona_localizacao', 'Urbana');
    	$this->fillField('nome', 'Centro');
    	$this->pressButton('Salvar');
    	$this->assertPageContainsText('Bairro - Listagem');
    	
    	//logradouro
    	//TODO criar passo para testar criação de logradouro
    	$this->visit('/intranet/public_logradouro_lst.php');
    	$this->pressButton('Novo');
    	$this->selectOption('idpais', 'Brasil');
    	$this->selectOption('sigla_uf', 'Rio Grande do Sul');
    	$this->selectOption('idmun', 'Porto Alegre');
    	$this->selectOption('idtlog', 'Avenida');
    	$this->fillField('nome', 'Augusto de Carvalho');
    	$this->pressButton('Salvar');
    	$this->assertPageContainsText('Filtros de busca');
    	
    	//cep
    	//TODO criar passo para testar criação de cep
    	$this->visit('/intranet/urbano_cep_logradouro_lst.php');
    	$this->pressButton('Novo');
    	$this->selectOption('idpais', 'Brasil');
    	$this->selectOption('sigla_uf', 'Rio Grande do Sul');
    	$this->selectOption('idmun', 'Porto Alegre');
    	$this->selectOption('idlog', 'Augusto de Carvalho');
    	$this->fillField('cep[0]', '90010-390');
    	$this->selectOption('idbai[0]', 'Centro');
    	$this->pressButton('Salvar');
    	$this->assertPageContainsText('Cep Logradouro - Listagem');
    	
    	//PJ
    	//TODO criar passo para testar criação de PJ
    	$this->visit('/intranet/empresas_lst.php');
    	$this->pressButton('Novo');
    	$this->fillField('busca_empresa', '58.253.348/0001-70');
    	$this->pressButton('Salvar');
    	$this->fillField('fantasia', 'Escola de Teste');
    	$this->fillField('razao_social', 'Escola de Teste');
    	$this->getSession()->executeScript('jQuery("#lupa").click()');
    	$this->euEstouNavegandoNoFrame('miolo');
    	$this->fillField('nm_bairro', '');
    	$this->fillField('nr_cep', '90010-390');
    	$this->fillField('nm_logradouro', '');
    	$this->fillField('cidade', '');
    	$this->selectOption('ref_sigla_uf', 'RS');
    	$this->pressButton('busca');
    	$this->clickLink('90010-390');
    	$this->euFocoNaJanelaPrincipal();
    	$this->pressButton('Salvar');
    	$this->assertPageContainsText('Empresas');
    	
    	//Rede de Ensino
    	//TODO criar passo para testar criação de Rede de Ensino
    	$this->visit('/intranet/educar_escola_rede_ensino_lst.php');
    	$this->pressButton('Novo');
    	$this->selectOption('ref_cod_instituicao', utf8_encode('Serviço Federal de Processamento de Dados - SERPRO'));
    	$this->fillField('nm_rede', 'Federal');
    	$this->pressButton('Salvar');
    	$this->assertPageContainsText('Escola Rede Ensino - Listagem');
    	    	
    	//Escola Localização
    	//TODO criar passo para testar criação de Localização de Escola
    	$this->visit('/intranet/educar_escola_localizacao_lst.php');
    	$this->pressButton('Novo');
    	$this->selectOption('ref_cod_instituicao', utf8_encode('Serviço Federal de Processamento de Dados - SERPRO'));
    	$this->fillField('nm_localizacao', 'Urbana');
    	$this->pressButton('Salvar');
    	$this->assertPageContainsText(utf8_encode('Escola Localização - Listagem'));
    	
    	//Nivel de Ensino
    	//TODO criar passo para testar criação de Nível de Ensino
    	$this->visit('/intranet/educar_nivel_ensino_lst.php');
    	$this->pressButton('Novo');
    	$this->selectOption('ref_cod_instituicao', utf8_encode('Serviço Federal de Processamento de Dados - SERPRO'));
    	$this->fillField('nm_nivel', 'Nivel de Ensino Teste');
    	$this->pressButton('Salvar');
    	$this->assertPageContainsText(utf8_encode('Nível Ensino - Listagem'));
    	
    	//Tipo de Ensino
    	//TODO criar passo para testar criação de Tipo de Ensino
    	$this->visit('/intranet/educar_tipo_ensino_lst.php');
    	$this->pressButton('Novo');
    	$this->selectOption('ref_cod_instituicao', utf8_encode('Serviço Federal de Processamento de Dados - SERPRO'));
    	$this->fillField('nm_tipo', 'Tipo de Ensino Teste');
    	$this->pressButton('Salvar');
    	$this->assertPageContainsText('Tipo Ensino - Listagem');
    	
    	//Tipo de Regime de Ensino - não é obrigatório
    	//TODO criar passo para testar criação de Tipo de Regime de Ensino
    	/*$this->visit('/intranet/educar_tipo_regime_lst.php');
    	$this->pressButton('Novo');
    	$this->selectOption('ref_cod_instituicao', utf8_encode('Serviço Federal de Processamento de Dados - SERPRO'));
    	$this->fillField('nm_tipo', 'Regime de Ensino de Teste');
    	$this->pressButton('Salvar');
    	$this->assertPageContainsText('Tipo Regime - Listagem');*/
    	
    	//Curso
    	//TODO criar passo para testar criação de Curso
    	$this->visit('/intranet/educar_curso_lst.php');
    	$this->pressButton('Novo');
    	$this->selectOption('ref_cod_instituicao', utf8_encode('Serviço Federal de Processamento de Dados - SERPRO'));
    	$this->selectOption('ref_cod_nivel_ensino', 'Nivel de Ensino Teste');
    	$this->selectOption('ref_cod_tipo_ensino', 'Tipo de Ensino Teste');
    	$this->fillField('nm_curso', 'Curso de Teste SERPRO');
    	$this->fillField('sgl_curso', 'CT SERPRO');
    	$this->fillField('qtd_etapas', '1');
    	$this->fillField('carga_horaria', '720');
    	$this->pressButton('Salvar');
    	$this->assertPageContainsText('Curso - Listagem');
    	
    	//Escola
    	//TODO criar passo para testar criação de Escola
    	$this->visit('/intranet/educar_escola_lst.php');
    	$this->pressButton('Novo');
    	$this->clicoNaImagem('Pesquisa');
    	$this->euEstouNavegandoNoFrame('temp_win_popless');
    	$this->fillField('cnpj', '58.253.348/0001-70');
    	$this->pressButton('busca');
    	$this->clickLink('58.253.348/0001-70');
    	$this->euFocoNaJanelaPrincipal();
    	$this->fillField('sigla', 'SERPRO');
    	$this->selectOption('ref_cod_instituicao', utf8_encode('Serviço Federal de Processamento de Dados - SERPRO'));
    	$this->selectOption('ref_cod_escola_rede_ensino', 'Federal');
    	$this->selectOption('ref_cod_escola_localizacao', 'Urbana');
    	$this->selectOption('ref_cod_curso', 'Curso de Teste SERPRO');
    	$this->getSession()->executeScript('jQuery("img[title=\'Incluir\']").click();');
    	$this->pressButton('Salvar');
    	$this->assertPageContainsText('Escola - Listagem');
    }
    
    /**
     * @Given /^(?:|Que )existe o Usuario de Teste$/ 
     */
    public function criaUsuarioTeste()
    {
    	$this->euEstouAutenticadoComoAdministrador();
    	
    	//TODO extrair criação de pessoa física em um passo genérico utilizando parâmetros
    	$this->visit('/intranet/atendidos_lst.php');
    	$this->pressButton('Novo');
    	$this->fillField('id_federal', '351.897.752-08');
    	$this->fillField('nm_pessoa', 'Usuario de Teste');
    	$this->selectOption('sexo', 'Masculino');
    	$this->selectOption('estado_civil_id', 'Solteiro(a)');
    	$this->fillField('data_nasc', '05/05/1980');
    	$this->fillField('naturalidade_nome', '4927');
    	//espera aparecer a caixa de sugestões para selecionar
    	$this->getSession()->wait(500, 'jQuery(".ui-menu-item").first("li").find("a").length > 0');
    	$this->getSession()->executeScript('jQuery(".ui-menu-item").first("li").find("a").click();');
    	$this->fillField('cep_', '90220-200');
    	$this->selectOption('sigla_uf', 'RS');
    	$this->fillField('cidade', 'Porto Alegre');
    	$this->fillField('bairro', 'Floresta');
    	$this->selectOption('zona_localizacao', 'Urbana');
    	$this->selectOption('idtlog', 'Travessa');
    	$this->fillField('logradouro', 'Azevedo');
    	$this->pressButton('Salvar');
    	$this->assertPageContainsText(utf8_encode('Pessoas Físicas'));

    	//TODO extrair criação de login genérico em um passo utilizando parâmetros
		//cria login para Usuario de Teste
    	$this->visit('/intranet/funcionario_lst.php');
    	$this->pressButton('Novo');
    	$this->getSession()->executeScript('jQuery("#nome_busca_lupa").click()');
    	$this->euEstouNavegandoNoFrame('temp_win_popless');
    	$this->fillField('cpf', '351.897.752-08');
    	$this->pressButton('busca');
    	$this->clickLink('351.897.752-08');
    	$this->fillField('matricula', '35189775208');
    	$this->fillField('_senha', '12345678');
    	$this->selectOption('ativo', 'Ativo');
    	$this->selectOption('ref_cod_funcionario_vinculo', 'Efetivo');
    	$this->selectOption('tempo_expira_conta', '365');
    	$this->pressButton('Salvar');
    	$this->assertPageContainsText(utf8_encode('Usuários'));
    }

    /**
     * @Given /^(?:|Eu )clico na imagem "([^"]*)"$/
     */
    public function clicoNaImagem($alt)
    {
    	$this->getSession()->executeScript('jQuery("img[alt=\''.$alt.'\']").click();');
    }
    
    /**
     * Executa alguma função específica de acordo com o passo.
     *  
     * @BeforeStep
     */
    public function beforeStep(Behat\Behat\Event\StepEvent $event)
    {
    	//TODO melhorar a verificação de passo - testar o texto do passo é ruim
		if ($event->getStep()->getText() == "Que existe o Usuario de Teste") {
			//remover o usuário de testes diretamente do banco de dados
			$dbconn = pg_connect("host=localhost port=5432 dbname=ieducar user=ieducar password=ieducar");
			$result = pg_query($dbconn, "SELECT idpes FROM cadastro.pessoa WHERE nome = 'Usuario de Teste'");
			$id_pes = "";
			if ($result) {
				while ($row = pg_fetch_row($result)) {
					$id_pes .= $row[0].',';
				}
				$id_pes = substr($id_pes,0,strlen($id_pes)-1);
				if (strlen($id_pes) > 0) {
					pg_query($dbconn, "DELETE FROM portal.intranet_segur_permissao_negada WHERE ref_ref_cod_pessoa_fj IN (".$id_pes.")");
					pg_query($dbconn, "DELETE FROM portal.agenda WHERE ref_ref_cod_pessoa_own IN (".$id_pes.")");
					$result = pg_query($dbconn, "SELECT ref_cod_tipo_usuario FROM pmieducar.usuario WHERE cod_usuario IN (".$id_pes.")");
					pg_query($dbconn, "DELETE FROM pmieducar.usuario WHERE cod_usuario IN (".$id_pes.")");
					pg_query($dbconn, "DELETE FROM menu_tipo_usuario WHERE ref_cod_tipo_usuario IN (".$id_pes.")");
					if ($result) {
						$cod_tipo_usuario = "";
						while ($row = pg_fetch_row($result)) {
							$cod_tipo_usuario .= $row[0].',';
						}
						$cod_tipo_usuario = substr($cod_tipo_usuario, 0, strlen($cod_tipo_usuario)-1);
						if (strlen($cod_tipo_usuario) > 0)
							pg_query($dbconn, "DELETE FROM pmieducar.tipo_usuario WHERE cod_tipo_usuario IN (".$cod_tipo_usuario.")");
					}
					pg_query($dbconn, "DELETE FROM portal.menu_funcionario WHERE ref_ref_cod_pessoa_fj IN (".$id_pes.")");
					pg_query($dbconn, "DELETE FROM portal.funcionario WHERE ref_cod_pessoa_fj IN (".$id_pes.")");
					pg_query($dbconn, "DELETE FROM cadastro.endereco_externo WHERE idpes IN (".$id_pes.")");
					pg_query($dbconn, "DELETE FROM cadastro.endereco_pessoa WHERE idpes IN (".$id_pes.")");
					pg_query($dbconn, "DELETE FROM cadastro.fisica WHERE idpes IN (".$id_pes.")");
					pg_query($dbconn, "DELETE FROM cadastro.pessoa WHERE idpes IN (".$id_pes.")");
				}
			}
		}
		if ($event->getStep()->getText() == "Que existe a Escola de Teste") {
			//remove tipos de regime, redes de ensino, localização de escolas, cursos,
			//escolas, pessoa física (Escola de Teste), ceps, bairros e logradouros
			$dbconn = pg_connect("host=localhost port=5432 dbname=ieducar user=ieducar password=ieducar");
			pg_query($dbconn, "DELETE FROM pmieducar.escola_ano_letivo");
			pg_query($dbconn, "DELETE FROM pmieducar.escola_complemento");
			pg_query($dbconn, "DELETE FROM pmieducar.escola_curso");
			pg_query($dbconn, "DELETE FROM pmieducar.curso");
			pg_query($dbconn, "DELETE FROM pmieducar.escola");
			pg_query($dbconn, "DELETE FROM pmieducar.escola_localizacao");
			pg_query($dbconn, "DELETE FROM pmieducar.escola_rede_ensino");
			pg_query($dbconn, "DELETE FROM pmieducar.tipo_regime");
			pg_query($dbconn, "DELETE FROM pmieducar.tipo_ensino");
			pg_query($dbconn, "DELETE FROM pmieducar.nivel_ensino");
			
			$result = pg_query($dbconn, "SELECT idpes FROM cadastro.pessoa WHERE nome = 'Escola de Teste'");
			$id_pes = "";
			if ($result) {
				while ($row = pg_fetch_row($result)) {
					$id_pes .= $row[0].',';
				}
				$id_pes = substr($id_pes,0,strlen($id_pes)-1);
				if (strlen($id_pes) > 0) {
					pg_query($dbconn, "DELETE FROM cadastro.endereco_externo WHERE idpes IN (".$id_pes.")");
					pg_query($dbconn, "DELETE FROM cadastro.endereco_pessoa WHERE idpes IN (".$id_pes.")");
					pg_query($dbconn, "DELETE FROM cadastro.juridica WHERE idpes IN (".$id_pes.")");
					pg_query($dbconn, "DELETE FROM cadastro.pessoa WHERE idpes IN (".$id_pes.")");
				}
			}
			
			pg_query($dbconn, "DELETE FROM urbano.cep_logradouro_bairro");
			pg_query($dbconn, "DELETE FROM urbano.cep_logradouro");
			pg_query($dbconn, "DELETE FROM public.logradouro_fonetico");
			pg_query($dbconn, "DELETE FROM public.logradouro");
			pg_query($dbconn, "DELETE FROM public.bairro");
		}
    }
    
    /**
     * Tira print da tela quando um passo falha.
     * Funciona apenas com o Selenium2Driver.
     *
     * @AfterStep
     */
    public function takeScreenshotAfterFailedStep(Behat\Behat\Event\StepEvent $event) {
    	if (Behat\Behat\Event\StepEvent::FAILED === $event->getResult()) {
    		$driver = $this->getSession()->getDriver();
    		if ($driver instanceof Behat\Mink\Driver\Selenium2Driver) {
    			$step = $event->getStep();
    			$id = $step->getParent()->getTitle() . '.' . $step->getType() . ' ' . $step->getText();
    			$fileName = '/tmp/behat/failed_steps/'.date("Y-m-d_H:i:s").'.'.preg_replace('/[^a-zA-Z0-9-_\.]/','_', $id).'.png';
    			file_put_contents($fileName, $driver->getScreenshot());
    		}
    	}
    }
    
    /**
     * Limpar dados utilizados nos testes.
     * Executado depois de todas as features e cenários.
     *  
     * @AfterSuite @database
     */
    /*public static function teardown(AfterSuiteScope $scope)
    {
    	//TODO limpar dados utilizados nos testes
    }*/
}
