<?php

namespace App\Nova;

use App\Traits\UUID;
use Illuminate\Http\Request;
use Iwaves\ComposerInspector\ComposerInspector;
use Laravel\Nova\Fields\BelongsToMany;
use Laravel\Nova\Fields\Boolean;
use Laravel\Nova\Fields\Date;
use Laravel\Nova\Fields\HasMany;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\URL;
use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Http\Requests\NovaRequest;

class Application extends Resource
{
    use UUID;

    /**
     * The model the resource corresponds to.
     *
     * @var class-string<\App\Models\Application>
     */
    public static $model = \App\Models\Application::class;

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
        'domain',
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
            ID::make()->sortable()
                ->rules('uuid'),

            Text::make('Name')
                ->sortable()
                ->rules('required', 'max:255'),

            Text::make('Domain')
                ->sortable()
                ->rules('required', 'url')
                ->creationRules('unique:applications,domain')
                ->updateRules('unique:applications,domain,{{resourceId}}'),

            Boolean::make('Active')
                ->sortable()
                ->rules('required'),

            ComposerInspector::make()->withMeta(['application_url' => config('app.url'), 'token' => $this->model()->tokens->first()->token ?? null]),
            HasMany::make('Tokens', 'tokens', ApplicationToken::class),

            BelongsToMany::make('Packages'),
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
        return [];
    }
}
