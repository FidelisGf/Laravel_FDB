<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;

class Help extends Controller
{
    function startTransaction(){
        $pdo = DB::getPdo();
        $pdo->setAttribute(\PDO::ATTR_AUTOCOMMIT,0);
        $pdo->beginTransaction();
    }
    function rollbackTransaction(){
        $pdo = DB::getPdo();
        $pdo->rollback();
        $pdo->setAttribute(\PDO::ATTR_AUTOCOMMIT,1);
    }
    function commit(){
        $pdo = DB::getPdo();
        $pdo->setAttribute(\PDO::ATTR_AUTOCOMMIT,1);
        $pdo->commit();
    }
}
