<?php

return array(
    'router' => array(
        'routes' => array(
            'image' => array(
                'type' => 'Zend\Mvc\Router\Http\HostName',
                'options' => array(
                    'route' => ':subdomain[:opt1.][:opt2.][:opt3.][:opt4.]:tld'
                ),
                'may_terminate' => false,
                'child_routes' => array(
                    'iview' => array(
                        'type' => 'Zend\Mvc\Router\Http\Segment',
                        'options' => array(
                            'route' => '/iview/:id',
                            'defaults' => array(
                                'controller' => 'Labs\Image\Controller\Index',
                                'action' => 'view',
                            )
                        )
                    ),
                    'idownload' => array(
                        'type' => 'Zend\Mvc\Router\Http\Segment',
                        'options' => array(
                            'route' => '/idownload/:id',
                            'defaults' => array(
                                'controller' => 'Labs\Image\Controller\Index',
                                'action' => 'download',
                            )
                        )
                    ),
                    'iupload' => array(
                        'type' => 'Zend\Mvc\Router\Http\Literal',
                        'options' => array(
                            'route' => '/iupload',
                            'defaults' => array(
                                'controller' => 'Labs\Image\Controller\Index',
                                'action' => 'upload',
                            )
                        )
                    ),
                    'idelete' => array(
                        'type' => 'Zend\Mvc\Router\Http\Segment',
                        'options' => array(
                            'route' => '/idelete/:id',
                            'defaults' => array(
                                'controller' => 'Labs\Image\Controller\Index',
                                'action' => 'delete',
                            )
                        )
                    ),
                )
            )
        )
    ),
    'controllers' => array(
        'factories' => array(
            'Labs\Image\Controller\Index' => 'Labs\Image\Controller\Factory\IndexControllerFactory',
        )
    ),
    'service_manager' => array(
        'factories' => array(
            'Labs\Image\Service\ImageService' => 'Labs\Image\Service\Factory\ImageServiceFactory',
            'Labs\Image\Strategy\File\SimpleStrategy' => 'Labs\Image\Strategy\File\SimpleStrategy'
        )
    ),
    'view_manager' => array(
        'display_not_found_reason' => true,
        'display_exceptions' => true,
        'doctype' => 'HTML5',
        'not_found_template' => '404',
        'exception_template' => 'error',
        'template_map' => array(
            'error' => __DIR__ . '/../view/error/error.phtml',
            '404' => __DIR__ . '/../view/error/404.phtml',
            'labs/index/upload' => __DIR__ . '/../view/index/upload.phtml',
            'labs/index/view' => __DIR__ . '/../view/index/view.phtml',
            'labs/index/download' => __DIR__ . '/../view/index/download.phtml',
            'labs/index/delete' => __DIR__ . '/../view/index/delete.phtml',
            'layout/layout' => __DIR__ . '/../view/layout/layout.phtml'
        )
    )
);