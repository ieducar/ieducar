-- Altera o search path.

ALTER DATABASE ieducar SET search_path = "$user", public, portal, cadastro, acesso, alimentos, consistenciacao, historico, pmiacoes, pmicontrolesis, pmidrh, pmieducar, pmiotopic, urbano, modules;

-- Trim de tipo Date, removido nas versões mais novas.
CREATE FUNCTION pg_catalog.btrim(date) RETURNS TEXT LANGUAGE SQL STABLE
as $f$ 
  SELECT trim($1::text); 
$f$;
