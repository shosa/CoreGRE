<?php

namespace App\Models;

/**
 * ScmSettings Model
 * Table: scm_settings
 *
 * Auto-generated from database table
 */
class ScmSettings extends BaseModel
{
    protected $table = 'scm_settings';

    protected $fillable = [
            'system_name',
            'company_name',
            'timezone',
            'language',
            'launch_number_prefix',
            'auto_start_phases',
            'require_phase_notes',
            'max_articles_per_launch',
            'notify_launch_completed',
            'notify_phase_blocked',
            'notify_laboratory_login',
            'notification_email',
            'session_timeout',
            'max_login_attempts',
            'password_min_length',
            'require_password_symbols',
        ];

    protected $casts = [
            'auto_start_phases' => 'integer',
            'require_phase_notes' => 'integer',
            'max_articles_per_launch' => 'integer',
            'notify_launch_completed' => 'integer',
            'notify_phase_blocked' => 'integer',
            'notify_laboratory_login' => 'integer',
            'session_timeout' => 'integer',
            'max_login_attempts' => 'integer',
            'password_min_length' => 'integer',
            'require_password_symbols' => 'integer',
        ];

    // TODO: Add relationships here
}
