<?php

namespace App\Repositories\Board;

use App\Repositories\BaseRepository;
use App\Models\Board\Column as Model;

class Column extends BaseRepository
{
    protected static $model = Model::class;

    protected static $rules = [
        'save' => [
            'title' => 'required|unique:columns'
        ],
        'update' => []
    ];

    public static function update($id, $data): array
    {
        static::$rules['update'] = [
            'title' => 'required|unique:columns,id,' . $id
        ];
        return parent::update($id, $data);
    }

    public static function delete($id): array
    {
        $model = static::$model::find($id);
        if ($model) {
            $model->cards()->delete();
        }
        return parent::delete($id);
    }
}