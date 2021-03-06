<?php

namespace App\Nova;

use Illuminate\Validation\Rules;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\BelongsToMany;
use Laravel\Nova\Fields\HasMany;
use Laravel\Nova\Fields\Gravatar;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\MorphToMany;
use Laravel\Nova\Fields\Password;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\NovaRequest;

class User extends Resource
{
    public static $group = 'Основное';

    public static function label() {
        return 'Пользователи';
    }
    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = \App\Models\User::class;

    //6.06. Для разбивки ресурсов на группы, используй св-во $group;
    // public static $group = 'Основное';

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
        'id', 'name', 'email',
    ];

    /**
     * Get the fields displayed by the resource.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return array
     */
    public function fields(NovaRequest $request)
    {
        // 8.06. Отсюда не можем получить id, чтобы найти юзера
        // dd($request);
        return [
            ID::make()->sortable(),

            Gravatar::make()->maxWidth(50),

            Text::make('Name')
                ->sortable()
                ->rules('required', 'max:255'),

            Text::make('Email')
                ->sortable(),

            Password::make('Password')
                ->onlyOnForms()
                ->creationRules('required', Rules\Password::defaults())
                ->updateRules('nullable', Rules\Password::defaults())
                ->hideFromIndex()
                ->hideFromDetail(),

            // 06.07.2022. Отображение имени супервайзера
            Text::make('Supervised by', function () {
                $parentId = $this->pid;
                $user = User::where('id', '=', $parentId)->firstOrFail();
                return $user->name;
            })->hideFromIndex(),


            MorphToMany::make('Роли', 'roles', \Itsmejoshua\Novaspatiepermissions\Role::class),

            // 06.07.2022. Проверка Permissions должна быть от Roles, а не от Users
            // MorphToMany::make('Права', 'permissions', \Itsmejoshua\Novaspatiepermissions\Permission::class),

            //22.06. Ставит поле Place в верхнюю карточку
            BelongsTo::make('Place')->sortable()->nullable(),

            // 31.05. Добавил Роль в панели создания юзера
            // Select::make('Role')
            //     ->options([
            //         'stuff' => 'Stuff',
            //         'manager' => 'Manager',
            //         'head of department' => 'Head of Department',
            //         'CEO' => 'CEO'
            //     ])
            //     ->displayUsingLabels()
            //     ->sortable()
            //     // Убрал проверку
            //     // ->rules('required', 'role', 'max:254')
            //     // ->updateRules('unique:users,role,{{resourceId}}'),
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
