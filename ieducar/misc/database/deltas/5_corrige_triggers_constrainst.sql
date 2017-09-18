-- //

--
-- Restaura triggers e constraints das tabelas public.municipio e public.uf
-- que foram totalmente desabilitadas pelos scripts de limpeza de banco de
-- dados. O schema de todos os comandos � o public.
--
-- Nenhum efeito colateral foi relatado por conta dessas remo��es. Este
-- delta visa apenas a reestabelecer a eventual consist�ncia que estas
-- triggers e constraint d�o.
--
-- Para visualizar a diferen�a e ter um n�mero que indique a quantidade
-- de triggers habilitadas, execute a seguinte query em uma base do
-- i-Educar 1.0.2 (rev. 57) e outra do primeiro beta do i-Educar 1.1.0
-- (rev. 80). Substitua ? por 'municipio' e depois por 'uf'.
--
-- Quantidades esperadas (1.0.2 e 1.1.0-beta1, respectivamente):
-- * uf: 16/12
-- * municipio: 26/6
--
-- <code>
-- (SELECT
-- count(pg_trigger.*)
-- FROM
--   pg_catalog.pg_class, pg_catalog.pg_trigger
-- WHERE
--   pg_class.relname = '?'
--   AND pg_class.oid = pg_trigger.tgrelid)
-- <code>
--
-- O script que n�o reestabeleceu as triggers e constraints foi o da
-- terceira vers�o (2_populate_basic_data.sql@6516), dispon�vel no ticket #37:
-- {@link http://svn.softwarepublico.gov.br/trac/ieducar/ticket/37#change_1}.
--
-- @author   Eriksen Costa Paix�o <eriksen.paixao_bs@cobra.com.br>
-- @license  http://creativecommons.org/licenses/GPL/2.0/legalcode.pt  CC GNU GPL
-- @version  $Id$
--

CREATE TRIGGER trg_bef_municipio_historico
    BEFORE UPDATE ON municipio
    FOR EACH ROW
    EXECUTE PROCEDURE historico.fcn_grava_historico_municipio();

CREATE TRIGGER trg_delete_municipio_historico
    AFTER DELETE ON municipio
    FOR EACH ROW
    EXECUTE PROCEDURE historico.fcn_delete_grava_historico_municipio();

ALTER TABLE ONLY municipio
    ADD CONSTRAINT fk_logradouro_municipio FOREIGN KEY (idmun) REFERENCES municipio(idmun);

ALTER TABLE ONLY municipio
    ADD CONSTRAINT fk_municipio_municipiopai FOREIGN KEY (idmun_pai) REFERENCES municipio(idmun);

ALTER TABLE ONLY municipio
    ADD CONSTRAINT fk_municipio_sistema_idpes_cad FOREIGN KEY (idpes_cad) REFERENCES cadastro.pessoa(idpes) ON DELETE SET NULL;

ALTER TABLE ONLY municipio
    ADD CONSTRAINT fk_municipio_sistema_idpes_rev FOREIGN KEY (idpes_rev) REFERENCES cadastro.pessoa(idpes) ON DELETE SET NULL;

ALTER TABLE ONLY municipio
    ADD CONSTRAINT fk_municipio_sistema_idsis_cad FOREIGN KEY (idsis_cad) REFERENCES acesso.sistema(idsis) ON DELETE SET NULL;

ALTER TABLE ONLY municipio
    ADD CONSTRAINT fk_municipio_sistema_idsis_rev FOREIGN KEY (idsis_rev) REFERENCES acesso.sistema(idsis) ON DELETE SET NULL;

ALTER TABLE ONLY municipio
    ADD CONSTRAINT fk_municipio_uf FOREIGN KEY (sigla_uf) REFERENCES uf(sigla_uf);

ALTER TABLE ONLY uf
    ADD CONSTRAINT fk_uf_pais FOREIGN KEY (idpais) REFERENCES pais(idpais);

-- //@UNDO

-- //
