<?php

/**
 * Created by PhpStorm.
 * User: laboratorio
 * Date: 16/03/15
 * Time: 17:46.
 */

namespace AppBundle\Controller\Frontend;

use AppBundle\Entity\Folder;
use AppBundle\Entity\File;
use AppBundle\Entity\User;
use AppBundle\Form\Type\AccessFolderAnonType;
use AppBundle\Form\Type\AccessFolderType;
use AppBundle\Form\Type\CreateFolderType;
use AppBundle\Event\FileEvent;
use AppBundle\FileEvents;
use AppBundle\Form\Type\CreateFileType;
use AppBundle\Form\Type\CreateFileAnonType;
use AppBundle\Form\Type\EditFolderType;
use AppBundle\Form\Type\FolderCreateFileType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

/**
 * Class FolderController
 * @package AppBundle\Controller\Frontend
 * @Route("/folder")
 */
class FolderController extends Controller
{
    /**
     * @Route("/new" , name="folder_new")
     * @Template("frontend/Folder/new.html.twig")
     */
    public function newAction(Request $request)
    {
        $folder = new Folder();
        $this->denyAccessUnlessGranted('CREATE', $folder);

        $user = $this->getUser();

        $form = $this->createForm(new CreateFolderType(), $folder);

        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $folder->setUser($user);
            $em->persist($folder);
            $em->flush();

            $this->addFlash('success', $this->get('translator')->trans('create.success', ['folder' => $folder ]));

            return $this->redirectToRoute('homepage');
        }

        return [
            'form' => $form->createView(),
        ];
    }

    /**
     * @Route("/{slug}/delete", name="folder_delete")
     */
    public function deleteAction(Folder $folder)
    {
        $this->denyAccessUnlessGranted('DELETE', $folder);

        $em = $this->getDoctrine()->getManager();
        $em->remove($folder);
        $em->flush();

        $this->addFlash('success', $this->get('translator')->trans('delete.success', ['folder' => $folder ]));

        return $this->redirectToRoute('homepage');
    }

    /**
     * @Route("/{slug}" , name="folder_show")
     * @Method(methods={"GET"})
     * @Template("frontend/Folder/show.html.twig")
     */
    public function showAction(Folder $folder)
    {
        $em = $this->getDoctrine()->getManager();
        $sizeAndNumOfFiles= $em->getRepository('AppBundle:File')->sizeAndNumOfFiles();
        $files = $em->getRepository('AppBundle:Folder')->listFiles($folder);

        if (false === $this->isGranted('ACCESS', $folder)) {
            $form = $this->createAccessFolderForm($folder);
            return $this->render("frontend/Folder/show_with_password.html.twig", [
                'folder' => $folder,
                'days_before_clean' => $this->container->getParameter('days_before_clean'),
                'form' => $form->createView(),
            ]);
        }

        return [
            'files' => $files,
            'folder' => $folder,
            'sum' => $sizeAndNumOfFiles,
            'days_before_clean' => $this->container->getParameter('days_before_clean'),
        ];
    }




    /**
     * @Route("/{slug}", name="folder_check")
     * @Method(methods={"POST"})
     * @Template("frontend/Folder/show.html.twig")
     */
    public function checkPasswordAction(Folder $folder, Request $request)
    {
        $user = $this->getUser();
        $em = $this->getDoctrine()->getManager();
        $session = $this->get('session');
        $sizeAndNumOfFiles= $em->getRepository('AppBundle:File')->sizeAndNumOfFiles();
        $files = $em->getRepository('AppBundle:Folder')->listFiles($folder);


        if (false === $this->isGranted('ACCESS', $folder)) {
            $form = $this->createAccessFolderForm($folder);
            $form->handleRequest($request);

            if ($form->isValid()) {
                if ($user instanceof User) {
                    $folder->addUsersWithAccess($user);
                    $em->persist($folder);
                    $em->flush();
                } else {
                    $session->set($folder->getSlug(), true);
                }

                $this->addFlash('success', $this->get('translator')->trans('message.password.valid'));
            } else {
                $this->addFlash('danger', $this->get('translator')->trans('message.password.invalid'));
                return $this->render("frontend/Folder/show_with_password.html.twig", [
                    'folder' => $folder,
                    'days_before_clean' => $this->container->getParameter('days_before_clean'),
                    'form' => $form->createView(),
                    'files' => $files,

                ]);
            }
        }

        return [
            'folder' => $folder,
            'sum' => $sizeAndNumOfFiles,
            'days_before_clean' => $this->container->getParameter('days_before_clean'),
            'files' => $files,

        ];
    }

    private function createAccessFolderForm($folder)
    {
        $factory = $this->get('security.encoder_factory');
        if ($this->getUser() instanceof User) {
            $type = new AccessFolderType($factory);
        } else {
            $type = new AccessFolderAnonType($factory);
        }

        return $this->createForm($type, $folder);
    }

    /**
     * @Route("/{slug}/share" , name="folder_edit")
     * @Template("frontend/Folder/share.html.twig")
     */
    public function shareAction(Folder $folder, Request $request)
    {
        $this->denyAccessUnlessGranted('SHARE', $folder);

        $em = $this->getDoctrine()->getManager();
        $files = $em->getRepository('AppBundle:Folder')->listFiles($folder);

        $form = $this->createForm(new EditFolderType(), $folder);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($folder);
            $em->flush();

            $this->addFlash('success', $this->get('translator')->trans('edit.success', array('folder' => $folder)));

            return $this->redirectToRoute("folder_show", ['slug' => $folder->getSlug()]);
        }

        return [
            'files'=>$files,
            'folder' => $folder,
            'form' => $form->createView(),
        ];
    }

    /**
     * @Route("/s/{shareCode}/{slug}", name="folder_share")
     */
    public function ShareFileAction(Folder $folder)
    {
        $user = $this->getUser();
        $session = $this->get('session');
        $em = $this->getDoctrine()->getManager();

        if (false === $this->get('security.authorization_checker')->isGranted('access', $folder)) {
            if ($user) {
                $folder->addUsersWithAccess($user);
                $em->persist($folder);
                $em->flush();
            } else {
                $session->set($folder->getSlug(), true);
            }
        }
        return $this->redirectToRoute('folder_show', array('slug' => $folder->getSlug()));
    }

    /**
     * @Route("/{slug}/file/upload" , name="folder_file_upload")
     * @Template("frontend/Folder/file_upload.html.twig")
     */
    public function createFileAction(Request $request, Folder $folder)
    {
        $this->denyAccessUnlessGranted('UPLOAD', $folder);

        $em = $this->getDoctrine()->getManager();
        $files = $em->getRepository('AppBundle:Folder')->listFiles($folder);

        $file = new File();
        $form = $this->createForm(new FolderCreateFileType(), $file);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $file->setFolder($folder);
            $file->setUser($this->getUser());
            $file->setIpAddress($request->getClientIp());


            $this->get('stof_doctrine_extensions.uploadable.manager')->markEntityToUpload($file, $file->getFilename());
            $em->persist($file);
            $em->flush();

            $this->container->get('event_dispatcher')->dispatch(FileEvents::SUBMITTED, new FileEvent($file));

            if($file->getScanStatus()==File::SCAN_STATUS_OK) {
                $this->addFlash('success', $this->get('translator')->trans('upload.success', array('file' => $file)));
            } else if($file->getScanStatus()==File::SCAN_STATUS_FAILED) {
                $this->addFlash('danger', $this->get('translator')->trans('upload.failed', array('file' => $file)));
            } else {
                $this->addFlash('danger', $this->get('translator')->trans('upload.virus', ['file' => $file]));
            }

            return $this->redirectToRoute('folder_show', array('slug' => $folder->getSlug()));
        }

        return [
            'files' => $files,
            'folder' => $folder,
            'form' => $form->createView(),
        ];
    }

    /**
     * @Route("/file/{slug}/download", name="folder_file_download")
     */
    public function downloadFileAction(File $file)
    {
        $this->denyAccessUnlessGranted('DOWNLOAD', $file->getFolder());

        $fileToDownload = $file->getPath();
        $response = new BinaryFileResponse($fileToDownload);
        $response->trustXSendfileTypeHeader();
        $response->setContentDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,
            $file->getFilename(),
            iconv('UTF-8', 'ASCII//TRANSLIT', $file->getFilename())
        );

        return $response;
    }

    /**
     * @Route("/file/{slug}/delete", name="folder_file_delete")
     */
    public function deleteFileAction(File $file)
    {
        $folder = $file->getFolder();
        $this->denyAccessUnlessGranted('DELETE', $folder);

        $em = $this->getDoctrine()->getManager();
        $em->remove($file);
        $em->flush();

        $this->addFlash('success', $this->get('translator')->trans('deleteFile.success', array('file' => $file)));

        return $this->redirectToRoute("folder_show", ['slug' => $folder->getSlug()]);
    }
}
