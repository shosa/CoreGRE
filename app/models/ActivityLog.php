<?php

namespace App\Models;

/**
 * Activity Log Model
 * Gestisce il log delle attività degli utenti nel sistema
 */
class ActivityLog extends BaseModel
{
    protected $table = 'core_log';

    protected $fillable = [
        'user_id',
        'category',
        'activity_type',
        'description',
        'note',
        'text_query'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'user_id' => 'integer'
    ];

    // Disable updated_at since this table only has created_at
    public $timestamps = false;

    protected $dates = ['created_at'];

    /**
     * Relationship with User model
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Scope for filtering by category
     */
    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    /**
     * Scope for filtering by activity type
     */
    public function scopeByType($query, $type)
    {
        return $query->where('activity_type', $type);
    }

    /**
     * Scope for filtering by user
     */
    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope for recent activities
     */
    public function scopeRecent($query, $days = 7)
    {
        $date = new \DateTime();
        $date->sub(new \DateInterval('P' . $days . 'D'));
        return $query->where('created_at', '>=', $date);
    }

    /**
     * Scope for recent report generation activities
     */
    public function scopeRecentReports($query, $days = 30)
    {
        $date = date('Y-m-d H:i:s', strtotime("-{$days} days"));
        return $query->whereIn('activity_type', [
                'GENERATE_CARTEL_PDF',
                'GENERATE_LOT_PDF',
                'GENERATE_CARTEL_EXCEL',
                'GENERATE_LOT_EXCEL'
            ])
            ->where('created_at', '>=', $date);
    }

   
    /**
     * Scope for today's activities
     */
    public function scopeToday($query)
    {
        $today = new \DateTime();
        return $query->whereDate('created_at', $today->format('Y-m-d'));
    }

    /**
     * Static method to log activity
     */
    public static function logActivity($userId, $category, $activityType, $description, $note = '', $textQuery = '')
    {
        return static::create([
            'user_id' => $userId,
            'category' => $category,
            'activity_type' => $activityType,
            'description' => $description,
            'note' => $note,
            'text_query' => $textQuery
        ]);
    }

    /**
     * Get formatted time ago
     */
    public function getTimeAgoAttribute()
    {
        $time = time() - strtotime($this->created_at);

        if ($time < 60) {
            return $time == 1 ? '1 secondo fa' : $time . ' secondi fa';
        }

        $time = round($time / 60);
        if ($time < 60) {
            return $time == 1 ? '1 minuto fa' : $time . ' minuti fa';
        }

        $time = round($time / 60);
        if ($time < 24) {
            return $time == 1 ? '1 ora fa' : $time . ' ore fa';
        }

        $time = round($time / 24);
        if ($time < 30) {
            return $time == 1 ? '1 giorno fa' : $time . ' giorni fa';
        }

        $time = round($time / 30);
        if ($time < 12) {
            return $time == 1 ? '1 mese fa' : $time . ' mesi fa';
        }

        $time = round($time / 12);
        return $time == 1 ? '1 anno fa' : $time . ' anni fa';
    }

    /**
     * Get category display name
     */
    public function getCategoryDisplayAttribute()
    {
        $categories = [
            'auth' => 'Autenticazione',
            'users' => 'Gestione Utenti',
            'riparazioni' => 'Riparazioni',
            'produzione' => 'Produzione',
            'quality' => 'Controllo Qualità',
            'export' => 'Esportazioni',
            'settings' => 'Impostazioni',
            'system' => 'Sistema'
        ];

        return $categories[$this->category] ?? ucfirst($this->category);
    }

    /**
     * Get activity type display name
     */
    public function getTypeDisplayAttribute()
    {
        $types = [
            'login' => 'Accesso',
            'logout' => 'Disconnessione',
            'create' => 'Creazione',
            'update' => 'Modifica',
            'delete' => 'Eliminazione',
            'view' => 'Visualizzazione',
            'export' => 'Esportazione',
            'import' => 'Importazione'
        ];

        return $types[$this->activity_type] ?? ucfirst($this->activity_type);
    }

    /**
     * Get activity icon based on type
     */
    public function getIconAttribute()
    {
        $icons = [
            'login' => 'fas fa-sign-in-alt',
            'logout' => 'fas fa-sign-out-alt',
            'create' => 'fas fa-plus',
            'update' => 'fas fa-edit',
            'delete' => 'fas fa-trash',
            'view' => 'fas fa-eye',
            'export' => 'fas fa-download',
            'import' => 'fas fa-upload'
        ];

        return $icons[$this->activity_type] ?? 'fas fa-info-circle';
    }
}