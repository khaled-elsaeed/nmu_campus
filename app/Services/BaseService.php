<?php

namespace App\Services;

abstract class BaseService
{
    /**
     * The model class to use for this service.
     * Should be set in the child class.
     * @var string
     */
    protected $model;

    public function list()
    {
        return $this->model::all();
    }

    public function create(array $data)
    {
        return $this->model::create($data);
    }

    public function find($id)
    {
        return $this->model::find($id);
    }

    public function update($id, array $data)
    {
        $item = $this->model::find($id);
        if (!$item) {
            return false;
        }
        return $item->update($data);
    }

    public function delete($id)
    {
        $item = $this->model::find($id);
        if (!$item) {
            return false;
        }
        return $item->delete();
    }

    public function count(array $filters = [])
    {
        $query = $this->model::query();
        if (!empty($filters)) {
            foreach ($filters as $field => $value) {
                $query->where($field, $value);
            }
        }
        return $query->count();
    }
} 