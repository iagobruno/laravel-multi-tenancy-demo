<?php

namespace App\Filament\Forms\Components;

use Filament\Forms\Components\TextInput;
use function App\Helpers\{ensureFloatHasZeroAtEnd};

/**
 * Create a text input with mask and currency formatting.
 */
class MoneyInput
{
    public static function make(string $name): TextInput
    {
        return TextInput::make($name)
            ->numeric()
            ->step(0.01)
            ->minValue(0)
            ->placeholder('0,00')
            ->formatStateUsing(function ($state) {
                if (!$state) return null;

                return ensureFloatHasZeroAtEnd($state / 100);
            })
            ->mutateDehydratedStateUsing(function ($state) {
                if (!$state) return null;

                return intval($state * 100);
            });

        // ->mask(
        //     fn (TextInput\Mask $mask) => $mask
        //         ->numeric()
        //         ->decimalPlaces(2)
        //         ->decimalSeparator('.')
        //         ->thousandsSeparator(',')
        //         ->padFractionalZeros()
        //         ->normalizeZeros(false)
        //         ->signed()
        // )
        // ->formatStateUsing(function ($state) {
        //     if (!$state) return null;

        //     // Convert integer to decimal. Ex: 2990 => 29,90
        //     return substr_replace($state, '.', -2, 0);
        // })
        // ->mutateDehydratedStateUsing(function ($state) {
        //     if (!$state) return null;

        //     // Convert float to integer to save in database. Ex: 29.9 => 2990
        //     return (int) filter_var(
        //         ensureFloatHasZeroAtEnd($state),
        //         FILTER_SANITIZE_NUMBER_INT
        //     );
        // });
    }
}
