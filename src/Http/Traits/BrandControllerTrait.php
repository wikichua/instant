<?php

namespace Wikichua\Instant\Http\Traits;

use Illuminate\Http\Request;

trait BrandControllerTrait
{
    public function register($brandName)
    {
        $this->brand = cache()->tags('brand')->remember('register-'.$brandName, (60 * 60 * 24), function () use ($brandName) {
            return app(config('instant.Models.Brand'))->query()
                ->where('name', $brandName)->first();
        });
        \Config::set('main.brand', $this->brand);

        return $this;
    }

    public function setLocale()
    {
        $locale = request()->route('locale');
        if (!in_array($locale, $this->supportedLocales)) {
            $locale = 'en';
        }
        app()->setLocale($locale);

        return $this;
    }

    public function slug(Request $request)
    {
        if (count($request->segments()) > 1) {
            $segs = $request->segments();
            unset($segs[0]);
            $slug = implode('/', $segs);
        }
        $model = app(config('instant.Models.Page'))->query()
            ->where('brand_id', $this->brand->id)
            ->where('locale', app()->getLocale())
            ->where('slug', strtolower($slug))
            ->first()
        ;
        if (!$model) {
            abort(404);
        }

        return $model;
    }

    public function page(Request $request, $locale)
    {
        $model = $this->slug($request);

        return $this->getViewPage($model->blade_file ?? 'page', compact('model'));
    }

    public function getViewPage($file, array $compact = [])
    {
        return view($this->page_path.$file, $compact);
    }
}
