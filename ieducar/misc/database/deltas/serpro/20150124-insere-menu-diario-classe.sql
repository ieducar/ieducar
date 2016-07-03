INSERT INTO portal.menu_submenu (
  cod_menu_submenu, 
  ref_cod_menu_menu, 
  cod_sistema, 
  nm_submenu, 
  arquivo, 
  title, 
  nivel
 ) VALUES (
  999700,
  55,
  2,
  'Diário de Classe',
  'module/Reports/DiarioClasse',
  'Diário de Classe',
  3
);

INSERT INTO pmicontrolesis.menu (
  cod_menu, 
  ref_cod_menu_pai, 
  tt_menu, 
  ord_menu, 
  alvo, 
  suprime_menu, 
  ref_cod_tutormenu, 
  ref_cod_ico
 ) VALUES (
  999700,
  21127,
  'Diários',
  4,
  '_self',
  1,
  15,
  21
);

INSERT INTO pmicontrolesis.menu (
  cod_menu, 
  ref_cod_menu_submenu, 
  ref_cod_menu_pai, 
  tt_menu, 
  ord_menu, 
  caminho, 
  alvo, 
  suprime_menu, 
  ref_cod_tutormenu, 
  ref_cod_ico
 ) VALUES (
  999701,
  999700,
  999700,
  'Diário de Classe',
  1,
  'module/Reports/DiarioClasse',
  '_self',
  1,
  15,
  190
);
