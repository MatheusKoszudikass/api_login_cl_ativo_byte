# Caminho para o diretório raiz do seu projeto
$rootDir = "D:/DevWeb/source/Project/AtivoByte/api_login_cl_ativo_byte\"

# Encontra todos os diretórios "vendor"
$directories = Get-ChildItem -Path $rootDir -Recurse -Directory -Filter "vendor"

# Adiciona cada diretório encontrado como seguro no Git
foreach ($dir in $directories) {
    $path = $dir.FullName
    Write-Output "Adicionando $path como um diretório seguro..."
    git config --global --add safe.directory "$path"
}

Write-Output "Todos os diretórios 'vendor' foram adicionados como seguros."
