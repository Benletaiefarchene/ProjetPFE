<?php

namespace App\Controller;

use App\Entity\CV;
use App\Form\CVType;
use App\Entity\Candidat;
use App\Entity\Competance;
use App\Form\CompetanceType;
use App\Repository\CVRepository;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Common\Collections\ArrayCollection;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CandidatController extends AbstractController
{
   
    

    /**
     * @Route("/addcv", name="candidat")
     */
    public function addCV(Request $request){
        $cv= new CV();
        $candidat= new Candidat();
        $em = $this->getDoctrine()->getManager();
        
        $form= $this->createForm(CVType ::class, $cv );
        $form->handleRequest($request);

        $orignalExp = new ArrayCollection();
        foreach ($cv->getExperiences() as $exp) {
            $orignalExp->add($exp);
        }
        $orignalCom = new ArrayCollection();
        foreach ($cv->getCompetances() as $com) {
            $orignalCom->add($com);
        }

        if($form->isSubmitted() && $form->isValid())
        {
            $file= $cv->getPhoto();
            $filename = md5(uniqid()).'.'.$file->guessExtension();
            try{
                $file->move(
                    $this->getParameter('images_directory'),
                    $filename
                );
            }catch(FileException $e){

            }
            $file1= $cv->getVideo();
            $filename1 = md5(uniqid()).'.'.$file1->guessExtension();
            try{
                $file1->move(
                    $this->getParameter('images_directory'),
                    $filename1
                );
            }catch(FileException $e){
                
            }
            $cv->setVideo($filename1);    

            
            foreach ($orignalExp as $exp) {
                // check if the exp is in the $user->getExp()
//                dump($user->getExp()->contains($exp));
                if ($cv->getExperiences()->contains($exp) === false) {
                    $em->remove($exp);
                }
            }
            foreach ($orignalCom as $com) {
                // check if the exp is in the $user->getExp()
//                dump($user->getExp()->contains($exp));
                if ($cv->getCompetances()->contains($com) === false) {
                    $em->remove($com);
                }
            }
            
            $em->persist($cv);
            $em->flush();
            $cv->setPhoto($filename);   
            
            $candidat->setCV($cv);
            $candidat->setUser($this->getUser());
            $em->persist($candidat);
            $em->flush();
            dd($candidat);
            $this->addFlash('info', 'Created Successfully !');
            return $this->redirectToRoute('homee');
        }
        
        return $this->render('Candidat/CV.html.twig', [
            'cv' => $form->createView(),
        ]);
        
    }
    /**
     * @Route("/detailedCV/{id}", name="detailed")
     */
        public function showdetailedAction($id)
    {
        $CV= $this->getDoctrine()->getRepository(CV::class)->find($id);
        
        
        return $this->render('Candidat/detailedCV.html.twig',[
            'cv'=> $CV
        ] 
            
           
        );
    }
   
   
}
