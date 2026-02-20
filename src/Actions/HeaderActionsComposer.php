<?php

namespace Harvirsidhu\FilamentHeaderActions\Actions;

use Filament\Actions\ActionGroup;
use Harvirsidhu\FilamentHeaderActions\Support\FilamentCompatibility;
use InvalidArgumentException;

class HeaderActionsComposer
{
    /**
     * @param  array<mixed>  $actions
     */
    final public function __construct(
        protected array $actions,
        protected int $primaryCount = 1,
        protected string $moreLabel = 'More',
        protected ?string $moreIcon = null,
        protected string $moreColor = 'gray',
        protected bool $moreHiddenLabel = false,
    ) {
        $this->moreIcon ??= $this->resolveDefaultMoreIcon();
    }

    /**
     * @param  array<mixed>  $actions
     */
    public static function make(array $actions): static
    {
        return new static(
            actions: $actions,
            primaryCount: (int) config('header-actions.primary_count', 1),
            moreLabel: (string) config('header-actions.more.label', __('header-actions.more')),
            moreIcon: config('header-actions.more.icon'),
            moreColor: (string) config('header-actions.more.color', 'gray'),
            moreHiddenLabel: (bool) config('header-actions.more.hidden_label', false),
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

    public function moreLabel(string $label = 'More'): static
    {
        $this->moreLabel = $label;

        return $this;
    }

    public function moreIcon(?string $icon = null): static
    {
        $this->moreIcon = $icon ?? $this->resolveDefaultMoreIcon();

        return $this;
    }

    public function moreHiddenLabel(bool $state = true): static
    {
        $this->moreHiddenLabel = $state;

        return $this;
    }

    public function moreColor(string $color = 'gray'): static
    {
        $this->moreColor = $color;

        return $this;
    }

    /**
     * @return array<mixed>
     */
    public function toHeaderActions(): array
    {
        $primary = array_values(array_slice($this->actions, 0, $this->primaryCount));
        $overflow = array_values(array_slice($this->actions, $this->primaryCount));

        if ($overflow === []) {
            return $primary;
        }

        if (count($overflow) === 1) {
            return [...$primary, ...$overflow];
        }

        return [...$primary, $this->makeMoreGroup($overflow)];
    }

    /**
     * @return array<mixed>
     */
    public function toActions(): array
    {
        return $this->toHeaderActions();
    }

    /**
     * @param  array<mixed>  $overflow
     */
    protected function makeMoreGroup(array $overflow): ActionGroup
    {
        $group = ActionGroup::make($overflow)
            ->label($this->moreLabel)
            ->color($this->moreColor);

        if ($this->moreIcon !== null) {
            $group->icon($this->moreIcon);
        }

        if ($this->moreHiddenLabel && FilamentCompatibility::supportsHiddenLabel($group)) {
            $group->hiddenLabel();
        }

        return $group;
    }

    protected function resolveDefaultMoreIcon(): string
    {
        return FilamentCompatibility::defaultMoreIcon();
    }
}
