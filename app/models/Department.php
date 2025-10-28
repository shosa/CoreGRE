<?php

namespace App\Models;

/**
 * Reparti Model
 * Table: reparti
 *
 * Auto-generated from database table
 */
class Department extends BaseModel
{
    protected $table = 'rip_reparti';
    protected $primaryKey = 'ID';

    const UPDATED_AT = null;

    protected $fillable = [
            'Nome',
        ];

    protected $casts = [

        'ID' => 'integer',
        

    ];

    // TODO: Add relationships here
}
