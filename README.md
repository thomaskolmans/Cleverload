# Cleverload
A lightweight php library that takes care of routing and has a template engine

# Installation
We recommend using `composor` to install our library. Look at how to install composor [here](https://getcomposer.org/)
```sh
composer require nytrix/cleverload
```
You can also download the library and install it manually. Download button will be added when the first official release is there.

# Features

- Router, you can easily create a routing structure for your project
- Template engine, to make your code clean and easily adaptable
- Different method in regard of loading pages
- Form finder, so you don't have to implement code to all those forms

# Usage
_How to use Cleverload_

In the case that you've installed our library, you are ready to go! From the point you want to catch (most commonly from your `public_html` folder) you place the `.htacces` and the `index.php` file. Then everything is installed and you are ready to go!
You need to put the the `router` class in that same location aswell. 

In your `index.php` file you should have this:

```php
use lib\Cleverload;
require_once("autoloader.php");
new Cleverload;
```

And your `.htacces needs to contain this. 

```htacces
RewriteEngine On
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^(?!index\.php$). index.php [NS,L,DPI]
```

Full documentation you can find [**here**](https://github.com/thomaskolmans/Cleverload/blob/master/docs/README.md)

# License 

Cleverload is under the `MIT` license, read it [here](https://github.com/thomaskolmans/SimpelSQL/blob/master/LICENSE)



