<?php
/**
 * Configurazione Cron Jobs
 * Lista job registrati da eseguire automaticamente
 *
 * Aggiungi qui le classi dei job da schedulare
 */

return [
    // Job di pulizia
    'CleanupTempFilesJob',          // Ogni giorno alle 3:00
    // 'CleanupCronLogsJob',        // DISABILITATO - Causava problemi con i log
    // Job di backup
    'DatabaseBackupJob',             // Ogni giorno alle 2:00

    // Job di sincronizzazione
    'SyncCoreSCMJob',                // Ogni 15 minuti - Sync con CoreSCM su Aruba

    // Aggiungi altri job qui...
    // 'SendDailyReportsJob',
    // 'SyncProductionDataJob',
];
