<?php

namespace Harvirsidhu\FilamentHeaderActions\Actions;

use BackedEnum;
use Filament\Actions\ActionGroup;
use Filament\Support\Contracts\ScalableIcon;
use Filament\Support\Enums\IconPosition;
use Filament\Support\Enums\IconSize;
use Harvirsidhu\FilamentHeaderActions\Support\FilamentCompatibility;
use InvalidArgumentException;
use Throwable;

class HeaderActionsComposer
{
    /**
     * @param  array<mixed>  $actions
     */
    final public function __construct(
        protected array $actions,
        protected int $primaryCount = 1,
        protected string $label = 'More',
        protected ?string $icon = null,
        protected string $color = 'gray',
        protected bool $hiddenLabel = false,
        protected bool $button = true,
        protected IconPosition $iconPosition = IconPosition::After,
        protected bool $filterUnauthorized = false,
    ) {
        $this->icon ??= $this->resolveDefaultMoreIcon();
    }

    /**
     * @param  array<mixed>  $actions
     */
    public static function make(array $actions): static
    {
        return new static(
            actions: $actions,
            primaryCount: (int) config('header-actions.primary_count', 1),
            label: (string) config('header-actions.label', __('header-actions.more')),
            icon: static::normalizeIcon(config('header-actions.icon')),
            color: (string) config('header-actions.color', 'gray'),
            hiddenLabel: (bool) config('header-actions.hidden_label', false),
            button: (bool) config('header-actions.button', true),
            iconPosition: static::normalizeIconPosition(config('header-actions.icon_position', IconPosition::After)),
            filterUnauthorized: (bool) config('header-actions.filter_unauthorized', false),
        );
    }

    public function primaryCount(int $count = 1): static
    {
        if ($count < 0) {
            throw new InvalidArgumentException('Primary count cannot be negative.');
        }

        $this->primaryCount = $count;

        return $this;
    }

    public function label(string $label = 'More'): static
    {
        $this->label = $label;

        return $this;
    }

    public function icon(string | BackedEnum | null $icon = null): static
    {
        $this->icon = static::normalizeIcon($icon) ?? $this->resolveDefaultMoreIcon();

        return $this;
    }

    public function hiddenLabel(bool $state = true): static
    {
        $this->hiddenLabel = $state;

        return $this;
    }

    public function color(string $color = 'gray'): static
    {
        $this->color = $color;

        return $this;
    }

    public function button(bool $state = true): static
    {
        $this->button = $state;

        return $this;
    }

    public function iconPosition(IconPosition | string | BackedEnum | null $position = IconPosition::After): static
    {
        $this->iconPosition = static::normalizeIconPosition($position);

        return $this;
    }

    public function filterUnauthorized(bool $state = true): static
    {
        $this->filterUnauthorized = $state;

        return $this;
    }

    /**
     * @return array<mixed>
     */
    public function toActions(): array
    {
        $availableActions = $this->filterAvailableActions($this->actions);

        $primary = array_values(array_slice($availableActions, 0, $this->primaryCount));
        $overflow = array_values(array_slice($availableActions, $this->primaryCount));

        if ($overflow === []) {
            return $primary;
        }

        if (count($overflow) === 1) {
            return [...$primary, ...$overflow];
        }

        return [...$primary, $this->makeMoreGroup($overflow)];
    }

    /**
     * @param  array<mixed>  $actions
     * @return array<mixed>
     */
    protected function filterAvailableActions(array $actions): array
    {
        return array_values(array_filter(
            $actions,
            fn (mixed $action): bool => $this->isActionAvailable($action),
        ));
    }

    protected function isActionAvailable(mixed $action): bool
    {
        if (! is_object($action)) {
            return true;
        }

        $isHidden = $this->resolveBooleanMethodResult($action, ['isHidden']);
        if ($isHidden === true) {
            return false;
        }

        $isVisible = $this->resolveBooleanMethodResult($action, ['isVisible']);
        if ($isVisible === false) {
            return false;
        }

        if ($this->filterUnauthorized) {
            $isAuthorized = $this->resolveBooleanMethodResult($action, ['isAuthorized']);
            if ($isAuthorized === false) {
                return false;
            }
        }

        return true;
    }

    /**
     * @param  array<string>  $methodNames
     */
    protected function resolveBooleanMethodResult(object $target, array $methodNames): ?bool
    {
        foreach ($methodNames as $methodName) {
            if (! method_exists($target, $methodName)) {
                continue;
            }

            try {
                $result = $target->{$methodName}();
            } catch (Throwable) {
                return null;
            }

            return is_bool($result) ? $result : null;
        }

        return null;
    }

    /**
     * @param  array<mixed>  $overflow
     */
    protected function makeMoreGroup(array $overflow): ActionGroup
    {
        $group = ActionGroup::make($overflow)
            ->label($this->label)
            ->color($this->color);

        if ($this->button) {
            $group->button();
        }

        if ($this->icon !== null) {
            $group->icon($this->icon);
        }

        if (method_exists($group, 'iconPosition')) {
            $group->iconPosition($this->iconPosition);
        }

        if ($this->hiddenLabel && FilamentCompatibility::supportsHiddenLabel($group)) {
            $group->hiddenLabel();
        }

        return $group;
    }

    protected function resolveDefaultMoreIcon(): string
    {
        return FilamentCompatibility::defaultMoreIcon();
    }

    protected static function normalizeIcon(mixed $icon): ?string
    {
        if ($icon === null) {
            return null;
        }

        if (is_string($icon)) {
            return $icon;
        }

        if ($icon instanceof ScalableIcon) {
            return $icon->getIconForSize(IconSize::Medium);
        }

        if ($icon instanceof BackedEnum) {
            return (string) $icon->value;
        }

        throw new InvalidArgumentException('More icon must be a string, backed enum, or null.');
    }

    protected static function normalizeIconPosition(mixed $position): IconPosition
    {
        if ($position === null) {
            return IconPosition::After;
        }

        if ($position instanceof IconPosition) {
            return $position;
        }

        if ($position instanceof BackedEnum) {
            $value = $position->value;

            if (is_string($value) && IconPosition::tryFrom($value) !== null) {
                return IconPosition::from($value);
            }
        }

        if (is_string($position) && IconPosition::tryFrom($position) !== null) {
            return IconPosition::from($position);
        }

        throw new InvalidArgumentException('Icon position must be a Filament IconPosition enum value.');
    }
}
