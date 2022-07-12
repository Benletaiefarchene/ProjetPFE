<?php

namespace App\Controller;
use Dompdf\Options;

use App\Entity\News;
use App\Entity\Type;
use App\Data\SearchData;
use App\Entity\Candidat;
use App\Form\SearchForm;
use App\Entity\Recruteur;
use App\Entity\Competance;
use App\Entity\Experience;
use App\Entity\OffreEmploi;
use App\Form\TypeOffreType;
use App\Service\PdfService;
use App\Service\T_HTML2PDF;
use App\Entity\OffreFormation;
use App\Repository\OffreEmploiRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;


class HomeController extends Controller
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
        
        
      
        return $this->render('home/home.html.twig', [
            "news" =>$news,
            "rec"=>$rec,
            "can"=>$can,
        ]);
    }
   /**
     * 
     * @Route("/detailedOffre/{id}", name="detailedOff")
     */
    public function detailedOffreAction($id)
    {
        
        $Off= $this->getDoctrine()->getRepository(OffreEmploi::class)->find($id);
        
       dd($Off);
        return $this->render('Recruteur/detailedOffre.html.twig',[
           
            'off'=>$Off
        ]   
        );
    }
  
    /**
     * @Route("/listPosthome", name="listposthome")
     */
    public function listposteAction(Request $request,OffreEmploiRepository $repository,PaginatorInterface $paginator)
    {
        
        
        $data = new SearchData();
        $form = $this->createForm(SearchForm::class , $data);
        $form->handleRequest($request);
       $rec=$this->getDoctrine()->getRepository(Recruteur::class)->findAll();
       $can=$this->getDoctrine()->getRepository(Candidat::class)->findAll();
        $posts=$repository->Search($data);
        $pagination = $paginator->paginate(
            $posts, /* query NOT result */
            $request->query->getInt('page', 1), /*page number*/
            4 /*limit per page*/
        );
        
        return $this->render('recruteur/listPost.html.twig', array(
            "posts" =>$pagination,
            "rec"=>$rec,
            "can"=>$can,
            'form'=>$form->createView(),
            
        ));
       // return $this->redirectToRoute('list_post');

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
       // $posts=$em->getRepository(OffreEmploi::class)->findBy(array('titre'=> 'mobile'));
        
        
        return $this->render('home/Actualites.html.twig', array(
            "news" =>$news,
            "rec"=>$rec,
            "can"=>$can,
        ));
       // return $this->redirectToRoute('list_post');

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
       
        return $this->render('home/detailedNews.html.twig',[
            'new'=> $new,
            "rec"=>$rec,
            "can"=>$can,
            "news"=>$news,
         
        ]   
        );
    }
    // public function search(OffreEmploiRepository $repository){
        
    //     $data = new SearchData();
    //     $form = $this->createForm(SearchForm::class , $data);
    //     $offre=$repository->findSearch();
    //     return $this->render('recruteur/listPost.html.twig',[
    //         'offre'=>$offre,
    //         'form'=>$form->createView()
    //     ]);

    // }
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
        $html2pdf->save();
        return $html2pdf->generatePdf($template, "CV");
    }
         /**
     * @Route("/listFormation", name="listFormation")
     
     */
    public function listFormationAction(Request $request)
    {
       
        $em=$this->getDoctrine()->getManager();
       $role= $this->get('security.token_storage')->getToken()->getUser()->getRoles();
       if($role["0"] == "Role_CANDIDAT"){
        $Form=$em->getRepository(OffreFormation::class)->findBy(array('Role'=>1));
       }else if($role["0"] == "Role_RECRUTEUR"){
        $Form=$em->getRepository(OffreFormation::class)->findBy(array('Role'=>0));
       }else{
        $Form=$em->getRepository(OffreFormation::class)->findAll();
       }
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
    * @Route("/downloadfile/{id}", name="downloadfile")
    */
    public function downloadAction($id) {
        try {
        
            $Formation= $this->getDoctrine()->getRepository(OffreFormation::class)->find($id);
            if (! $Formation) {
                $array = array (
                    'status' => 0,
                    'message' => 'File does not exist' 
                );
                $response = new JsonResponse ( $array, 200 );
                return $response;
            }
            $displayName = $Formation->getFolder ();
            $fileName = $Formation->getFolder ();
            $file_with_path = $this->getParameter ( 'images_directory' ) . "/" . $fileName;
            $response = new BinaryFileResponse ( $file_with_path );
            $response->headers->set ( 'Content-Type', 'text/plain' );
            $response->setContentDisposition ( ResponseHeaderBag::DISPOSITION_ATTACHMENT, $displayName );
            return $response;
        } catch ( Exception $e ) {
            $array = array (
                'status' => 0,
                'message' => 'Download error' 
            );
            $response = new JsonResponse ( $array, 400 );
            return $response;
        }
    }
}
