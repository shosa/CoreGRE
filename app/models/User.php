<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * User Model
 * Rappresenta gli utenti del sistema COREGRE
 *
 * @property int $id
 * @property string $user_name
 * @property string $nome
 * @property string $password
 * @property string|null $theme_color
 * @property string|null $series_id
 * @property string|null $remember_token
 * @property \Carbon\Carbon|null $expires
 * @property string $admin_type
 * @property string|null $mail
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $last_login
 * @property \Carbon\Carbon|null $updated_at
 */
class User extends BaseModel
{
    /**
     * The table associated with the model.
     */
    protected $table = 'auth_users';

    /**
     * The primary key for the model.
     */
    protected $primaryKey = 'id';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'user_name',
        'nome',
        'password',
        'theme_color',
        'admin_type',
        'mail',
        'last_login'
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'expires' => 'datetime',
        'created_at' => 'datetime',
        'last_login' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * The attributes that should be hidden for serialization.
     */
    protected $hidden = [
        'password',
        'remember_token',
        'series_id'
    ];

    /**
     * Automatically hash password when setting
     */
    public function setPasswordAttribute($value)
    {
        if (!empty($value)) {
            $this->attributes['password'] = password_hash($value, PASSWORD_DEFAULT, ['cost' => HASH_COST]);
        }
    }

    /**
     * Check if password matches
     */
    public function checkPassword($password)
    {
        return password_verify($password, $this->password);
    }

    /**
     * Check if user is admin
     */
    public function isAdmin()
    {
        return $this->admin_type === 'admin';
    }

    /**
     * Get full display name
     */
    public function getDisplayNameAttribute()
    {
        return $this->nome ?: $this->user_name;
    }

    /**
     * Scope for active users
     */
    public function scopeActive($query)
    {
        return $query->whereNull('expires')->orWhere('expires', '>', new \DateTime());
    }

    /**
     * User permissions relationship
     */
    public function permissions(): HasOne
    {
        return $this->hasOne(Permission::class, 'id_utente');
    }

    /**
     * User activity logs relationship
     */
    public function activityLogs(): HasMany
    {
        return $this->hasMany(ActivityLog::class, 'user_id');
    }

    /**
     * User notifications relationship
     */
    public function notifications(): HasMany
    {
        return $this->hasMany(Notification::class, 'user_id');
    }

    /**
     * User widgets relationship
     */
    public function widgets(): HasMany
    {
        return $this->hasMany(UserWidget::class, 'user_id');
    }

    /**
     * User created shipments relationship
     */
    public function shipments(): HasMany
    {
        return $this->hasMany(Shipment::class, 'createdby_userid');
    }

    /**
     * User created MRP materials relationship
     */
    public function mrpMaterials(): HasMany
    {
        return $this->hasMany(MrpMaterial::class, 'user_id');
    }

    /**
     * Generate remember token
     */
    public function generateRememberToken()
    {
        $this->remember_token = bin2hex(random_bytes(32));
        $this->series_id = bin2hex(random_bytes(16));
        $expires = new \DateTime();
        $expires->add(new \DateInterval('PT' . REMEMBER_TOKEN_LIFETIME . 'S'));
        $this->expires = $expires;
        $this->save();

        return $this->remember_token;
    }

    /**
     * Clear remember token
     */
    public function clearRememberToken()
    {
        $this->remember_token = null;
        $this->series_id = null;
        $this->expires = null;
        $this->save();
    }

    /**
     * Update last login timestamp
     */
    public function updateLastLogin()
    {
        $this->last_login = new \DateTime();
        $this->save();
    }
}