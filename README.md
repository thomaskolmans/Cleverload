<img src="/cleverload.svg" width="260px" align="center" />


# Cleverload
_A lightweight library, that takes care of your routing and file loading_

## Installation
_How do you install?_

You can use composer
```sh
composer require nytrix/cleverload
```
Or by manually downloading it from here.

## Usage

**Activate Cleverload**
In order to have cleverload work, you have to 

_.htaccess_ from the folder you want Cleverload to handle the request.

```apacheconf
RewriteEngine On
RewriteRule ^(.*)$ index.php [NC,L,QSA]
```
Or when you use a view folder to load your files, you can also use this:

```apacheconf
RewriteEngine On
RewriteCond %{DOCUMENT_ROOT}/view/%{REQUEST_URI} -f
RewriteRule (.*) /view/$1 [R=301,L]

RewriteRule ^ index.php [L]

```
_index.php_ same folder as the _.htaccess_.
```php
use lib\Cleverload;
use lib\Http\Request;
use lib\Routing\Router;

require_once("autoloader.php");

$request = new Request($_SERVER);
$cleverload = new Cleverload($request);
$cleverload->getRequest()->getRouter()->getResponse();
```

In the _routes_ folder, you can add files or use the existing _web.php_ file to add routes.

##Routes

We support _GET_,_POST_,PUT,_PATCH_,_DELETE_,_ALL_ request, you can route each like so

```php
Route::get("/",function(){});
Route::post("/",function(){});
Route::put("/",function(){});
Route::patch("/",function(){});
Route::delete("/",function(){});
Route::all("/",function(){});
```

You can also return files instead of functions, your file comes from the viewdir, you can set this in your _index.php_ by adding 

```php
$cleverload->viewDir("/path/to/view");
```

Then you can return a file from there in the routes like so:

```php
Route::get("/","index.php");
```