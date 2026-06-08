@echo off
setlocal EnableDelayedExpansion
chcp 65001 >nul 2>&1

echo.
echo ================================================
echo   Apuestas Deportivas - Deploy para Laragon
echo ================================================
echo.

:: ── Configuración ──────────────────────────────────────
set "PROJECT_NAME=ApuestasDeportivas"
set "LARAGON_ROOT=C:\laragon\www"
set "PROJECT_PATH=%LARAGON_ROOT%\%PROJECT_NAME%"
set "REPO_URL=https://github.com/jlmg22204/ApuestasDeportivas.git"
set "BRANCH=main"

:: ── Verificar Composer ──────────────────────────────────
where composer >nul 2>&1
if errorlevel 1 (
    echo [ERROR] Composer no encontrado. Asegurate de que Laragon este en PATH.
    echo         O ejecuta este script desde la terminal de Laragon.
    pause & exit /b 1
)

:: ── Verificar PHP ───────────────────────────────────────
where php >nul 2>&1
if errorlevel 1 (
    echo [ERROR] PHP no encontrado en PATH.
    pause & exit /b 1
)

echo [OK] PHP y Composer encontrados.
echo.

:: ── Clonar o actualizar repositorio ────────────────────
if exist "%PROJECT_PATH%\.git" (
    echo [INFO] Proyecto existente detectado. Actualizando...
    cd /d "%PROJECT_PATH%"
    git fetch origin
    git checkout %BRANCH%
    git pull origin %BRANCH%
    set "FRESH_INSTALL=0"
) else (
    echo [INFO] Clonando repositorio...
    if not exist "%LARAGON_ROOT%" mkdir "%LARAGON_ROOT%"
    git clone -b %BRANCH% %REPO_URL% "%PROJECT_PATH%"
    if errorlevel 1 (
        echo [ERROR] No se pudo clonar el repositorio.
        pause & exit /b 1
    )
    cd /d "%PROJECT_PATH%"
    set "FRESH_INSTALL=1"
)

echo.
echo [INFO] Directorio: %PROJECT_PATH%
echo.

:: ── Instalar dependencias ───────────────────────────────
echo [INFO] Instalando dependencias de Composer...
composer install --no-dev --optimize-autoloader --no-interaction
if errorlevel 1 (
    echo [WARN] Reintentando con --no-scripts...
    composer install --no-dev --no-scripts --no-interaction
)
echo.

:: ── Configurar .env ─────────────────────────────────────
if not exist ".env" (
    echo [INFO] Creando archivo .env desde .env.example...
    copy ".env.example" ".env" >nul
    echo [INFO] Configurando APP_NAME...
    powershell -Command "(Get-Content '.env') -replace 'APP_NAME=.*', 'APP_NAME=\"Apuestas Deportivas\"' | Set-Content '.env'"
)

:: ── Crear base de datos SQLite ──────────────────────────
if not exist "database\database.sqlite" (
    echo [INFO] Creando base de datos SQLite...
    type nul > "database\database.sqlite"
)

:: ── Generar APP_KEY si no existe ────────────────────────
findstr /C:"APP_KEY=base64" ".env" >nul 2>&1
if errorlevel 1 (
    echo [INFO] Generando clave de aplicacion...
    php artisan key:generate --ansi
)

:: ── Migraciones ─────────────────────────────────────────
echo.
if "%FRESH_INSTALL%"=="1" (
    echo [INFO] Primera instalacion: ejecutando migraciones y seeders...
    php artisan migrate:fresh --seed --force
) else (
    echo [INFO] Actualizacion: ejecutando migraciones pendientes...
    php artisan migrate --force
)

:: ── Storage link ────────────────────────────────────────
echo.
echo [INFO] Creando enlace de storage...
php artisan storage:link --force 2>nul

:: ── Limpiar y optimizar cache ───────────────────────────
echo.
echo [INFO] Optimizando aplicacion...
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache

:: ── Permisos (no aplica en Windows, solo informativo) ───
echo.
echo ================================================
echo   Deploy completado exitosamente!
echo ================================================
echo.
echo   URL:     http://%PROJECT_NAME%.test
echo   Admin:   admin@apuestas.com / password
echo   Usuario: demo@apuestas.com  / password
echo.
echo   Si es la primera vez, agrega el virtual host
echo   en Laragon haciendo clic derecho en la barra
echo   de sistema ^> Apache ^> www ^> %PROJECT_NAME%
echo.
pause
