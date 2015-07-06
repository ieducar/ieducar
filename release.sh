#!/bin/bash

# Gera um arquivo com o conteúdo do código-fonte, numerando a versão
# de acordo com a data do último commit e a short hash dele.
# Para executar, rode:
# $ release.sh

last_commit_id=`git show -s --format=%h`
last_commit_date=$(date -d "@`git show -s --format=%ct`" "+%Y%m%d")
release_version="$last_commit_date-$last_commit_id"

workdir="/tmp/ieducar-$release_version-$$"
output_file="/tmp/ieducar-$release_version.tar.gz"

mkdir "$workdir"

# git archive para exportar o que é relevante para a gente
# Após o pipe ele descompacta a partir do gerado, para adicionar mais coisas.
git archive --format=tar origin/master | (cd "$workdir" && tar -xf -)

# Adiciona o arquivo com o número de versão
echo "$release_version" > "$workdir/ieducar/version.txt"

tar -C "$workdir" -cz ieducar/ > "$output_file"

rm -rf "$workdir"

echo "Gerado arquivo $output_file ."
