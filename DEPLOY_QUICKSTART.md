# 🚀 Deploy Automatico - Quick Start

Guida rapida per attivare il deploy automatico con GitHub Actions in **5 minuti**.

---

## ⚡ SETUP RAPIDO (5 minuti)

### 1️⃣ Push del codice su GitHub

```bash
# Se NON hai ancora pushato il progetto su GitHub:
cd c:\xampp\htdocs\webgre3

# Inizializza Git (se non già fatto)
git init
git add .
git commit -m "Initial commit con GitHub Actions"

# Collega a GitHub (sostituisci con il tuo repo)
git remote add origin https://github.com/TUO-USERNAME/webgre3.git
git branch -M main
git push -u origin main

# Crea branch developing
git checkout -b developing
git push -u origin developing
```

### 2️⃣ Configura i Secrets su GitHub

Vai su: **GitHub.com → Repository → Settings → Secrets and variables → Actions**

Click **"New repository secret"** e aggiungi:

| Nome | Valore (ESEMPIO) | Dove trovarlo |
|------|------------------|---------------|
| `FTP_SERVER` | `ftp.tuosito.it` | Pannello Aruba → Gestione FTP |
| `FTP_USERNAME` | `webXXXXX` | Pannello Aruba → Gestione FTP |
| `FTP_PASSWORD` | `la_tua_password` | Pannello Aruba → Gestione FTP |
| `DB_HOST` | `localhost` | Database Aruba (di solito localhost) |
| `DB_NAME` | `my_webgre` | Nome del tuo database |
| `DB_USER` | `root` o `db_user` | Username database |
| `DB_PASS` | `password_db` | Password database |
| `DANGEROUS_SALT` | `abc123xyz789` | Stringa random qualsiasi |

**✅ FATTO!** Il deploy automatico è configurato.

---

## 🎯 COME USARLO

### Deploy Automatico su PRODUZIONE

```bash
# 1. Fai modifiche
git add .
git commit -m "feat: nuova funzionalità"

# 2. Push su main → DEPLOY AUTOMATICO!
git push origin main
```

⚡ **GitHub Actions deploya automaticamente su Aruba!**

### Deploy Automatico su STAGING

```bash
# 1. Lavora su developing
git checkout developing

# 2. Fai modifiche e push
git add .
git commit -m "test: nuova feature"
git push origin developing
```

⚡ **Deploy automatico su staging (se configurato)!**

---

## 📊 Monitorare il Deploy

1. Vai su **GitHub.com → Repository → Actions**
2. Vedrai il workflow in esecuzione
3. Click per vedere i dettagli

**Indicatori:**
- 🟢 Verde = Deploy OK
- 🔴 Rosso = Errore (controlla i log)
- 🟡 Giallo = In esecuzione

---

## 🛠️ Deploy Manuale (per test)

Se vuoi testare il deploy prima di pushare:

1. Vai su **GitHub → Actions**
2. Seleziona **"Deploy to Aruba Production"**
3. Click **"Run workflow"**
4. Seleziona branch **main**
5. Click **"Run workflow"** (bottone verde)

---

## 🔧 Script Helper (OPZIONALE)

Se usi Git Bash/WSL, puoi usare lo script helper:

```bash
# Rendi eseguibile
chmod +x deploy.sh

# Deploy su staging
./deploy.sh staging

# Deploy su production
./deploy.sh production

# Controlla status
./deploy.sh status
```

---

## 📁 File Deployati Automaticamente

GitHub Actions carica solo i file necessari:

✅ **Inclusi:**
- `app/` - Codice applicazione
- `core/` - Framework
- `config/` - Configurazioni
- `vendor/` - Dipendenze Composer
- `public/` - Assets
- `.user.ini` - Opcache
- `database/migrations/` - Migrazioni

❌ **Esclusi:**
- `.git/` - History Git
- `tests/` - Test
- `storage/cache/` - Cache locale
- `*.md` - Documentazione
- `.env` - File locale

---

## ⚠️ Troubleshooting

### Errore: "Authentication failed"
→ Controlla FTP_USERNAME e FTP_PASSWORD nei Secrets

### Errore: "Directory not found"
→ Verifica il path: `/httpdocs/webgre3/` o `/public_html/webgre3/`

### Deploy troppo lento
→ Normale la prima volta (upload vendor/ ~50MB)

### Composer install failed
→ Verifica che `composer.json` sia committato

---

## 📋 Workflow Git Consigliato

```bash
# 1. Sviluppo su branch developing
git checkout developing
# ... fai modifiche ...
git add .
git commit -m "feat: nuova funzionalità"
git push origin developing  # ← Deploy su STAGING

# 2. Testa su staging
# Se tutto OK:

# 3. Merge su main
git checkout main
git merge developing
git push origin main  # ← Deploy su PRODUZIONE
```

---

## ✅ Checklist Prima del Deploy

Prima di deployare su produzione:

- [ ] Codice testato in locale
- [ ] Commit committati
- [ ] Testato su staging (developing branch)
- [ ] Database migrations verificate
- [ ] Nessun file sensibile committato (.env)
- [ ] Backup database produzione fatto

---

## 🎉 Deploy Completato!

Ora ogni volta che pushhi su `main`, il codice viene deployato automaticamente su Aruba!

**Link utili:**
- 📖 [Guida completa](.github/SETUP_GITHUB_ACTIONS.md)
- 🔐 [Esempio secrets](.github/secrets.example.txt)
- 📊 [GitHub Actions](https://github.com/TUO-USERNAME/webgre3/actions)

---

**Hai domande?** Controlla la documentazione completa in `.github/SETUP_GITHUB_ACTIONS.md`
