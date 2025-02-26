<?php

namespace NovaCore\Database\Relations;

class HasMany extends Relation
{
    public function getResults()
    {
        return $this->query
            ->where($this->foreignKey, $this->parent->{$this->localKey})
            ->get();
    }
}
