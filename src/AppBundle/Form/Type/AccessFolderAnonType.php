<?php
/**
 * Created by PhpStorm.
 * User: jamartinez
 * Date: 19/03/15
 * Time: 12:58
 */

namespace AppBundle\Form\Type;

use AppBundle\Entity\Folder;


use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\Session\Session;
use EWZ\Bundle\RecaptchaBundle\Validator\Constraints\True;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;


class AccessFolderAnonType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options){

        $folderPassword=$options['folderPassword'];
        $builder
            ->add('password', 'password', array(
                'constraints' => new Assert\EqualTo(array(
                    'value' => $folderPassword,
                    'message' => 'The password is not correct'
                ))))
            ->add('captcha', 'ewz_recaptcha', array(
                'attr' => array(
                    'options' => array(
                        'theme' => 'light',
                        'type'  => 'image'
                    )
                ),
                'mapped'      => false,
                'constraints' => array(
                    new True()
                )
            ))
            ->add('submit', 'submit')
            ->getForm();
    }


    public function setDefaultOptions(OptionsResolverInterface $resolver){
        $resolver->setDefaults(array(
            'folderPassword' => $this->folder->getPassword(),
        ));
    }

    public function getName(){
        return 'folder';
    }

    public function __construct(Folder $folder){
        $this->folder= $folder;
    }
}