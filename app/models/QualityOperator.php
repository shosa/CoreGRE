<?php

namespace App\Models;

/**
 * Operators Model
 * Table: operators
 *
 * Auto-generated from database table
 */
class QualityOperator extends BaseModel
{
    protected $table = 'inwork_operators';
    protected $primaryKey = 'user';
    public $incrementing = false;
    protected $keyType = 'string';
    public const UPDATED_AT = null;

    protected $fillable = [
            'user',
            'full_name',
            'pin',
            'reparto',
        ];

    protected $casts = [
            'pin' => 'integer',
        ];

    // TODO: Add relationships here
}
