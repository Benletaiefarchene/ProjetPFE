<?php

namespace App\Controller;

use Swift;
use App\Entity\CV;
use Dompdf\Dompdf;
use Dompdf\Options;
use App\Entity\User;
use App\Form\CVType;
use App\Form\UserType;
use App\Data\SearchData;
use App\Entity\Candidat;
use App\Form\SearchForm;
use App\Entity\Recruteur;
use App\Entity\Competance;
use App\Entity\Experience;
use App\Form\UserPassType;
use App\Entity\Candidature;
use App\Entity\OffreEmploi;
use App\Form\CompetanceType;
use App\Services\QrcodeService;
use App\Repository\CVRepository;
use Symfony\Component\Form\FormError;
use App\Repository\OffreEmploiRepository;
use Symfony\Component\Form\FormInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\HttpFoundation\JsonResponse;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
   
class CandidatController extends AbstractController

{
   
    /**
     * IsGranted("ROLE_CANDIDAT)
     * @Route("/addcv", name="addcv")
     */
    public function addCV(Request $request, \Swift_Mailer $mailer,TranslatorInterface $Trans){
        $this->denyAccessUnlessGranted('ROLE_CANDIDAT');
        
        $cv= new CV();
        $candidat= new Candidat();
        $em = $this->getDoctrine()->getManager();
        
        $form= $this->createForm(CVType ::class, $cv );
        $form->handleRequest($request);
        $email=$this->getUser()->getEmail();
        
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
            $cv->setEmail($email);
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
           dd($request);
            $mail=$Trans->trans('Your account has been successfully created');
           $message=(new \Swift_Message('Internisa'))
  
           ->setFrom ('archene9@gmail.com')
           ->setTo ($email)
            ->setBody(
            
                "<p>'$mail'</p>",'text/html'
            )
            ;
            $mailer->send($message);
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
        $rec=$this->getDoctrine()->getRepository(Recruteur::class)->findAll();
        $can=$this->getDoctrine()->getRepository(Candidat::class)->findAll();
        
        $Competance= $this->getDoctrine()->getRepository(Competance::class)->findComById($id);
    
        $Experience= $this->getDoctrine()->getRepository(Experience::class)->findExpById($id);
       
        return $this->render('Candidat/detailedCV.html.twig',[
            'cv'=> $Candidat,
            'Competances'=>$Competance,
            'Experiences'=>$Experience,
            'qrCode' => $qrCode,
            "rec"=>$rec,
            "can"=>$can,

        ] 
            
           
        );
    }
    
    /**
     * IsGranted("ROLE_CANDIDAT)
     * @Route("/editCV/{id}", name="editCV")
     */
    public function editCVAction(Request $request , $id){
        $this->denyAccessUnlessGranted('ROLE_CANDIDAT');
        $em=$this->getDoctrine()->getManager();
        $candidat=$em->getRepository(Candidat::class)->find($id);
        $idcv=$candidat->getCV()->getId();
        $iduser=$candidat->getUser()->getId();
        
        $p= $em->getRepository(CV::class)->find($idcv);
        $user=$em->getRepository(User::class)->find($iduser);
        $form1=$this->createForm(UserType::class,$user);
        $form1->handleRequest($request);
        $form=$this->createForm(CVType::class,$p);
        $form->handleRequest($request);
        $rec=$this->getDoctrine()->getRepository(Recruteur::class)->findAll();
        $can=$this->getDoctrine()->getRepository(Candidat::class)->findAll();

       $email=$form1->get('email')->getViewData();
        if($form->isSubmitted() && $form1->isSubmitted() ){
            $file = $p->getPhoto();
            $filename= md5(uniqid()) . '.' . $file->guessExtension();
            $file->move($this->getParameter('images_directory'), $filename);
            $p->setPhoto($filename);

            $file1 = $p->getVideo();

            $filename1= md5(uniqid()) . '.' . $file1->guessExtension();
            $file1->move($this->getParameter('images_directory'), $filename1);
            $p->setVideo($filename1);
            $p->setEmail($email);
            $em= $this->getDoctrine()->getManager();

            $em->persist($p);
            $em->flush();
            $em->persist($user);
            $em->flush();
            return $this->redirectToRoute('detailedCV', array('id' => $id));

        }
        return $this->render('Candidat/editCV.html.twig', array(
            "user"=>$form1->createView(),
            "cv"=> $form->createView(),
            "rec"=>$rec,
            "can"=>$can,
        ));

    }
       /**
     * IsGranted("ROLE_CANDIDAT)
     * @Route("/editProfil/{id}", name="editProfil")
     */
    public function editProfileAction(Request $request , $id){
        $this->denyAccessUnlessGranted('ROLE_CANDIDAT');
        $em=$this->getDoctrine()->getManager();
        $candidat=$em->getRepository(Candidat::class)->find($id);
        $iduser=$candidat->getUser()->getId();
        $user=$em->getRepository(User::class)->find($iduser);
        $form1=$this->createForm(UserType::class,$user);
        $form1->handleRequest($request);
        $rec=$this->getDoctrine()->getRepository(Recruteur::class)->findAll();
        $can=$this->getDoctrine()->getRepository(Candidat::class)->findAll();
        
       
        if($form1->isSubmitted() && $form1->isSubmitted() ){
          
            $em= $this->getDoctrine()->getManager();
          
            $em->persist($user);
            $em->flush();
           // return $this->redirectToRoute('detailed');

        }
        return $this->render('Candidat/editProfil.html.twig', array(
            "user"=>$form1->createView(),
            "rec"=>$rec,
            "can"=>$can,
          
        ));

    }
       /**
     * IsGranted("ROLE_CANDIDAT)
     * @Route("/editPass/{id}", name="editPass")
     */
    public function editPassAction(Request $request , $id,UserPasswordEncoderInterface $userPasswordEncoder){
        $this->denyAccessUnlessGranted('ROLE_CANDIDAT');
        $em=$this->getDoctrine()->getManager();
        $candidat=$em->getRepository(Candidat::class)->find($id);
        $iduser=$candidat->getUser()->getId();
        $user=$em->getRepository(User::class)->find($iduser);
        $form1=$this->createForm(UserPassType::class);
        $form1->handleRequest($request);
        $OldPassword=$form1->get('OldPassword')->getViewData();
        $rec=$this->getDoctrine()->getRepository(Recruteur::class)->findAll();
        $can=$this->getDoctrine()->getRepository(Candidat::class)->findAll();
        $u=$user->getPassword();

        if($form1->isSubmitted() && $form1->isSubmitted() ){
            
            if(!password_verify($form1->get('OldPassword')->getViewData(), $user->getPassword()))
            {
               
                $form1->get('OldPassword')->addError(new FormError("Le mot de passe renseigné ne correspond pas à votre mot de passe actuel"));
            }
            else{
                $NewPassword=    $userPasswordEncoder->encodePassword(
                    $user,
                     $form1->get('NewPassword')->getViewData()
                 )
                ;
                $em= $this->getDoctrine()->getManager();
                if( $form1->get('NewPassword')->getViewData()== $form1->get('ConfPassword')->getViewData()){
            
                $user->setPassword( $NewPassword);
                $em->persist($user);
                $em->flush();

                }else{
                $form1->get('ConfPassword')->addError(new FormError("Le nouveau mot de passe  ne correspond pas au mot de passe de confirmation "));
                }

            
            }
            // $em->persist($user);
            // $em->flush();
           // return $this->redirectToRoute('detailed');

        }
        return $this->render('Candidat/editPass.html.twig', array(
            "user"=>$form1->createView(),
            "rec"=>$rec,
            "can"=>$can,
          
        ));

    }
   /**
     * @Route("/listPost", name="listpost")
     */
    public function listpostAction(Request $request,OffreEmploiRepository $repository)
    {
       
        
        
        $data = new SearchData();
        $form = $this->createForm(SearchForm::class , $data);
        $form->handleRequest($request);
       $rec=$this->getDoctrine()->getRepository(Recruteur::class)->findAll();
       $can=$this->getDoctrine()->getRepository(Recruteur::class)->findAll();
        $posts=$repository->findSearch($data);
        
        return $this->render('recruteur/listPost.html.twig', array(
            "posts" =>$posts,
            "rec"=>$rec,
            "can"=>$can,
            'form'=>$form->createView()
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
        $rec=$this->getDoctrine()->getRepository(Recruteur::class)->findAll();
        $can=$this->getDoctrine()->getRepository(Candidat::class)->findAll();
       dd($Off);
        return $this->render('Recruteur/detailedOffre.html.twig',[
           
            'off'=>$Off,
            "rec"=>$rec,
            "can"=>$can,
        ]   
        );
    }
    /**
     * @Route("/listOffPostuler", name="listOffPostuler")
     */
    public function listOffPostulerAction(Request $request, PaginatorInterface $paginator)
    {
       
        $user=$this->getUser()->getId();
        $candidat=$this->getDoctrine()->getRepository(Candidat::class)->findBy(array('User'=>$user));
        $rec=$this->getDoctrine()->getRepository(Recruteur::class)->findAll();
        $can=$this->getDoctrine()->getRepository(Candidat::class)->findAll();

        $cand=$candidat["0"];
        $id=$cand->getId();
        $Candidature=$this->getDoctrine()->getRepository(Candidature::class)->findBy(array('candidat'=>$id));
        
        // $idoff=$Candidature[];
        // $offre=$this->getDoctrine()->getRepository(OffreEmploi::class)->findBy(array('id'=>$idoff));
        $pagination = $paginator->paginate(
            $Candidature, /* query NOT result */
            $request->query->getInt('page', 1), /*page number*/
            3 /*limit per page*/
        );
        return $this->render('candidat/listOffPostuler.html.twig', array(
            "cand" =>$Candidature,
            'Form' => $pagination,
            "rec"=>$rec,
            "can"=>$can,
           
        ));
      

    }

  
   
   
}
