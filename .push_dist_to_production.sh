#!/bin/bash

set -e

SOURCE_PATH="."
BACKUP_PATH="/tmp/deploy_backup"

echo "Iniciando deploy"

echo "Trocando para a branch 'master'..."
git checkout master
git pull origin master

if [ ! -d "$SOURCE_PATH" ]; then
  echo "Erro: o diretório '$SOURCE_PATH' não foi encontrado."
  exit 1
fi

echo "Criando backup dos arquivos em $BACKUP_PATH..."
rm -rf "$BACKUP_PATH"
mkdir -p "$BACKUP_PATH"
cp -r "$SOURCE_PATH"/* "$BACKUP_PATH"

echo "Excluindo a branch 'production' local..."
git branch -D production || echo "Branch 'production' não existe localmente."

echo "Excluindo a branch 'production' remota no GitHub..."
git push origin --delete production || echo "Branch 'production' não existe remotamente no GitHub."

echo "Forçando exclusão da branch 'production' remota no cPanel..."
git push cpanel --delete production || echo "Falha ao excluir a branch remota no cPanel. Certifique-se de que 'receive.denyDeleteCurrent' está configurado para 'warn'."

echo "Recriando a branch 'production' baseada na 'master'..."
git checkout -b production
git push origin production
git push cpanel production

echo "Restaurando arquivos do backup..."
rm -rf "$SOURCE_PATH"/*
cp -r "$BACKUP_PATH"/* "$SOURCE_PATH"

if git diff --quiet; then
  echo "Nenhuma alteração detectada. Nada a commitar."
else
  echo "Adicionando arquivos ao commit..."
  git add -A
  git commit -m "Deploy: Atualizando arquivos para produção"
fi

echo "Enviando mudanças para o repositório remoto (branch production no GitHub)..."
git push origin production

echo "Enviando mudanças para o repositório remoto (branch production no cPanel)..."
git push cpanel production

echo "Limpando arquivos temporários..."
rm -rf "$BACKUP_PATH"

echo "Atualizando a branch 'release'..."
git checkout release || git checkout -b release
git merge production

echo "Processo de deploy concluído com sucesso."
