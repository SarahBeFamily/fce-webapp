<?php

declare(strict_types=1);

namespace App\Orchid\Layouts\User;

use Orchid\Screen\Field;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Layouts\Rows;

class UserEditLayout extends Rows
{
    /**
     * The screen's layout elements.
     *
     * @return Field[]
     */
    public function fields(): array
    {
        return [
            Input::make('user.name')
                ->type('text')
                ->max(255)
                ->required()
                ->title(__('Username'))
                ->placeholder(__('Username')),

            Input::make('user.email')
                ->type('email')
                ->required()
                ->title(__('Email'))
                ->placeholder(__('Email')),

            Input::make('user.user_firstname')
                ->type('text')
                ->max(255)
                ->required()
                ->title(__('Nome'))
                ->placeholder(__('Nome')),

            Input::make('user.user_lastname')
                ->type('text')
                ->max(255)
                ->required()
                ->title(__('Cognome'))
                ->placeholder(__('Cognome')),

            Input::make('user.user_birthdate')
                ->type('date')
                ->required()
                ->title(__('Data di nascita')),
        ];
    }
}
