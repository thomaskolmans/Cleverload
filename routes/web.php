<?php
use lib\Routing\Route;
use lib\Cleverload;

Route::post("/set/username",function(){
    var_dump($_POST);
});
Route::get("/home",function(){
    echo "fuck you <br>";
})->primary();
?>