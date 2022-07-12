<?php

namespace App\Controller;

use App\Entity\CV;
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
use App\Repository\CandidatRepository;
use App\Repository\CompetanceRepository;
use App\Repository\ExperienceRepository;
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
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
/**
 * @Route("/rest", name="rest_")
 */
class RestCandidatController extends AbstractController
{
   
    /**
   
     * @Route("/addcv", name="addcv")
     */
    public function addCV(Request $request,NormalizerInterface $normalizer,CandidatRepository $cand,CVRepository $CV , CompetanceRepository $com , ExperienceRepository $exp){
       
        
        $cv= new CV();
        $candidat= new Candidat();
        $em = $this->getDoctrine()->getManager();
        
        $form->handleRequest($request);
        $email=$this->getUser($request->get('id'))->getEmail();
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
            $cv->setVideo($request->get('video'));    

            
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
          
            $cv->setPhoto($request->get('photo')); 
            
            $candidat->setCV($cv);
            $candidat->setUser($this->getUser());
           
           
           $ex=$this->getUser()->setExist(true);
        
           $cand->add($candidat);
           $CV->add($cv);


           $jsonContent = $normalizer->normalize($cand,'json',['Candidat'=>'post:read']);
           $jsonContent1 = $normalizer->normalize($CV,'json',['cv'=>'post:read']);
           return new Response(json_encode($jsonContent, $jsonContent1));
        }
        
      
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
    
        if (!$Candidat) {
 
            return $this->json('No Offre Job found for id' . $id, 404);
        }
        $com = [];
        foreach ($Competance as $Competances) {
            $com[] = [
                'id' => $Competances->getId(),
                'Competance'=>$Competances->getCompetance(),
                
            ];
        }
        $exp = [];
        foreach ($Experience as $Experiences) {
            $exp[] = [
                'id' => $Experiences->getId(),
                'Date_debut'=>$Experiences->getDateDebut(),
                'Date_fin'=>$Experiences->getDateFin(),
                'Titre'=>$Experiences->getTitre(),
                'Lieu'=>$Experiences->getLieu(),
                'Description'=>$Experiences->getDescription(),
                

                
            ];
        }
        $data =  [
            'id' => $Candidat->getId(),
            'CV'=>[
                'id' => $Candidat->getCV()->getId(),
                'email'=>$Candidat->getCV()->getEmail(),
                'sexe'=>$Candidat->getCV()->getSexe(),
                'pays'=>$Candidat->getCV()->getPays(),
                'datenaissance'=>$Candidat->getCV()->getDateNaissance(),
                'ville'=>$Candidat->getCV()->getVille(),
                'languepreferee'=>$Candidat->getCV()->getLanguePreferee(),
                'photo'=>$Candidat->getCV()->getPhoto(),
                'video'=>$Candidat->getCV()->getVideo(),
                'Competance'=> $com,
                'Experience'=>$exp,
               
            ],
           
        ];
       
       
        $jsonc = json_encode($data);

        return new Response($jsonc);
 
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
    
     * @Route("/editProfil", name="editProfil")
     */
    public function editProfileAction(Request $request,NormalizerInterface $normalizer){

        $em=$this->getDoctrine()->getManager();
        $user= new User();
        $candidat=$em->getRepository(Candidat::class)->find($request->get('id'));
        $iduser=$candidat->getUser()->getId();
        $user=$em->getRepository(User::class)->find($iduser);
      
        $user->setEmail($request->get('email'));
        $user->setNom($request->get('nom'));
        $user->setPrenom($request->get('prenom'));
        $em->flush();
           
        $normalizer = new ObjectNormalizer();
        $normalizer->setCircularReferenceLimit(1);
        $normalizer->setCircularReferenceHandler(function ($user) {
            return $rep->getId();
        });
        $encoders = [new JsonEncoder()];
        $normalizers = array($normalizer);
        $serializer = new Serializer($normalizers,$encoders);
        $formatted = $serializer->normalize($user);
        return new JsonResponse($formatted);

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
        $posts=$repository->Search($data);

        $data = [];
 
        foreach ($posts as $post) {
           $data[] = [
            'id' => $post->getId(),
            'description'=>$post->getDescription(),
            'dateOffre'=>$post->getDateOffre(),
            
            'recruteur'=>[
                'id' => $post->getRecruteur()->getId(),
                'email'=>$post->getRecruteur()->getEmail(),
                'pays'=>$post->getRecruteur()->getPays(),
                'photo'=>$post->getRecruteur()->getPhoto(),
                'ville'=>$post->getRecruteur()->getVille(),
                'diplome'=>$post->getRecruteur()->getDiplome(),
                'langue_preferee'=>$post->getRecruteur()->getLanguePreferee(),
                'OffreEmploi'=> $post->getRecruteur()->getOffreEmploi(),
               'User'=>
                    ['id'=> $post->getRecruteur()->getUser()->getId(),
                    ],

            ],
            'type'=>[
                'id' => $post->getType()->getId(),
                'type' => $post->getType()->getType(),
        ],
            'titre'=>$post->getTitre(),
            'categorie'=>$post->getCategorie(),
            'salaire'=>$post->getSalaire(),
      
            'DateFinOffre'=>$post->getDateFinOffre(),
            'blocked'=>$post->getBlocked(),
            'accepted'=>$post->getAccepted(),
            
            
         
            
               
           ];
        }
 
         $jsonc = json_encode($data);
        return new Response($jsonc);

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
           
        if (!$Off) {
 
            return $this->json('No Offre Job found for id' . $id, 404);
        }
      
        $data = [
            'id' => $Off->getId(),
            'description'=>$Off->getDescription(),
            'dateOffre'=>$Off->getDateOffre(),
            
            'recruteur'=>[
                'id' => $Off->getRecruteur()->getId(),
                'email'=>$Off->getRecruteur()->getEmail(),
                'pays'=>$Off->getRecruteur()->getPays(),
                'photo'=>$Off->getRecruteur()->getPhoto(),
                'ville'=>$Off->getRecruteur()->getVille(),
                'diplome'=>$Off->getRecruteur()->getDiplome(),
                'langue_preferee'=>$Off->getRecruteur()->getLanguePreferee(),
                'OffreEmploi'=> $Off->getRecruteur()->getOffreEmploi(),
               'User'=>
                    ['id'=> $Off->getRecruteur()->getUser()->getId(),
                    ],

            ],
            'type'=>[
                'id' => $Off->getType()->getId(),
                'type' => $Off->getType()->getType(),
        ],
            'titre'=>$Off->getTitre(),
            'categorie'=>$Off->getCategorie(),
            'salaire'=>$Off->getSalaire(),
      
            'DateFinOffre'=>$Off->getDateFinOffre(),
            'blocked'=>$Off->getBlocked(),
            'accepted'=>$Off->getAccepted(),
            
            
         
            
               
           ];
        $jsonc = json_encode($data);
        return new Response($jsonc);
    }
    /**
     * @Route("/listOffPostuler", name="listOffPostuler")
     */
    public function listOffPostulerAction(Request $request, PaginatorInterface $paginator)
    {
       
        $user=$request->get('id');
        $candidat=$this->getDoctrine()->getRepository(Candidat::class)->findBy(array('User'=>$request->get('id')));
      
         $cand=$candidat["0"];
        $id=$cand->getId();
        $Candidature=$this->getDoctrine()->getRepository(Candidature::class)->findBy(array('candidat'=>$id));
           
        if (!$Candidature) {
 
            return $this->json('No Offre Job found for id' . $user, 404);
        }
       
        $data = [];
        foreach ($Candidature as $Candidatures) {
        $data[] = [
            'id' => $Candidatures->getId(),
            'Candidat' =>[
                'id'=> $Candidatures->getCandidat()->getId(),
                'cv'=>[
                  'id'=>   $Candidatures->getCandidat()->getCV()->getId(),
                ],
                'user'=>[

                'id'=>$Candidatures->getCandidat()->getUser()->getId(),
                ],
                
            ],
            'Cv' => $Candidatures->getCv(),
            'Job' => ['id' => $Candidatures->getJob()->getId(),
            'description'=>$Candidatures->getJob()->getDescription(),
            'dateOffre'=>$Candidatures->getJob()->getDateOffre(),
            
            'recruteur'=>[
                'id' => $Candidatures->getJob()->getRecruteur()->getId(),
                'email'=>$Candidatures->getJob()->getRecruteur()->getEmail(),
                'pays'=>$Candidatures->getJob()->getRecruteur()->getPays(),
                'photo'=>$Candidatures->getJob()->getRecruteur()->getPhoto(),
                'ville'=>$Candidatures->getJob()->getRecruteur()->getVille(),
                'diplome'=>$Candidatures->getJob()->getRecruteur()->getDiplome(),
                'langue_preferee'=>$Candidatures->getJob()->getRecruteur()->getLanguePreferee(),
                'OffreEmploi'=> $Candidatures->getJob()->getRecruteur()->getOffreEmploi(),
               'User'=>
                    ['id'=> $Candidatures->getJob()->getRecruteur()->getUser()->getId(),
                    ],

            ],
            'type'=>[
                'id' => $Candidatures->getJob()->getType()->getId(),
                'type' => $Candidatures->getJob()->getType()->getType(),
        ],
            'titre'=>$Candidatures->getJob()->getTitre(),
            'categorie'=>$Candidatures->getJob()->getCategorie(),
            'salaire'=>$Candidatures->getJob()->getSalaire(),
      
            'DateFinOffre'=>$Candidatures->getJob()->getDateFinOffre(),
            'blocked'=>$Candidatures->getJob()->getBlocked(),
            'accepted'=>$Candidatures->getJob()->getAccepted(),
            
            
         
            
               
        ],
            'CreatedAt' => $Candidatures->getCreatedAt(),
            'etat' => $Candidatures->getEtat(),
            
            
           ];
        }
        $jsonc = json_encode($data);
        return new Response($jsonc);

    }

   
}
