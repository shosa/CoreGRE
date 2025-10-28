<?php

namespace App\Models;

/**
 * InvAnagrafiche Model
 * Table: inv_anagrafiche
 *
 * Auto-generated from database table
 */
class CoreAnagrafica extends BaseModel
{
    protected $table = 'core_anag';
    protected $primaryKey = 'ID';

    const UPDATED_AT = null;

    protected $fillable = [
            'cm',
            'art',
            'des',
            'barcode',
        ];

    protected $casts = [];

    // TODO: Add relationships here
}
