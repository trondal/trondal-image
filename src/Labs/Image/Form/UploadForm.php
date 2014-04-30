<?php

namespace Labs\Image\Form;

use Labs\Image\Filter\File\RenameUpload;
use Zend\Form\Form;
use Zend\InputFilter\FileInput;
use Zend\InputFilter\InputFilter;
use Zend\Validator\File\IsImage;
use Zend\Validator\File\Size;

class UploadForm extends Form {

    public function __construct($configuration) {
        parent::__construct('upload-form');

        $this->add(array(
            'name' => 'imageId',
            'type' => 'Zend\Form\Element\Textarea',
            'attributes' => array(
                'class' => 'imageId'
            )
        ));

        $this->add(array(
            'name' => 'image-file',
            'type' => 'File',
            'attributes' => array(
                'id' => 'image-file',
                'value' => 'select_image',
                'class' => 'btn selectFile',
                'data-id-target' => '.imageId',
                'multiple' => true
            )
        ));

        $this->add(array(
            'type' => 'Zend\Form\Element\Submit',
            'name' => 'submit',
            'attributes' => array(
                'value' => 'send'
            ))
        );
        $this->addInputFilter($configuration);
    }

    public function addInputFilter($configuration) {
        $inputFilter = new InputFilter();

        $fileInput = new FileInput('image-file');
        $fileInput->setRequired(true);

        $isImageValidator = new IsImage();
        $sizeValidator = new Size(array(
            'max' => $configuration['labsimage']['max_size'],
            'min' => $configuration['labsimage']['min_size']
        ));

        $fileInput->getValidatorChain()->attach($isImageValidator);
        $fileInput->getValidatorChain()->attach($sizeValidator);

        $path = $configuration['labsimage']['configuration']['options']['content_directory'];

        $renameUpload = new RenameUpload(array('target' => $path, 'randomize' => true));
        $renameUpload->setUseUploadName(true);
        $fileInput->getFilterChain()->attach($renameUpload);

        $inputFilter->add($fileInput);

        $this->setInputFilter($inputFilter);
    }

}