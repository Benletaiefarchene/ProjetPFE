<?php

namespace App\Controller;

use App\Entity\Type;
use App\Entity\User;
use App\Entity\Forum;
use App\Form\EtatType;
use App\Form\UserType;
use App\Form\ForumType;
use App\Data\SearchData;
use App\Entity\Candidat;
use App\Form\SearchForm;
use App\Entity\Recruteur;
use App\Form\AddPostType;
use App\Entity\Competance;
use App\Entity\Experience;
use App\Form\UserPassType;
use App\Entity\Candidature;
use App\Entity\Commentaire;
use App\Entity\OffreEmploi;
use App\Form\FormationType;
use App\Form\RecruteurType;
use App\Form\TypeOffreType;
use App\Service\T_HTML2PDF;
use App\Form\CandidatureType;
use App\Form\CommentaireType;
use App\Entity\OffreFormation;
use App\Repository\ForumRepository;
use Symfony\Component\Form\FormError;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\OffreEmploiRepository;
use Symfony\Component\Form\FormInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
 
    /**
     * @Route("/rest", name="rest_")
     */
class RestRecruteurController extends AbstractController
{
   
    /**
     * @Route("/addpost", name="addPost")
     */
    public function addPost(Request $request,NormalizerInterface $normalizer,OffreEmploiRepository $off){
        
        $offre= new OffreEmploi();
      
       
        $recruteur= $this->getDoctrine()->getRepository(Recruteur::class)->findBy(array('User'=>$request->get('id')));

        
        $em = $this->getDoctrine()->getManager();
        $user=$this->getDoctrine()->getRepository(User::class)->find($request->get('id'));
      
        $recruteur["0"]->setUser($user);
   
 
        $types= $this->getDoctrine()->getRepository(Type::class)->findAll();
      
            $offre->setDescription($request->get('description'));
            $offre->setTitre($request->get('titre'));
            $offre->setCategorie($request->get('categorie'));
            $offre->setSalaire($request->get('salaire'));
            
            $offre->setType($types[$request->get('type')]);
           $offre->setDateOffre(new \DateTime('now')); 
            $offre->setBlocked(0);
            $offre->setAccepted(0);
            $offre->setRecruteur($recruteur["0"]);
            $off->add($offre);
            $encoders = array(new XmlEncoder(), new JsonEncoder());
            $normalizers = array(new ObjectNormalizer());
            $serializer = new Serializer($normalizers, $encoders);
            $jsonContent = $serializer->serialize($offre, 'json');
            return new JsonResponse( array('OffreEmploi'=>$jsonContent) );
           
     
        
    }
    
     /**
     * @Route("/detailedOffre/{id}", name="detailedOff")
     */
    public function detailedOffreAction(Request $request,$id)
    {
        
        $Off= $this->getDoctrine()->getRepository(OffreEmploi::class)->find($id);
        $idrec= $Off->getRecruteur()->getId();
        $rec=$this->getDoctrine()->getRepository(Recruteur::class)->findBy(array('id'=>$idrec));
       
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
     * @Route("/postuler/{id}", name="postuler")
     */
    public function ApplyOffreAction(Request $request,$id)
    {
        $em = $this->getDoctrine()->getManager();
        $candidature = new Candidature();
        $Off= $this->getDoctrine()->getRepository(OffreEmploi::class)->find($id);
        $form= $this->createForm(CandidatureType ::class, $candidature );
        $form->handleRequest($request);
        $user=$this->getUser()->getId();
        $candidat=$this->getDoctrine()->getRepository(Candidat::class)->findBy(array('User' => $user));
        $rec=$this->getDoctrine()->getRepository(Recruteur::class)->findAll();
        $can=$this->getDoctrine()->getRepository(Candidat::class)->findAll();

       $cand=$candidat["0"];
       $date=new \DateTime('now');
    
        if($form->isSubmitted()&& $form->isValid()){
            $file= $candidature->getCv();
            dd($file);
            $filename = md5(uniqid()).'.'.$file->guessExtension();
            try{
                $file->move(
                    $this->getParameter('images_directory'),
                    $filename
                );
            }catch(FileException $e){

            }

            $candidature->setEtat(-1);
            $candidature->setCv( $filename);
            $candidature->setCandidat($cand);
            $candidature->setCreatedAt($date);
            $candidature->setJob($Off);
            $em->persist($candidature);
            $em->flush();

          return $this->redirectToRoute('detailedOff', array('id' => $id));
        }



        return $this->render('Recruteur/postuler.html.twig',[
            'Offre' => $form->createView(),
            'off'=>$Off,
            'can'=>$can,
            'rec'=>$rec,
        ]   
        );
    }
      /**
     * @Route("/etat/{id}", name="etat")
     */
    public function etatAction(Request $request , $id){

        $candidature= $this->getDoctrine()->getRepository(Candidature::class)->find($id);
        $rec=$this->getDoctrine()->getRepository(Recruteur::class)->findAll();
        $can=$this->getDoctrine()->getRepository(Candidat::class)->findAll();
        $form=$this->createForm(EtatType::class,$candidature);
        $form->handleRequest($request);
     
        if($form->isSubmitted()){
          
            $em= $this->getDoctrine()->getManager();
            $em->persist($candidature);
            $em->flush();
         //  return $this->redirectToRoute(path('recruteur_detailedOff','id'== $id));
           
        }
        return $this->render('Recruteur/etat.html.twig', array(
            "candidature"=>$candidature,
            "off"=> $form->createView(),
            'can'=>$can,
            'rec'=>$rec,
         
        ));

    }

     /**
     * @Route("/editPost/{id}", name="editPost")
     */
    public function editPostAction(Request $request , $id){

        $off= $this->getDoctrine()->getRepository(OffreEmploi::class)->find($id);
        $rec=$this->getDoctrine()->getRepository(Recruteur::class)->findAll();
        $can=$this->getDoctrine()->getRepository(Candidat::class)->findAll();
        $types= $this->getDoctrine()->getRepository(Type::class)->findAll();
        $form=$this->createForm(AddPostType::class,$off);
        $form->handleRequest($request);
    
        if($form->isSubmitted() ){
            $type=$request->request->get('co');
            $off->setType($types[$type-1]);
            $off->setDateOffre(new \DateTime('now'));
            $em= $this->getDoctrine()->getManager();
            $em->persist($off);
            $em->flush();
         //  return $this->redirectToRoute(path('recruteur_detailedOff','id'== $id));
           
        }
        return $this->render('Recruteur/editPost.html.twig', array(
            'types' => $types,
            "off"=> $form->createView(),
            'can'=>$can,
            'rec'=>$rec,
        ));

    }
    /**
     * @Route("/listPost", name="listpost")
     */
    public function listpostAction(Request $request,OffreEmploiRepository $repository,PaginatorInterface $paginator)
    {
       
      
        
        $data = new SearchData();
        $data->page =$request->get('page',1);
        $form = $this->createForm(SearchForm::class , $data);
        $form->handleRequest($request);
        $rec=$this->getDoctrine()->getRepository(Recruteur::class)->findAll();
        $can=$this->getDoctrine()->getRepository(Candidat::class)->findAll();
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
     * @Route("/listOffre", name="listOffre")
     */
    public function listOffreAction(Request $request,OffreEmploiRepository $repository, PaginatorInterface $paginator)
    {
       
      
        $user=$this->getUser()->getId();
        $recru=$this->getDoctrine()->getRepository(Recruteur::class)->findOneBy(array('User' => $user));
        $recc=$recru->getId();
        $off=$this->getDoctrine()->getRepository(OffreEmploi::class)->findBy(array('recruteur' => $recc));
        $rec=$this->getDoctrine()->getRepository(Recruteur::class)->findAll();
        $can=$this->getDoctrine()->getRepository(Candidat::class)->findAll();
        $offre=$this->getDoctrine()->getRepository(OffreEmploi::class)->findAll();
        $dql=$repository->findBy(array('recruteur'=>$recc));
        $pagination = $paginator->paginate(
            $off, /* query NOT result */
            $request->query->getInt('page', 1), /*page number*/
            4 /*limit per page*/
        );
        
        
        return $this->render('recruteur/listJob.html.twig', array(
        
            "off"=>$off,
            "rec"=>$rec,
            "can"=>$can,
            "list"=>$pagination,
           
        ));
       // return $this->redirectToRoute('list_post');

    }
     /**
     * @Route("/listCondidature/{id}", name="listCondidature")
     */
    public function listCondidatureAction(Request $request,OffreEmploiRepository $repository,$id)
    {
       
      
        $Off= $this->getDoctrine()->getRepository(OffreEmploi::class)->find($id);
        $recc=$Off->getId();
        $candidature=$this->getDoctrine()->getRepository(Candidature::class)->findBy(array('job' => $recc));
        $rec=$this->getDoctrine()->getRepository(Recruteur::class)->findAll();
        $can=$this->getDoctrine()->getRepository(Candidat::class)->findAll();


        $data = [];
 
        foreach ($candidature as $candidatures) {
           $data[] = [
            'id' => $candidatures->getId(),
            'candidat' =>[
                'id'=>$candidatures->getCandidat()->getId(),
                'CV'=>[
                    $candidatures->getCandidat()->getCV()->getId(),
                    ],
                'User'=>
                [$candidatures->getCandidat()->getUser()->getId(),
                ],

            ] ,
            'cv' => $candidatures->getCv(),
            'job' => [
              'id'=>  $candidatures->getJob()->getId(),
              'description'=>$candidatures->getJob()->getDescription(),
              'dateOffre'=>$candidatures->getJob()->getDateOffre(),
              'recruteur'=>[
                'id' =>  $candidatures->getJob()->getRecruteur()->getId(),
                'email'=> $candidatures->getJob()->getRecruteur()->getEmail(),
                'pays'=> $candidatures->getJob()->getRecruteur()->getPays(),
                'photo'=> $candidatures->getJob()->getRecruteur()->getPhoto(),
                'ville'=> $candidatures->getJob()->getRecruteur()->getVille(),
                'diplome'=> $candidatures->getJob()->getRecruteur()->getDiplome(),
                'langue_preferee'=> $candidatures->getJob()->getRecruteur()->getLanguePreferee(),
                'OffreEmploi'=>  $candidatures->getJob()->getRecruteur()->getOffreEmploi(),
               'User'=>
                    ['id'=>  $candidatures->getJob()->getRecruteur()->getUser()->getId(),
                    ],
              
              ],
              'titre'=>$candidatures->getJob()->getTitre(),
              'categorie'=>$candidatures->getJob()->getCategorie(),
              'salaire'=>$candidatures->getJob()->getSalaire(),
              'type'=>[
                'id'=>  $candidatures->getJob()->getType()->getId(),
                'Type'=>  $candidatures->getJob()->getType()->getType(),

              ],
              'DateFinOffre'=>$candidatures->getJob()->getDateFinOffre(),
              'blocked'=>$candidatures->getJob()->getBlocked(),
              'accepted'=>$candidatures->getJob()->getAccepted(),
              'blocked'=>$candidatures->getJob()->getDateFinOffre(),
              'blocked'=>$candidatures->getJob()->getDateFinOffre(),
              'blocked'=>$candidatures->getJob()->getDateFinOffre(),

            ],
            'createdAt' => $candidatures->getCreatedAt(),
            'etat' => $candidatures->getEtat(),
           
            
          
           ];
        }
 
         $jsonc = json_encode($data);
        return new Response($jsonc);
    }
    /**
     * @Route("/addProfilerec", name="addpofile")
     */
    public function addProfileRec(Request $request){
        
        $Rec= new Recruteur();
        
        $em = $this->getDoctrine()->getManager();
        
        $form= $this->createForm(RecruteurType ::class, $Rec );
        $form->handleRequest($request);
        $rec=$this->getDoctrine()->getRepository(Recruteur::class)->findAll();
        $can=$this->getDoctrine()->getRepository(Candidat::class)->findAll();
        $email=$this->getUser()->getEmail();
       
       

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
           $Rec->setEmail($email);
            $Rec->setPhoto($filename);   
            $Rec->setUser($this->getUser());
            $em->persist($Rec);
            $em->flush();
            $ex=$this->getUser()->setExist(true);
            $em->persist($ex);
            $em->flush();
            $this->addFlash('info', 'Created Successfully !');
            return $this->redirectToRoute('listposthome');
        }
        
        return $this->render('Recruteur/AddProfileRec.html.twig', [
            'Rec' => $form->createView(),
            "rec"=>$rec,
            "can"=>$can,
        ]);
        
    }
     /**
     * @Route("/detailedRec/{id}", name="detailedRec")
     */
    public function showdetailedAction($id)
    {
        
        $Rec= $this->getDoctrine()->getRepository(Recruteur::class)->find($id);
        $rec=$this->getDoctrine()->getRepository(Recruteur::class)->findAll();
        $can=$this->getDoctrine()->getRepository(Candidat::class)->findAll();

 
       
           $data = [
            'id' => $Rec->getId(),
            'email'=> $Rec->getEmail(),
            'pays'=> $Rec->getPays(),
            'photo'=> $Rec->getPhoto(),
            'ville'=> $Rec->getVille(),
            'diplome'=> $Rec->getDiplome(),
            'langue_preferee'=> $Rec->getLanguePreferee(),
      
           'User'=>
                ['id'=>  $Rec->getUser()->getId(),
                ],
           ];
   
 
         $jsonc = json_encode($data);
        return new Response($jsonc);
       
      
    }
     /**
     * @Route("/editProfileRec/{id}", name="editProfileRec")
     */
    public function editProfileAction(Request $request , $id){
        $em=$this->getDoctrine()->getManager();
        $Rec= $em->getRepository(Recruteur::class)->find($id);
        $rec=$this->getDoctrine()->getRepository(Recruteur::class)->findAll();
        $can=$this->getDoctrine()->getRepository(Candidat::class)->findAll();
        $iduser=$Rec->getUser()->getId();
        $user=$em->getRepository(User::class)->find($iduser);
        $form=$this->createForm(RecruteurType::class,$Rec);
        $form->handleRequest($request);
        $form1=$this->createForm(UserType::class,$user);
        $form1->handleRequest($request);
       
        $email=$form1->get('email')->getViewData();
        if($form->isSubmitted()){
            $file = $Rec->getPhoto();
            $filename= md5(uniqid()) . '.' . $file->guessExtension();
            $file->move($this->getParameter('images_directory'), $filename);
            $Rec->setPhoto($filename);
            $Rec->setEmail($email);
           
            $em->persist($Rec);
            $em->flush();
            $em->persist($user);
            $em->flush();
            //return $this->redirectToRoute('detailed'{$id});

        }
        return $this->render('Recruteur/editProfileRec.html.twig', array(
            "user"=>$form1->createView(),
            "Rec"=> $form->createView(),
            "rec"=>$rec,
            "can"=>$can,
        ));
 
    }
     /**
     * IsGranted("ROLE_RECRUTEUR)
     * @Route("/editPassRec/{id}", name="editPassRec")
     */
    public function editPassRecAction(Request $request , $id,UserPasswordEncoderInterface $userPasswordEncoder){
        
        $em=$this->getDoctrine()->getManager();
        $candidat=$em->getRepository(Recruteur::class)->find($id);
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
           
      
        }
        return $this->render('Candidat/editPass.html.twig', array(
            "user"=>$form1->createView(),
            "rec"=>$rec,
            "can"=>$can,
          
        ));

    }
     /**
     * @Route("/addFormation/{id}", name="addFormation")
     */
    public function addFormationAction(Request $request,$id){
        
        $Formation= new OffreFormation();
        $recruteur= $this->getDoctrine()->getRepository(Recruteur::class)->find($id);
        $em = $this->getDoctrine()->getManager();
        $recruteur->setUser($this->getUser());
        $rec=$this->getDoctrine()->getRepository(Recruteur::class)->findAll();
        $can=$this->getDoctrine()->getRepository(Candidat::class)->findAll();
        $form= $this->createForm(FormationType ::class, $Formation );
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
           
            $Formation->setRecruteur($recruteur);
            $em->persist($Formation);
            $em->flush();
            
         
        }
        
        return $this->render('recruteur/addFormation.html.twig', [
            'Formation' => $form->createView(),
            "rec"=>$rec,
            "can"=>$can,
        ]);
        
    }
     /**
     * @Route("/detailedFormation/{id}", name="detailedFormation")
     */
    public function detailedFormationAction($id)
    {
        
        $Formation= $this->getDoctrine()->getRepository(OffreFormation::class)->find($id);
        $rec=$this->getDoctrine()->getRepository(Recruteur::class)->findAll();
        $can=$this->getDoctrine()->getRepository(Candidat::class)->findAll();
        
      
       
        return $this->render('Recruteur/detailedFormation.html.twig',[
            
            'Formation'=>$Formation,
            "rec"=>$rec,
            "can"=>$can,
        ]   
        );
    }
     /**
     * @Route("/editFormation/{id}", name="editFormation")
     */
    public function editFormationAction(Request $request , $id){

        $Formation= $this->getDoctrine()->getRepository(OffreFormation::class)->find($id);
        
        $form=$this->createForm(FormationType::class,$Formation);
        $form->handleRequest($request);
        $rec=$this->getDoctrine()->getRepository(Recruteur::class)->findAll();
        $can=$this->getDoctrine()->getRepository(Candidat::class)->findAll();
       
        if($form->isSubmitted()){
          
            $em= $this->getDoctrine()->getManager();
            $em->persist($Formation);
            $em->flush();
            //return $this->redirectToRoute('detailed'{$id});

        }
        return $this->render('Recruteur/editFormation.html.twig', array(
            
            "Formation"=> $form->createView(),
            "rec"=>$rec,
            "can"=>$can,
        ));

    }
     /**
     * @Route("/listFormation", name="listFormation")
     * @IsGranted("ROLE_RECRUTEUR")
     */
    public function listFormationAction(Request $request)
    {
       
        $em=$this->getDoctrine()->getManager();
        $Form=$em->getRepository(OffreFormation::class)->findAll();
        $rec=$this->getDoctrine()->getRepository(Recruteur::class)->findAll();
        $can=$this->getDoctrine()->getRepository(Candidat::class)->findAll();
        return $this->render('recruteur/listFormation.html.twig', array(
            "Form" =>$Form,
            "rec"=>$rec,
            "can"=>$can,
        ));
       //return $this->redirectToRoute('list_post');

    }
    
    /**
     * 
     * @Route("/addquestion", name="addquestion")
     */
    public function addQuestion(Request $request,NormalizerInterface $normalizer,ForumRepository $Forum){
        // $this->denyAccessUnlessGranted('ROLE_CANDIDAT');
        
        $question= new Forum();
        $em = $this->getDoctrine()->getManager();
        $recruteur= $this->getDoctrine()->getRepository(Recruteur::class)->findBy(array('User'=>$request->get('id')));
       
        

           
            $question->setRecruteur($recruteur["0"]);
            $question->setQuestion($request->get('question'));
          
       
            $Forum->add($question);

            $jsonContent = $normalizer->normalize($question,'json',['Forum'=>'post:read']);
            return new Response(json_encode($jsonContent));
        
    }
       /**
     * @Route("/listQuestion", name="listQuestion")
     * 
     */
 
        public function listQuestionAction(EntityManagerInterface $em, PaginatorInterface $paginator, Request $request,ForumRepository $repository)
{
    $Questions   =  $repository->findAll();
 
    $rec=$this->getDoctrine()->getRepository(Recruteur::class)->findAll();
    $can=$this->getDoctrine()->getRepository(Candidat::class)->findAll();
 
    $data = [];
 
    foreach ($Questions as $Question) {
       $data[] = [
        'id' => $Question->getId(),
        'Question'=>$Question->getQuestion(),
        'recruteur'=>[
            'id' => $Question->getRecruteur()->getId(),
            'email'=> $Question->getRecruteur()->getEmail(),
            'pays'=> $Question->getRecruteur()->getPays(),
            'photo'=> $Question->getRecruteur()->getPhoto(),
            'ville'=> $Question->getRecruteur()->getVille(),
            'diplome'=> $Question->getRecruteur()->getDiplome(),
            'langue_preferee'=> $Question->getRecruteur()->getLanguePreferee(),
      
           'User'=>
                ['id'=>  $Question->getRecruteur()->getUser()->getId(),
                ],

        ]
      
       ];
    }

     $jsonc = json_encode($data);
    return new Response($jsonc);
}
    /**
     * @Route("/Myquestion", name="MyQuestion")
    
     */
 
    public function MyQuestionAction(EntityManagerInterface $em, PaginatorInterface $paginator, Request $request,ForumRepository $repository)
    {
         
        $recru=$this->getDoctrine()->getRepository(Recruteur::class)->findOneBy(array('User' => $request->get('id')));
      
       
        
        $Questions   =  $repository->findBy(array('recruteur' => $recru));
       
  
      $data = [];
 
    foreach ($Questions as $Question) {
       $data[] = [
        'id' => $Question->getId(),
        'Question'=>$Question->getQuestion(),
        'recruteur'=>[
            'id' => $Question->getRecruteur()->getId(),
            'email'=> $Question->getRecruteur()->getEmail(),
            'pays'=> $Question->getRecruteur()->getPays(),
            'photo'=> $Question->getRecruteur()->getPhoto(),
            'ville'=> $Question->getRecruteur()->getVille(),
            'diplome'=> $Question->getRecruteur()->getDiplome(),
            'langue_preferee'=> $Question->getRecruteur()->getLanguePreferee(),
      
           'User'=>
                ['id'=>  $Question->getRecruteur()->getUser()->getId(),
                ],

        ]
      
       ];
    }

     $jsonc = json_encode($data);
    return new Response($jsonc);
    }
      /**
     * @Route("/detailedquestion/{id}", name="detailedquestion")
     */
    public function detailedQuestionAction($id, Request $request)
    {
        
        $question= $this->getDoctrine()->getRepository(Forum::class)->find($id);
        
        $commentaire= new Commentaire;
        $commentForm = $this->createForm(CommentaireType ::class, $commentaire );
        $commentForm->handleRequest($request);
        $user = $this->get('security.token_storage')->getToken()->getUser();
        $rec=$this->getDoctrine()->getRepository(Recruteur::class)->findAll();
        $can=$this->getDoctrine()->getRepository(Candidat::class)->findAll();
        $securityContext = $this->container->get('security.authorization_checker');
       if($securityContext->isGranted('IS_AUTHENTICATED_REMEMBERED') ){
     
            if($user->getRoles()==["ROLE_RECRUTEUR"]){
                $candidat= $this->getDoctrine()->getRepository(Recruteur::class)->findOneBy(['User' => $user]);
                $candidat->setUser($this->getUser()); 
            }else if($user->getRoles()==["ROLE_CANDIDAT"]){
            $candidat= $this->getDoctrine()->getRepository(Candidat::class)->findOneBy(['User' => $user]);
            $candidat->setUser($this->getUser());
            }
        
            if($commentForm->isSubmitted() && $commentForm->isValid())
            {
                $parentid=$commentForm->get('parentid')->getData();
            
                $commentaire->setCreatedAt(new \DateTime('now'));
                $commentaire->setCandidat($candidat);
                $commentaire->setForum($question);
                $em = $this->getDoctrine()->getManager();
                if($parentid != null){
                $parent=$em->getRepository(Commentaire::class)->find($parentid);
                }
                $commentaire->setParent($parent ?? null);



                $em->persist($commentaire);
                $em->flush();
            
            }
        }
       
        return $this->render('Recruteur/detailedquestion.html.twig',[
            
            'question'=>$question,
            'commentForm'=>$commentForm->createView(),
            "rec"=>$rec,
            "can"=>$can,
        ]   
        );
    }
    /**
     * @Route("/editQuestion", name="editQuestion")
     */
    public function editQuestionAction(Request $request ,NormalizerInterface $normalizer){

        $question= $this->getDoctrine()->getRepository(Forum::class)->find($request->get('id'));
        
       
            $em= $this->getDoctrine()->getManager();
            $question->setQuestion($request->get('question'));

            $em->flush();
            $normalizer = new ObjectNormalizer();
            $normalizer->setCircularReferenceLimit(1);
            $normalizer->setCircularReferenceHandler(function ($question) {
                return $question->getId();
            });
            $encoders = [new JsonEncoder()];
            $normalizers = array($normalizer);
            $serializer = new Serializer($normalizers,$encoders);
            $formatted = $serializer->normalize($question);
            return new JsonResponse($formatted);
  
      

    }
      /**
     * @Route("/deleteCommentaire", name="deleteCommentaire")
     */
    public function deleteCommentAction(Request $request)
    {
        
       
        $em= $this->getDoctrine()->getManager();
        $comment=$em->getRepository(Commentaire::class)->find($request->get('id'));
        $em->remove($comment);
        $em->flush();
        

        $jsonContent = $normalizer->normalize($comment,'json',['Commentaire'=>'post:read']);
        return new Response(json_encode($jsonContent));

        
    }
     
  
        
}
