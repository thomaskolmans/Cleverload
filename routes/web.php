<?php
use lib\Routing\Route;
use lib\Cleverload;

Route::get("/hey",function(){
    echo "ey?";
})->primary();
Route::get("/bye",function(){
    echo "bye?";
});
?>