<?php
use lib\Routing\Route;
use lib\Cleverload;

Route::group(["namespace" => "/fuck"],function(){
    Route::group(["domain" => "localhost"],function(){
        Route::get("/hey",function(){
            echo  "hey?";
        });
    }); 
});
Route::get("/shitz","index.tpl";
Route::get("/another",function(){
    echo "test";
})->primary();

?>