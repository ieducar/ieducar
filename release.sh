#!/bin/bash

# Gera um release do i-Educar próprio para se repassar
# para a equipe de produção.
# a partir do diretório deste arquivo, rode
# $ release.sh [arquivo de saída]

current_commit_id=`git rev-list --all --abbrev=0 --abbrev-commit --max-count=1`
current_date=`date '+%Y%m%d'`
release_version="$current_date.$current_commit_id"

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
