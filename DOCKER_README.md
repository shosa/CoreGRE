# 🐳 WEBGRE3 - Docker Quick Reference

## 🚀 Avvio Rapido (3 minuti)

### 1. Verifica prerequisiti

```bash
# Esegui pre-flight check
bash docker-preflight-check.sh
```

### 2. Configura ambiente

```bash
# Copia template
cp .env.docker .env

# IMPORTANTE: Modifica .env con le tue credenziali!
# - DB_HOST: nome container MySQL core-services
# - DB_USER, DB_PASS: credenziali database
# - WEBGRE3_PORT: porta da esporre (default 8080)
```

### 3. Build e avvio

```bash
# Metodo 1: Makefile (consigliato)
make build
make up
make logs

# Metodo 2: Docker Compose
docker-compose build
docker-compose up -d
docker-compose logs -f
```

### 4. Verifica

```bash
# Check salute applicazione
curl http://localhost:8080/health

# O apri browser
http://localhost:8080
```

---

## 📋 Comandi Utili (Make)

```bash
make help              # Lista tutti i comandi
make up                # Avvia container
make down              # Ferma container
make restart           # Riavvia container
make logs              # Vedi logs real-time
make shell             # Entra nel container
make mysql             # Connetti a MySQL
make migrate           # Esegui migrations
make db-backup         # Backup database
make clean             # Rimuovi tutto
```

Vedi `Makefile` per lista completa comandi!

---

## 🔧 Troubleshooting Rapido

### "Cannot connect to MySQL"
```bash
# Verifica nome container MySQL
docker ps | grep mysql

# Aggiorna DB_HOST in .env
DB_HOST=nome_container_mysql_corretto
```

### "Network not found"
```bash
# Verifica nome network
docker network ls | grep core

# Aggiorna docker-compose.yml se necessario
```

### "Port already in use"
```bash
# Cambia porta in .env
WEBGRE3_PORT=8081

# Riavvia
make restart
```

---

## 📖 Documentazione Completa

- [Setup Completo](DOCKER_SETUP.md) - Guida dettagliata 20+ pagine
- [Makefile](Makefile) - Lista comandi Make
- [docker-compose.yml](docker-compose.yml) - Configurazione container

---

## 🎯 Architettura

```
┌─────────────────────────────────────────┐
│   Browser → http://localhost:8080       │
└────────────────┬────────────────────────┘
                 │
         ┌───────▼────────┐
         │  Nginx:80      │
         │  (reverse      │
         │   proxy)       │
         └───────┬────────┘
                 │
         ┌───────▼────────┐
         │  PHP-FPM:9000  │
         │  (WEBGRE3)     │
         └──┬──────────┬──┘
            │          │
    ┌───────▼───┐  ┌──▼────────┐
    │ Redis:6379│  │MySQL:3306 │
    │ (cache)   │  │(core-svc) │
    └───────────┘  └───────────┘
```

**Network:**
- `webgre3-network` - Interna (webgre3 ↔ redis)
- `core-services_default` - Esterna (webgre3 ↔ mysql)

---

## ✅ Checklist Produzione

- [ ] `.env` configurato con credenziali corrette
- [ ] `APP_ENV=production` e `APP_DEBUG=false`
- [ ] Opcache abilitato (`PHP_OPCACHE_ENABLE=1`)
- [ ] Backup automatici configurati
- [ ] SSL/TLS configurato (se pubblico)
- [ ] Firewall configurato
- [ ] Monitoraggio attivo

---

**Pronto! Container WEBGRE3 running! 🚀**

Problemi? Vedi [DOCKER_SETUP.md](DOCKER_SETUP.md) per guida completa.
