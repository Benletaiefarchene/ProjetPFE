<?php

namespace App\Controller;

use App\Entity\News;
use App\Entity\User;
use App\Form\NewsType;
use App\Entity\Candidat;
use App\Entity\Recruteur;
use App\Form\BlockedType;
use App\Form\AddAdminType;
use App\Entity\OffreEmploi;
use App\Entity\Administrateur;
use App\Security\UserAuthenticator;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
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
        
        
            return $this->render('Admin/home.html.twig', array(
                "offre" =>$offre,
                "offreAcc"=>$offreAcc,
                "user" =>$user,
                "userblock"=>$userblock,
                "recruteur" =>$recruteur,
                "candidat" =>$candidat,
             
            
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

        if($offre->getAccepted()==true){
        $accept = false;
        $offre->setAccepted($accept);
        $entityManager->persist($offre);
        $entityManager->flush();
         }else   {
        $accept = true;
        $offre->setAccepted($accept);
        $entityManager->persist($offre);
        $entityManager->flush();
        }
      
       return $this->redirectToRoute('admin_listAcceptedOffre');
        return $this->render('Admin/AcceptedOffre.html.twig',[
            'offre'=> $offre,
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
        
       
}


