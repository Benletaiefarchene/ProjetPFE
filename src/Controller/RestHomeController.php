<?php

namespace App\Controller;
use Dompdf\Options;

use App\Entity\News;
use App\Entity\Type;
use App\Data\SearchData;
use App\Entity\Candidat;
use App\Form\SearchForm;
use App\Entity\Recruteur;
use App\Service\T_HTML2PDF;
use App\Entity\Competance;
use App\Entity\Experience;
use App\Entity\OffreEmploi;
use App\Form\TypeOffreType;
use App\Service\PdfService;
use App\Repository\OffreEmploiRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
/**
     * @Route("/rest", name="rest_")
     */
class RestHomeController extends AbstractController
{
   /**
     * @Route("/home", name="homee")
     */
    public function Home(): Response
    {
        $em=$this->getDoctrine()->getManager();
        $news=$em->getRepository(News::class)->findAll();
        $rec=$this->getDoctrine()->getRepository(Recruteur::class)->findAll();
        $can=$this->getDoctrine()->getRepository(Candidat::class)->findAll();
       // $posts=$em->getRepository(OffreEmploi::class)->findBy(array('titre'=> 'mobile'));
        
       $data = [];
 
        foreach ($news as $new) {
           $data[] = [
            'id' => $new->getId(),
            'Titre'=>$new->getTitre(),
            'photo'=>$new->getPhoto(),
            'description'=>$new->getDescription(),
            'createdAt'=>$new->getCreatedAt(),

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
     * @Route("/listPosthome", name="listposthome")
     */
    public function listposteAction(Request $request,OffreEmploiRepository $repository)
    {
        
        
        $data = new SearchData();
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
     * @Route("/Actualites", name="Actualites")
     */
    public function ActualitespostAction(Request $request)
    {
        $em=$this->getDoctrine()->getManager();
        $news=$em->getRepository(News::class)->findAll();
        $rec=$this->getDoctrine()->getRepository(Recruteur::class)->findAll();
        $can=$this->getDoctrine()->getRepository(Candidat::class)->findAll();
          
        $data = [];
 
        foreach ($news as $new) {
           $data[] = [
            'id' => $new->getId(),
            'Titre'=>$new->getTitre(),
            'photo'=>$new->getPhoto(),
            'description'=>$new->getDescription(),
            'createdAt'=>$new->getCreatedAt(),

           ];
        }
 
         $jsonc = json_encode($data);
        return new Response($jsonc);

    }
          /**
     * @Route("/About", name="About")
     */
    public function AboutAction(Request $request)
    {
        $rec=$this->getDoctrine()->getRepository(Recruteur::class)->findAll();
        $can=$this->getDoctrine()->getRepository(Candidat::class)->findAll();
        
        
        return $this->render('home/About.html.twig',array(
            "rec"=>$rec,
            "can"=>$can,
        ))
        ;
       
    }
  
     /**
     * 
     * @Route("/printCV/{id}", name="printCV")
     */
    public function printCVAction($id)
    {
       
        

        $Candidat= $this->getDoctrine()->getRepository(Candidat::class)->find($id);
      
        $Competance= $this->getDoctrine()->getRepository(Competance::class)->findComById($id);
    
        $Experience= $this->getDoctrine()->getRepository(Experience::class)->findExpById($id);
        // Retrieve the HTML generated in our twig file

       return $this->render('Candidat/printCV.html.twig',[
            'cv'=> $Candidat,
            'Competances'=>$Competance,
            'Experiences'=>$Experience,
        ]);
  
       
    }
      /**
     * 
     * @Route("/prCV/{id}", name="prCV")
     */
    public function prCVAction($id)
    {
        
        
        $Candidat= $this->getDoctrine()->getRepository(Candidat::class)->find($id);
      
        $Competance= $this->getDoctrine()->getRepository(Competance::class)->findComById($id);
    
        $Experience= $this->getDoctrine()->getRepository(Experience::class)->findExpById($id);
        // Retrieve the HTML generated in our twig file

      

        return $this->render('Candidat/printCV.html.twig',[
            'cv'=> $Candidat,
            'Competances'=>$Competance,
            'Experiences'=>$Experience,
        ]);
        
       
       
     
       
        
    }
     /**
     * @Route("/detailedNews/{id}", name="detailedNews")
     */
    public function detailedNewsAction($id)
    {
        $em=$this->getDoctrine()->getManager();
        $news=$em->getRepository(News::class)->findAll();
        $new= $this->getDoctrine()->getRepository(News::class)->find($id);
        $rec=$this->getDoctrine()->getRepository(Recruteur::class)->findAll();
        $can=$this->getDoctrine()->getRepository(Candidat::class)->findAll();
          
 
 
       
           $data = [
            'id' => $new->getId(),
            'Titre'=>$new->getTitre(),
            'photo'=>$new->getPhoto(),
            'description'=>$new->getDescription(),
            'createdAt'=>$new->getCreatedAt(),

           ];
    
 
         $jsonc = json_encode($data);
        return new Response($jsonc);

       
    }
   
      /**
     * @Route("/test/{id}", name="test")
     */
    public function testAction($id )
    {
        
    
       
        $Candidat= $this->getDoctrine()->getRepository(Candidat::class)->find($id);
      
        $Competance= $this->getDoctrine()->getRepository(Competance::class)->findComById($id);
    
        $Experience= $this->getDoctrine()->getRepository(Experience::class)->findExpById($id);
       $image=$Candidat->getCV()->getPhoto();
       $logo = 'uploads/images/'.$image;
      
       



        // Retrieve the HTML generated in our twig file
        $template = $this->render('test.html.twig',[
            'cv'=> $Candidat,
            'Competances'=>$Competance,
            'Experiences'=>$Experience,
            'html'=>$logo

        ]);
        
        $html2pdf = new T_Html2Pdf('P','A4','fr','true','UTF-8');
        $html2pdf->create('P','A4','fr','true','UTF-8');
        $html2pdf->pdf->SetDisplayMode('fullpage');
   
        return $html2pdf->generatePdf($template, "CV");
    }
}
