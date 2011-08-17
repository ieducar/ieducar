-- //

--
-- Adiciona o item "Registro de Abandono" no menu
--
-- @author   Diogo Rissi <diogo@dccobra.com.br>
-- @license  @@license@@
-- @version  $Id$
--

INSERT INTO portal.menu_submenu ( cod_menu_submenu, ref_cod_menu_menu, cod_sistema, nm_submenu, arquivo, title, nivel) VALUES (999 ,55, 2, 'Registro de Abandono', 'educar_relatorio_registro_abandono.php', '', '3');

INSERT INTO pmicontrolesis.menu (tt_menu, ord_menu , ref_cod_menu_submenu, ref_cod_menu_pai, ref_cod_ico, caminho, alvo, suprime_menu, ref_cod_tutormenu) VALUES ('Registro de Abandono', '26' , 999, (SELECT cod_menu FROM pmicontrolesis.menu WHERE tt_menu = convert('Relatórios', 'UTF8', 'LATIN1') AND ref_cod_menu_pai IS NULL) , '1' , 'educar_relatorio_registro_abandono.php' , '_self' , '1' , '15' );

SELECT currval('pmicontrolesis.menu_cod_menu_seq'::text);

-- //@UNDO

DELETE FROM pmicontrolesis.menu WHERE tt_menu = 'Registro de Abandono';

DELETE FROM portal.menu_submenu WHERE cod_menu_submenu = 999;

-- //
