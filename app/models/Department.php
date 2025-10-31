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

   

    protected $fillable = [
        'Nome',
    ];

    protected $casts = [

        'ID' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'


    ];

    // TODO: Add relationships here
}
