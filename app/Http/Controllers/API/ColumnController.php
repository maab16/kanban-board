<?php

namespace App\Http\Controllers\API;

use Exception;
use Illuminate\Http\Request;
use App\Repositories\Board\Column;
use App\Http\Controllers\Controller;

class ColumnController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {

            $columns = Column::get([], ['cards']);
            return response()->json($columns);

        }
        catch (Exception $ex) {
            return response()->json([
                'status' => 'error',
                'data' => $ex->getMessage()
            ]);
        }
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

            $data = [
                'title' => $request->title
            ];

            $card = Column::save($data);
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
     * @param  \App\Models\Board\Column  $column
     * @return \Illuminate\Http\Response
     */
    public function show(Column $column)
    {
    //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Board\Column  $column
     * @return \Illuminate\Http\Response
     */
    public function edit(Column $column)
    {
    //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Board\Column  $column
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Column $column)
    {
    //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Board\Column  $column
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $card = Column::delete($id);
            return response()->json($card);

        }
        catch (Exception $ex) {
            return response()->json([
                'status' => 'error',
                'data' => $ex->getMessage()
            ]);
        }
    }
}
