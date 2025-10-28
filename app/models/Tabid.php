<?php

namespace App\Models;

/**
 * Tabid Model
 * Table: tabid
 *
 * Auto-generated from database table
 */
class Tabid extends BaseModel
{
    protected $table = 'rip_tabid';
    protected $primaryKey = 'ID';
    public $timestamps = false;

    protected $fillable = [
            'ID',
        ];

    protected $casts = [
            'ID' => 'integer',
            'created_at' => 'datetime',
            'updated_at' => 'datetime'
        ];

    // TODO: Add relationships here
}
