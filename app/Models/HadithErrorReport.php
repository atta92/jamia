<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HadithErrorReport extends Model
{
    public const TYPE_MISMATCHED_TRANSLATION = 'mismatched_translation';

    public const TYPE_SPELLING_MISTAKE = 'spelling_mistake';

    public const TYPE_INCOMPLETE_TEXT = 'incomplete_text';

    public const TYPE_MISTRANSLATION = 'mistranslation';

    public const TYPE_WRONG_NUMBER = 'wrong_number';

    public const TYPE_OTHER = 'other';

    public const STATUS_PENDING = 'pending';

    public const STATUS_REVIEWED = 'reviewed';

    public const STATUS_FIXED = 'fixed';

    public const STATUS_DISMISSED = 'dismissed';

    protected $fillable = [
        'hadith_id',
        'error_type',
        'other_detail',
        'details',
        'notify_email',
        'email',
        'status',
        'page_url',
        'ip_address',
        'user_agent',
    ];

    protected function casts(): array
    {
        return [
            'notify_email' => 'boolean',
        ];
    }

    public static function errorTypes(): array
    {
        return [
            self::TYPE_MISMATCHED_TRANSLATION => 'Mismatched translation',
            self::TYPE_SPELLING_MISTAKE => 'Spelling mistake',
            self::TYPE_INCOMPLETE_TEXT => 'Incomplete text',
            self::TYPE_MISTRANSLATION => 'Mistranslation',
            self::TYPE_WRONG_NUMBER => 'Wrong numbering',
            self::TYPE_OTHER => 'Other (please specify)',
        ];
    }

    public function errorTypeLabel(): string
    {
        return self::errorTypes()[$this->error_type] ?? $this->error_type;
    }

    public function hadith(): BelongsTo
    {
        return $this->belongsTo(Hadith::class);
    }
}
