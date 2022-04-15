<?php

namespace App\Controller;
use App\Entity\Type;
use App\Entity\User;
use App\Entity\Recruteur;
use App\Form\AddPostType;
use App\Entity\OffreEmploi;
use App\Form\FormationType;
use App\Form\RecruteurType;
use App\Form\TypeOffreType;
use App\Entity\OffreFormation;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Common\Collections\ArrayCollection;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
 /**
     * @Route("/recruteur", name="recruteur_")
     */
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
           $te= $offre->setDateOffre(new \DateTime('now')); 
          
            $offre->setRecruteur($recruteur);
            $em->persist($offre);
            $em->flush();

        return $this->redirectToRoute('recruteur_listpost');
         
        }
        
        return $this->render('recruteur/addpost.html.twig', [
            'Offre' => $form->createView(),
            'types' => $types,
        ]);
        
    }
     /**
     * @Route("/detailedOffre/{id}", name="detailedOff")
     */
    public function detailedOffreAction($id)
    {
        
        $Off= $this->getDoctrine()->getRepository(OffreEmploi::class)->find($id);
      
     //  dd($Off);
        return $this->render('Recruteur/detailedOffre.html.twig',[
           
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
     * @Route("/editProfileRec/{id}", name="editProfile")
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
}
