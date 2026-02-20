<?php

use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Support\Enums\IconPosition;
use Filament\Support\Icons\Heroicon;
use Harvirsidhu\FilamentHeaderActions\Actions\HeaderActionsComposer;

enum FakeMoreIcon: string
{
    case EllipsisVertical = 'heroicon-m-ellipsis-vertical';
}

it('uses one primary action by default', function (): void {
    $actions = makeActions(['view', 'edit', 'archive']);

    $composed = HeaderActionsComposer::make($actions)->toActions();

    expect($composed)->toHaveCount(2)
        ->and($composed[0])->toBe($actions[0])
        ->and($composed[1])->toBeInstanceOf(ActionGroup::class);
});

it('supports a custom primary action count', function (): void {
    $actions = makeActions(['view', 'edit', 'archive', 'delete']);

    $composed = HeaderActionsComposer::make($actions)
        ->primaryCount(2)
        ->toActions();

    expect($composed)->toHaveCount(3)
        ->and($composed[0])->toBe($actions[0])
        ->and($composed[1])->toBe($actions[1])
        ->and($composed[2])->toBeInstanceOf(ActionGroup::class);
});

it('does not add a more action when there is no overflow', function (): void {
    $actions = makeActions(['view', 'edit']);

    $composed = HeaderActionsComposer::make($actions)
        ->primaryCount(2)
        ->toActions();

    expect($composed)->toHaveCount(2)
        ->and($composed[0])->toBe($actions[0])
        ->and($composed[1])->toBe($actions[1]);
});

it('flattens a single overflow action', function (): void {
    $actions = makeActions(['view', 'edit']);

    $composed = HeaderActionsComposer::make($actions)
        ->primaryCount(1)
        ->toActions();

    expect($composed)->toHaveCount(2)
        ->and($composed[0])->toBe($actions[0])
        ->and($composed[1])->toBe($actions[1])
        ->and($composed[1])->not->toBeInstanceOf(ActionGroup::class);
});

it('groups multiple overflow actions under more', function (): void {
    $actions = makeActions(['view', 'edit', 'archive']);

    $composed = HeaderActionsComposer::make($actions)
        ->primaryCount(1)
        ->toActions();

    expect($composed)->toHaveCount(2)
        ->and($composed[1])->toBeInstanceOf(ActionGroup::class);
});

it('supports configurable more presentation options', function (): void {
    $actions = makeActions(['view', 'edit', 'archive']);

    $composed = HeaderActionsComposer::make($actions)
        ->label('Options')
        ->icon('heroicon-m-bars-3')
        ->color('danger')
        ->toActions();

    /** @var ActionGroup $group */
    $group = $composed[1];

    expect(getConfiguredValue($group, ['label'], 'getLabel'))->toBe('Options')
        ->and(getConfiguredValue($group, ['icon'], 'getIcon'))->toBe('heroicon-m-bars-3')
        ->and(getConfiguredValue($group, ['color'], 'getColor'))->toBe('danger');
});

it('accepts a backed enum icon with moreIcon', function (): void {
    $actions = makeActions(['view', 'edit', 'archive']);

    $composed = HeaderActionsComposer::make($actions)
        ->icon(FakeMoreIcon::EllipsisVertical)
        ->toActions();

    /** @var ActionGroup $group */
    $group = $composed[1];

    expect(getConfiguredValue($group, ['icon'], 'getIcon'))
        ->toBe('heroicon-m-ellipsis-vertical');
});

it('accepts a Heroicon enum icon from config', function (): void {
    config()->set('header-actions.icon', Heroicon::EllipsisVertical);

    $actions = makeActions(['view', 'edit', 'archive']);
    $composed = HeaderActionsComposer::make($actions)->toActions();

    /** @var ActionGroup $group */
    $group = $composed[1];

    expect(getConfiguredValue($group, ['icon'], 'getIcon'))
        ->toBe('heroicon-m-ellipsis-vertical');
});

it('can hide the more label when supported by current filament version', function (): void {
    $actions = makeActions(['view', 'edit', 'archive']);

    $composed = HeaderActionsComposer::make($actions)
        ->hiddenLabel()
        ->toActions();

    /** @var ActionGroup $group */
    $group = $composed[1];

    if (method_exists($group, 'isLabelHidden')) {
        expect($group->isLabelHidden())->toBeTrue();

        return;
    }

    expect(getConfiguredValue($group, ['isLabelHidden']))->toBeTrue();
});

it('can configure icon position with right as default', function (): void {
    $actions = makeActions(['view', 'edit', 'archive']);

    $default = HeaderActionsComposer::make($actions)->toActions();
    $configured = HeaderActionsComposer::make($actions)
        ->iconPosition(IconPosition::Before)
        ->toActions();

    /** @var ActionGroup $defaultGroup */
    $defaultGroup = $default[1];
    /** @var ActionGroup $configuredGroup */
    $configuredGroup = $configured[1];

    if (method_exists($defaultGroup, 'getIconPosition')) {
        expect(normalizeBackedEnumValue($defaultGroup->getIconPosition()))->toBe('after')
            ->and(normalizeBackedEnumValue($configuredGroup->getIconPosition()))->toBe('before');

        return;
    }

    expect(getConfiguredValue($defaultGroup, ['iconPosition']))->toBe('after')
        ->and(getConfiguredValue($configuredGroup, ['iconPosition']))->toBe('before');
});

it('returns actions with toActions', function (): void {
    $actions = makeActions(['view', 'edit', 'archive']);

    $composer = HeaderActionsComposer::make($actions)->primaryCount(1);

    expect($composer->toActions())->toHaveCount(2);
});

it('filters hidden actions before composing', function (): void {
    $hidden = makeFakeAction('hidden', hidden: true);
    $archive = makeFakeAction('archive');
    $delete = makeFakeAction('delete');

    $composed = HeaderActionsComposer::make([$hidden, $archive, $delete])
        ->primaryCount(1)
        ->toActions();

    expect($composed)->toHaveCount(2)
        ->and($composed[0])->toBe($archive)
        ->and($composed[1])->toBe($delete);
});

it('filters invisible and unauthorized actions before composing', function (): void {
    $invisible = makeFakeAction('invisible', visible: false);
    $unauthorized = makeFakeAction('unauthorized', authorized: false);
    $view = makeFakeAction('view');

    $composed = HeaderActionsComposer::make([$invisible, $unauthorized, $view])
        ->primaryCount(1)
        ->toActions();

    expect($composed)->toHaveCount(1)
        ->and($composed[0])->toBe($view);
});

/**
 * @param  array<string>  $names
 * @return array<Action>
 */
function makeActions(array $names): array
{
    return array_map(static fn (string $name): Action => Action::make($name), $names);
}

function makeFakeAction(
    string $name,
    bool $hidden = false,
    ?bool $visible = true,
    ?bool $authorized = true,
): object {
    return new class($name, $hidden, $visible, $authorized)
    {
        public function __construct(
            public string $name,
            private bool $hidden,
            private ?bool $visible,
            private ?bool $authorized,
        ) {}

        public function isHidden(): bool
        {
            return $this->hidden;
        }

        public function isVisible(): bool
        {
            return $this->visible ?? true;
        }

        public function isAuthorized(): bool
        {
            return $this->authorized ?? true;
        }
    };
}

/**
 * @param  array<string>  $propertyNames
 */
function getConfiguredValue(object $target, array $propertyNames, ?string $getter = null): mixed
{
    if ($getter !== null && method_exists($target, $getter)) {
        return $target->{$getter}();
    }

    $reflection = new ReflectionClass($target);

    foreach ($propertyNames as $propertyName) {
        if (! $reflection->hasProperty($propertyName)) {
            continue;
        }

        $property = $reflection->getProperty($propertyName);

        return $property->getValue($target);
    }

    return null;
}

function normalizeBackedEnumValue(mixed $value): mixed
{
    if ($value instanceof \BackedEnum) {
        return $value->value;
    }

    return $value;
}
