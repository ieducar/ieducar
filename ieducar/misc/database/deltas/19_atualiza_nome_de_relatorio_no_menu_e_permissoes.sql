-- //

--
-- @author   Eriksen Costa Paix�o <eriksen.paixao_bs@cobra.com.br>
-- @license  @@license@@
-- @version  $Id$
--

UPDATE portal.menu_submenu SET nm_submenu = 'Alunos em Exame' WHERE cod_menu_submenu = 917;
UPDATE pmicontrolesis.menu SET tt_menu = 'Alunos em Exame' WHERE cod_menu = 21184;

-- //@UNDO

UPDATE portal.menu_submenu SET nm_submenu = 'Alunos 5� Avalia��o' WHERE cod_menu_submenu = 917;
UPDATE pmicontrolesis.menu SET tt_menu = 'Alunos 5� Avalia��o' WHERE cod_menu = 21184;

-- //