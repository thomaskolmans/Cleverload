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
RewriteRule ^((?!index\.php).+)$ ./index.php [NC,L,QSA]
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