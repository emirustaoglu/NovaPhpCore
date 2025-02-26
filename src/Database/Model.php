<?php

namespace NovaCore\Database;

abstract class Model
{
    protected string $table;
    protected string $primaryKey = 'id';
    protected array $fillable = [];
    protected array $hidden = [];
    protected array $dates = ['created_at', 'updated_at'];
    protected bool $timestamps = true;
    protected array $attributes = [];
    protected array $original = [];
    protected array $relations = [];

    public function __construct(array $attributes = [])
    {
        $this->fill($attributes);
    }

    public function fill(array $attributes): self
    {
        foreach ($attributes as $key => $value) {
            if (in_array($key, $this->fillable)) {
                $this->setAttribute($key, $value);
            }
        }
        return $this;
    }

    public function setAttribute(string $key, $value): void
    {
        $this->attributes[$key] = $value;
    }

    public function getAttribute(string $key)
    {
        return $this->attributes[$key] ?? null;
    }

    public function __get(string $key)
    {
        return $this->getAttribute($key);
    }

    public function __set(string $key, $value): void
    {
        $this->setAttribute($key, $value);
    }

    public function save(): bool
    {
        $now = date('Y-m-d H:i:s');
        if ($this->timestamps) {
            $this->attributes['updated_at'] = $now;
            if (empty($this->attributes[$this->primaryKey])) {
                $this->attributes['created_at'] = $now;
            }
        }

        if (empty($this->attributes[$this->primaryKey])) {
            return $this->insert();
        }

        return $this->update();
    }

    protected function insert(): bool
    {
        $db = Database::getInstance();
        return $db->table($this->table)->insert([$this->attributes]);
    }

    protected function update(): bool
    {
        $db = Database::getInstance();
        return $db->table($this->table)
            ->where($this->primaryKey, $this->attributes[$this->primaryKey])
            ->update($this->attributes);
    }

    public function delete(): bool
    {
        if (empty($this->attributes[$this->primaryKey])) {
            return false;
        }

        $db = Database::getInstance();
        return $db->table($this->table)
            ->where($this->primaryKey, $this->attributes[$this->primaryKey])
            ->delete();
    }

    public static function query(): QueryBuilder
    {
        $model = new static;
        return Database::getInstance()->table($model->table);
    }

    public static function find($id)
    {
        $model = new static;
        $result = Database::getInstance()
            ->table($model->table)
            ->where($model->primaryKey, $id)
            ->first();

        if (!$result) {
            return null;
        }

        return new static($result);
    }

    public static function create(array $attributes): self
    {
        $model = new static($attributes);
        $model->save();
        return $model;
    }

    public static function all(): array
    {
        $model = new static;
        $results = Database::getInstance()
            ->table($model->table)
            ->get();

        return array_map(function ($attributes) {
            return new static($attributes);
        }, $results);
    }

    public function hasOne(string $related, string $foreignKey = null, string $localKey = null): HasOne
    {
        $related = new $related;
        $foreignKey = $foreignKey ?? strtolower(class_basename($this)) . '_id';
        $localKey = $localKey ?? $this->primaryKey;

        return new HasOne($related, $this, $foreignKey, $localKey);
    }

    public function hasMany(string $related, string $foreignKey = null, string $localKey = null): HasMany
    {
        $related = new $related;
        $foreignKey = $foreignKey ?? strtolower(class_basename($this)) . '_id';
        $localKey = $localKey ?? $this->primaryKey;

        return new HasMany($related, $this, $foreignKey, $localKey);
    }

    public function belongsTo(string $related, string $foreignKey = null, string $ownerKey = null): BelongsTo
    {
        $related = new $related;
        $foreignKey = $foreignKey ?? strtolower(class_basename($related)) . '_id';
        $ownerKey = $ownerKey ?? $related->primaryKey;

        return new BelongsTo($related, $this, $foreignKey, $ownerKey);
    }

    public function belongsToMany(string $related, string $table = null, string $foreignKey = null, string $relatedKey = null): BelongsToMany
    {
        $related = new $related;
        $table = $table ?? $this->joiningTable($related);
        $foreignKey = $foreignKey ?? strtolower(class_basename($this)) . '_id';
        $relatedKey = $relatedKey ?? strtolower(class_basename($related)) . '_id';

        return new BelongsToMany($related, $this, $table, $foreignKey, $relatedKey);
    }

    protected function joiningTable($related): string
    {
        $models = [
            strtolower(class_basename($this)),
            strtolower(class_basename($related))
        ];
        sort($models);
        return implode('_', $models);
    }

    public function toArray(): array
    {
        $attributes = $this->attributes;
        foreach ($this->hidden as $hidden) {
            unset($attributes[$hidden]);
        }
        return $attributes;
    }

    public function fresh(): ?self
    {
        if (empty($this->attributes[$this->primaryKey])) {
            return null;
        }

        return static::find($this->attributes[$this->primaryKey]);
    }

    public function isDirty(): bool
    {
        return !empty(array_diff_assoc($this->attributes, $this->original));
    }

    public function isClean(): bool
    {
        return !$this->isDirty();
    }

    public function getChanges(): array
    {
        return array_diff_assoc($this->attributes, $this->original);
    }
}
