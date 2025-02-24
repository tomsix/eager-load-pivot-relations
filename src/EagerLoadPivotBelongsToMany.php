<?php

namespace TomSix\EagerLoadPivotRelations;

use Closure;
use Illuminate\Contracts\Pagination\CursorPaginator;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Arr;
use Illuminate\Support\LazyCollection;
use Illuminate\Support\Str;

/**
 * @template TRelatedModel of \Illuminate\Database\Eloquent\Model
 */
class EagerLoadPivotBelongsToMany extends BelongsToMany
{
    /**
     * The Eloquent query builder instance.
     *
     * @var EagerLoadPivotBuilder<TRelatedModel>
     */
    protected $query;

    public function as($accessor): static
    {
        // Add the custom accessor to the builder
        $this->query->addPivotAccessor($accessor);

        return parent::as($accessor);
    }

    /**
     * Execute the query as a "select" statement.
     *
     * @param  array  $columns
     */
    public function get($columns = ['*']): Collection
    {
        // First we'll add the proper select columns onto the query so it is run with
        // the proper columns. Then, we will get the results and hydrate our pivot
        // models with the result of those columns as a separate model relation.
        $builder = $this->query->applyScopes();

        $columns = $builder->getQuery()->columns ? [] : $columns;

        $models = $builder->addSelect(
            $this->shouldSelect($columns),
        )->getModels();

        $this->hydratePivotRelation($models);

        // If we actually found models we will also eager load any relationships that
        // have been specified as needing to be eager loaded. This will solve the
        // n + 1 query problem for the developer and also increase performance.
        if (count($models) > 0) {
            $pivotEagerLoad = $this->getPivotEagerLoads($builder);

            if (count($pivotEagerLoad) !== 0) {
                $this->eagerLoadPivotRelations($models, $pivotEagerLoad);
            }

            $models = $builder->eagerLoadRelations($models);
        }

        return $this->related->newCollection($models);
    }

    /**
     * Get the pivot eager load relations.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $builder
     * @return array<string, Closure>
     */
    protected function getPivotEagerLoads($builder): array
    {
        // Only return the eagerLoad `pivot.*` but not the `pivot`
        // because `pivot.*` contains the actual relations we want to eager load from the pivot model.
        $pivotEagerLoad = array_filter($builder->getEagerLoads(), function ($relation) {
            return Str::startsWith($relation, "{$this->accessor}.");
        }, ARRAY_FILTER_USE_KEY);

        $builder->without(array_merge(
            [$this->accessor], // We make sure to also remove the `pivot` in the eagerLoad.
            array_keys($pivotEagerLoad),
        ));

        return $pivotEagerLoad;
    }

    /**
     * Eager load the relations of the pivot of the models.
     */
    protected function eagerLoadPivotRelations(array $models, array $eagerLoad): static
    {
        $pivots = Arr::pluck($models, $this->accessor);
        $eagerLoad = $this->removePivotFromEagerLoadKeys($eagerLoad);
        $builder = head($pivots)->query();
        $builder->with($eagerLoad)->eagerLoadRelations($pivots);

        return $this;
    }

    /**
     * Remove the `pivot.` part of the eager load relations
     * to get the actual relations of the pivot model.
     */
    protected function removePivotFromEagerLoadKeys(array $eagerLoad): array
    {
        $newEagerLoad = [];
        foreach ($eagerLoad as $name => $callback) {
            $name = substr($name, strlen($this->accessor) + 1);
            $newEagerLoad[$name] = $callback;
        }

        return $newEagerLoad;
    }

    /**
     * Chunk the results of the query.
     *
     * @param  int  $count
     */
    public function chunk($count, callable $callback): bool
    {
        return parent::chunk($count, function ($results, $page) use ($callback) {
            $this->hydratePivotRelation($results);

            return $callback($results, $page);
        });
    }

    /**
     * Get a lazy collection for the given query.
     *
     * @return \Illuminate\Support\LazyCollection<int, TRelatedModel>
     */
    public function cursor(): LazyCollection
    {
        return tap(parent::cursor(), function ($model) {
            $this->query->eagerLoadPivotRelations([$model]);

            return $model;
        });
    }

    /**
     * Paginate the given query into a cursor paginator.
     *
     * @param  int|null  $perPage
     * @param  array  $columns
     * @param  string  $cursorName
     * @param  string|null  $cursor
     * @return \Illuminate\Contracts\Pagination\CursorPaginator
     */
    public function cursorPaginate($perPage = null, $columns = ['*'], $cursorName = 'cursor', $cursor = null): CursorPaginator
    {
        return tap(parent::cursorPaginate($perPage, $columns, $cursorName, $cursor), function ($models) {
            $this->query->eagerLoadPivotRelations($models->items());
        });
    }

    /**
     * Query lazily, by chunks of the given size.
     *
     * @param  int  $chunkSize
     * @return \Illuminate\Support\LazyCollection<int, TRelatedModel>
     */
    public function lazy($chunkSize = 1000): LazyCollection
    {
        return tap(parent::lazy($chunkSize), function ($model) {
            $this->query->eagerLoadPivotRelations([$model]);

            return $model;
        });
    }

    /**
     * Query lazily, by chunking the results of a query by comparing IDs.
     *
     * @param  int  $chunkSize
     * @param  string|null  $column
     * @param  string|null  $alias
     * @return \Illuminate\Support\LazyCollection<int, TRelatedModel>
     */
    public function lazyById($chunkSize = 1000, $column = null, $alias = null): LazyCollection
    {
        return tap(parent::lazyById($chunkSize, $column, $alias), function ($model) {
            $this->query->eagerLoadPivotRelations([$model]);

            return $model;
        });
    }

    /**
     * Query lazily, by chunking the results of a query by comparing IDs in descending order.
     *
     * @param  int  $chunkSize
     * @param  string|null  $column
     * @param  string|null  $alias
     * @return \Illuminate\Support\LazyCollection<int, TRelatedModel>
     */
    public function lazyByIdDesc($chunkSize = 1000, $column = null, $alias = null): LazyCollection
    {
        return tap(parent::lazyByIdDesc($chunkSize, $column, $alias), function ($model) {
            $this->query->eagerLoadPivotRelations([$model]);

            return $model;
        });
    }

    /**
     * Chunk the results of a query by comparing IDs in a given order.
     *
     * @param  int  $count
     * @param  callable  $callback
     * @param  string|null  $column
     * @param  string|null  $alias
     * @param  bool  $descending
     * @return bool
     */
    public function orderedChunkById($count, callable $callback, $column = null, $alias = null, $descending = false): bool
    {
        return parent::orderedChunkById($count, function ($results, $page) use ($callback) {
            $this->hydratePivotRelation($results);

            return $callback($results, $page);
        }, $column, $alias, $descending);
    }

    /**
     * Get a paginator for the "select" statement.
     *
     * @param  int|null  $perPage
     * @param  array  $columns
     * @param  string  $pageName
     * @param  int|null  $page
     */
    public function paginate($perPage = null, $columns = ['*'], $pageName = 'page', $page = null): LengthAwarePaginator
    {
        return tap(parent::paginate($perPage, $columns, $pageName, $page), function ($models) {
            $this->query->eagerLoadPivotRelations($models->items());
        });
    }

    /**
     * Paginate the given query into a simple paginator.
     *
     * @param  int|null  $perPage
     * @param  array  $columns
     * @param  string  $pageName
     * @param  int|null  $page
     * @return \Illuminate\Contracts\Pagination\Paginator
     */
    public function simplePaginate($perPage = null, $columns = ['*'], $pageName = 'page', $page = null): Paginator
    {
        $this->query->addSelect($this->shouldSelect($columns));

        return tap($this->query->simplePaginate($perPage, $columns, $pageName, $page), function ($paginator) {
            $this->hydratePivotRelation($paginator->items());
        });
    }
}
