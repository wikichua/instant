<?php

namespace Wikichua\Instant\Http\Traits;

trait Searchable
{
    public function searchableAs()
    {
        return get_class($this);
    }

    public function toSearchableArray()
    {
        $array = [];
        if (isset($this->searchableFields)) {
            foreach ($this->searchableFields as $field) {
                $array[$field] = $this->attributes[$field] ?? $this->{$field};
            }
        } else {
            $array = $this->toArray();
        }

        return $array;
    }

    protected function createSearchable()
    {
        if (count($this->toSearchableArray())) {
            $searchable = app(config('instant.Models.Searchable'))->create([
                'model' => $this->searchableAs(),
                'model_id' => $this->id,
                'tags' => $this->toSearchableArray(),
                'brand_id' => $this->brand_id ?? 0,
            ]);
        }
    }

    protected function updateSearchable()
    {
        if (count($this->toSearchableArray())) {
            $searchable = app(config('instant.Models.Searchable'))
                ->where('model', $this->searchableAs())
                ->where('model_id', $this->id)
            ;

            if ($this->brand_id) {
                $searchable->where('brand_id', $this->brand_id);
            }

            $searchable = $searchable->update([
                'model' => $this->searchableAs(),
                'model_id' => $this->id,
                'tags' => $this->toSearchableArray(),
                'brand_id' => $this->brand_id ?? 0,
            ]);
        }
    }

    protected function deleteSearchable()
    {
        if (count($this->toSearchableArray())) {
            $searchable = app(config('instant.Models.Searchable'))
                ->where('model', $this->searchableAs())
                ->where('model_id', $this->id)
            ;

            if ($this->brand_id) {
                $searchable->where('brand_id', $this->brand_id);
            }

            $searchable->delete();
        }
    }
}
