<?php

namespace Wikichua\Instant\Http\Traits;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;

trait DynamicFillable
{
    // set fillable using db table columns
    public function getFillable()
    {
        if ('User' != class_basename(get_class($this))
            && isset($this->fillable)
            && is_array($this->fillable)
            && count($this->fillable)) {
            return $this->fillable;
        }

        return Cache::tags('fillable')->remember('fillable:'.$this->getTable(), (60 * 60 * 24), function () {
            return Schema::connection($this->connection)->getColumnListing($this->getTable());
        });
    }
}
