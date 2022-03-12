<?php

namespace App\Controller;
use Symfony\Component\Form\FormInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\OffreEmploi;
use App\Form\AddPostType;

class RecruteurController extends AbstractController
{
   
    public function index(): Response
    {
        return $this->render('recruteur/addpost.html.twig', [
            'controller_name' => 'RecruteurController',
        ]);
    }
     /**
     * @Route("/addpost", name="recruteur")
     */
    public function addpost(Request $request){
        $Offre= new OffreEmploi();
        $form= $this->createForm(AddPostType ::class, $Offre);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $em = $this->getDoctrine()->getManager();
            $Offre->setCreator($this->getUser());
            $Offre->setPostdate(new \DateTime('now'));

            $em->persist($Offre);
            $em->flush();

            $this->addFlash('info', 'Created Successfully !');
        }
        return $this->render('recruteur/addpost.html.twig', [
            'Offre' => $form->createView(),
        ]);
        return $this->redirectToRoute('home');
    }
   
}
