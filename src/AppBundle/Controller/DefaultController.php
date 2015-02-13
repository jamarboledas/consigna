<?php

namespace AppBundle\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
    /**
     *
     * @Route("/" , name="files-list")
     */
    public function filesListAction()
    {
        $em = $this->getDoctrine()->getManager();
        $files = $em->getRepository('AppBundle:File')->findAll();



        return $this->render(
            'Default/filesList.html.twig',
            array(
                'files' => $files
            )
        );
    }

    /**
     * @Route("/file/{slug}/delete/", name="delete-file")
     */
    public function deleteFileAction(Request $request)
    {
        $user= $this->get('security.context')->getToken()->getUser();

        if ($user != $request->getUser()) {
            throw $this->createAccessDeniedException();
        }

        $file=$this->getDoctrine()->getRepository('AppBundle:File')->findOneByslug($request->get('slug'));
        $em = $this->getDoctrine()->getManager();
        $em->remove($file);
        $em->flush();

        return $this->redirectToRoute('files-list');

    }

    /**
     * @Route("/find/", name="find")
     *
     */
    public function findFileAction(Request $request)
    {
        $word = $request->get('word');
        $em=$this->getDoctrine()->getManager();
        $foundFiles= $em->getRepository('AppBundle:File')->findFiles($word);


        return $this->render(
            'Default/filesList.html.twig', array(
               'files' => $foundFiles
        ));
    }


    /**
     * @Route("/my-files/", name="my-files")
     */

    public function myFilesAction()
    {
        $user=$this->getUser();



        return $this->render(
            'Default/filesList.html.twig', array(
            'files' => $user->getFiles()
        ));
    }

    /**
     * @Route("/Login/", name="new_login")
     *
     */
    public function newLoginAction()
    {
        return $this->render(
            'Default/newLogin.html.twig');
    }
}