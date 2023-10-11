<?php

namespace App\Nova;

use App\Nova\Actions\BuildPackage;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\BelongsToMany;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Line;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\NovaRequest;

class Package extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var class-string<\App\Models\Package>
     */
    public static $model = \App\Models\Package::class;

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'name';

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
        'id',
        'name',
        'technical_name',
    ];

    /**
     * Get the fields displayed by the resource.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return array
     */
    public function fields(NovaRequest $request)
    {
        return [
            ID::make(),
            Text::make(__('Name'), 'name')
                ->sortable()
                ->rules('required', 'max:255'),
            Text::make(__('Technical Name'), 'technical_name')
                ->sortable()
                ->rules('required', 'max:255')
                ->copyable()
                ->creationRules('unique:packages,technical_name')
                ->updateRules('unique:packages,technical_name,{{resourceId}}'),
            Text::make(__('Url'), 'url')
                ->sortable()
                ->copyable()
                ->rules('required', 'max:255')
                ->creationRules('unique:packages,url')
                ->updateRules('unique:packages,url,{{resourceId}}'),
            BelongsToMany::make('Applications'),
        ];
    }

    /**
     * Get the cards available for the request.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return array
     */
    public function cards(NovaRequest $request)
    {
        return [];
    }

    /**
     * Get the filters available for the resource.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return array
     */
    public function filters(NovaRequest $request)
    {
        return [];
    }

    /**
     * Get the lenses available for the resource.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return array
     */
    public function lenses(NovaRequest $request)
    {
        return [];
    }

    /**
     * Get the actions available for the resource.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return array
     */
    public function actions(NovaRequest $request)
    {
        return [
            new BuildPackage(),
        ];
    }
}
