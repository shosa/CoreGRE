<?php

namespace App\Models;

/**
 * Linee Model
 * Table: linee
 *
 * Auto-generated from database table
 */
class Line extends BaseModel
{
    protected $table = 'rip_linee';
    protected $primaryKey = 'ID';

    const UPDATED_AT = null;

    protected $fillable = [
            'sigla',
            'descrizione',
        ];

    protected $casts = [];

    // TODO: Add relationships here
}
