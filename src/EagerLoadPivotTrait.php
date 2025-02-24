<?php

namespace TomSix\EagerLoadPivotRelations;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

trait EagerLoadPivotTrait
{
    /**
     * Instantiate a new BelongsToMany relationship.
     *
     * @param  string|class-string<Model>  $table
     * @param  string  $foreignPivotKey
     * @param  string  $relatedPivotKey
     * @param  string  $parentKey
     * @param  string  $relatedKey
     * @param  null  $relationName
     */
    protected function newBelongsToMany(
        Builder $query,
        Model $parent,
        $table,
        $foreignPivotKey,
        $relatedPivotKey,
        $parentKey,
        $relatedKey,
        $relationName = null,
    ): EagerLoadPivotBelongsToMany {
        return new EagerLoadPivotBelongsToMany($query, $parent, $table, $foreignPivotKey, $relatedPivotKey, $parentKey, $relatedKey, $relationName);
    }

    /**
     * Create a new Eloquent query builder for the model.
     *
     * @param  \Illuminate\Database\Query\Builder  $query
     */
    public function newEloquentBuilder($query): EagerLoadPivotBuilder
    {
        return new EagerLoadPivotBuilder($query);
    }
}
