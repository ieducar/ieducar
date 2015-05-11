#language: pt

Funcionalidade: Login
	Como um usuário
	Eu quero me logar no sistema utilizando minhas credenciais
	Para que eu possa utilizar o i-Educar

	@mink:selenium2
	Cenário: Logar com as credenciais corretas
	    Dado Eu estou em "/index.php"
	    E Eu estou navegando no frame "central"
	    Quando Eu preencho "login" com "admin"
	    E Eu preencho "senha" com "ieducar@serpro"
	    E Eu pressiono "Entrar"
    	Então Eu devo ver "Usuário atual: Administrador"
    
	@mink:selenium2
	Cenário: Não logar utilizando credenciais incorretas
		Dado estou em "/index.php"
		E Eu estou navegando no frame "central"
		Quando preencho "login" com "bruno"
		E Eu preencho "senha" com "ieducar@bruno"
		E Eu pressiono "Entrar"
		Então Eu devo ver "Usuário ou senha incorreta."
		
	Cenário: Logar com as credenciais corretas através diretamente pelo endereço do frame
		Dado Eu estou em "intranet/index.php"
		Quando Eu preencho "login" com "admin"
	    E Eu preencho "senha" com "ieducar@serpro"
	    E Eu pressiono "Entrar"
    	Então Eu devo ver "Usuário atual: Administrador"