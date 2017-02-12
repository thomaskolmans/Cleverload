# Cleverload
A lightweight php library that let's you manipulate loading pages.

# How to use

Put this in your `index.php` file, and you will be good to go.

    <?php
    use lib\Cleverload
    require_once('autoloader.php`)
    new Cleverload;
    ?>
    
All the router files in `/routes` folder will automatically loaded, you can also define paths in the `pages.php` configuration file. 
