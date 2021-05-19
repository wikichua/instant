<?php

namespace Wikichua\Instant\Models;

class Mailer extends \Spatie\MailTemplates\Models\MailTemplate
{
    use \Illuminate\Database\Eloquent\SoftDeletes;
    use \Wikichua\Instant\Http\Traits\AllModelTraits;

    protected $table = 'mail_templates';
    protected $menu_icon = 'fas fa-mail-bulk';
    protected $need_audit = true;
    protected $snapshot = true;

    protected $dates = ['deleted_at'];
    protected $fillable = [
        'mailable',
        'subject',
        'html_template',
        'text_template',
        'created_by',
        'updated_by',
    ];

    protected $appends = [
        'readUrl',
    ];

    protected $searchableFields = ['subject'];

    protected $casts = [
    ];

    public function scopeFilterSubject($query, $search)
    {
        return $query->where('subject', 'like', "%{$search}%");
    }

    public function getReadUrlAttribute($value)
    {
        return $this->readUrl = route('mailer.show', $this->id);
    }
}
