@echo off
REM ============================================================================
REM CoreGre - Database Setup Script (Windows)
REM Importa backup.sql nel database MySQL di CoreServices
REM ============================================================================

echo.
echo ====================================
echo   CoreGre Database Setup
echo ====================================
echo.

SET MYSQL_CONTAINER=core-mysql
SET MYSQL_USER=root
SET MYSQL_PASSWORD=rootpassword
SET DB_NAME=coregre
SET BACKUP_FILE=backup.sql

REM Verifica che il file backup esista
if not exist "%BACKUP_FILE%" (
    echo [ERROR] File %BACKUP_FILE% non trovato!
    exit /b 1
)

echo [OK] File backup trovato: %BACKUP_FILE%
echo.

REM Verifica che MySQL container sia attivo
echo Verifico che MySQL sia attivo...
docker ps | findstr %MYSQL_CONTAINER% >nul 2>&1
if errorlevel 1 (
    echo [ERROR] Container MySQL non attivo!
    echo         Avvia prima CoreServices:
    echo         cd ..\CoreServices
    echo         docker-compose up -d
    exit /b 1
)

echo [OK] MySQL container attivo
echo.

REM Crea il database
echo Creo database %DB_NAME%...
docker exec -i %MYSQL_CONTAINER% mysql -u%MYSQL_USER% -p%MYSQL_PASSWORD% -e "CREATE DATABASE IF NOT EXISTS %DB_NAME% CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;" 2>nul
echo [OK] Database creato/verificato
echo.

REM Importa il backup
echo Importo backup nel database...
echo Questo potrebbe richiedere alcuni minuti...
docker exec -i %MYSQL_CONTAINER% mysql -u%MYSQL_USER% -p%MYSQL_PASSWORD% %DB_NAME% < %BACKUP_FILE%

if errorlevel 1 (
    echo [ERROR] Errore durante import del backup!
    exit /b 1
)

echo [OK] Backup importato con successo!
echo.

REM Mostra tabelle
echo Tabelle importate:
docker exec -i %MYSQL_CONTAINER% mysql -u%MYSQL_USER% -p%MYSQL_PASSWORD% %DB_NAME% -e "SHOW TABLES;" 2>nul
echo.

echo ====================================
echo   Database CoreGre configurato!
echo ====================================
echo.
echo Puoi ora avviare CoreGre:
echo   docker-compose up -d --build
echo.
echo Accedi via:
echo   - Locale: http://localhost:3008
echo   - Nginx:  http://localhost:84
echo.

pause
