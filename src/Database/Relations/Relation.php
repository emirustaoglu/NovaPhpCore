<?php

namespace NovaCore\Database\Relations;

use NovaCore\Database\Model;
use NovaCore\Database\QueryBuilder;

abstract class Relation
{
    protected Model $related;
    protected Model $parent;
    protected string $foreignKey;
    protected string $localKey;
    protected QueryBuilder $query;

    public function __construct(Model $related, Model $parent, string $foreignKey, string $localKey)
    {
        $this->related = $related;
        $this->parent = $parent;
        $this->foreignKey = $foreignKey;
        $this->localKey = $localKey;
        $this->query = $related::query();
    }

    abstract public function getResults();
}
