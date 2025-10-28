<?php

namespace App\Models;

/**
 * Laboratori Model
 * Table: laboratori
 *
 * Auto-generated from database table
 */
class Laboratory extends BaseModel
{
    protected $table = 'rip_laboratori';
    protected $primaryKey = 'ID';

    const UPDATED_AT = null;

    protected $fillable = [
            'Nome',
        ];

    protected $casts = [];

    // TODO: Add relationships here
}
