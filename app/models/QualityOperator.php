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
    protected $table = 'cq_operators';
    const UPDATED_AT = null;

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
