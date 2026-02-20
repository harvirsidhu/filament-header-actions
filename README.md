# Filament Header Actions

[![Latest Version on Packagist](https://img.shields.io/packagist/v/harvirsidhu/filament-header-actions.svg?style=flat-square)](https://packagist.org/packages/harvirsidhu/filament-header-actions)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/harvirsidhu/filament-header-actions/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/harvirsidhu/filament-header-actions/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/harvirsidhu/filament-header-actions/fix-php-code-style-issues.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/harvirsidhu/filament-header-actions/actions?query=workflow%3A"Fix+PHP+code+styling"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/harvirsidhu/filament-header-actions.svg?style=flat-square)](https://packagist.org/packages/harvirsidhu/filament-header-actions)

`filament-header-actions` composes an ordered list of Filament actions into:
- primary actions (first `N`, default `1`),
- and a `More` overflow action group for remaining actions.

Behavior is deterministic:
- no overflow => no `More`,
- one overflow action => flattened directly,
- two or more overflow actions => grouped under `More`.

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
    'more' => [
        'label' => 'More',
        'icon' => 'heroicon-m-ellipsis-horizontal',
        'color' => 'gray',
        'hidden_label' => false,
    ],
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

    return FilamentHeaderActions::compose($actions)->toHeaderActions();
}
```

### Full usage (all options)

```php
FilamentHeaderActions::compose($actions)
    ->primaryCount(int $count = 1)
    ->moreLabel(string $label = 'More')
    ->moreIcon(?string $icon = null)
    ->moreColor(string $color = 'gray')
    ->moreHiddenLabel(bool $state = true)
    ->toHeaderActions();
```

### Other API

```php
HeaderActionsComposer::make($actions)->toActions(); // alias of toHeaderActions()
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
