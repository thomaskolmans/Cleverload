<?php
use lib\routing\Route;
use lib\http\Response;
use lib\Cleverload;

//Enter your routes here, you can add as many files with routes in this folder too.

Route::group(["prefix" => ["", "/mkb", "/grootzakelijk"]], function(){
    Route::get("/",  "test.html")->primary();
    Route::get("/over-ons", function() { echo "over ons"; });
});

?>