#Requires -Version 5.1
<#
.SYNOPSIS
    Script de deploy para Apuestas Deportivas en Laragon (Windows)
.DESCRIPTION
    Clona o actualiza el proyecto, instala dependencias, configura
    el entorno y ejecuta migraciones automáticamente.
.EXAMPLE
    .\deploy-laragon.ps1
    .\deploy-laragon.ps1 -Fresh        # Reinstalacion completa
    .\deploy-laragon.ps1 -SkipSeed     # Actualizar sin ejecutar seeders
#>

param(
    [switch]$Fresh,
    [switch]$SkipSeed,
    [string]$Branch = "main"
)

$ErrorActionPreference = "Stop"

# ── Colores ─────────────────────────────────────────────────────────────────
function Write-Step  { param($msg) Write-Host "`n[>>] $msg" -ForegroundColor Cyan }
function Write-OK    { param($msg) Write-Host "[OK] $msg"   -ForegroundColor Green }
function Write-Warn  { param($msg) Write-Host "[!!] $msg"   -ForegroundColor Yellow }
function Write-Fail  { param($msg) Write-Host "[XX] $msg"   -ForegroundColor Red; exit 1 }

# ── Banner ───────────────────────────────────────────────────────────────────
Write-Host ""
Write-Host "  =============================================" -ForegroundColor Yellow
Write-Host "   Apuestas Deportivas — Deploy para Laragon  " -ForegroundColor Yellow
Write-Host "  =============================================" -ForegroundColor Yellow
Write-Host ""

# ── Configuración ────────────────────────────────────────────────────────────
$ProjectName  = "ApuestasDeportivas"
$LaragonRoot  = "C:\laragon\www"
$ProjectPath  = Join-Path $LaragonRoot $ProjectName
$RepoUrl      = "https://github.com/jlmg22204/ApuestasDeportivas.git"

# ── Verificar herramientas ───────────────────────────────────────────────────
Write-Step "Verificando herramientas..."

@("php", "composer", "git") | ForEach-Object {
    if (-not (Get-Command $_ -ErrorAction SilentlyContinue)) {
        Write-Fail "$_ no está en PATH. Abre la terminal de Laragon o agrega Laragon al PATH del sistema."
    }
    Write-OK "$_ encontrado"
}

$phpVersion = php -r "echo PHP_VERSION;"
Write-OK "PHP $phpVersion"

# ── Clonar o actualizar ──────────────────────────────────────────────────────
Write-Step "Preparando código fuente..."

$isNewInstall = $false

if ($Fresh -and (Test-Path $ProjectPath)) {
    Write-Warn "Modo Fresh: eliminando instalación anterior..."
    Remove-Item $ProjectPath -Recurse -Force
}

if (Test-Path (Join-Path $ProjectPath ".git")) {
    Write-OK "Proyecto existente detectado — actualizando desde $Branch..."
    Set-Location $ProjectPath
    git fetch origin 2>&1 | Out-Null
    git checkout $Branch 2>&1
    git pull origin $Branch 2>&1
} else {
    Write-OK "Clonando repositorio..."
    if (-not (Test-Path $LaragonRoot)) { New-Item -ItemType Directory -Path $LaragonRoot | Out-Null }
    git clone -b $Branch $RepoUrl $ProjectPath
    Set-Location $ProjectPath
    $isNewInstall = $true
}

Write-OK "Código fuente listo en $ProjectPath"

# ── Dependencias ─────────────────────────────────────────────────────────────
Write-Step "Instalando dependencias de Composer..."
composer install --no-dev --optimize-autoloader --no-interaction
if ($LASTEXITCODE -ne 0) {
    Write-Warn "Reintentando sin scripts..."
    composer install --no-dev --no-scripts --no-interaction
}
Write-OK "Dependencias instaladas"

# ── Archivo .env ─────────────────────────────────────────────────────────────
Write-Step "Configurando entorno..."

if (-not (Test-Path ".env")) {
    Copy-Item ".env.example" ".env"
    Write-OK ".env creado desde .env.example"

    # Configurar valores para Laragon
    $envContent = Get-Content ".env" -Raw
    $envContent = $envContent -replace 'APP_NAME=.*',      'APP_NAME="Apuestas Deportivas"'
    $envContent = $envContent -replace 'APP_URL=.*',       "APP_URL=http://$ProjectName.test"
    $envContent = $envContent -replace 'DB_CONNECTION=.*', 'DB_CONNECTION=sqlite'
    Set-Content ".env" $envContent
    Write-OK ".env configurado para Laragon"
} else {
    Write-OK ".env ya existe — no se sobreescribe"
}

# ── Base de datos SQLite ─────────────────────────────────────────────────────
$sqlitePath = "database\database.sqlite"
if (-not (Test-Path $sqlitePath)) {
    New-Item $sqlitePath -ItemType File | Out-Null
    Write-OK "Archivo SQLite creado"
}

# ── APP_KEY ──────────────────────────────────────────────────────────────────
$envKey = (Get-Content ".env" | Select-String "^APP_KEY=base64:").ToString()
if (-not $envKey) {
    Write-Step "Generando clave de aplicación..."
    php artisan key:generate --ansi
    Write-OK "APP_KEY generada"
}

# ── Migraciones ───────────────────────────────────────────────────────────────
Write-Step "Ejecutando migraciones..."

if ($Fresh -or $isNewInstall) {
    if ($SkipSeed) {
        php artisan migrate:fresh --force
    } else {
        php artisan migrate:fresh --seed --force
        Write-OK "Migraciones y seeders ejecutados"
    }
} else {
    php artisan migrate --force
    Write-OK "Migraciones aplicadas"
}

# ── Storage link ──────────────────────────────────────────────────────────────
Write-Step "Creando enlace de storage..."
php artisan storage:link --force 2>&1 | Out-Null
Write-OK "Storage link creado"

# ── Optimizar ────────────────────────────────────────────────────────────────
Write-Step "Optimizando la aplicación..."
php artisan optimize:clear | Out-Null
php artisan config:cache  | Out-Null
php artisan route:cache   | Out-Null
php artisan view:cache    | Out-Null
Write-OK "Cache optimizada"

# ── Resumen ───────────────────────────────────────────────────────────────────
Write-Host ""
Write-Host "  =============================================" -ForegroundColor Green
Write-Host "   Deploy completado exitosamente!" -ForegroundColor Green
Write-Host "  =============================================" -ForegroundColor Green
Write-Host ""
Write-Host "  URL del proyecto : " -NoNewline; Write-Host "http://$ProjectName.test" -ForegroundColor Cyan
Write-Host "  Ruta local       : " -NoNewline; Write-Host $ProjectPath -ForegroundColor Cyan
Write-Host ""
Write-Host "  Credenciales de acceso:"
Write-Host "    Admin   : admin@apuestas.com / password" -ForegroundColor Yellow
Write-Host "    Usuario : demo@apuestas.com  / password" -ForegroundColor Yellow
Write-Host ""

if ($isNewInstall) {
    Write-Host "  NOTA: Laragon crea el virtual host automaticamente." -ForegroundColor DarkCyan
    Write-Host "        Si no aparece, reinicia Laragon." -ForegroundColor DarkCyan
    Write-Host ""
}
