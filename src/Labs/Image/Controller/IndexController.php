<?php

namespace Labs\Image\Controller;

use Labs\Image\Form\UploadForm;
use Labs\Image\Service\ImageService;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class IndexController extends AbstractActionController {

    private $imageService;

    public function __construct(ImageService $imageService) {
        $this->imageService = $imageService;
    }

    /**
     * Raw view, no html tags.
     *
     * @return ViewModel
     */
    public function viewAction() {
        $imageName = $this->params()->fromRoute('id');

        $cache = 0;
        if ($this->params()->fromQuery('cache')) {
            $cache = $this->params()->fromQuery('cache');
        }

        $image = null;
        if ($this->params()->fromQuery('width')) {
            $image = $this->imageService->getVariant($imageName, $this->params()->fromQuery('width'));
        } else {
            $image = $this->imageService->get($imageName);
        }

        if (! $image) {
            $this->getResponse()->setStatusCode(404);
            return;
        }

        $response = $this->getResponse();
        $response->getHeaders()
            ->addHeaderLine('Content-Type: ' . $image->getMimeType())
            ->addHeaderLine('X-Cache-Control: max-age= ' . $cache . ', private');

        $viewModel = new ViewModel(array(
            'image' => $image
        ));
        $viewModel->setTerminal(true);

        return $viewModel;
    }

    public function downloadAction() {
        $imageName = $this->params()->fromRoute('id');

        $image = $this->imageService->get($imageName);

        if (!$image) {
            return $this->getResponse()->setStatusCode(404);
        };

        $response = $this->getResponse();
        $response->getHeaders()
            ->addHeaderLine('Content-Type: application/octet-stream')
            ->addHeaderLine('Content-Disposition: attachment; filename="' . $imageName . '"');
        $viewModel = new ViewModel(array(
            'image' => $image
        ));
        $viewModel->setTerminal(true);

        return $viewModel;
    }

    public function uploadAction() {
        $form = new UploadForm($this->getServiceLocator()->get('Config'));

        $request = $this->getRequest();
        $fileNames = array();
        if ($request->isPost()) {
            $post = array_merge_recursive(
                    $request->getPost()->toArray(), $request->getFiles()->toArray()
            );

            $form->setData($post);

            if ($form->isValid()) {
                $data = $form->getData();
                foreach ($data['image-file'] as $value) {
                    $fileNames[] = $this->imageService->save($value['tmp_name']);
                }

            }
        }

        return array('form' => $form, 'fileNames' => $fileNames);
    }

    public function deleteAction() {
        $id = $this->params()->fromRoute('id');
        $result = $this->imageService->delete($id);

        return array('result' => $result);
    }

}