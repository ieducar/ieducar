#language: pt

Funcionalidade: Tipos de usuário
	Como gestor público educacional
	Preciso de perfis de acesso ao sistema
	Para limitar os acessos dos usuários às funcionalidades do sistema.

	@mink:selenium2
	Cenário: Criar tipo de usuário (perfil de acesso)
		Dado Que eu estou autenticado como Administrador
		E estou em "/intranet/educar_tipo_usuario_lst.php"
		E pressiono "Novo"
		E preencho "nm_tipo" com "Teste de inclusão perfil de acesso"
		E seleciono "Escola" de "nivel"
		E seleciono "Desmarcar Todos" de "todos"
		E pressiono "Salvar"
		Então Eu devo ver "Teste de inclusão perfil de acesso"

	@mink:selenium2
	Cenário: Editar tipo de usuário criado (perfil de acesso)
		Dado Que eu estou autenticado como Administrador
		E estou em "/intranet/educar_tipo_usuario_lst.php"
		E sigo o link "Teste de inclusão perfil de acesso"
		E pressiono "Editar"
		E preencho "descricao" com "Inclusão de perfil realizado com testes automatizados"
		E pressiono "Salvar"
		Então Eu devo ver "Teste de inclusão perfil de acesso"
		
	@mink:selenium2
	Cenário: Remover tipo de usuário criado (perfil de acesso)
		Dado Que eu estou autenticado como Administrador
		E estou em "/intranet/educar_tipo_usuario_lst.php"
		E sigo o link "Teste de inclusão perfil de acesso"
		E pressiono "Editar"
		E pressiono "Excluir"
		E confirmo o popup
		Então Eu não devo ver "Teste de inclusão perfil de acesso"
	
	@mink:selenium2
	Cenário: Alterar Tipo de Usuário reflete nas permissões dos perfis de acesso
		Dado Que existe o Usuario de Teste 
		E estou autenticado como Administrador
		E estou em "/intranet/educar_tipo_usuario_lst.php"
		E pressiono "Novo"
		E preencho "nm_tipo" com "Teste alteração perfil"
		E preencho "descricao" com "Perfil criado para garantir que alteração de permissões refletem automaticamente"
		E seleciono "Escola" de "nivel"
		E seleciono "Marcar Todos" de "todos"
		E pressiono "Salvar"
		Então Eu devo ver "Teste alteração perfil"

		Dado Que eu estou autenticado como Administrador
		E Que existe a Escola de Teste
		E estou em "/intranet/educar_usuario_lst.php"
		E pressiono "Novo"
		E clico na imagem "Pesquisa"
		E estou navegando no frame "temp_win_popless"
		E preencho "campo_busca" com "Usuario de Teste"
		E pressiono "busca"
		E sigo o link "Usuario de Teste"
		E foco na janela principal
		E seleciono "Teste alteração perfil" de "ref_cod_tipo_usuario"
		E seleciono "Serviço Federal de Processamento de Dados - SERPRO" de "ref_cod_instituicao"
		E seleciono "Escola de Teste" de "ref_cod_escola"
		E pressiono "Salvar"
		Então devo ver "Usuário - Listagem"

		Dado Que eu estou autenticado como Usuario de Teste
		E estou em "/intranet/educar_curso_lst.php"
		Então devo ver "Curso - Listagem"
		
		Dado estou em "/intranet/educar_falta_nota_aluno_lst.php"
		Então devo ver "Faltas/Notas Aluno - Listagem"
		
		Dado Que eu estou autenticado como Administrador
		E estou em "/intranet/educar_tipo_usuario_lst.php"
		E sigo o link "Teste alteração perfil"
		E pressiono "Editar"
		E seleciono "Desmarcar Todos" de "todos"
		E pressiono "Salvar"
		Então devo ver "Teste alteração perfil"
		
		Dado Que eu estou autenticado como Usuario de Teste
		E estou em "/intranet/educar_curso_lst.php"
		Então devo ver "Acesso negado para este usuário."
		
		Dado Eu estou em "/intranet/educar_falta_nota_aluno_lst.php"
		Então devo ver "Acesso negado para este usuário."