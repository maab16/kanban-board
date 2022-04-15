<?php

namespace App\Http\Controllers\API;

use Exception;
use Illuminate\Http\Request;
use App\Repositories\Board\Card;
use App\Http\Controllers\Controller;

class CardController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
    //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
    //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {

            $order = Card::getMaxOrder($request->column_id);

            $data = [
                'column_id' => $request->column_id,
                'order' => $order['data'] + 1,
                'title' => $request->title,
                'description' => $request->description
            ];

            $card = Card::save($data);
            return response()->json($card);

        }
        catch (Exception $ex) {
            return response()->json([
                'status' => 'error',
                'data' => $ex->getMessage()
            ]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Board\Card  $card
     * @return \Illuminate\Http\Response
     */
    public function show(Card $card)
    {
    //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Board\Card  $card
     * @return \Illuminate\Http\Response
     */
    public function edit(Card $card)
    {
    //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Board\Card  $card
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        try {

            $order = Card::getMaxOrder($request->column_id);

            $data = [
                'column_id' => $request->column_id,
                'title' => $request->title,
                'order' => $order['data'] + 1,
                'description' => $request->description ?? null
            ];

            $card = Card::update($id, $data);
            return response()->json($card);

        }
        catch (Exception $ex) {
            return response()->json([
                'status' => 'error',
                'data' => $ex->getMessage()
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Board\Card  $card
     * @return \Illuminate\Http\Response
     */
    public function destroy(Card $card)
    {
    //
    }

    /**
     * Swap card.
     * 
     * @param   \Illuminate\Http\Request  $request
     * 
     * @return array
     * 
     */

    public function swap(Request $request)
    {
        try {

            $data = [
                'column_id' => $request->column_id,
                'card_id' => $request->card_id,
                'order' => $request->order,
            ];

            // return response()->json($data);

            $card = Card::swap($data);
            return response()->json($card);

        }
        catch (Exception $ex) {
            return response()->josn([
                'status' => 'error',
                'data' => $ex->getMessage()
            ]);
        }
    }

    public function sort(Request $request)
    {
        try {

            $data = [
                'column_id' => $request->column_id,
            ];
            $sortBy = $request->sort ?? 'ASC';

            // return response()->json($data);

            $card = Card::sort($data, $sortBy);
            return response()->json($card);

        }
        catch (Exception $ex) {
            return response()->josn([
                'status' => 'error',
                'data' => $ex->getMessage()
            ]);
        }
    }

    public function listCards(Request $request)
    {
        $data = [
            'date' => $request->date ?? null,
            'status' => $request->status ?? null,
        ];
        $cards = Card::filter($request);

        return response()->json($cards);
    }
}
