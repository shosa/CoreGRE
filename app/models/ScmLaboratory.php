<?php

namespace App\Models;

/**
 * SCM Laboratory Model - Laboratori
 * Tabella: scm_laboratories
 */
class ScmLaboratory extends BaseModel
{
    protected $table = 'scm_laboratories';
    protected $primaryKey = 'id';

    protected $fillable = [
        'name',
        'email',
        'username',
        'password_hash',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'last_login' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    protected $hidden = [
        'password_hash'
    ];

    /**
     * Relationship with launches
     */
    public function launches()
    {
        return $this->hasMany(ScmLaunch::class, 'laboratory_id');
    }

    /**
     * Relationship with active launches
     */
    public function activeLaunches()
    {
        return $this->hasMany(ScmLaunch::class, 'laboratory_id')->active();
    }

    /**
     * Scope for active laboratories
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for inactive laboratories
     */
    public function scopeInactive($query)
    {
        return $query->where('is_active', false);
    }

    /**
     * Check password
     */
    public function checkPassword($password)
    {
        return password_verify($password, $this->password_hash);
    }

    /**
     * Hash and set password
     */
    public function setPassword($password)
    {
        $this->password_hash = password_hash($password, PASSWORD_DEFAULT);
    }

    /**
     * Get total launches count
     */
    public function getTotalLaunchesAttribute()
    {
        return $this->launches()->count();
    }

    /**
     * Get active launches count
     */
    public function getActiveLaunchesCountAttribute()
    {
        return $this->activeLaunches()->count();
    }

    /**
     * Check if laboratory is active
     */
    public function isActive()
    {
        return $this->is_active;
    }

    /**
     * Get status badge class
     */
    public function getStatusBadgeAttribute()
    {
        return $this->is_active
            ? 'bg-green-100 text-green-800'
            : 'bg-red-100 text-red-800';
    }

    /**
     * Get status text
     */
    public function getStatusTextAttribute()
    {
        return $this->is_active ? 'Attivo' : 'Disattivo';
    }
}