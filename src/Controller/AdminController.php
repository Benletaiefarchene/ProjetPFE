<?php

namespace App\Controller;

use App\Entity\News;
use App\Entity\User;
use App\Form\NewsType;
use App\Entity\Candidat;
use App\Entity\Recruteur;
use App\Form\BlockedType;
use App\Form\MessageType;
use App\Form\AddAdminType;
use App\Entity\OffreEmploi;
use App\Form\FormationType;
use App\Entity\Administrateur;
use App\Entity\OffreFormation;
use App\Security\UserAuthenticator;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\Security\Guard\GuardAuthenticatorHandler;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
/**
 * @Route("/admin", name="admin_")
 */
class AdminController extends AbstractController
{
    /**
     * IsGranted("ROLE_ADMIN)
     * @Route("/", name="count")
     */
    public function countoffre(Request $request): Response
        {
            $em=$this->getDoctrine()->getManager();
            $offre=$em->getRepository(OffreEmploi::class)->countoffre();
            $offreAcc=$em->getRepository(OffreEmploi::class)->countoffreAcc();
            $user=$em->getRepository(User::class)->countuser();
            $userblock=$em->getRepository(User::class)->countuserBlocekd();
            $recruteur=$em->getRepository(Recruteur::class)->countRec();
            $candidat=$em->getRepository(Candidat::class)->countCan();
            $twig = new \DateTime('now');

            $jan=$em->getRepository(OffreEmploi::class)->countNumberOffrePerMonth("01");
            $jan=$jan['1'];
            $fev=$em->getRepository(OffreEmploi::class)->countNumberOffrePerMonth("02");
            $fev=$fev['1'];
            $mar=$em->getRepository(OffreEmploi::class)->countNumberOffrePerMonth("03");
            $mar=$mar['1'];
            $avr=$em->getRepository(OffreEmploi::class)->countNumberOffrePerMonth("04");
            $avr=$avr['1'];
            $mai=$em->getRepository(OffreEmploi::class)->countNumberOffrePerMonth("05");
            $mai=$mai['1'];
            $juin=$em->getRepository(OffreEmploi::class)->countNumberOffrePerMonth("06");
            $juin=$juin['1'];
            $juil=$em->getRepository(OffreEmploi::class)->countNumberOffrePerMonth("07");
            $juil=$juil['1'];
            $aou=$em->getRepository(OffreEmploi::class)->countNumberOffrePerMonth("08");
            $aou=$aou['1'];
            $sep=$em->getRepository(OffreEmploi::class)->countNumberOffrePerMonth("09");
            $sep=$sep['1'];
            $oct=$em->getRepository(OffreEmploi::class)->countNumberOffrePerMonth("10");
            $oct=$oct['1'];
            $nouv=$em->getRepository(OffreEmploi::class)->countNumberOffrePerMonth("11");
            $nouv=$nouv['1'];
            $dec=$em->getRepository(OffreEmploi::class)->countNumberOffrePerMonth("12");
            $dec=$dec['1'];
            // affichage des utilisateurs inscrit par mois
            $jan1=$em->getRepository(User::class)->countNumberUsersPerMonth("01");
            $jan1=$jan1['1'];
            $fev1=$em->getRepository(User::class)->countNumberUsersPerMonth("02");
            $fev1=$fev1['1'];
            $mar1=$em->getRepository(User::class)->countNumberUsersPerMonth("03");
            $mar1=$mar1['1'];
            $avr1=$em->getRepository(User::class)->countNumberUsersPerMonth("04");
            $avr1=$avr1['1'];
            $mai1=$em->getRepository(User::class)->countNumberUsersPerMonth("05");
            $mai1=$mai1['1'];
            $juin1=$em->getRepository(User::class)->countNumberUsersPerMonth("06");
            $juin1=$juin1['1'];
            $juil1=$em->getRepository(User::class)->countNumberUsersPerMonth("07");
            $juil1=$juil1['1'];
            $aou1=$em->getRepository(User::class)->countNumberUsersPerMonth("08");
            $aou1=$aou1['1'];
            $sep1=$em->getRepository(User::class)->countNumberUsersPerMonth("09");
            $sep1=$sep1['1'];
            $oct1=$em->getRepository(User::class)->countNumberUsersPerMonth("10");
            $oct1=$oct1['1'];
            $nouv1=$em->getRepository(User::class)->countNumberUsersPerMonth("11");
            $nouv1=$nouv1['1'];
            $dec1=$em->getRepository(User::class)->countNumberUsersPerMonth("12");
            $dec1=$dec1['1'];

               
          
            return $this->render('Admin/home.html.twig', array(
                "offre" =>$offre,
                "offreAcc"=>$offreAcc,
                "user" =>$user,
                "userblock"=>$userblock,
                "recruteur" =>$recruteur,
                "candidat" =>$candidat,
                "da"=>$twig,
                "jan"=>$jan,
                "fev"=>$fev,
                "mar"=>$mar,
                "avr"=>$avr,
                "mai"=>$mai,
                "juin"=>$juin,
                "juil"=>$juil,
                "aou"=>$aou,
                "sep"=>$sep,
                "oct"=>$oct,
                "nouv"=>$nouv,
                "dec"=>$dec,
                "jan1"=>$jan1,
                "fev1"=>$fev1,
                "mar1"=>$mar1,
                "avr1"=>$avr1,
                "mai1"=>$mai1,
                "juin1"=>$juin1,
                "juil1"=>$juil1,
                "aou1"=>$aou1,
                "sep1"=>$sep1,
                "oct1"=>$oct1,
                "nouv1"=>$nouv1,
                "dec1"=>$dec1,
             
            
            ));
        }
    /**
     * IsGranted("ROLE_ADMIN)
     * @Route("/listUser", name="listUser")
     */
    public function listUser(Request $request): Response
        {
            $em=$this->getDoctrine()->getManager();
            $user=$em->getRepository(User::class)->findAdmin();
        
        
            return $this->render('Admin/listUser.html.twig', array(
                "user" =>$user,
             
            ));
       
    }
    /**
     * IsGranted("ROLE_ADMIN)
     * @Route("/addAdmin", name="addAdmin")
     */
    public function addAdmin(Request $request, UserPasswordEncoderInterface $userPasswordEncoder, GuardAuthenticatorHandler $guardHandler, UserAuthenticator $authenticator, EntityManagerInterface $entityManager): Response
        {
        $user = new User();
        $admin= new Administrateur();
        $form = $this->createForm(AddAdminType::class, $user);
        $form->handleRequest($request);
       
        
        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $user->setPassword(
            $userPasswordEncoder->encodePassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );
            
            
            $role =["ROLE_ADMIN"];
           
            $user->setRoles($role);

            
           
             $ex = true;
             $user->setExist($ex);
             $user->setCreatedAt(new \DateTime());
            $entityManager->persist($user);
            $entityManager->flush();
           
            $admin->setUser($user);
            $entityManager->persist($admin);
            $entityManager->flush();
            
        }
        return $this->render('admin/addAdmin.html.twig', [
            'addAdmin' => $form->createView(),
        ]);
    }
     /**
     * IsGranted("ROLE_ADMIN)
     * @Route("/editUser/{id}", name="editUser")
     */
    public function editUserAction(Request $request , $id){
        
        $em=$this->getDoctrine()->getManager();
        $user=$em->getRepository(User::class)->find($id);
        $form=$this->createForm(AddAdminType::class,$user);
        $form->handleRequest($request);
       
        
       
        if($form->isSubmitted() && $form->isSubmitted() ){
         
         
            $em= $this->getDoctrine()->getManager();
            
            $em->persist($user);
            $em->flush();
           // return $this->redirectToRoute('detailed');

        }
        return $this->render('Admin/editAdmin.html.twig', array(
            "user"=>$form->createView(),
            
        ));

    }
      /**
     * IsGranted("ROLE_ADMIN)
     * @Route("/listBlockedUser", name="listBlockedUser")
     */
    public function BlockedUser(Request $request,PaginatorInterface $paginator)
        {
            $em=$this->getDoctrine()->getManager();
            $query=$em->getRepository(User::class)->findAll();
          
            $user = $paginator->paginate(
                $query, /* query NOT result */
                $request->query->getInt('page', 1), /*page number*/
                5 /*limit per page*/
            );
          
            
            return $this->render('Admin/BlockedUser.html.twig', array(
                "user" =>$user,
               
              
            ));
      
    }
     /**
     * IsGranted("ROLE_ADMIN)
     * @Route("/blockedUser/{id}", name="BlockedUser")
     */
    public function blockedUserAction($id,EntityManagerInterface $entityManager)
    {
       
        $user= $this->getDoctrine()->getRepository(User::class)->find($id);

        if($user->getBlocked()==true){
        $block = false;
        $user->setBlocked($block);
        $entityManager->persist($user);
        $entityManager->flush();
         }else   {
        $block = true;
        $user->setBlocked($block);
        $entityManager->persist($user);
        $entityManager->flush();
        }
      
       return $this->redirectToRoute('admin_listBlockedUser');
        return $this->render('Admin/BlockedUser.html.twig',[
            'user'=> $user,
        ] 
            
           
        );
    }   
      /**
     * IsGranted("ROLE_ADMIN)
     * @Route("/listBlockedOffre", name="listBlockedOffre")
     */
    public function BlockedOffre(Request $request,PaginatorInterface $paginator): Response
        {
            $em=$this->getDoctrine()->getManager();
            $query=$em->getRepository(OffreEmploi::class)->findAll();

            $offre = $paginator->paginate(
                $query, /* query NOT result */
                $request->query->getInt('page', 1), /*page number*/
                5 /*limit per page*/
            );
           
            return $this->render('Admin/BlockedOffre.html.twig', array(
                "offre" =>$offre,
              
            ));
      
    }
      /**
     * IsGranted("ROLE_ADMIN)
     * @Route("/blockedOffre/{id}", name="blockedOffre")
     */
    public function blockedOffreAction($id,EntityManagerInterface $entityManager)
    {
       
        $offre= $this->getDoctrine()->getRepository(OffreEmploi::class)->find($id);

        if($offre->getBlocked()==true){
        $block = false;
        $offre->setBlocked($block);
        $entityManager->persist($offre);
        $entityManager->flush();
         }else   {
        $block = true;
        $offre->setBlocked($block);
        $entityManager->persist($offre);
        $entityManager->flush();
        }
      
       return $this->redirectToRoute('admin_listBlockedOffre');
        return $this->render('Admin/BlockedOffre.html.twig',[
            'offre'=> $offre,
        ] 
            
           
        );
    }   
       /**
     * IsGranted("ROLE_ADMIN)
     * @Route("/listAcceptedOffre", name="listAcceptedOffre")
     */
    public function AcceptedOffre(Request $request,PaginatorInterface $paginator): Response
        {
            $em=$this->getDoctrine()->getManager();
            $query=$em->getRepository(OffreEmploi::class)->findOffre();

            $offre = $paginator->paginate(
                $query, /* query NOT result */
                $request->query->getInt('page', 1), /*page number*/
                5 /*limit per page*/
            );
           
            return $this->render('Admin/AcceptedOffre.html.twig', array(
                "offre" =>$offre,
              
            ));
      
    }
      /**
     * IsGranted("ROLE_ADMIN)
     * @Route("/AcceptedOffre/{id}", name="AcceptedOffre")
     */
    public function AcceptedOffreAction($id,EntityManagerInterface $entityManager)
    {
       
        $offre= $this->getDoctrine()->getRepository(OffreEmploi::class)->find($id);

    
        $accept = true;
        $offre->setMessage(null);
        $offre->setAccepted($accept);
        $entityManager->persist($offre);
        $entityManager->flush();
      
      
       return $this->redirectToRoute('admin_listAcceptedOffre');
        return $this->render('Admin/AcceptedOffre.html.twig',[
            'offre'=> $offre,
        ] 
            
           
        );
    }   
       /**
     * IsGranted("ROLE_ADMIN)
     * @Route("/Message/{id}", name="Message")
     */
    public function MessageUnacceptedAction($id,EntityManagerInterface $entityManager,Request $request)
    {
       
        $offre= $this->getDoctrine()->getRepository(OffreEmploi::class)->find($id);
        $msg = $this->createForm(MessageType::class,$offre);
        $msg->handleRequest($request);
        if($msg->isSubmitted() && $msg->isValid()){
            $accept = false;
            $offre->setAccepted($accept);
          
            $entityManager->persist($offre);
            $entityManager->flush();

            return $this->redirectToRoute('admin_listAcceptedOffre');

        }
        
      
      
        return $this->render('Admin/Message.html.twig',[
            'msg'=> $msg->createView(),
        ] 
            
           
        );
    }   
      /**
     * @Route("/addNews", name="addNews")
     */
    public function addNews(Request $request){
        $em=$this->getDoctrine()->getManager();
        $news= new News();
        $user=$this->getUser()->getId();
        $admin= $this->getDoctrine()->getRepository(Administrateur::class)->findOneBy(['User' => $user]);
      
        $form= $this->createForm(NewsType ::class, $news );
        $form->handleRequest($request);
        

        if($form->isSubmitted() && $form->isValid())
        {
            $file= $news->getPhoto();
            $filename = md5(uniqid()).'.'.$file->guessExtension();
            try{
                $file->move(
                    $this->getParameter('images_directory'),
                    $filename
                );
            }catch(FileException $e){

            }
            $news->setPhoto($filename);
            $news->setAdmin($admin);
            $em->persist($news);
            $em->flush();

       
         
        }
        
        return $this->render('Admin/addnews.html.twig',[
            "news"=>$form->createView(),
        ] );
        
    }   
       /**
     * @Route("/ListNews", name="ListNews")
     */
    public function ListNewsAction(Request $request)
    {
        $em=$this->getDoctrine()->getManager();
        $news=$em->getRepository(News::class)->findAll();
       // $posts=$em->getRepository(OffreEmploi::class)->findBy(array('titre'=> 'mobile'));
        
        
        return $this->render('Admin/ListNews.html.twig', array(
            "news" =>$news
        ));
       // return $this->redirectToRoute('list_post');

    }
    /**
     * IsGranted("ROLE_ADMIN)
     * @Route("/editNews/{id}", name="editNews")
     */
    public function editNewsAction(Request $request , $id){
        
        $em=$this->getDoctrine()->getManager();
        $News=$em->getRepository(News::class)->find($id);
        $form=$this->createForm(NewsType::class,$News);
        $form->handleRequest($request);
       
        
       
        if($form->isSubmitted() && $form->isSubmitted() ){
         
         
            $em= $this->getDoctrine()->getManager();
            
            $em->persist($News);
            $em->flush();
           // return $this->redirectToRoute('detailed');

        }
        return $this->render('Admin/editNews.html.twig', array(
            "news"=>$form->createView(),
            
        ));

    }     
       /**
     * @Route("/detailedAdmin/{id}", name="detailedAdmin")
     */
    public function showdetailedAction($id)
    {
        
        $admin= $this->getDoctrine()->getRepository(Administrateur::class)->find($id);
        
       
        return $this->render('Admin/detailedAdmin.html.twig',[
            'admin'=> $admin,
         
        ]   
        );
    }
     /**
     * @Route("/editProfileAdmin/{id}", name="editProfileAdmin")
     */
    public function editProfileAction(Request $request , $id){
        $em=$this->getDoctrine()->getManager();
        $admin= $em->getRepository(Administrateur::class)->find($id);
        
        $iduser=$admin->getUser()->getId();
        $user=$em->getRepository(User::class)->find($iduser);
        $form=$this->createForm(AdminType::class,$admin);
        $form->handleRequest($request);
        $form1=$this->createForm(UserType::class,$user);
        $form1->handleRequest($request);
       
        $email=$form1->get('email')->getViewData();
        if($form->isSubmitted()){
            $file = $Rec->getPhoto();
            $filename= md5(uniqid()) . '.' . $file->guessExtension();
            $file->move($this->getParameter('images_directory'), $filename);
            $Rec->setPhoto($filename);
            $Rec->setEamil($email);
           
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
           
      
        }
        return $this->render('Candidat/editPass.html.twig', array(
            "user"=>$form1->createView(),
          
        ));

    } 
    
      /**
     * @Route("/addFormation", name="addFormation")
     */
    public function addFormationAction(Request $request){
        
        $Formation= new OffreFormation();
    
        $em = $this->getDoctrine()->getManager();
  
        $rec=$this->getDoctrine()->getRepository(Recruteur::class)->findAll();
        $can=$this->getDoctrine()->getRepository(Candidat::class)->findAll();
        $form= $this->createForm(FormationType ::class, $Formation );
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        { 
            $file= $Formation->getFolder();
         
            $filename = $file->getClientOriginalName();
            try{
                $file->move(
                    $this->getParameter('images_directory'),
                    $filename
                );
            }catch(FileException $e){

            }
            $Formation->setFolder($filename);
        
            $em->persist($Formation);
            $em->flush();
            
         
        }
        
        return $this->render('Admin/addFormation.html.twig', [
            'Formation' => $form->createView(),
            "rec"=>$rec,
            "can"=>$can,
        ]);
        
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

    
  
    
}


