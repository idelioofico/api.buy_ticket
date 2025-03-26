<?php

namespace App\Repositories;


class BaseRepository
{

    protected $obj;

    public function __construct(object $obj)
    {
        $this->obj = $obj;
    }

    public function all($sort = []): object
    {
        if ((isset($sort['sort']) && !empty($sort['sort']))  && (isset($sort['column']) && !empty($sort['column']))) {
            return $this->obj->orderBy($sort['column'], $sort['sort'])->get();
        } else {
            return $this->obj->all();
        }
    }

    public function nextID($field_id)
    {
        return ($this->obj->max($field_id) + 1);
    }

    public function store(array $attributes)
    {
        return $this->obj->create($attributes);
    }

    public function find(int $id)
    {
        return $this->obj->find($id);
    }

    public function update(int $id, array $attributes): bool
    {
        return $this->obj->find($id)->update($attributes);
    }

    public function findByColumn(string $column, $value): object
    {
        return $this->obj->where($column, $value)->get();
    }

    public function getByColumn(string $column, $value)
    {
        return $this->obj->where($column, $value)->first();
    }

    public function getByFilter(array $query)
    {
        return $this->obj->where($query);
    }

    public function findByColumnLikeValue(string $column, $value): object
    {
        return $this->obj->where($column, 'like', "$value%")->first();
    }

    public function delete(int $id): bool
    {
        return $this->obj->delete($id);
    }

    public function last()
    {
        return $this->obj->orderBy('row_id', 'desc')->first();
    }
}
