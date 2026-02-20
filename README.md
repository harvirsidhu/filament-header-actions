# Filament Header Actions

[![Latest Version on Packagist](https://img.shields.io/packagist/v/harvirsidhu/filament-header-actions.svg?style=flat-square)](https://packagist.org/packages/harvirsidhu/filament-header-actions)
[![Total Downloads](https://img.shields.io/packagist/dt/harvirsidhu/filament-header-actions.svg?style=flat-square)](https://packagist.org/packages/harvirsidhu/filament-header-actions)

`filament-header-actions` composes an ordered list of Filament actions into:
- primary actions (first `N`, default `1`),
- and a `More` overflow action group for remaining actions.

Behavior is deterministic:
- no overflow => no `More`,
- one overflow action => flattened directly,
- two or more overflow actions => grouped under `More`.
- actions that evaluate as hidden, invisible, or unauthorized are ignored before composing.

## Compatibility

| Package | Supported versions |
| --- | --- |
| Filament | `^4.0` and `^5.0` |
| PHP | `^8.2` |

## Installation

```bash
composer require harvirsidhu/filament-header-actions
```

Config is optional. The package works without publishing it.

If you want to customize defaults, publish config:

```bash
php artisan vendor:publish --tag="filament-header-actions-config"
```

```php
return [
    'primary_count' => 1,
    'label' => 'More',
    'icon' => 'heroicon-m-ellipsis-horizontal',
    'color' => 'gray',
    'hidden_label' => false,
    'button' => true,
    'icon_position' => \Filament\Support\Enums\IconPosition::After, // right
];
```

## Usage

### Easy usage

```php
use Filament\Actions\Action;
use Harvirsidhu\FilamentHeaderActions\Facades\FilamentHeaderActions;

public function getHeaderActions(): array
{
    $actions = [
        Action::make('edit'),
        Action::make('archive'),
        Action::make('delete'),
    ];

    return FilamentHeaderActions::make($actions)->toActions();
}
```

### Visibility filtering example

```php
public function getHeaderActions(): array
{
    $actions = [
        Action::make('edit')->hidden(true),     // ignored
        Action::make('archive'),                // kept
        Action::make('delete')->visible(false), // ignored
        Action::make('publish')->authorize('update', $this->record), // evaluated
    ];

    // primary_count = 1:
    // - first available action stays primary
    // - remaining available actions go to More (or flatten if only one)
    return FilamentHeaderActions::make($actions)->toActions();
}
```

### Full usage (all options)

```php
FilamentHeaderActions::make($actions)
    ->primaryCount(int $count = 1)
    ->label(string $label = 'More')
    ->icon(string|\BackedEnum|null $icon = null)
    ->color(string $color = 'gray')
    ->hiddenLabel(bool $state = true)
    ->button(bool $state = true)
    ->iconPosition(\Filament\Support\Enums\IconPosition $position = \Filament\Support\Enums\IconPosition::After)
    ->toActions();
```

## Testing

```bash
composer test
```

## Release checklist

- Update changelog with user-facing changes.
- Run linting and static analysis.
- Run Pest locally.
- Ensure CI passes Filament 4 and 5 matrix jobs.

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](.github/CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](.github/SECURITY.md) on how to report security vulnerabilities.

## Credits

- [harvirsidhu](https://github.com/harvirsidhu)
- [All Contributors](https://github.com/harvirsidhu/filament-header-actions/contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
