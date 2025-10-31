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

   

    protected $fillable = [
        'sigla',
        'descrizione',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    // TODO: Add relationships here
}
