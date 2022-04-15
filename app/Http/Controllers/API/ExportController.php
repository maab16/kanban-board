<?php

namespace App\Http\Controllers\API;

use Exception;
use Illuminate\Http\Request;
use App\Repositories\Board\Board;
use App\Http\Controllers\Controller;

class ExportController extends Controller
{
    /**
     * Export board.
     * 
     * @return array
     * 
     */
    public function export()
    {
        try {
            $card = Board::export();
            return response()->json($card);

        }
        catch (Exception $ex) {
            return response()->josn([
                'status' => 'error',
                'data' => $ex->getMessage()
            ]);
        }
    }
}
