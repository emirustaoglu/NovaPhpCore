<?php

namespace NovaCore\Database\Relations;

class BelongsToMany extends Relation
{
    protected string $table;
    protected string $relatedKey;

    public function __construct($related, $parent, string $table, string $foreignKey, string $relatedKey)
    {
        parent::__construct($related, $parent, $foreignKey, 'id');
        $this->table = $table;
        $this->relatedKey = $relatedKey;
    }

    public function getResults()
    {
        return $this->query
            ->join($this->table, $this->related->getTable() . '.id', '=', $this->table . '.' . $this->relatedKey)
            ->where($this->table . '.' . $this->foreignKey, $this->parent->{$this->localKey})
            ->get();
    }
}
