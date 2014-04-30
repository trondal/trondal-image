<?php

return array(
    'image' => array(
        'content_directory' => realpath(__DIR__ . '/LabsTest/Image/assets/images'),
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