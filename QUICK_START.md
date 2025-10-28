# CoreGre - Quick Start Guide

Guida rapida per avviare CoreGre nella CoreSuite in 5 minuti.

## ⚡ Setup in 3 Passi

### 1️⃣ Avvia CoreServices

```bash
cd ../CoreServices
docker-compose up -d
```

Verifica che sia tutto attivo:
```bash
docker-compose ps
```

Dovresti vedere:
- ✅ core-mysql
- ✅ core-nginx
- ✅ core-minio
- ✅ core-meilisearch
- ✅ core-phpmyadmin

### 2️⃣ Importa Database CoreGre

**Windows:**
```cmd
cd ..\CoreGre
setup-database.bat
```

**Linux/Mac:**
```bash
cd ../CoreGre
chmod +x setup-database.sh
./setup-database.sh
```

Lo script:
- Crea il database `coregre`
- Importa `backup.sql`
- Verifica l'import

### 3️⃣ Avvia CoreGre

```bash
docker-compose up -d --build
```

## 🎉 Fatto!

Accedi all'applicazione:

- 🌐 **Locale**: http://localhost:3008
- 🌐 **Nginx**: http://localhost:84

## 📊 Verifica Status

```bash
# Status container
docker-compose ps

# Logs in tempo reale
docker-compose logs -f app

# Health check
curl http://localhost:3008/health
```

Expected output:
```
NAME            STATUS          PORTS
coregre-app     Up (healthy)    0.0.0.0:3008->80/tcp
coregre-redis   Up (healthy)
```

## 🔧 Comandi Utili

```bash
# Stop
docker-compose down

# Restart
docker-compose restart

# Rebuild
docker-compose up -d --build

# Logs
docker-compose logs -f

# Shell
docker exec -it coregre-app sh

# MySQL shell
docker exec -it core-mysql mysql -uroot -prootpassword coregre
```

## 🆘 Troubleshooting

### Container non si avvia

```bash
# Controlla logs
docker-compose logs app

# Verifica CoreServices
cd ../CoreServices && docker-compose ps
```

### Errore connessione database

```bash
# Verifica che MySQL sia raggiungibile
docker exec -it coregre-app ping core-mysql

# Test connessione
docker exec -it core-mysql mysql -uroot -prootpassword -e "SHOW DATABASES;" | grep coregre
```

### Porta già in uso

Se la porta 3008 è occupata, modifica in `docker-compose.yml`:
```yaml
ports:
  - "3009:80"  # Usa porta diversa
```

## 📚 Prossimi Passi

- 📖 Leggi [README_CORESUITE.md](./README_CORESUITE.md) per dettagli completi
- 📖 Consulta [CORESUITE_INTEGRATION.md](./CORESUITE_INTEGRATION.md) per integrazione
- 🛠️ Usa `make help` per vedere tutti i comandi disponibili

## 🔗 URL Utili

| Servizio | URL | Note |
|----------|-----|------|
| CoreGre (locale) | http://localhost:3008 | Accesso diretto |
| CoreGre (nginx) | http://localhost:84 | Via reverse proxy |
| PHPMyAdmin | http://localhost:8080 | Gestione database |
| MinIO Console | http://localhost:9001 | Object storage (minioadmin/minioadmin123) |
| Meilisearch | http://localhost:7700 | Search engine |

## 🎯 Test Rapido

Dopo l'avvio, testa che tutto funzioni:

```bash
# 1. Health check
curl http://localhost:3008/health

# 2. Via nginx
curl http://localhost:84/health

# 3. Database
docker exec -i core-mysql mysql -uroot -prootpassword coregre -e "SHOW TABLES;"
```

Se tutti i comandi vanno a buon fine, CoreGre è configurato correttamente! ✅
