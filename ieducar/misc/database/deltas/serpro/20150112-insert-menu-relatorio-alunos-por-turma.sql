INSERT INTO portal.menu_submenu(
        cod_menu_submenu, ref_cod_menu_menu, cod_sistema, nm_submenu, arquivo, 
            title, nivel)
    VALUES (999618,55,2,'Alunos por Turma','module/Reports/AlunosTurma','Relatório de Alunos por Turma',3);

INSERT INTO pmicontrolesis.menu(
            cod_menu, ref_cod_menu_submenu, ref_cod_menu_pai, tt_menu, ord_menu, 
            caminho, alvo, suprime_menu, ref_cod_tutormenu, ref_cod_ico)
    VALUES (999618,999618,999300,'Alunos por Turma',2,'module/Reports/AlunosTurma','_self',1,15,190);