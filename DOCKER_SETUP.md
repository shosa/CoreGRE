# 🐳 WEBGRE3 - Docker Setup Guide

Guida completa per containerizzare WEBGRE3 e connetterlo al MySQL dello stack core-services.

---

## 📋 PREREQUISITI

- Docker Desktop installato e avviato
- Docker Compose V2+ installato
- Stack `core-services` con MySQL in esecuzione
- Porte disponibili: 8080 (o modificabile)

---

## 🚀 QUICK START (5 minuti)

### 1. Verifica che MySQL core-services sia attivo

```bash
# Controlla che MySQL sia in esecuzione
docker ps | grep mysql

# Esempio output:
# abc123  mysql:8.0  ... 3306/tcp  mysql
# O potrebbe essere: core-services-mysql-1
```

**Prendi nota del NOME del container MySQL!**

### 2. Verifica il nome della network core-services

```bash
# Lista le network Docker
docker network ls | grep core

# Esempio output:
# abc123  core-services_default  bridge  local
```

**Prendi nota del NOME della network!** (di solito `core-services_default`)

### 3. Configura l'ambiente

```bash
cd c:\xampp\htdocs\webgre3

# Copia il template .env.docker
copy .env.docker .env

# IMPORTANTE: Modifica .env con i tuoi valori!
```

**Modifica `.env` e imposta:**

```env
# Nome del container MySQL (quello che hai trovato al punto 1)
DB_HOST=mysql
# O se si chiama diversamente:
# DB_HOST=core-services-mysql-1

# Credenziali database
DB_NAME=webgre3
DB_USER=root
DB_PASS=la_password_mysql_di_core_services

# Porta su cui esporre WEBGRE3
WEBGRE3_PORT=8080
```

### 4. Modifica docker-compose.yml (se necessario)

Se la network core-services ha un nome diverso da `core-services_default`:

```yaml
networks:
  core-services_default:
    external: true
    name: IL_NOME_REALE_DELLA_NETWORK  # Cambia qui!
```

### 5. Build e avvio

```bash
# Build dell'immagine (prima volta)
docker-compose build

# Avvio container
docker-compose up -d

# Controlla i log
docker-compose logs -f webgre3
```

### 6. Verifica che funzioni

```bash
# Controlla che i container siano up
docker-compose ps

# Output atteso:
# NAME                STATUS              PORTS
# webgre3-app         Up 2 minutes        0.0.0.0:8080->80/tcp
# webgre3-redis       Up 2 minutes        6379/tcp

# Test connessione database
docker exec webgre3-app php -r "new PDO('mysql:host=mysql;dbname=webgre3', 'root', 'password');"

# Se non da errori = connessione OK!
```

### 7. Accedi all'applicazione

Apri il browser: **http://localhost:8080**

---

## 🔧 CONFIGURAZIONE DETTAGLIATA

### Struttura File Docker

```
webgre3/
├── Dockerfile                      # Immagine PHP-FPM + Nginx
├── docker-compose.yml              # Orchestrazione container
├── .env                            # Configurazione (da creare)
├── .env.docker                     # Template environment
│
├── docker/
│   ├── entrypoint.sh               # Script avvio container
│   │
│   ├── php/
│   │   ├── php.ini                 # Configurazione PHP
│   │   └── php-fpm.conf            # Configurazione PHP-FPM
│   │
│   ├── nginx/
│   │   ├── nginx.conf              # Config Nginx principale
│   │   └── default.conf            # Server block WEBGRE3
│   │
│   └── supervisor/
│       └── supervisord.conf        # Gestione processi
```

### Variabili Ambiente Importanti

| Variabile | Default | Descrizione |
|-----------|---------|-------------|
| `DB_HOST` | `mysql` | Nome container MySQL core-services |
| `DB_PORT` | `3306` | Porta MySQL |
| `DB_NAME` | `webgre3` | Nome database |
| `DB_USER` | `root` | Username database |
| `DB_PASS` | `secret` | Password database |
| `REDIS_HOST` | `redis` | Host Redis (container interno) |
| `APP_ENV` | `production` | Ambiente (production/development) |
| `PHP_OPCACHE_ENABLE` | `1` | Abilita Opcache |
| `WEBGRE3_PORT` | `8080` | Porta esposta esterna |

---

## 🌐 NETWORKING

### Come funziona la connessione a core-services?

```yaml
# docker-compose.yml

networks:
  # Network interna WEBGRE3
  webgre3-network:
    driver: bridge

  # Connessione alla network ESTERNA core-services
  core-services_default:
    external: true
    name: core-services_default
```

**Il container `webgre3-app` è connesso a DUE network:**
1. `webgre3-network` - Comunicazione interna (webgre3 ↔ redis)
2. `core-services_default` - Comunicazione con MySQL core-services

### Testare la connessione

```bash
# Entra nel container WEBGRE3
docker exec -it webgre3-app sh

# Testa connessione MySQL
ping mysql
# oppure
nc -zv mysql 3306

# Testa connessione PHP → MySQL
php -r "new PDO('mysql:host=mysql;dbname=webgre3', 'root', 'password');"
echo $?  # 0 = successo

# Esci dal container
exit
```

---

## 📦 COMANDI UTILI

### Gestione Container

```bash
# Avvia container
docker-compose up -d

# Ferma container
docker-compose down

# Riavvia container
docker-compose restart

# Rebuil + restart
docker-compose up -d --build

# Vedi logs
docker-compose logs -f

# Vedi logs solo webgre3
docker-compose logs -f webgre3

# Stato container
docker-compose ps

# Entra nel container
docker exec -it webgre3-app sh

# Ferma e rimuovi TUTTO (anche volumi)
docker-compose down -v
```

### Manutenzione

```bash
# Pulisci cache Opcache
docker exec webgre3-app killall -USR2 php-fpm

# Ricarica Nginx
docker exec webgre3-app nginx -s reload

# Vedi processi nel container
docker exec webgre3-app supervisorctl status

# Riavvia solo PHP-FPM
docker exec webgre3-app supervisorctl restart php-fpm

# Riavvia solo Nginx
docker exec webgre3-app supervisorctl restart nginx
```

### Database

```bash
# Crea database (se non esiste)
docker exec webgre3-app mysql -h mysql -u root -pPASSWORD -e "CREATE DATABASE webgre3;"

# Esegui migrations
docker exec webgre3-app php artisan migrate

# Rollback migrations
docker exec webgre3-app php artisan migrate:rollback

# Seed database
docker exec webgre3-app php artisan db:seed

# Connettiti a MySQL da terminale
docker exec -it webgre3-app mysql -h mysql -u root -pPASSWORD webgre3
```

### Debug

```bash
# Vedi configurazione PHP
docker exec webgre3-app php -i | grep opcache

# Vedi errori PHP
docker exec webgre3-app tail -f /var/www/html/storage/logs/php-fpm.log

# Vedi errori Nginx
docker exec webgre3-app tail -f /var/log/nginx/error.log

# Vedi status PHP-FPM
curl http://localhost:8080/status

# Test health check
curl http://localhost:8080/health
```

---

## 🔄 DEVELOPMENT vs PRODUCTION

### Mode Development

Modifica `.env`:

```env
APP_ENV=development
APP_DEBUG=true
LOG_LEVEL=debug
PHP_OPCACHE_ENABLE=0
```

Monta il codice come volume (già configurato in docker-compose.yml):

```yaml
volumes:
  - ./:/var/www/html:delegated  # ← Modifiche live reload
```

Riavvia:

```bash
docker-compose down
docker-compose up -d
```

### Mode Production

Modifica `.env`:

```env
APP_ENV=production
APP_DEBUG=false
LOG_LEVEL=warning
PHP_OPCACHE_ENABLE=1
```

**Rimuovi** il volume del codice in docker-compose.yml:

```yaml
volumes:
  # - ./:/var/www/html:delegated  # ← Commenta questa riga
  - webgre3-storage:/var/www/html/storage
```

Rebuild:

```bash
docker-compose down
docker-compose build --no-cache
docker-compose up -d
```

---

## 🚨 TROUBLESHOOTING

### Errore: "Cannot connect to MySQL"

**Causa**: Nome container MySQL errato

**Soluzione**:

```bash
# 1. Trova il nome esatto del container MySQL
docker ps | grep mysql

# 2. Aggiorna DB_HOST in .env
DB_HOST=nome_esatto_container_mysql

# 3. Riavvia
docker-compose down && docker-compose up -d
```

### Errore: "Network core-services_default not found"

**Causa**: Network con nome diverso

**Soluzione**:

```bash
# 1. Trova il nome esatto della network
docker network ls | grep core

# 2. Aggiorna docker-compose.yml
networks:
  core-services_default:
    external: true
    name: NOME_REALE_NETWORK

# 3. Riavvia
docker-compose down && docker-compose up -d
```

### Errore: "Port 8080 already in use"

**Soluzione**: Cambia porta in `.env`

```env
WEBGRE3_PORT=8081  # O altra porta libera
```

```bash
docker-compose down && docker-compose up -d
```

### Prestazioni lente

**Causa**: Opcache disabilitato o volume delegated lento

**Soluzione**:

```env
# Abilita Opcache
PHP_OPCACHE_ENABLE=1
```

In production, **NON** montare il codice come volume.

### Errore: "Permission denied" storage/

**Soluzione**:

```bash
# Ripristina permessi
docker exec webgre3-app chown -R www-data:www-data /var/www/html/storage
docker exec webgre3-app chmod -R 755 /var/www/html/storage
```

---

## 📊 PERFORMANCE

### Risorse Container

Limita risorse in docker-compose.yml:

```yaml
services:
  webgre3:
    deploy:
      resources:
        limits:
          cpus: '2.0'
          memory: 1G
        reservations:
          cpus: '0.5'
          memory: 512M
```

### Ottimizzazioni

**PHP Opcache** (già abilitato):
- 256MB memoria
- 20000 file max
- JIT compiler tracing

**Nginx**:
- Gzip compression
- Static file caching (30 giorni)
- Keepalive connections

**Redis** (per sessioni/cache future):
- Container dedicato
- Persistenza AOF

---

## 🔐 SECURITY

### Checklist Produzione

- [ ] `APP_DEBUG=false` in .env
- [ ] Passwords complesse per DB e Redis
- [ ] Firewall configurato (solo porte necessarie)
- [ ] SSL/TLS configurato (nginx-proxy)
- [ ] Backup automatici configurati
- [ ] Logs monitorati
- [ ] Container aggiornati regolarmente

### Hardening

```bash
# 1. Non esporre Redis esternamente (già configurato)
# 2. Usa secrets invece di variabili env (Docker Swarm)
# 3. Scansiona vulnerabilità
docker scan webgre3-app

# 4. Aggiorna immagini base
docker-compose pull
docker-compose up -d --build
```

---

## 📦 BACKUP & RESTORE

### Backup Database

```bash
# Backup automatico
docker exec webgre3-app mysqldump -h mysql -u root -pPASSWORD webgre3 > backup_$(date +%Y%m%d).sql

# Backup con gzip
docker exec webgre3-app mysqldump -h mysql -u root -pPASSWORD webgre3 | gzip > backup_$(date +%Y%m%d).sql.gz
```

### Restore Database

```bash
# Restore da backup
docker exec -i webgre3-app mysql -h mysql -u root -pPASSWORD webgre3 < backup_20251023.sql

# Restore da gzip
gunzip < backup_20251023.sql.gz | docker exec -i webgre3-app mysql -h mysql -u root -pPASSWORD webgre3
```

### Backup Volumi

```bash
# Backup storage
docker run --rm -v webgre3-storage:/data -v $(pwd):/backup alpine tar czf /backup/storage_backup.tar.gz /data

# Restore storage
docker run --rm -v webgre3-storage:/data -v $(pwd):/backup alpine tar xzf /backup/storage_backup.tar.gz -C /data
```

---

## 🎯 NEXT STEPS

1. ✅ Setup completato
2. ⏭️ Configura SSL con nginx-proxy
3. ⏭️ Setup backup automatici
4. ⏭️ Monitoraggio con Prometheus/Grafana
5. ⏭️ CI/CD con GitHub Actions → Docker

---

## 📞 SUPPORTO

**Logs importanti:**
- Application: `/var/www/html/storage/logs/`
- PHP-FPM: `docker-compose logs webgre3`
- Nginx: `docker exec webgre3-app tail -f /var/log/nginx/error.log`

**Health checks:**
- App: `http://localhost:8080/health`
- PHP-FPM: `http://localhost:8080/status`

---

**Container WEBGRE3 pronto! 🚀**
