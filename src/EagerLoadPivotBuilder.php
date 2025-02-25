<?php

namespace TomSix\EagerLoadPivotRelations;

use Closure;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class EagerLoadPivotBuilder extends Builder
{
    protected array $pivotEagerLoad = [];

    /** @var array<string> */
    private array $pivotAccessors;

    public function __construct(QueryBuilder $query, array $pivotAccessors = [])
    {
        parent::__construct($query);

        $this->pivotAccessors = $pivotAccessors;
    }

    public function addPivotAccessor(string $accessor): static
    {
        $this->pivotAccessors[] = $accessor;

        return $this;
    }

    public function getPivotAccessors(): array
    {
        return count($this->pivotAccessors) !== 0 ? $this->pivotAccessors : ['pivot'];
    }

    /**
     * Set the relationships that should be eager loaded.
     *
     * @param  array<array-key, array|(\Closure(\Illuminate\Database\Eloquent\Relations\Relation<*,*,*>): mixed)|string>|string  $relations
     * @param  (\Closure(\Illuminate\Database\Eloquent\Relations\Relation<*,*,*>): mixed)|string|null  $callback
     */
    public function with($relations, $callback = null): static
    {
        parent::with($relations, $callback);

        // Get the relation names that are pivot accessors.
        $pivotEagerLoad = array_keys(array_filter($this->getEagerLoads(), function (string $relation) {
            return $this->isPivotAccessor($relation);
        }, ARRAY_FILTER_USE_KEY));

        // Set the loaded pivot accessors
        $this->pivotEagerLoad = [...$this->pivotEagerLoad, ...$pivotEagerLoad];

        // Remove the pivot accessors from the eager loads
        $this->without($pivotEagerLoad);

        return $this;
    }

    public function eagerLoadPivotRelations(array $models): array
    {
        if (count($models) === 0) {
            return $models;
        }

        foreach ($this->pivotEagerLoad as $pivotAccessor) {
            $this->eagerLoadPivotRelation($models, $pivotAccessor);
        }

        return $models;
    }

    /**
     * Override.
     * Eagerly load the relationship on a set of models.
     *
     * @param  string  $name
     */
    protected function eagerLoadRelation(array $models, $name, Closure $constraints): array
    {
        if ($this->isPivotAccessor($name)) {
            return $models;
        }

        return parent::eagerLoadRelation($models, $name, $constraints);
    }

    /**
     * If relation name is a pivot accessor.
     */
    protected function isPivotAccessor(string $name): bool
    {
        return in_array($name, $this->getPivotAccessors());
    }

    /**
     * Eager load pivot relations.
     *
     * @param  array<Model>  $models
     */
    public function eagerLoadPivotRelation(array $models, string $pivotAccessor): static
    {
        $pivots = Arr::pluck($models, $pivotAccessor);
        $pivots = (head($pivots))->newCollection($pivots);
        $pivots->load($this->getPivotEagerLoadRelations($pivotAccessor));

        return $this;
    }

    /**
     * Get the pivot relations to be eager loaded.
     *
     * @return array<string>
     */
    protected function getPivotEagerLoadRelations(string $pivotAccessor): array
    {
        $relations = array_filter($this->eagerLoad, function ($relation) use ($pivotAccessor) {
            return $relation !== $pivotAccessor && Str::startsWith($relation, $pivotAccessor);
        }, ARRAY_FILTER_USE_KEY);

        return array_combine(
            array_map(function ($relation) use ($pivotAccessor) {
                return substr($relation, strlen("{$pivotAccessor}."));
            }, array_keys($relations)),
            array_values($relations),
        );
    }
}
