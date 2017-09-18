-- //

--
-- Define as permiss�es padr�es que o usu�rio do tipo Biblioteca ter�. Essas
-- permiss�es estavam ausentes e, juntamente com o bug relatado no 
-- {@link http://svn.softwarepublico.gov.br/trac/ieducar/ticket/41 ticket 41},
-- criava a dificuldade do usu�rio administrador criar usu�rios para o m�dulo
-- Biblioteca.
--
-- Todas as permiss�es existentes s�o atribu�das ao tipo, com permiss�o para
-- cadastro e exclus�o, exceto para a funcionalidade "Biblioteca".
--
-- Esse delta exclui todas as permis�es para o tipo referenciado de valor 3,
-- ent�o, caso tenha dado outra sem�ntica para esse tipo de usu�rio, 
-- desconsidere esse delta.
--
-- @author   Eriksen Costa Paix�o <eriksen.paixao_bs@cobra.com.br>
-- @license  http://creativecommons.org/licenses/GPL/2.0/legalcode.pt  CC GNU GPL
-- @version  $Id$
--

DELETE FROM pmieducar.menu_tipo_usuario WHERE ref_cod_tipo_usuario = 3;

INSERT INTO pmieducar.menu_tipo_usuario (ref_cod_tipo_usuario, ref_cod_menu_submenu, cadastra, visualiza, exclui) VALUES (3, 625, 1, 1, 1);
INSERT INTO pmieducar.menu_tipo_usuario (ref_cod_tipo_usuario, ref_cod_menu_submenu, cadastra, visualiza, exclui) VALUES (3, 592, 1, 1, 1);
INSERT INTO pmieducar.menu_tipo_usuario (ref_cod_tipo_usuario, ref_cod_menu_submenu, cadastra, visualiza, exclui) VALUES (3, 594, 1, 1, 1);
INSERT INTO pmieducar.menu_tipo_usuario (ref_cod_tipo_usuario, ref_cod_menu_submenu, cadastra, visualiza, exclui) VALUES (3, 591, 0, 1, 0);
INSERT INTO pmieducar.menu_tipo_usuario (ref_cod_tipo_usuario, ref_cod_menu_submenu, cadastra, visualiza, exclui) VALUES (3, 603, 1, 1, 1);
INSERT INTO pmieducar.menu_tipo_usuario (ref_cod_tipo_usuario, ref_cod_menu_submenu, cadastra, visualiza, exclui) VALUES (3, 593, 1, 1, 1);
INSERT INTO pmieducar.menu_tipo_usuario (ref_cod_tipo_usuario, ref_cod_menu_submenu, cadastra, visualiza, exclui) VALUES (3, 629, 1, 1, 1);
INSERT INTO pmieducar.menu_tipo_usuario (ref_cod_tipo_usuario, ref_cod_menu_submenu, cadastra, visualiza, exclui) VALUES (3, 628, 1, 1, 1);
INSERT INTO pmieducar.menu_tipo_usuario (ref_cod_tipo_usuario, ref_cod_menu_submenu, cadastra, visualiza, exclui) VALUES (3, 622, 1, 1, 1);
INSERT INTO pmieducar.menu_tipo_usuario (ref_cod_tipo_usuario, ref_cod_menu_submenu, cadastra, visualiza, exclui) VALUES (3, 595, 1, 1, 1);
INSERT INTO pmieducar.menu_tipo_usuario (ref_cod_tipo_usuario, ref_cod_menu_submenu, cadastra, visualiza, exclui) VALUES (3, 610, 1, 1, 1);
INSERT INTO pmieducar.menu_tipo_usuario (ref_cod_tipo_usuario, ref_cod_menu_submenu, cadastra, visualiza, exclui) VALUES (3, 606, 1, 1, 1);
INSERT INTO pmieducar.menu_tipo_usuario (ref_cod_tipo_usuario, ref_cod_menu_submenu, cadastra, visualiza, exclui) VALUES (3, 608, 1, 1, 1);
INSERT INTO pmieducar.menu_tipo_usuario (ref_cod_tipo_usuario, ref_cod_menu_submenu, cadastra, visualiza, exclui) VALUES (3, 590, 1, 1, 1);
INSERT INTO pmieducar.menu_tipo_usuario (ref_cod_tipo_usuario, ref_cod_menu_submenu, cadastra, visualiza, exclui) VALUES (3, 600, 1, 1, 1);
INSERT INTO pmieducar.menu_tipo_usuario (ref_cod_tipo_usuario, ref_cod_menu_submenu, cadastra, visualiza, exclui) VALUES (3, 607, 1, 1, 1);
INSERT INTO pmieducar.menu_tipo_usuario (ref_cod_tipo_usuario, ref_cod_menu_submenu, cadastra, visualiza, exclui) VALUES (3, 598, 1, 1, 1);
INSERT INTO pmieducar.menu_tipo_usuario (ref_cod_tipo_usuario, ref_cod_menu_submenu, cadastra, visualiza, exclui) VALUES (3, 609, 1, 1, 1);
INSERT INTO pmieducar.menu_tipo_usuario (ref_cod_tipo_usuario, ref_cod_menu_submenu, cadastra, visualiza, exclui) VALUES (3, 602, 1, 1, 1);
INSERT INTO pmieducar.menu_tipo_usuario (ref_cod_tipo_usuario, ref_cod_menu_submenu, cadastra, visualiza, exclui) VALUES (3, 596, 1, 1, 1);
INSERT INTO pmieducar.menu_tipo_usuario (ref_cod_tipo_usuario, ref_cod_menu_submenu, cadastra, visualiza, exclui) VALUES (3, 597, 1, 1, 1);

-- //@UNDO

-- //
