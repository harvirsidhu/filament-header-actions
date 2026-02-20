<?php

use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Harvirsidhu\FilamentHeaderActions\Actions\HeaderActionsComposer;

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
        ->moreLabel('Options')
        ->moreIcon('heroicon-m-bars-3')
        ->moreColor('danger')
        ->toActions();

    /** @var ActionGroup $group */
    $group = $composed[1];

    expect(getConfiguredValue($group, ['label'], 'getLabel'))->toBe('Options')
        ->and(getConfiguredValue($group, ['icon'], 'getIcon'))->toBe('heroicon-m-bars-3')
        ->and(getConfiguredValue($group, ['color'], 'getColor'))->toBe('danger');
});

it('can hide the more label when supported by current filament version', function (): void {
    $actions = makeActions(['view', 'edit', 'archive']);

    $composed = HeaderActionsComposer::make($actions)
        ->moreHiddenLabel()
        ->toActions();

    /** @var ActionGroup $group */
    $group = $composed[1];

    if (method_exists($group, 'isLabelHidden')) {
        expect($group->isLabelHidden())->toBeTrue();

        return;
    }

    expect(getConfiguredValue($group, ['isLabelHidden']))->toBeTrue();
});

it('returns actions with toActions', function (): void {
    $actions = makeActions(['view', 'edit', 'archive']);

    $composer = HeaderActionsComposer::make($actions)->primaryCount(1);

    expect($composer->toActions())->toHaveCount(2);
});

/**
 * @param  array<string>  $names
 * @return array<Action>
 */
function makeActions(array $names): array
{
    return array_map(static fn (string $name): Action => Action::make($name), $names);
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
