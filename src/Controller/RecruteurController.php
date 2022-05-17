<?php

namespace App\Controller;
use App\Entity\Type;
use App\Entity\User;
use App\Entity\Forum;
use App\Form\UserType;
use App\Form\ForumType;
use App\Data\SearchData;
use App\Entity\Candidat;
use App\Form\SearchForm;
use App\Entity\Recruteur;
use App\Form\AddPostType;
use App\Form\UserPassType;
use App\Entity\Candidature;
use App\Entity\Commentaire;
use App\Entity\OffreEmploi;
use App\Form\FormationType;
use App\Form\RecruteurType;
use App\Form\TypeOffreType;
use App\Form\CandidatureType;
use App\Form\CommentaireType;
use App\Entity\OffreFormation;
use App\Repository\ForumRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\OffreEmploiRepository;
use Symfony\Component\Form\FormInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Common\Collections\ArrayCollection;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

 
class RecruteurController extends AbstractController
{
    /**
     * @Route("/addpost/{id}", name="addPost")
     */
    public function addPost(Request $request,$id){
        
        $offre= new OffreEmploi();
        $recruteur= $this->getDoctrine()->getRepository(Recruteur::class)->find($id);
        $em = $this->getDoctrine()->getManager();
        $recruteur->setUser($this->getUser());
   
        $form= $this->createForm(AddPostType ::class, $offre );
        $form->handleRequest($request);
        $types= $this->getDoctrine()->getRepository(Type::class)->findAll();

        if($form->isSubmitted() && $form->isValid())
        {
          
            $type=$request->request->get('co');
            $offre->setType($types[$type-1]);
           $offre->setDateOffre(new \DateTime('now')); 
          
            $offre->setRecruteur($recruteur);
            $em->persist($offre);
            $em->flush();

        return $this->redirectToRoute('listposthome');
         
        }
        
        return $this->render('recruteur/addpost.html.twig', [
            'Offre' => $form->createView(),
            'types' => $types,
        ]);
        
    }
     /**
     * @Route("/detailedOffre/{id}", name="detailedOff")
     */
    public function detailedOffreAction(Request $request,$id)
    {
        
        $Off= $this->getDoctrine()->getRepository(OffreEmploi::class)->find($id);
     


        return $this->render('Recruteur/detailedOffre.html.twig',[
           
            'off'=>$Off
        ]   
        );
    }
      /**
     * @Route("/ApplyOffre/{id}", name="ApplyOffre")
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
       $can=$candidat["0"];
       $date=new \DateTime('now');
    
        if($form->isSubmitted()&& $form->isValid()){
            $file= $candidature->getCv();
            $filename = md5(uniqid()).'.'.$file->guessExtension();
            try{
                $file->move(
                    $this->getParameter('images_directory'),
                    $filename
                );
            }catch(FileException $e){

            }
            $candidature->setCv($filename);
            $candidature->setCandidat($can);
            $candidature->setCreatedAt($date);
            $candidature->setJob($Off);
            $em->persist($candidature);
            $em->flush();


        }



        return $this->render('Recruteur/apply.html.twig',[
            'Offre' => $form->createView(),
            'off'=>$Off
        ]   
        );
    }
     /**
     * @Route("/editPost/{id}", name="editPost")
     */
    public function editPostAction(Request $request , $id){

        $off= $this->getDoctrine()->getRepository(OffreEmploi::class)->find($id);
        
        $form=$this->createForm(AddPostType::class,$off);
        $form->handleRequest($request);
       
        if($form->isSubmitted()){
          
            $em= $this->getDoctrine()->getManager();
            $em->persist($off);
            $em->flush();
         //  return $this->redirectToRoute(path('recruteur_detailedOff','id'== $id));
           
        }
        return $this->render('Recruteur/editPost.html.twig', array(
            
            "off"=> $form->createView()
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
       $can=$this->getDoctrine()->getRepository(Candidat::class)->findAll();
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
     * @Route("/listOffre", name="listOffre")
     */
    public function listOffreAction(Request $request,OffreEmploiRepository $repository)
    {
       
      
        $user=$this->getUser()->getId();
        $rec=$this->getDoctrine()->getRepository(Recruteur::class)->findOneBy(array('User' => $user));
        $recc=$rec->getId();
       $off=$this->getDoctrine()->getRepository(OffreEmploi::class)->findBy(array('recruteur' => $recc));
      
        
        
        return $this->render('recruteur/listJob.html.twig', array(
        
            "off"=>$off,
           
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
        


        return $this->render('Recruteur/listCondidature.html.twig',[
            'can'=>$candidature,
            'off'=>$Off
        ]   
        );
       // return $this->redirectToRoute('list_post');

    }
    /**
     * @Route("/addProfilerec", name="addpofile")
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
            $ex=$this->getUser()->setExist(true);
            $em->persist($ex);
            $em->flush();
            $this->addFlash('info', 'Created Successfully !');
            return $this->redirectToRoute('listposthome');
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
     * @Route("/editProfileRec/{id}", name="editProfileRec")
     */
    public function editProfileAction(Request $request , $id){
        $em=$this->getDoctrine()->getManager();
        $Rec= $em->getRepository(Recruteur::class)->find($id);
        
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
            "Rec"=> $form->createView()
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
        ]);
        
    }
     /**
     * @Route("/detailedFormation/{id}", name="detailedFormation")
     */
    public function detailedFormationAction($id)
    {
        
        $Formation= $this->getDoctrine()->getRepository(OffreFormation::class)->find($id);
        
        
      
       
        return $this->render('Recruteur/detailedFormation.html.twig',[
            
            'Formation'=>$Formation
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
       
        if($form->isSubmitted()){
          
            $em= $this->getDoctrine()->getManager();
            $em->persist($Formation);
            $em->flush();
            //return $this->redirectToRoute('detailed'{$id});

        }
        return $this->render('Recruteur/editFormation.html.twig', array(
            
            "Formation"=> $form->createView()
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
        
        return $this->render('recruteur/listFormation.html.twig', array(
            "Form" =>$Form
        ));
       //return $this->redirectToRoute('list_post');

    }
    
    /**
     * 
     * @Route("/addquestion/{id}", name="addquestion")
     */
    public function addQuestion(Request $request,$id){
        // $this->denyAccessUnlessGranted('ROLE_CANDIDAT');
        
        $question= new Forum();
        $em = $this->getDoctrine()->getManager();
        $recruteur= $this->getDoctrine()->getRepository(Recruteur::class)->find($id);
        $form= $this->createForm(ForumType ::class, $question );
        $form->handleRequest($request);
        $recruteur->setUser($this->getUser());
      
        if($form->isSubmitted() && $form->isValid())
        {
           
            $question->setRecruteur($recruteur);
            $em->persist($question);
            $em->flush();
          
        }
        
        return $this->render('Recruteur/addquestion.html.twig', [
            'question' => $form->createView(),
        ]);
        
    }
       /**
     * @Route("/listQuestion", name="listQuestion")
     * @IsGranted("ROLE_RECRUTEUR")
     */
 
        public function listQuestionAction(EntityManagerInterface $em, PaginatorInterface $paginator, Request $request,ForumRepository $repository)
{
    $dql   =  $repository->findAll();
  

        $pagination = $paginator->paginate(
        $dql, /* query NOT result */
        $request->query->getInt('page', 1), /*page number*/
        6 /*limit per page*/
    );

    // parameters to template
    return $this->render('recruteur/listquestion.html.twig', ['Form' => $pagination]);
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
        $iduser= $user->getId();
        $candidat= $this->getDoctrine()->getRepository(Candidat::class)->findOneBy(['User' => $user]);
        $candidat->setUser($this->getUser());
     
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
      
       
        return $this->render('Recruteur/detailedquestion.html.twig',[
            
            'question'=>$question,
            'commentForm'=>$commentForm->createView(),
        ]   
        );
    }
    /**
     * @Route("/editQuestion/{id}", name="editQuestion")
     */
    public function editQuestionAction(Request $request , $id){

        $question= $this->getDoctrine()->getRepository(Forum::class)->find($id);
        
        $form=$this->createForm(ForumType::class,$question);
        $form->handleRequest($request);
       
        if($form->isSubmitted()){
          
            $em= $this->getDoctrine()->getManager();
            $em->persist($question);
            $em->flush();
         //  return $this->redirectToRoute(path('recruteur_detailedOff','id'== $id));
           
        }
        return $this->render('Recruteur/editQuestion.html.twig', array(
            
            "question"=> $form->createView()
        ));

    }
      /**
     * @Route("/deleteCommentaire/{id}", name="deleteCommentaire")
     */
    public function deleteCommentAction(Request $request,$id)
    {
        
       
        $em= $this->getDoctrine()->getManager();
        $comment=$em->getRepository(Commentaire::class)->find($id);
        $em->remove($comment);
        $em->flush();
        return $this->redirectToRoute('listQuestion');
    }
     
       
      
        
  
}
