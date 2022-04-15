<?php

namespace App\Repositories\Board;

use App\Repositories\BaseRepository;
use Illuminate\Support\Facades\File;
use Exception;

class Board extends BaseRepository
{

    public static function export()
    {
        try {
            // Get database options
            $default = config('database.default', 'mysql');
            $databaseName = config("database.connections.{$default}.database");
            $userName = config("database.connections.{$default}.username");
            $password = config("database.connections.{$default}.password");
            // Dump database
            \Spatie\DbDumper\Databases\MySql::create()
                ->setDbName($databaseName)
                ->setUserName($userName)
                ->setPassword($password)
                ->dumpToFile(public_path('backup/board.sql'));
            // Get dumped file
            $file = File::get(public_path('backup/board.sql'));
            // Return response file
            return [
                'status' => 'success',
                'data' => $file
            ];
        }
        catch (Exception $ex) {
            return [
                'status' => 'error',
                'data' => $ex->getMessage()
            ];
        }
    }
}