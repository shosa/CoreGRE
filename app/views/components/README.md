# Sistema Universale Modali e Notifiche COREGRE

## Panoramica
Questo sistema fornisce componenti JavaScript universali per modali e notifiche che possono essere utilizzati in tutta l'applicazione senza dover riscrivere codice.

## Componenti

### 1. Sistema Notifiche (`notifications.php`)
Sistema completo per mostrare notifiche toast con animazioni Tailwind.

#### Utilizzo Base
```javascript
// Mostra una notifica
CoregreNotifications.show('Messaggio di prova', 'success', 3000);

// Shortcuts per i vari tipi
CoregreNotifications.success('Operazione completata!');
CoregreNotifications.error('Si è verificato un errore');
CoregreNotifications.warning('Attenzione: controllare i dati');
CoregreNotifications.info('Informazione importante');

// Notifica persistente (deve essere chiusa manualmente)
const loadingId = CoregreNotifications.loading('Caricamento in corso...');
// ... operazione asincrona ...
CoregreNotifications.remove(loadingId);
```

#### Metodi Disponibili
- `show(message, type, duration)` - Mostra una notifica
- `success(message, duration)` - Notifica di successo
- `error(message, duration)` - Notifica di errore  
- `warning(message, duration)` - Notifica di avviso
- `info(message, duration)` - Notifica informativa
- `loading(message)` - Notifica persistente per caricamento
- `remove(id)` - Rimuove una specifica notifica
- `removeByText(text)` - Rimuove tutte le notifiche che contengono un testo

### 2. Sistema Modali (`modals.php`)
Sistema completo per modali di conferma e informativi.

#### Utilizzo Base
```javascript
// Modale di conferma generico
CoregreModals.confirm({
    title: 'Conferma Azione',
    message: 'Sei sicuro di voler procedere?',
    confirmText: 'Procedi',
    cancelText: 'Annulla',
    type: 'info', // 'info', 'warning', 'danger'
    onConfirm: () => {
        console.log('Confermato!');
    },
    onCancel: () => {
        console.log('Annullato');
    }
});

// Shortcut per eliminazione
CoregreModals.confirmDelete(
    'Sei sicuro di voler eliminare questo elemento?',
    () => {
        // Logica di eliminazione
        console.log('Elemento eliminato');
    },
    1 // numero di elementi (per il messaggio)
);

// Modale informativo (solo OK)
CoregreModals.alert(
    'Informazione',
    'Operazione completata con successo',
    () => console.log('Chiuso')
);
```

#### Tipi di Modale
- `info` - Modale informativo (blu)
- `warning` - Modale di avviso (giallo)  
- `danger` - Modale pericoloso (rosso, per eliminazioni)

## Integrazione

### Nel Layout Principale
I componenti sono già inclusi in `layouts/main.php`:
```php
<!-- Universal Components -->
<?php include APP_ROOT . '/app/views/components/notifications.php'; ?>
<?php include APP_ROOT . '/app/views/components/modals.php'; ?>
```

### Rimozione del Vecchio Codice
Quando si migra una pagina esistente:

1. **Rimuovere** i container di notifica duplicati:
```html
<!-- RIMUOVERE -->
<div id="notification-container" class="fixed top-24 right-6 z-[9999] space-y-4 pointer-events-none"></div>
```

2. **Sostituire** le funzioni personalizzate:
```javascript
// VECCHIO
showNotification('Messaggio', 'success');

// NUOVO  
CoregreNotifications.success('Messaggio');
```

3. **Sostituire** i modali personalizzati:
```javascript
// VECCHIO
if (confirm('Sei sicuro?')) {
    // azione
}

// NUOVO
CoregreModals.confirm({
    message: 'Sei sicuro?',
    onConfirm: () => {
        // azione
    }
});
```

## Esempi Completi

### Esempio Eliminazione con Conferma
```javascript
function deleteItem(id) {
    CoregreModals.confirmDelete(
        'Questa azione non può essere annullata',
        async () => {
            const loadingId = CoregreNotifications.loading('Eliminazione in corso...');
            
            try {
                const response = await fetch(`/api/items/${id}`, { 
                    method: 'DELETE' 
                });
                
                CoregreNotifications.remove(loadingId);
                
                if (response.ok) {
                    CoregreNotifications.success('Elemento eliminato');
                    // Ricarica dati o rimuovi dalla UI
                } else {
                    throw new Error('Errore server');
                }
            } catch (error) {
                CoregreNotifications.remove(loadingId);
                CoregreNotifications.error('Errore durante l\'eliminazione');
            }
        }
    );
}
```

### Esempio Form con Validazione
```javascript
async function submitForm(data) {
    const loadingId = CoregreNotifications.loading('Salvataggio in corso...');
    
    try {
        const response = await fetch('/api/save', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(data)
        });
        
        CoregreNotifications.remove(loadingId);
        
        const result = await response.json();
        
        if (result.success) {
            CoregreNotifications.success('Dati salvati con successo');
        } else {
            CoregreNotifications.warning(result.message || 'Controllare i dati inseriti');
        }
    } catch (error) {
        CoregreNotifications.remove(loadingId);  
        CoregreNotifications.error('Errore durante il salvataggio');
    }
}
```

## Vantaggi

1. **DRY (Don't Repeat Yourself)** - Codice riutilizzabile
2. **Consistenza** - Stile uniforme in tutta l'app
3. **Manutenibilità** - Modifiche centrali si propagano ovunque
4. **Accessibilità** - ARIA attributes e gestione tastiera
5. **Performance** - Caricamento una sola volta
6. **Backward Compatibility** - Alias per compatibilità con codice esistente

## Personalizzazione

Per modificare stili o comportamenti, editare direttamente:
- `/app/views/components/notifications.php`
- `/app/views/components/modals.php`

Le modifiche saranno applicate automaticamente a tutta l'applicazione.