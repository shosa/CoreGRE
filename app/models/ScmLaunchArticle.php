<?php

namespace App\Models;

/**
 * SCM Launch Article Model - Articoli nei lanci
 * Tabella: scm_launch_articles
 */
class ScmLaunchArticle extends BaseModel
{
    protected $table = 'scm_launch_articles';
    protected $primaryKey = 'id';

    protected $fillable = [
        'launch_id',
        'article_name',
        'total_pairs',
        'article_order',
        'notes'
    ];

    protected $casts = [
        'total_pairs' => 'integer',
        'article_order' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * Relationship with launch
     */
    public function launch()
    {
        return $this->belongsTo(ScmLaunch::class, 'launch_id');
    }

    /**
     * Relationship with progress tracking
     */
    public function progressTracking()
    {
        return $this->hasMany(ScmProgressTracking::class, 'article_id');
    }

    /**
     * Scope ordered by article order
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('article_order');
    }

    /**
     * Get formatted pairs
     */
    public function getFormattedPairsAttribute()
    {
        return number_format($this->total_pairs);
    }

    /**
     * Get display name
     */
    public function getDisplayNameAttribute()
    {
        return $this->article_name . " ({$this->formatted_pairs} paia)";
    }

    /**
     * Get completion percentage
     */
    public function getCompletionPercentageAttribute()
    {
        $totalCompleted = $this->progressTracking()
            ->where('status', 'COMPLETATA')
            ->sum('completed_pairs');

        if ($this->total_pairs == 0) return 0;

        return round(($totalCompleted / $this->total_pairs) * 100, 2);
    }

    /**
     * Check if article has notes
     */
    public function hasNotes()
    {
        return !empty($this->notes);
    }
}