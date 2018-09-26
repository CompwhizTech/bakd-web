<?php

namespace BAKD\Manage;

use Laravel\Nova\Fields\ID as Id;
use Laravel\Nova\Fields\Text as Text;
use Laravel\Nova\Fields\Uuid as Uuid;
use Laravel\Nova\Fields\Markdown as Markdown;
use Laravel\Nova\Fields\Image as Image;
use Laravel\Nova\Fields\Number as Number;
use Laravel\Nova\Fields\Avatar as Avatar;
use Laravel\Nova\Fields\DateTime as DateTime;
use Laravel\Nova\Fields\Select as Select;
use Laravel\Nova\Fields\Status as Status;
use Laravel\Nova\Fields\Trix as Trix;
use Illuminate\Http\Request;
use Laravel\Nova\Http\Requests\NovaRequest;

class Bounty extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = 'BAKD\Bounty';

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'Bounties';

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
        'id', 'uuid', 'name'
    ];

    /**
     * Get the fields displayed by the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function fields(Request $request)
    {
        $typeOptions = [];

        // TODO: Move to Bounty Type model
        $typeOptions = collect(\BAKD\BountyType::all())->map(function ($row) use ($typeOptions) {
            return $typeOptions[$row->id] = $row->name;
        })->toArray();

        return [
            ID::make('ID', 'id')->sortable()->onlyOnDetail(),
            Avatar::make('Logo', 'image')->sortable(),
            Text::make('Name', 'name')->sortable()->rules('required'),
            Select::make('Bounty Type', 'type_id')->options($typeOptions)->displayUsingLabels()->rules('required'),
            Trix::make('Description', 'description')->withFiles('/uploads/bounty')->rules('required'),
            Number::make('Reward Amount', 'reward')->min(0)->max(1000000)->step(10)->rules('required'),
            Number::make('Max Reward Amount', 'reward_total')->min(0)->step(100)->rules('required'),
            DateTime::make('Starts Date', 'start_date'),
            DateTime::make('Ends Date', 'end_date'),
            Text::make('Bounty UUID', 'uuid')->sortable()->exceptOnForms(),
        ];
    }

    /**
     * Get the cards available for the request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function cards(Request $request)
    {
        return [];
    }

    /**
     * Get the filters available for the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function filters(Request $request)
    {
        return [];
    }

    /**
     * Get the lenses available for the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function lenses(Request $request)
    {
        return [];
    }

    /**
     * Get the actions available for the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function actions(Request $request)
    {
        return [];
    }
}
