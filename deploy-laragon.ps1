#Requires -Version 5.1

param(
    [switch]$Fresh,
    [switch]$SkipSeed,
    [string]$Branch = "claude/laravel-sports-betting-bvty6"
)

$ErrorActionPreference = "Stop"

function Write-Step { param($msg) Write-Host "" ; Write-Host "[>>] $msg" -ForegroundColor Cyan }
function Write-OK   { param($msg) Write-Host "[OK] $msg" -ForegroundColor Green }
function Write-Warn { param($msg) Write-Host "[!!] $msg" -ForegroundColor Yellow }
function Write-Fail { param($msg) Write-Host "[XX] $msg" -ForegroundColor Red ; exit 1 }

Write-Host ""
Write-Host "  ============================================" -ForegroundColor Yellow
Write-Host "  Apuestas Deportivas - Deploy para Laragon  " -ForegroundColor Yellow
Write-Host "  ============================================" -ForegroundColor Yellow
Write-Host ""

$ProjectName = "ApuestasDeportivas"
$LaragonRoot = "C:\laragon\www"
$ProjectPath = Join-Path $LaragonRoot $ProjectName
$RepoUrl     = "https://github.com/jlmg22204/ApuestasDeportivas.git"

# Verificar herramientas
Write-Step "Verificando herramientas..."

foreach ($tool in @("php", "composer", "git")) {
    if (-not (Get-Command $tool -ErrorAction SilentlyContinue)) {
        Write-Fail "$tool no encontrado en PATH. Abre la terminal de Laragon."
    }
    Write-OK "$tool encontrado"
}

$phpVer = php -r "echo PHP_VERSION;"
Write-OK "PHP $phpVer"

# Clonar o actualizar repo
Write-Step "Preparando codigo fuente..."

$isNewInstall = $false

if ($Fresh -and (Test-Path $ProjectPath)) {
    Write-Warn "Modo Fresh: eliminando instalacion anterior..."
    Remove-Item $ProjectPath -Recurse -Force
}

$gitDir = Join-Path $ProjectPath ".git"

if (Test-Path $gitDir) {
    Write-OK "Proyecto existente detectado. Actualizando rama $Branch..."
    Set-Location $ProjectPath
    git fetch origin
    git checkout $Branch
    git pull origin $Branch
} else {
    Write-OK "Clonando repositorio desde rama $Branch..."
    if (-not (Test-Path $LaragonRoot)) {
        New-Item -ItemType Directory -Path $LaragonRoot | Out-Null
    }
    git clone -b $Branch $RepoUrl $ProjectPath
    Set-Location $ProjectPath
    $isNewInstall = $true
}

Write-OK "Codigo fuente listo en $ProjectPath"

# Dependencias Composer
Write-Step "Instalando dependencias de Composer..."
composer install --no-dev --optimize-autoloader --no-interaction
if ($LASTEXITCODE -ne 0) {
    Write-Warn "Reintentando sin scripts..."
    composer install --no-dev --no-scripts --no-interaction
}
Write-OK "Dependencias instaladas"

# Configurar .env
Write-Step "Configurando entorno..."

if (-not (Test-Path ".env")) {
    Copy-Item ".env.example" ".env"
    $env = Get-Content ".env" -Raw
    $env = $env -replace 'APP_NAME=.*',      'APP_NAME="Apuestas Deportivas"'
    $env = $env -replace 'APP_URL=.*',       "APP_URL=http://$ProjectName.test"
    $env = $env -replace 'DB_CONNECTION=.*', 'DB_CONNECTION=sqlite'
    Set-Content ".env" $env
    Write-OK ".env creado y configurado para Laragon"
} else {
    Write-OK ".env ya existe, no se sobreescribe"
}

# Base de datos SQLite
$dbPath = "database\database.sqlite"
if (-not (Test-Path $dbPath)) {
    New-Item $dbPath -ItemType File | Out-Null
    Write-OK "Archivo SQLite creado"
}

# APP_KEY
$keyLine = Get-Content ".env" | Where-Object { $_ -match "^APP_KEY=base64:" }
if (-not $keyLine) {
    Write-Step "Generando clave de aplicacion..."
    php artisan key:generate --ansi
}

# Migraciones
Write-Step "Ejecutando migraciones..."

if ($Fresh -or $isNewInstall) {
    if ($SkipSeed) {
        php artisan migrate:fresh --force
        Write-OK "Migraciones ejecutadas (sin seeders)"
    } else {
        php artisan migrate:fresh --seed --force
        Write-OK "Migraciones y seeders ejecutados"
    }
} else {
    php artisan migrate --force
    Write-OK "Migraciones aplicadas"
}

# Storage link
Write-Step "Creando enlace de storage..."
php artisan storage:link --force 2>&1 | Out-Null
Write-OK "Storage link creado"

# Optimizar cache
Write-Step "Optimizando la aplicacion..."
php artisan optimize:clear | Out-Null
php artisan config:cache   | Out-Null
php artisan route:cache    | Out-Null
php artisan view:cache     | Out-Null
Write-OK "Cache optimizada"

# Resumen final
Write-Host ""
Write-Host "  ============================================" -ForegroundColor Green
Write-Host "  Deploy completado exitosamente!            " -ForegroundColor Green
Write-Host "  ============================================" -ForegroundColor Green
Write-Host ""
Write-Host "  URL:   http://$ProjectName.test" -ForegroundColor Cyan
Write-Host "  Ruta:  $ProjectPath" -ForegroundColor Cyan
Write-Host ""
Write-Host "  Acceso Admin  : admin@apuestas.com / password" -ForegroundColor Yellow
Write-Host "  Acceso Usuario: demo@apuestas.com  / password" -ForegroundColor Yellow
Write-Host ""

if ($isNewInstall) {
    Write-Host "  NOTA: Laragon genera el virtual host automaticamente." -ForegroundColor DarkCyan
    Write-Host "        Si no aparece en el menu, reinicia Laragon." -ForegroundColor DarkCyan
    Write-Host ""
}
