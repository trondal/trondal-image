<?php

/**
 * For max_image and min_size options see
 * http://framework.zend.com/manual/2.1/en/modules/zend.validator.file.html#size
 * for supported options.
 *
 * If storedResized is true, the resized image will resize in a child folder
 * (folder named after original filename) with a suffix '_width' to filename.
 *
 * When creating new templates, add another array with template name to config.
 */

return array(
    'image' => array(
        'content_directory' => '/Users/trondal/Sites/labs-image-images', // no trailing slash, must be writable
        'minWidth' => 1,
        'maxWidth' => 800, // refuse to resize to larger than
        'maxHeight' => 800, // refuse to resize to larger than
        'tree' => array(  // when using tree template
            'store_resized' => true,
            'directory_depth' => 1 // interval 0-3
        ),
        'max_size' => '5MB',
        'min_size' => '5kB',
    )
);