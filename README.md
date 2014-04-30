# Labs/Image is an image module for Zend Framework2.

It supports basic image capabilities like uploading one or more images to the server, viewing them, scaling and deleting.

The theory is that this can be a highly reusable module because:

"Each ImageService can have its own strategy for handling images"
One application can store images in different formats, in different places, and caching different. Some will use Varnish and fetch images on the fly,
some will store the scaled images on disk and so on. The module is designed to be extended, and be ready for new customized strategies.

For the two first implemented strategies (storing directly in folder, and storing in a tree),
it seems strange to have Zend handle each request right? Wrong! Nothing disqualifies the developer
to have the images accessed directly via Varnish or Apache for any folder.
A number of different Mod_Rewrites could be used for viewing images, the limits is in the developers imagination.
Also the TreeStrategy can handle billions of images on the server, this approach is very
tailored for massive amounts of images, and is used by Facebook and such.

This lightweight, but VERY powerful module should handle most of your image needs!

## Requirements
 - PHP 5.3.3 or higher
 - [Zend Framework 2](http://www.github.com/zendframework/zf2)

## Installation

Installation of Boost/Image uses composer.

#### Installation steps

  1. `cd my/project/directory`
  2. create a `composer.json` file with following contents:

    ```
    "repositories": [
        {
            "type": "vcs",
            "url": "git+ssh://git.boost.no/srv/git_repos/boost-image"
        }
    ],
    ```

     ```json
     {
         "require": {
             "boost/boost-image": "*"
         }
     }
     ```
  3. install composer via `curl -s http://getcomposer.org/installer | php` (on windows, download
     http://getcomposer.org/installer and execute it with PHP)
  4. run `php composer.phar install`
  5. open `my/project/directory/configs/application.config.php` and add the following key to your `modules`:

     ```php
     'Labs\Image',
     ```
  6. Optionally add the route for the images as an own virtualhost.

The initial urls available are /upload, /view, /delete

To run the module as an application, add files run.php and global.php to the root:

-- run.php

chdir(dirname(__DIR__));

// Setup autoloading
require 'vendor/autoload.php';

$config = array(
    'modules' => array(
        'Labs\Image'
    ),
    'module_listener_options' => array(
        'config_glob_paths' => array(
            __DIR__ . '/global.php',
        ),
        'module_paths' => array(
            'src',
            'vendor'
        )
    ),
);

// Run the application!
Zend\Mvc\Application::init($config)->run();

-- global.php

return array(
    'image' => array(
        'content_directory' => '/home/user/images', // no trailing slash, must be writable
        'minWidth' => 1,
        'maxWidth' => 800, // refuse to resize to larger than
        'maxHeight' => 800, // refuse to resize to larger than
        'tree' => array(// when using tree template
            'store_resized' => true,
            'directory_depth' => 1 // interval 0-3
        ),
        'max_size' => '5MB',
        'min_size' => '5kB',
    )
);