<?php

namespace App\Models;

/**
 * Reparti Model
 * Table: reparti
 *
 * Auto-generated from database table
 */
class Reparti extends BaseModel
{
    protected $table = 'reparti';
    protected $primaryKey = 'ID';

    const UPDATED_AT = null;

    protected $fillable = [
            'Nome',
        ];

    protected $casts = [];

    // TODO: Add relationships here
}
