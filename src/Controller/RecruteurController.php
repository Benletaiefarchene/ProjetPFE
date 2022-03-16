<?php

namespace App\Controller;
use App\Entity\Recruteur;
use App\Form\AddPostType;
use App\Entity\OffreEmploi;
use App\Form\RecruteurType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class RecruteurController extends AbstractController
{
    /**
     * @Route("/addpost", name="candidat")
     */
    public function addPost(Request $request){
        
        $offre= new OffreEmploi();
        $recruteur = new Recruteur();
        $em = $this->getDoctrine()->getManager();
        
        $form= $this->createForm(AddPostType ::class, $offre );
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
           
            $offre->setRecruteur();
            $em->persist($offre);
            $em->flush();
            
         
        }
        
        return $this->render('recruteur/addpost.html.twig', [
            'Offre' => $form->createView(),
        ]);
        
    }
    /**
     * @Route("/addProfilerec", name="candidatemail")
     */
    public function addProfileRec(Request $request){
        
        $Rec= new Recruteur();
        
        $em = $this->getDoctrine()->getManager();
        
        $form= $this->createForm(RecruteurType ::class, $Rec );
        $form->handleRequest($request);

       

        if($form->isSubmitted() && $form->isValid())
        {
            $file= $Rec->getPhoto();
            $filename = md5(uniqid()).'.'.$file->guessExtension();
            try{
                $file->move(
                    $this->getParameter('images_directory'),
                    $filename
                );
            }catch(FileException $e){

            }
           
            $Rec->setPhoto($filename);   
            $Rec->setUser($this->getUser());
            $em->persist($Rec);
            $em->flush();
            $this->addFlash('info', 'Created Successfully !');
            return $this->redirectToRoute('homee');
        }
        
        return $this->render('Recruteur/AddProfileRec.html.twig', [
            'Rec' => $form->createView(),
        ]);
        
    }
     /**
     * @Route("/detailedRec/{id}", name="detailedRec")
     */
    public function showdetailedAction($id)
    {
        
        $Rec= $this->getDoctrine()->getRepository(Recruteur::class)->find($id);
        
       
        return $this->render('Recruteur/detailedRec.html.twig',[
            'Rec'=> $Rec,
         
        ]   
        );
    }
     /**
     * @Route("/editProfileRec/{id}", name="edit.Recruteur")
     */
    public function editProfileAction(Request $request , $id){

        $Rec= $this->getDoctrine()->getRepository(Recruteur::class)->find($id);
        $Rec->setUser($this->getUser());
        
        $form=$this->createForm(RecruteurType::class,$Rec);
        $form->handleRequest($request);
       
        if($form->isSubmitted()){
            $file = $Rec->getPhoto();
            $filename= md5(uniqid()) . '.' . $file->guessExtension();
            $file->move($this->getParameter('images_directory'), $filename);
            $Rec->setPhoto($filename);
          
            $em= $this->getDoctrine()->getManager();
            $em->persist($Rec);
            $em->flush();
            //return $this->redirectToRoute('detailed'{$id});

        }
        return $this->render('Recruteur/editProfileRec.html.twig', array(
            
            "Rec"=> $form->createView()
        ));

    }
   
}
