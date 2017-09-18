-- //

--
-- Remove a obrigatoriedade de F�rmula de M�dia para uma Regra de Avalia��o,
-- possibilitando que o campo contenha o valor NULL.
--
-- @author   Eriksen Costa <eriksencosta@gmail.com>
-- @license  @@license@@
-- @version  $Id$
--

ALTER TABLE modules.regra_avaliacao
   ALTER COLUMN formula_media_id SET DEFAULT NULL;
ALTER TABLE modules.regra_avaliacao
   ALTER COLUMN formula_media_id DROP NOT NULL;

-- //@UNDO

-- N�o � o ideal, j� que esse pode ser de uma institui��o diferente. Mas como a
-- necessidade de um rollback � muito remota e precisamos satisfazer uma
-- foreign key, pegamos o primeiro id dispon�vel.
UPDATE
  modules.regra_avaliacao
SET
  formula_media_id = (SELECT id FROM modules.formula_media OFFSET 0 LIMIT 1)
WHERE
  formula_media_id IS NULL;

ALTER TABLE modules.regra_avaliacao
   ALTER COLUMN formula_media_id DROP DEFAULT;
ALTER TABLE modules.regra_avaliacao
   ALTER COLUMN formula_media_id SET NOT NULL;

-- //