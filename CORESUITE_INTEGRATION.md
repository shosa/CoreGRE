# CoreGre - Integrazione CoreSuite

CoreGre è la componente ERP della CoreSuite, precedentemente nota come WEBGRE3.

## Architettura

CoreGre è un'applicazione PHP monolitica che utilizza:
- **PHP 8.1 FPM** (FastCGI Process Manager)
- **Nginx** integrato nel container
- **Composer** per dependency management
- **Redis** condiviso da CoreServices per caching
- **MySQL** condiviso da CoreServices
- **MinIO** condiviso da CoreServices (opzionale, per future features)

## Porte Assegnate

- **Porta applicazione**: 3008 (container interno: 80)
- **Porta nginx CoreServices**: 84

## URL di Accesso

- **Locale (diretta)**: http://localhost:3008
- **Tramite nginx (CoreServices)**: http://localhost:84

## Configurazione Database

CoreGre si connette al database MySQL condiviso:

```env
DB_HOST=core-mysql
DB_PORT=3306
DB_NAME=coregre
DB_USER=root
DB_PASS=rootpassword

REDIS_HOST=core-redis
REDIS_PORT=6379
REDIS_PASSWORD=coresuite_redis
```

## Avvio con CoreSuite

### 1. Assicurati che CoreServices sia attivo

```bash
cd ../CoreServices
docker-compose up -d
```

### 2. Importa il database (prima volta)

CoreGre include un backup del database esistente (`backup.sql`). Usa lo script fornito per importarlo:

**Windows:**
```cmd
setup-database.bat
```

**Linux/Mac:**
```bash
chmod +x setup-database.sh
./setup-database.sh
```

**Manualmente:**
```bash
# Crea database
docker exec -it core-mysql mysql -uroot -prootpassword -e "CREATE DATABASE IF NOT EXISTS coregre CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

# Importa backup
docker exec -i core-mysql mysql -uroot -prootpassword coregre < backup.sql
```

### 3. Avvia CoreGre

```bash
cd CoreGre
docker-compose up -d --build
```

### 4. Verifica lo stato

```bash
docker-compose ps
docker-compose logs -f app
```

### 5. Accedi all'applicazione

- Locale: http://localhost:3008
- Via nginx: http://localhost:84

## Struttura Container

- **coregre-app**: Applicazione PHP + Nginx + Supervisor

### Servizi Condivisi (da CoreServices)

- **core-redis**: Cache Redis condiviso con tutte le app CoreSuite
- **core-mysql**: Database MySQL condiviso
- **core-minio**: Object storage condiviso
- **core-meilisearch**: Search engine condiviso

## Network

CoreGre si connette alla `core-network` condivisa con:
- CoreHub
- CoreMachine
- CoreDocument
- CoreVisitor
- CoreServices (MySQL, MinIO, Meilisearch)

## Volumi Persistenti

- `coregre-storage`: Directory storage dell'applicazione
- `coregre-uploads`: File uploads pubblici
- `core-redis-data`: Dati Redis (condiviso in CoreServices)

## Development vs Production

### Development (codice live)
Il docker-compose attuale monta il codice live:
```yaml
volumes:
  - ./:/var/www/html:delegated
```

### Production
Per produzione, commenta il mount del codice e rebuilda l'immagine.

## Migrazioni Database

```bash
# Entra nel container
docker exec -it coregre-app sh

# Esegui migrazioni (se previste dall'app)
php artisan migrate
# oppure
php migrate.php
```

## Logs

```bash
# Logs applicazione
docker-compose logs -f app

# Logs Redis
docker-compose logs -f redis

# Logs nginx (all'interno del container)
docker exec -it coregre-app tail -f /var/log/nginx/access.log
docker exec -it coregre-app tail -f /var/log/nginx/error.log
```

## Troubleshooting

### Problemi di connessione al database
```bash
# Verifica che MySQL sia raggiungibile
docker exec -it coregre-app ping core-mysql

# Test connessione diretta
docker exec -it coregre-app mysql -h core-mysql -uroot -prootpassword -e "SHOW DATABASES;"
```

### Permission issues
```bash
# Fix permessi storage
docker exec -it coregre-app chown -R www-data:www-data /var/www/html/storage
docker exec -it coregre-app chmod -R 755 /var/www/html/storage
```

### Cache issues
```bash
# Clear solo cache CoreGre (consigliato)
make clear-cache

# Oppure manualmente - clear TUTTE le app (attenzione!)
docker exec -it core-redis redis-cli -a coresuite_redis FLUSHALL
```

## Health Check

Il container ha un health check integrato:
```bash
curl http://localhost:3008/health
```

## Configurazione Nginx CoreServices

CoreGre è configurato in CoreServices nginx sulla porta 84:
- **File**: `CoreServices/nginx/nginx.conf`
- **Server block**: `listen 84`
- **Proxy**: `coregre-app:80`

## Note Importanti

1. **Redis condiviso**: CoreGre usa Redis condiviso (`core-redis`). Usa prefisso `coregre:` per le chiavi
2. **Redis password**: `coresuite_redis` (condivisa tra tutte le app)
3. **PHP Settings**: Configurabili in `docker/php/php.ini`
4. **Nginx Config**: Configurabile in `docker/nginx/default.conf`
5. **Timezone**: Europe/Rome (configurato nel Dockerfile)

## Comandi Utili

```bash
# Restart container
docker-compose restart app

# Rebuild senza cache
docker-compose build --no-cache app

# Shell nel container
docker exec -it coregre-app sh

# Composer install (se aggiungi dipendenze)
docker exec -it coregre-app composer install

# Clear opcache
docker exec -it coregre-app kill -USR2 1
```
