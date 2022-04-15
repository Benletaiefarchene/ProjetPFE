<?php

namespace App\Controller;

use App\Entity\CV;
use App\Entity\User;
use App\Form\CVType;
use App\Form\UserType;
use App\Entity\Candidat;
use App\Entity\Recruteur;
use App\Entity\Competance;
use App\Entity\Experience;
use App\Entity\OffreEmploi;
use App\Form\CompetanceType;

use App\Services\QrcodeService;
use App\Repository\CVRepository;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Common\Collections\ArrayCollection;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
     /**
     * @Route("/candidat", name="candidat_")
     */
class CandidatController extends AbstractController
{
    /**
     * IsGranted("ROLE_CANDIDAT)
     * @Route("/addcv", name="addcv")
     */
    public function addCV(Request $request){
        $this->denyAccessUnlessGranted('ROLE_CANDIDAT');
        
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
           
           $ex=$this->getUser()->setExist(true);
           $em->persist($ex);
           $em->flush();
           
            $this->addFlash('info', 'Created Successfully !');
            return $this->redirectToRoute('homee');
        }
        
        return $this->render('Candidat/CV.html.twig', [
            'cv' => $form->createView(),
        ]);
        
    }
    /**
     * 
     * @Route("/detailedCV/{id}", name="detailedCV")
     */
        public function showdetailedAction($id,QrcodeService $qrcodeService)
    {
        $qrCode = null;
        $Candidat= $this->getDoctrine()->getRepository(Candidat::class)->find($id);
        $video=$Candidat->getCV()->getVideo();
        $qrCode = $qrcodeService->qrcode($video);
        
        $Competance= $this->getDoctrine()->getRepository(Competance::class)->findComById($id);
    
        $Experience= $this->getDoctrine()->getRepository(Experience::class)->findExpById($id);
       
        return $this->render('Candidat/detailedCV.html.twig',[
            'cv'=> $Candidat,
            'Competances'=>$Competance,
            'Experiences'=>$Experience,
            'qrCode' => $qrCode

        ] 
            
           
        );
    }
    
    /**
     * IsGranted("ROLE_CANDIDAT)
     * @Route("/editProfile/{id}", name="editProfile")
     */
    public function editProfileAction(Request $request , $id){
        $this->denyAccessUnlessGranted('ROLE_CANDIDAT');
        $em=$this->getDoctrine()->getManager();
        $p= $em->getRepository(CV::class)->find($id);
        $user=$em->getRepository(User::class)->find($id);
        $form1=$this->createForm(UserType::class,$user);
        $form1->handleRequest($request);
        $form=$this->createForm(CVType::class,$p);
        $form->handleRequest($request);
        
       
        if($form->isSubmitted() && $form1->isSubmitted() ){
            $file = $p->getPhoto();
            $filename= md5(uniqid()) . '.' . $file->guessExtension();
            $file->move($this->getParameter('images_directory'), $filename);
            $p->setPhoto($filename);

            $file1 = $p->getVideo();

            $filename1= md5(uniqid()) . '.' . $file1->guessExtension();
            $file1->move($this->getParameter('images_directory'), $filename1);
            $p->setVideo($filename1);
         
            $em= $this->getDoctrine()->getManager();
            $em->persist($p);
            $em->flush();
            $em->persist($user);
            $em->flush();
           // return $this->redirectToRoute('detailed');

        }
        return $this->render('Candidat/editProfile.html.twig', array(
            "user"=>$form1->createView(),
            "cv"=> $form->createView()
        ));

    }
   /**
     * @Route("/listPost", name="listpost")
     */
    public function listpostAction(Request $request)
    {
       
        $em=$this->getDoctrine()->getManager();
        $posts=$em->getRepository(OffreEmploi::class)->findAll();
        
        
        
        return $this->render('recruteur/listPost.html.twig', array(
            "posts" =>$posts
        ));
       // return $this->redirectToRoute('list_post');

    }
    /**
     * 
     * @Route("/detailedOffre/{id}", name="detailedOff")
     */
    public function detailedOffreAction($id)
    {
        
        $Off= $this->getDoctrine()->getRepository(OffreEmploi::class)->find($id);
      
       
        return $this->render('Recruteur/detailedOffre.html.twig',[
           
            'off'=>$Off
        ]   
        );
    }
   
}
