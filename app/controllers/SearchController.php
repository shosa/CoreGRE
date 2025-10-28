<?php
/**
 * Search Controller
 * Gestisce la ricerca globale nel sistema
 */

use App\Models\User;
use App\Models\ExportDocument;
use App\Models\Repair;
use App\Models\QualityRecord;
use App\Models\MrpMaterial;
use App\Models\ScmLaunch;

class SearchController extends BaseController
{
    /**
     * Ricerca globale
     */
    public function search()
    {
        $this->requireAuth();

        if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
            $this->json(['error' => 'Metodo non permesso'], 405);
            return;
        }

        $query = trim($this->input('q'));

        if (empty($query) || strlen($query) < 2) {
            $this->json([
                'success' => true,
                'results' => [],
                'message' => 'Inserisci almeno 2 caratteri per cercare'
            ]);
            return;
        }

        try {
            $results = [];

            // Cerca in base al tipo di query
            if (is_numeric($query)) {
                // Se è numerico, cerca cartellini, ordini, ID
                $results = array_merge($results, $this->searchNumeric($query));
            } else {
                // Se è testo, cerca nomi, descrizioni, etc.
                $results = array_merge($results, $this->searchText($query));
            }

            // Limita risultati
            $results = array_slice($results, 0, 20);

            $this->json([
                'success' => true,
                'results' => $results,
                'query' => $query,
                'total' => count($results)
            ]);

        } catch (Exception $e) {
            error_log("Search error: " . $e->getMessage());
            $this->json([
                'success' => false,
                'error' => 'Errore durante la ricerca'
            ], 500);
        }
    }

    /**
     * Ricerca numerica (cartellini, ID, ordini)
     */
    private function searchNumeric($query)
    {
        $results = [];



        // Cerca DDT/Export
        try {
    if ($this->hasPermission('export')) {
        $exports = ExportDocument::query()
            ->join('exp_terzisti', 'exp_documenti.id_terzista', '=', 'exp_terzisti.id')
            ->where('exp_documenti.id', $query)
            ->select('exp_documenti.*', 'exp_terzisti.ragione_sociale')
            ->limit(5)
            ->get();

        foreach ($exports as $export) {
            $results[] = [
                'type' => 'export',
                'icon' => 'fas fa-file-export',
                'title' => "DDT #{$export->id} - {$export->ragione_sociale}",
                'subtitle' => "Stato: {$export->stato} - " . date('d/m/Y', strtotime($export->data)),
                'url' => $this->url("/export/continue/{$export->id}"),
                'category' => 'Export'
            ];
        }
    }
} catch (Exception $e) {
    error_log("Search ExportDocument error: " . $e->getMessage());
}

        // Cerca riparazioni
        try {
            if ($this->hasPermission('riparazioni')) {
                $repairs = Repair::where('IDRIP', $query)
                    ->orWhere('CARTELLINO', 'LIKE', "%{$query}%")
                    ->limit(5)
                    ->get();

                foreach ($repairs as $repair) {
                    $results[] = [
                        'type' => 'repair',
                        'icon' => 'fas fa-hammer',
                        'title' => "Riparazione #{$repair->IDRIP}",
                        'subtitle' => "Cartellino: {$repair->CARTELLINO} - " . ($repair->CAUSALE ?? 'N/A'),
                        'url' => $this->url("/riparazioni/show/{$repair->IDRIP}"),
                        'category' => 'Riparazioni'
                    ];
                }
            }
        } catch (Exception $e) {
            error_log("Search Repair error: " . $e->getMessage());
        }

        return $results;
    }

    /**
     * Ricerca testuale (nomi, descrizioni)
     */
    private function searchText($query)
    {
        $results = [];

        // Cerca utenti
        try {
            if ($this->hasPermission('utenti')) {
                $users = User::where('nome', 'LIKE', "%{$query}%")
                    ->orWhere('user_name', 'LIKE', "%{$query}%")
                    ->orWhere('mail', 'LIKE', "%{$query}%")
                    ->limit(5)
                    ->get();

                foreach ($users as $user) {
                    $results[] = [
                        'type' => 'user',
                        'icon' => 'fas fa-user',
                        'title' => $user->nome,
                        'subtitle' => "@{$user->user_name} - {$user->mail}",
                        'url' => $this->url("/users/edit/{$user->id}"),
                        'category' => 'Utenti'
                    ];
                }
            }
        } catch (Exception $e) {
            error_log("Search User error: " . $e->getMessage());
        }

        // Cerca materiali MRP
        try {
            if ($this->hasPermission('mrp')) {
                $materials = MrpMaterial::where('description', 'LIKE', "%{$query}%")
                    ->orWhere('material_code', 'LIKE', "%{$query}%")
                    ->limit(5)
                    ->get();

                foreach ($materials as $material) {
                    $results[] = [
                        'type' => 'material',
                        'icon' => 'fas fa-box',
                        'title' => $material->description,
                        'subtitle' => "Codice: {$material->material_code}",
                        'url' => $this->url("/mrp/material/{$material->id}"),
                        'category' => 'MRP'
                    ];
                }
            }
        } catch (Exception $e) {
            error_log("Search MrpMaterial error: " . $e->getMessage());
        }



        return $results;
    }

    /**
     * Mostra pagina risultati ricerca (opzionale)
     */
    public function results()
    {
        $this->requireAuth();

        $query = trim($this->input('q'));

        $data = [
            'pageTitle' => 'Risultati Ricerca - ' . APP_NAME,
            'query' => $query
        ];

        $this->render('search.results', $data);
    }
}