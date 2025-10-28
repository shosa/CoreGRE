<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Notification Model
 *
 * Sistema di notifiche interno WEBGRE
 *
 * @property int $id
 * @property int $user_id
 * @property string $type
 * @property string $title
 * @property string $message
 * @property string|null $link
 * @property string|null $icon
 * @property string|null $color
 * @property \Carbon\Carbon|null $read_at
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 */
class Notification extends BaseModel
{
    protected $table = 'auth_notifications';

    protected $fillable = [
        'user_id',
        'type',
        'title',
        'message',
        'link',
        'icon',
        'color',
        'read_at'
    ];

    protected $casts = [
        'user_id' => 'integer',
        'read_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * Tipi di notifica disponibili
     */
    const TYPE_INFO = 'info';
    const TYPE_SUCCESS = 'success';
    const TYPE_WARNING = 'warning';
    const TYPE_ERROR = 'error';
    const TYPE_REPAIR = 'repair';
    const TYPE_QUALITY = 'quality';
    const TYPE_PRODUCTION = 'production';
    const TYPE_EXPORT = 'export';
    const TYPE_SYSTEM = 'system';

    /**
     * Relazione: appartiene ad un utente
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Scope: solo notifiche non lette
     */
    public function scopeUnread($query)
    {
        return $query->whereNull('read_at');
    }

    /**
     * Scope: solo notifiche lette
     */
    public function scopeRead($query)
    {
        return $query->whereNotNull('read_at');
    }

    /**
     * Scope: per utente specifico
     */
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope: per tipo
     */
    public function scopeOfType($query, $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Scope: recenti (ultimi 30 giorni)
     */
    public function scopeRecent($query, $days = 30)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    /**
     * Marca la notifica come letta
     */
    public function markAsRead()
    {
        if ($this->read_at === null) {
            $this->read_at = now();
            $this->save();
        }
    }

    /**
     * Marca la notifica come non letta
     */
    public function markAsUnread()
    {
        $this->read_at = null;
        $this->save();
    }

    /**
     * Verifica se la notifica è stata letta
     */
    public function isRead(): bool
    {
        return $this->read_at !== null;
    }

    /**
     * Verifica se la notifica è non letta
     */
    public function isUnread(): bool
    {
        return $this->read_at === null;
    }

    /**
     * Ottieni l'icona di default per tipo
     */
    public function getDefaultIcon(): string
    {
        if ($this->icon) {
            return $this->icon;
        }

        return match($this->type) {
            self::TYPE_SUCCESS => 'fas fa-check-circle',
            self::TYPE_WARNING => 'fas fa-exclamation-triangle',
            self::TYPE_ERROR => 'fas fa-times-circle',
            self::TYPE_REPAIR => 'fas fa-tools',
            self::TYPE_QUALITY => 'fas fa-shield-alt',
            self::TYPE_PRODUCTION => 'fas fa-industry',
            self::TYPE_EXPORT => 'fas fa-file-export',
            self::TYPE_SYSTEM => 'fas fa-cog',
            default => 'fas fa-bell'
        };
    }

    /**
     * Ottieni il colore di default per tipo
     */
    public function getDefaultColor(): string
    {
        if ($this->color) {
            return $this->color;
        }

        return match($this->type) {
            self::TYPE_SUCCESS => 'green',
            self::TYPE_WARNING => 'yellow',
            self::TYPE_ERROR => 'red',
            self::TYPE_REPAIR => 'blue',
            self::TYPE_QUALITY => 'purple',
            self::TYPE_PRODUCTION => 'indigo',
            self::TYPE_EXPORT => 'orange',
            self::TYPE_SYSTEM => 'gray',
            default => 'blue'
        };
    }

    /**
     * Helper statico: Crea notifica per utente
     *
     * @param int $userId
     * @param string $type
     * @param string $title
     * @param string $message
     * @param string|null $link
     * @param string|null $icon
     * @param string|null $color
     * @return self
     */
    public static function create(array $attributes = [])
    {
        return parent::create($attributes);
    }

    /**
     * Helper statico: Notifica multipla a più utenti
     *
     * @param array $userIds
     * @param string $type
     * @param string $title
     * @param string $message
     * @param string|null $link
     * @return int Numero notifiche create
     */
    public static function notifyUsers(array $userIds, string $type, string $title, string $message, ?string $link = null): int
    {
        $count = 0;
        foreach ($userIds as $userId) {
            self::create([
                'user_id' => $userId,
                'type' => $type,
                'title' => $title,
                'message' => $message,
                'link' => $link
            ]);
            $count++;
        }
        return $count;
    }

    /**
     * Helper statico: Notifica a tutti gli utenti
     *
     * @param string $type
     * @param string $title
     * @param string $message
     * @param string|null $link
     * @return int Numero notifiche create
     */
    public static function notifyAll(string $type, string $title, string $message, ?string $link = null): int
    {
        $userIds = User::pluck('id')->toArray();
        return self::notifyUsers($userIds, $type, $title, $message, $link);
    }

    /**
     * Helper statico: Notifica agli admin
     *
     * @param string $type
     * @param string $title
     * @param string $message
     * @param string|null $link
     * @return int Numero notifiche create
     */
    public static function notifyAdmins(string $type, string $title, string $message, ?string $link = null): int
    {
        $adminIds = User::where('admin_type', 'admin')->pluck('id')->toArray();
        return self::notifyUsers($adminIds, $type, $title, $message, $link);
    }
}
