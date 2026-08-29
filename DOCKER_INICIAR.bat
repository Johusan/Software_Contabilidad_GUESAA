@echo off
title GUESAA SIC - Iniciar con Docker
color 0b

echo ===============================================================
echo   GUESAA SIC - SISTEMA DE INFORMACION CONTABLE (DOCKER)
echo ===============================================================
echo.
echo Iniciando contenedores de aplicacion y PostgreSQL...
echo.

docker compose up -d --build

if %ERRORLEVEL% NEQ 0 (
    echo.
    echo [ERROR] No se pudo iniciar Docker. Verifica que Docker Desktop este abierto.
    echo.
    pause
    exit /b %ERRORLEVEL%
)

echo.
echo ===============================================================
echo   SISTEMA ACTIVO CORRECTAMENTE EN DOCKER
echo ===============================================================
echo   Acceso Local: http://localhost:8000
echo ===============================================================
echo.
echo Abriendo el navegador...
start http://localhost:8000

echo.
echo Para detener los contenedores en cualquier momento, ejecuta:
echo docker compose down
echo.
pause
