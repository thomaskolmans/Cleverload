<?php
use lib\routing\Route;
use lib\http\Response;
use lib\Cleverload;

//Enter your routes here, you can add as many files with routes in this folder too.
Route::middleware(["namespace" => "/auth"], function(){
    if (true) return Response::notAuthorized();
}, function() {
    Route::get("/", function() {
        echo "hey! you're authenticated";
    });
});

Route::group(["namespace" => "/test"], function(){
    Route::get("/ing", function(){
        echo "hey!";
    });
})

?>