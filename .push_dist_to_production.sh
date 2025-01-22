#!/bin/bash

echo "Iniciando deploy"

echo "Trocando para a branch 'master'..."
git checkout master

echo "Puxandos as mudanças do repositório remoto master..."
git pull origin master

echo "Testando integração"
php vendor/bin/phpunit

echo "Limpando e aquecendo o cache"
php bin/console cache:clear --env=prod --no-warmup
php bin/console cache:warmup --env=prod

SOURCE_PATH="."
BACKUP_PATH="/tmp/deploy_backup"

if [ ! -d "$SOURCE_PATH" ]; then
  echo "Erro: o diretório '$SOURCE_PATH' não foi encontrado."
  exit 1
fi

echo "Criando backup dos arquivos em $BACKUP_PATH..."
rm -rf "$BACKUP_PATH"
mkdir -p "$BACKUP_PATH"
cp -r "$SOURCE_PATH"/* "$BACKUP_PATH"

echo "Trocando para a branch 'production'..."
git checkout production
if [ $? -ne 0 ]; then
  echo "Erro ao trocar para a branch 'production'."
  exit 1
fi

echo "Removendo arquivos do diretório atual..."
rm -rf "$SOURCE_PATH"/*

echo "Restaurando arquivos do backup..."
cp -r "$BACKUP_PATH"/* "$SOURCE_PATH"

echo "Adicionando arquivos ao commit..."
git add -f "$SOURCE_PATH"
git commit -m "Deploy: Atualizando arquivos para produção"

echo "Enviando mudanças para o repositório remoto (branch production)..."
git push origin production
if [ $? -ne 0 ]; then
  echo "Erro ao enviar mudanças para o repositório remoto."
  exit 1
fi

echo "Enviando mudanças para o cPanel..."
git push cpanel
if [ $? -ne 0 ]; then
  echo "Erro ao enviar mudanças para o cPanel."
  exit 1
fi

echo "Limpando arquivos temporários..."
rm -rf "$BACKUP_PATH"

echo "Excluindo branch 'release'..."
git branch -D release

echo "Criando branch 'release'..."
git checkout -b release

echo "Processo de deploy concluído com sucesso."
