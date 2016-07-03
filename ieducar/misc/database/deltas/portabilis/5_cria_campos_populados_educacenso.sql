 	-- //

 	--
 	-- Popula as tabelas escola_localizacao, cadastro.deficiencia ,   
 	-- modules.educacenso_cod_turma.
 	-- @author   Ricardo Bortolotto Dagostim <ricardo@portabilis.com.br>
 	-- @license  @@license@@
 	-- @version  $Id$
 	--

	insert into pmieducar.escola_localizacao values(1,NULL,1,'Urbana',current_timestamp,NULL,1,1);
	insert into pmieducar.escola_localizacao values(2,NULL,1,'Rural',current_timestamp,NULL,1,1);
    --
	insert into cadastro.deficiencia values(1,'Nenhuma');
	insert into cadastro.deficiencia values(2,'Cegueira');
	insert into cadastro.deficiencia values(3,'Baixa Vis�o');
	insert into cadastro.deficiencia values(4,'Surdez');
	insert into cadastro.deficiencia values(5,'Defici�ncia Auditiva');
	insert into cadastro.deficiencia values(6,'Surdocegueira');
	insert into cadastro.deficiencia values(7,'Defici�ncia F�sica');
	insert into cadastro.deficiencia values(8,'Defici�ncia Mental');
	insert into cadastro.deficiencia values(9,'Defici�ncia M�ltipla');
	insert into cadastro.deficiencia values(10,'Autismo Cl�ssico');
	insert into cadastro.deficiencia values(11,'S�ndrome de Asperger');
	insert into cadastro.deficiencia values(12,'S�ndrome de Rett');
	insert into cadastro.deficiencia values(13,'Transtorno desintegrativo da inf�ncia (psicose infantil)');
	insert into cadastro.deficiencia values(14,'Altas Habilidades/Superdota��o');

	--
	
	insert into cadastro.raca values(1,NULL,1,'Branca',current_timestamp,NULL,'t');
	insert into cadastro.raca values(2,NULL,1,'Preta',current_timestamp,NULL,'t');
	insert into cadastro.raca values(3,NULL,1,'Parda',current_timestamp,NULL,'t');
	insert into cadastro.raca values(4,NULL,1,'Amarela',current_timestamp,NULL,'t');
	insert into cadastro.raca values(5,NULL,1,'Ind�gena',current_timestamp,NULL,'t');
	insert into cadastro.raca values(6,NULL,1,'N�o Declarada',current_timestamp,NULL,'t');


	--

	insert into cadastro.escolaridade values(1,'Fundamental Incompleto');
	insert into cadastro.escolaridade values(2,'Fundamental Completo');
	insert into cadastro.escolaridade values(3,'Ensino M�dio (Normal/Magist�rio)');
	insert into cadastro.escolaridade values(4,'Ensino M�dio (Normal/Magist�rio Ind�gena)');
	insert into cadastro.escolaridade values(5,'Ensino M�dio');
	insert into cadastro.escolaridade values(6,'Superior Completo');

 	-- //@UNDO
	
    delete from pmieducar.escola_localizacao where cod_escola_localizacao in(1,2);
	
	delete from cadastro.deficiencia where cod_deficiencia in(1,2,3,4,5,6,7,8,9,10,11,12,13,14);
	
 	delete from cadastro.raca where cod_raca in(1,2,3,4,5,6);
	
 	delete from cadastro.escolaridade where idesco in(1,2,3,4,5,6);

 	-- //		