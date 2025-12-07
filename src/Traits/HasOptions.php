<?php

namespace Secretwebmaster\LaravelOptionable\Traits;

use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Str;
use Secretwebmaster\LaravelOptionable\Models\Option;

trait HasOptions
{
    /**
     * Polymorphic relationship to option rows.
     *
     * @return MorphMany
     */
    public function options(): MorphMany
    {
        return $this->morphMany(Option::class, 'optionable');
    }

    /**
     * Get a single option value.
     * Supports:
     * - nullable scope/group
     * - dot notation for nested json
     * - repeatable options (key.sort.field)
     * - fallback and fallbackOnEmptyValue
     *
     * @param string $key
     * @param string|null $scope
     * @param string|null $group
     * @param mixed $fallback
     * @param bool $fallbackOnEmptyValue
     * @return mixed
     */
    public function getOption(
        string $key,
        ?string $scope = null,
        ?string $group = null,
        mixed $fallback = null,
        bool $fallbackOnEmptyValue = true
    ): mixed {
        // load rows filtered by scope/group
        $rows = $this->loadOptionRows($scope, $group);

        // handle dot notation
        if (Str::contains($key, '.')) {
            // check if key directly exists
            $directRow = $rows->where('key', $key)->whereNull('sort')->first();

            if ($directRow) {
                $value = $directRow->value;

                // apply fallback logic for null or empty string
                if ($fallbackOnEmptyValue && ($value === null || $value === '')) {
                    return $fallback;
                }
                return $value;
            }

            [$root, $sub] = explode('.', $key, 2);

            // handle repeatable rows with sort index
            if (is_numeric($sub)) {
                $sort = (int) $sub;
                $row = $rows->where('key', $root)->where('sort', $sort)->first();
                if (!$row) {
                    return $fallback;
                }

                $value = $row->value;

                // apply fallback logic for null or empty string
                if ($fallbackOnEmptyValue && ($value === null || $value === '')) {
                    return $fallback;
                }

                return $value;
            }

            // handle nested json
            $row = $rows->where('key', $root)->whereNull('sort')->first();
            if (!$row) {
                return $fallback;
            }

            $value = $row->value;
            $nested = data_get($value, $sub);

            // apply fallback logic for null or empty string
            if ($fallbackOnEmptyValue && ($nested === null || $nested === '')) {
                return $fallback;
            }

            return $nested ?? $fallback;
        }

        // handle simple (non-repeatable) option
        $row = $rows->where('key', $key)->whereNull('sort')->first();

        if ($row) {
            $value = $row->value;

            // apply fallback logic for null or empty string
            if ($fallbackOnEmptyValue && ($value === null || $value === '')) {
                return $fallback;
            }

            return $value;
        }

        return $fallback;
    }

    /**
     * Get all options for a given scope and group.
     * Sorted by sort index for repeatable items.
     *
     * @param string|null $scope
     * @param string|null $group
     * @return \Illuminate\Support\Collection
     */
    public function getOptions(?string $scope = null, ?string $group = null)
    {
        // load filtered rows
        $rows = $this->loadOptionRows($scope, $group);

        // sort repeatable rows by numeric order
        return $rows->sortBy(fn($row) => $row->sort ?? -1)->values();
    }

    /**
     * Save or update a single option row.
     * scope/group/sort are optional.
     *
     * @param string $key
     * @param mixed $value
     * @param string|null $scope
     * @param string|null $group
     * @param int|null $sort
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function setOption(
        string $key,
        mixed $value,
        ?string $scope = null,
        ?string $group = null,
        ?int $sort = null
    ) {
        // update or create based on composite key
        return $this->options()->updateOrCreate(
            [
                'scope' => $scope,
                'group' => $group,
                'key'   => $key,
                'sort'  => $sort,
            ],
            [
                'value' => $value,
            ]
        );
    }

    /**
     * Save a batch of options for a scope/group.
     * Each item requires: key, value, and optional sort.
     *
     * @param string|null $scope
     * @param string|null $group
     * @param array $items
     * @return void
     */
    public function setOptions(?string $scope, ?string $group, array $items): void
    {
        // delete all existing options for this scope/group
        $this->clearOptions($scope, $group);

        // insert each new item
        foreach ($items as $item) {
            $this->setOption(
                key: $item['key'] ?? null,
                value: $item['value'] ?? null,
                scope: $scope,
                group: $group,
                sort: $item['sort'] ?? null
            );
        }
    }

    /**
     * Delete a single option row.
     * Matches the same composite key fields used by setOption().
     *
     * @param string      $key
     * @param string|null $scope
     * @param string|null $group
     * @param int|null    $sort
     * @return int number of deleted rows
     */
    public function deleteOption(string $key, ?string $scope = null, ?string $group = null, ?int $sort = null): int
    {
        $query = $this->options()
            ->where('key', $key)
            ->where('sort', $sort);

        // filter by scope
        $scope !== null
            ? $query->where('scope', $scope)
            : $query->whereNull('scope');

        // filter by group
        $group !== null
            ? $query->where('group', $group)
            : $query->whereNull('group');

        return $query->delete();
    }

    /**
     * Delete all options under a specific scope and group.
     *
     * @param string|null $scope
     * @param string|null $group
     * @return int
     */
    public function clearOptions(?string $scope, ?string $group): int
    {
        // build query
        $query = $this->options();

        // filter by scope
        if ($scope !== null) {
            $query->where('scope', $scope);
        } else {
            $query->whereNull('scope');
        }

        // filter by group
        if ($group !== null) {
            $query->where('group', $group);
        } else {
            $query->whereNull('group');
        }

        // delete rows
        return $query->delete();
    }

    /**
     * Load option rows filtered by nullable scope and group.
     *
     * @param string|null $scope
     * @param string|null $group
     * @return \Illuminate\Support\Collection
     */
    protected function loadOptionRows(?string $scope, ?string $group)
    {
        // load relation or query fresh
        $rows = $this->relationLoaded('options')
            ? $this->options
            : $this->options()->get();

        // filter by scope
        if ($scope !== null) {
            $rows = $rows->where('scope', $scope);
        } else {
            $rows = $rows->whereNull('scope');
        }

        // filter by group
        if ($group !== null) {
            $rows = $rows->where('group', $group);
        } else {
            $rows = $rows->whereNull('group');
        }

        return $rows;
    }

    /**
     * Legacy support for snake_case method names.
     *
     * @param string $method
     * @param array $parameters
     * @return mixed
     */
    public function __call($method, $parameters)
    {
        // convert to snake case
        $snakeMethod = Str::snake($method);

        // call snake version if exists
        if (method_exists($this, $snakeMethod)) {
            return $this->{$snakeMethod}(...$parameters);
        }

        return parent::__call($method, $parameters);
    }
}
