<?php

namespace NovaCore\Database\Relations;

class BelongsTo extends Relation
{
    public function getResults()
    {
        return $this->query
            ->where($this->localKey, $this->parent->{$this->foreignKey})
            ->first();
    }
}
