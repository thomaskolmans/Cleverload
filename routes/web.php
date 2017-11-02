<?php
use lib\Routing\Route;
use lib\Cleverload;

Route::group(["namespace" => "/fuck"],function(){
    Route::get("/hey","test.html");
});
Route::get("/shitz","index.tpl");
Route::get("/another",function(){
    echo "test";
})->primary();

?>