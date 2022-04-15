<?php

namespace App\Repositories\Board;

use Illuminate\Validation\Rule;
use App\Models\Board\Card as Model;
use App\Repositories\BaseRepository;
use Illuminate\Support\Facades\Validator;
use Exception;

class Card extends BaseRepository
{
    protected static $model = Model::class;


    protected static $rules = [
        'save' => [
            'column_id' => 'required|integer',
            'title' => 'required|unique:cards'
        ],
        'update' => []
    ];

    public static function save($data): array
    {
        static::$rules['save']['order'] = [
            'required',
            'integer',
            Rule::unique('cards')->where(function ($query) use ($data) {
            return $query->where('order', $data['order'])->where('column_id', $data['column_id']);
        })
        ];

        return parent::save($data);
    }

    public static function update($id, $data): array
    {

        static::$rules['update'] = [
            'column_id' => 'required|integer',
            'order' => [
                'required',
                'integer',
                Rule::unique('cards')->where(function ($query) use ($data) {
            return $query->where('order', $data['order'])->where('column_id', $data['column_id']);
        })->ignore($id)
            ],
            'title' => 'required|unique:cards,id,' . $id
        ];

        return parent::update($id, $data);
    }

    /**
     * Get models.
     * 
     * @param   integer $column_id
     * 
     * @return array
     * 
     */
    public static function getMaxOrder($column_id): array
    {
        try {
            // Get Max Order
            $order = Model::where('column_id', $column_id)->max('order');


            return [
                'status' => 'success',
                'data' => $order
            ];
        }
        catch (Exception $ex) {
            return [
                'status' => 'error',
                'data' => $ex->getMessage()
            ];
        }
    }

    /**
     * Swap card order.
     * 
     * @param   array $data
     * 
     * @return array
     * 
     */

    public static function swap($data)
    {
        try {
            // Check validation.
            $validator = Validator::make($data, [
                'column_id' => 'required',
                'card_id' => 'required',
                'order' => 'required'
            ]);
            // If validation failed then return the error.
            if ($validator->fails()) {
                return [
                    'status' => 'error',
                    'data' => $validator->errors()->toArray()
                ];
            }

            $cards = Model::whereColumnId($data['column_id'])->orderBy('order', 'ASC')->get();
            $newCard = Model::find($data['card_id']);
            if ($newCard->order >= $data['order']) {
                $order = 1;

                foreach ($cards as $card) {
                    if ($card->id == $data['card_id'] && $card->column_id == $data['column_id']) {
                        continue;
                    }
                    if ($card->order == $data['order']) {
                        $order++;
                    }

                    $card->order = $order;
                    $card->save();
                    $order++;
                }
            }
            else {
                $order = 1;

                foreach ($cards as $card) {
                    if ($card->id == $data['card_id'] && $card->column_id == $data['column_id']) {
                        continue;
                    }
                    if ($card->order > $data['order']) {
                        $order++;
                    }

                    $card->order = $order;
                    $card->save();
                    if ($card->order < $data['order']) {
                        $order++;
                    }
                }
            }



            $newCard->column_id = $data['column_id'];
            $newCard->order = $data['order'];
            $newCard->save();

            // Return response
            return [
                'status' => 'success',
                'data' => $cards,
            ];
        }
        catch (Exception $ex) {
            return [
                'status' => 'error',
                'data' => $ex->getMessage()
            ];
        }
    }

    /**
     * Swap card order.
     * 
     * @param   array $data
     * 
     * @return array
     * 
     */

    public static function sort($data, $sortBy = 'ASC')
    {
        try {
            // Check validation.
            $validator = Validator::make($data, [
                'column_id' => 'required',
            ]);
            // If validation failed then return the error.
            if ($validator->fails()) {
                return [
                    'status' => 'error',
                    'data' => $validator->errors()->toArray()
                ];
            }

            $cards = Model::whereColumnId($data['column_id'])->orderBy('order', $sortBy)->get();
            $order = 1;

            foreach ($cards as $card) {
                $card->order = $order;
                $card->save();
                $order++;
            }

            // Return response
            return [
                'status' => 'success',
                'data' => $cards,
            ];
        }
        catch (Exception $ex) {
            return [
                'status' => 'error',
                'data' => $ex->getMessage()
            ];
        }
    }

    public static function filter($data)
    {
        $model = static::$model::query();

        if ($data['date']) {
            $model->whereDate('created_at', $data['date']);
        }

        if ($data['status'] === 1 || $data['status'] === '1') {
            return $model->get();
        }

        if ($data['status'] === 0 || $data['status'] === '0') {
            return $model->onlyTrashed()->get();
        }

        return $model->withTrashed()->get();
    }
}