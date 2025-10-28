#!/bin/bash
# ============================================================================
# CoreGre - Database Setup Script
# Importa backup.sql nel database MySQL di CoreServices
# ============================================================================

set -e  # Exit on error

echo "🗄️  CoreGre Database Setup"
echo "================================"
echo ""

# Configurazione
MYSQL_CONTAINER="core-mysql"
MYSQL_USER="root"
MYSQL_PASSWORD="rootpassword"
DB_NAME="coregre"
BACKUP_FILE="backup.sql"

# Verifica che il file backup esista
if [ ! -f "$BACKUP_FILE" ]; then
    echo "❌ Errore: File $BACKUP_FILE non trovato!"
    exit 1
fi

echo "✓ File backup trovato: $BACKUP_FILE"
echo ""

# Verifica che MySQL container sia attivo
echo "🔍 Verifico che MySQL sia attivo..."
if ! docker ps | grep -q $MYSQL_CONTAINER; then
    echo "❌ Errore: Container MySQL ($MYSQL_CONTAINER) non è attivo!"
    echo "   Avvia prima CoreServices:"
    echo "   cd ../CoreServices && docker-compose up -d"
    exit 1
fi

echo "✓ MySQL container attivo"
echo ""

# Crea il database se non esiste
echo "📦 Creo database $DB_NAME..."
docker exec -i $MYSQL_CONTAINER mysql -u$MYSQL_USER -p$MYSQL_PASSWORD -e "CREATE DATABASE IF NOT EXISTS $DB_NAME CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;" 2>/dev/null
echo "✓ Database creato/verificato"
echo ""

# Importa il backup
echo "📥 Importo backup nel database..."
echo "   Questo potrebbe richiedere alcuni minuti..."
docker exec -i $MYSQL_CONTAINER mysql -u$MYSQL_USER -p$MYSQL_PASSWORD $DB_NAME < $BACKUP_FILE

if [ $? -eq 0 ]; then
    echo "✓ Backup importato con successo!"
    echo ""

    # Mostra informazioni database
    echo "📊 Informazioni database:"
    echo "   Nome: $DB_NAME"
    echo "   Host: core-mysql"
    echo "   User: $MYSQL_USER"
    echo ""

    # Mostra tabelle importate
    echo "📋 Tabelle importate:"
    docker exec -i $MYSQL_CONTAINER mysql -u$MYSQL_USER -p$MYSQL_PASSWORD $DB_NAME -e "SHOW TABLES;" 2>/dev/null | grep -v "Tables_in"
    echo ""

    echo "✅ Database CoreGre configurato correttamente!"
    echo ""
    echo "Puoi ora avviare CoreGre:"
    echo "  docker-compose up -d --build"
    echo ""
else
    echo "❌ Errore durante l'import del backup!"
    exit 1
fi
