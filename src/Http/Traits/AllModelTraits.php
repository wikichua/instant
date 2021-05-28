<?php

namespace Wikichua\Instant\Http\Traits;

trait AllModelTraits
{
    use \Wikichua\Instant\Http\Traits\ModelScopes;
    use \Wikichua\Instant\Http\Traits\Searchable;
    use \Wikichua\Instant\Http\Traits\DynamicFillable;
    use \Wikichua\Instant\Http\Traits\UserTimezone;

    protected static $opendns;

    public function setReadUrlAttribute($value)
    {
    }

    protected static function booted()
    {
        static::$opendns = '' == trim(static::$opendns) ?? opendns();
        static::saved(function ($model) {
            $onWhichEvent = $model->wasRecentlyCreated ? 'onCreatedEvent' : 'onUpdatedEvent';
            $mode = $model->wasRecentlyCreated ? 'Created' : 'Updated';
            $model->executeEvents([$onWhichEvent, 'onCachedEvent', 'updateSearchable']);
            $model->auditLogIt($mode);
            $model->snapshotIt($mode);
        });
        static::deleted(function ($model) {
            $model->executeEvents(['onDeletedEvent', 'onCachedEvent', 'deleteSearchable']);
            $model->auditLogIt('Deleted');
            $model->snapshotIt('Deleted');
        });
    }

    protected function executeEvents(array $methods)
    {
        foreach ($methods as $method) {
            if (method_exists($this, $method)) {
                call_user_func_array([$this, $method], [$this]);
            }
        }
    }

    protected function auditLogIt($mode = 'Created')
    {
        if (!\Str::contains(get_class($this), ['Searchable', 'Audit', 'Alert', 'Versionizer']) && isset($this->need_audit) && $this->need_audit) {
            $name = basename(str_replace('\\', '/', get_class($this)));
            if (isset($this->activity_name)) {
                $name = $this->activity_name;
            }
            audit($mode.' '.$name.': '.$this->id, $this->attributes, $this, static::$opendns);
        }
    }

    protected function snapshotIt($mode = 'Updated')
    {
        if ('created' != strtolower($mode) && !\Str::contains(get_class($this), ['Searchable', 'Audit', 'Alert', 'Versionizer']) && isset($this->snapshot) && $this->snapshot) {
            $changes = $this->getChanges();
            if (count($changes) || 'deleted' == strtolower($mode)) {
                $data = $this->getOriginal();
                app(config('instant.Models.Versionizer'))->create([
                    'mode' => $mode,
                    'model_class' => get_class($this),
                    'model_id' => $this->id,
                    'data' => $data,
                    'changes' => $changes,
                    'brand_id' => $this->brand_id ?? 0,
                ]);
            }
        }
    }
}
