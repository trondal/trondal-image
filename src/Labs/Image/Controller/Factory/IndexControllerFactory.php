<?php

namespace Labs\Image\Controller\Factory;

use Labs\Image\Controller\IndexController;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class IndexControllerFactory implements FactoryInterface {

    public function createService(ServiceLocatorInterface $controllerManager) {
        $serviceLocator = $controllerManager->getServiceLocator();
        $config = $serviceLocator->get('Configuration');
        $strategyConfig = $config['labsimage']['configuration'];

        $imageStrategy = new $strategyConfig['class'](
            $strategyConfig['options']['content_directory'],
            $strategyConfig['options']['storeVariants']
        );
        $imageService = new \Labs\Image\Service\ImageService($imageStrategy);

        return new IndexController($imageService);
    }

}