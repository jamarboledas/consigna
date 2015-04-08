<?php
/**
 * Created by PhpStorm.
 * User: jamartinez
 * Date: 19/03/15
 * Time: 12:58
 */

namespace AppBundle\Form\Type;

use AppBundle\Entity\File;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;


class DownloadFileType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options){

        $filePassword=$options['filePassword'];
        $builder
            ->add('password', 'password', array(
                'constraints' => new Assert\EqualTo(array(
                    'value' => $filePassword,
                    'message' => 'The password is not correct'
                ))))
            ->add('submit', 'submit')
            ->getForm();
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver){
        $resolver->setDefaults(array(
            'filePassword' => $this->file->getPassword(),
        ));
    }

    public function getName(){
        return 'file';
    }

    public function __construct(File $file){
        $this->file= $file;
    }
}