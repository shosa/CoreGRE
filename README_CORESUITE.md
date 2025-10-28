# CoreGre - ERP System

**CoreGre** è la componente ERP della **CoreSuite**, precedentemente nota come WEBGRE3.

## 🎯 Overview

CoreGre è un sistema ERP ottimizzato sviluppato in PHP, completamente containerizzato e integrato con l'ecosistema CoreSuite.

### Caratteristiche Principali

- ✅ **PHP 8.1** con FPM e OPcache
- ✅ **Nginx** integrato nel container
- ✅ **Redis condiviso** per caching (CoreServices)
- ✅ **MySQL condiviso** da CoreServices
- ✅ **Docker** multi-stage build ottimizzato
- ✅ **Health checks** integrati
- ✅ **Integrazione CoreSuite** completa

## 🚀 Quick Start

### Prerequisiti

1. **Docker** e **Docker Compose** installati
2. **CoreServices** attivo (MySQL, Nginx, MinIO, Meilisearch)

### Installazione

```bash
# 1. Assicurati che CoreServices sia attivo
cd ../CoreServices
docker-compose up -d

# 2. Torna in CoreGre e importa il database
cd ../CoreGre
./setup-database.sh  # Linux/Mac
# oppure
setup-database.bat   # Windows

# 3. Avvia CoreGre
docker-compose up -d --build

# 4. Verifica lo stato
docker-compose ps
docker-compose logs -f app
```

### Accesso

- **Locale (diretta)**: http://localhost:3008
- **Tramite nginx (CoreServices)**: http://localhost:84
- **PHPMyAdmin**: http://localhost:8080 (per gestire il database)

## 📦 Architettura

```
CoreGre
├── Container: coregre-app (PHP 8.1 + Nginx + Supervisor)
│   └── Porta: 3008 → 80
└── Network: core-network (condivisa CoreSuite)
    ├── core-mysql (database condiviso)
    ├── core-redis (cache condivisa) ← NUOVO
    ├── core-minio (storage condiviso)
    ├── core-meilisearch (search condiviso)
    └── core-nginx (reverse proxy - porta 84)
```

## 🛠️ Comandi Utili (Makefile)

```bash
make help          # Mostra tutti i comandi disponibili
make setup         # Setup completo (prima volta)
make up            # Avvia CoreGre
make down          # Ferma CoreGre
make restart       # Riavvia CoreGre
make logs          # Mostra logs in tempo reale
make shell         # Shell nel container
make db-backup     # Backup del database
make url           # Mostra URL di accesso
```

## 📁 Struttura File

```
CoreGre/
├── app/                    # Applicazione PHP
├── config/                 # Configurazioni
├── core/                   # Core framework
├── database/               # Migrazioni e seeds
├── docker/                 # Configurazioni Docker
│   ├── nginx/             # Config Nginx
│   ├── php/               # Config PHP
│   └── supervisor/        # Config Supervisor
├── public/                 # File pubblici
├── routes/                 # Route definitions
├── storage/                # Storage applicazione (logs, cache, uploads)
├── vendor/                 # Dipendenze Composer
├── docker-compose.yml      # Docker Compose config
├── Dockerfile              # Build instructions
├── backup.sql              # Backup database
├── setup-database.sh       # Script setup database (Linux/Mac)
├── setup-database.bat      # Script setup database (Windows)
├── Makefile.core           # Comandi make
└── CORESUITE_INTEGRATION.md # Documentazione integrazione
```

## 🔧 Configurazione

### Variabili d'Ambiente (.env)

```env
# Application
APP_ENV=production
APP_DEBUG=false
APP_NAME=CoreGre
APP_VERSION=1.0.0

# Database (usa CoreServices MySQL)
DB_HOST=core-mysql
DB_PORT=3306
DB_NAME=coregre
DB_USER=root
DB_PASS=rootpassword

# Redis (condiviso da CoreServices)
REDIS_HOST=core-redis
REDIS_PORT=6379
REDIS_PASSWORD=coresuite_redis

# PHP Settings
PHP_OPCACHE_ENABLE=1
PHP_MEMORY_LIMIT=256M
PHP_UPLOAD_MAX_FILESIZE=50M
PHP_POST_MAX_SIZE=50M
```

### Database

Il database `coregre` è gestito tramite MySQL condiviso di CoreServices:
- **Host**: core-mysql
- **Porta**: 3306
- **Database**: coregre
- **User**: root
- **Password**: rootpassword

## 📊 Monitoring

### Health Check

```bash
# Verifica health status
curl http://localhost:3008/health

# Verifica via nginx
curl http://localhost:84/health
```

### Logs

```bash
# Logs applicazione
docker-compose logs -f app

# Logs Redis (condiviso in CoreServices)
cd ../CoreServices && docker-compose logs -f redis

# Logs Nginx (dentro il container)
docker exec -it coregre-app tail -f /var/log/nginx/access.log
docker exec -it coregre-app tail -f /var/log/nginx/error.log

# Logs PHP-FPM
docker exec -it coregre-app tail -f /var/log/php8/error.log
```

### Stats Risorse

```bash
# Statistiche container CoreGre
docker stats coregre-app --no-stream

# Statistiche tutti i servizi Core
docker stats core-mysql core-redis core-minio core-nginx coregre-app --no-stream

# Dettagli
docker-compose ps
```

## 🔒 Sicurezza

- **PHP OPcache** abilitato in produzione
- **Redis** protetto da password
- **File uploads** validati
- **SQL injection** prevenuta tramite prepared statements
- **XSS protection** attiva

## 🐛 Troubleshooting

### Container non si avvia

```bash
# Verifica logs
docker-compose logs app

# Verifica che CoreServices sia attivo
docker ps | grep core-

# Riavvia CoreServices se necessario
cd ../CoreServices && docker-compose restart
```

### Problemi di connessione database

```bash
# Testa connessione
docker exec -it coregre-app mysql -h core-mysql -uroot -prootpassword -e "SHOW DATABASES;"

# Verifica che il database esista
docker exec -it core-mysql mysql -uroot -prootpassword -e "SHOW DATABASES;" | grep coregre
```

### Permission errors

```bash
# Fix permessi storage
make fix-permissions

# Oppure manualmente
docker exec -it coregre-app chown -R www-data:www-data /var/www/html/storage
docker exec -it coregre-app chmod -R 755 /var/www/html/storage
```

### Cache issues

```bash
# Pulisci solo cache CoreGre (consigliato)
make clear-cache

# Pulisci TUTTA la cache Redis (ATTENZIONE: tutte le app!)
make clear-cache-all

# Oppure manualmente
docker exec -it core-redis redis-cli -a coresuite_redis FLUSHALL

# Pulisci OPcache (riavvia PHP-FPM)
docker-compose restart app
```

## 📝 Note Importanti

1. **Redis Condiviso**: CoreGre usa `core-redis` condiviso. Cache keys usa prefisso `coregre:`
2. **Volumi Persistenti**: I dati sono salvati in volumi Docker (`coregre-storage`, `coregre-uploads`)
3. **Development Mode**: Il codice è montato live (modifiche immediate)
4. **Production Mode**: Per produzione, commenta il mount del codice e rebuilda
5. **Backup**: Fai backup regolari del database con `make db-backup`
6. **Timezone**: Europe/Rome (configurato nel container)

## 🔗 Link Utili

- **CoreServices**: ../CoreServices
- **Documentazione Integrazione**: [CORESUITE_INTEGRATION.md](./CORESUITE_INTEGRATION.md)
- **PHPMyAdmin**: http://localhost:8080
- **MinIO Console**: http://localhost:9001
- **Meilisearch**: http://localhost:7700

## 🆘 Supporto

Per problemi o domande:
1. Controlla i logs: `make logs`
2. Verifica CoreServices: `make check-services`
3. Consulta la documentazione: `CORESUITE_INTEGRATION.md`

## 📜 Licenza

Proprietario - Uso interno CoreSuite
