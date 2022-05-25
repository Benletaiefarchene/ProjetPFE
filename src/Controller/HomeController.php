<?php

namespace App\Controller;

use Dompdf\Dompdf;
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
use App\Repository\OffreEmploiRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;


class HomeController extends Controller
{
    // /**
    //  * @Route("/home", name="homee")
    //  */
    // public function index(): Response
    // {
    //     return $this->render('home/index.html.twig', [
    //         'controller_name' => 'HomeController',
    //     ]);
    // }
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
        $posts=$repository->findSearch($data);
        
        return $this->render('recruteur/listPost.html.twig', array(
            "posts" =>$posts,
            "rec"=>$rec,
            "can"=>$can,
            'form'=>$form->createView()
        ));
       // return $this->redirectToRoute('list_post');

    }
    //  /**
    //  * @Route("/listPostRec/{id}", name="listpostRec")
    //  */
    // public function listpostAction(Request $request,$id,OffreEmploiRepository $repository)
    // {
    //     $data = new SearchData();
    //     $form = $this->createForm(SearchForm::class , $data);
    //     $form->handleRequest($request);
       
    //     $posts=$repository->findSearch($data);
        
    //     $em=$this->getDoctrine()->getManager();
    //     $posts=$em->getRepository(Type::class)->findtypeRec($id);
    //    // $posts=$em->getRepository(OffreEmploi::class)->findBy(array('titre'=> 'mobile'));
        
        
    //     return $this->render('recruteur/listPost.html.twig', array(
    //         "posts" =>$posts,
    //         'form'=>$form->createView()

    //     ));
    //    // return $this->redirectToRoute('list_post');

    // }
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
       
        
        
        return $this->render('home/About.html.twig')
        ;
       
    }
  
     /**
     * 
     * @Route("/printCV/{id}", name="printCV")
     */
    public function printCVAction($id)
    {
       
        // Configure Dompdf according to your needs
       
        // $pdfOptions = new Options();
        // $pdfOptions->set('defaultFont', 'Arial');
        // $pdfOptions->set('isRemoteEnabled', true);
         
        // // Instantiate Dompdf with our options
        // $dompdf = new Dompdf($pdfOptions);
        // $dompdf->set_base_path("css");
        // $contxt = stream_context_create([
        //     'ssl' => [
        //         'verify_peer' => FALSE,
        //         'verify_peer_name' => FALSE,
        //         'allow_self_signed'=> TRUE
        //     ]
        // ]);
        // $dompdf->setHttpContext($contxt);

        $Candidat= $this->getDoctrine()->getRepository(Candidat::class)->find($id);
      
        $Competance= $this->getDoctrine()->getRepository(Competance::class)->findComById($id);
    
        $Experience= $this->getDoctrine()->getRepository(Experience::class)->findExpById($id);
        // Retrieve the HTML generated in our twig file

       return $this->render('Candidat/printCV.html.twig',[
            'cv'=> $Candidat,
            'Competances'=>$Competance,
            'Experiences'=>$Experience,
        ]);
        
    //     // Load HTML to Dompdf
    //    // $html = '<link type="text/css" media="dompdf" href="D:/xampp/htdocs/projetPFE/public/css/printCV.css" rel="stylesheet" />';
    //    $html .= ob_get_contents();
    //    $dompdf->loadHtml($html);
    //    $dompdf->render();
       //$dompdf->set_base_path(" /public/css/printCV.css");
        // (Optional) Setup the paper size and orientation 'portrait' or 'portrait'
       // $dompdf->setPaper('A4', 'portrait');

        // Render the HTML as PDF
        

        // // Output the generated PDF to Browser (force download)
        // $dompdf->stream("mypdf.pdf", [
        //     "Attachment" => false
        // ]);
       
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
        
        $new= $this->getDoctrine()->getRepository(News::class)->find($id);
        $rec=$this->getDoctrine()->getRepository(Recruteur::class)->findAll();
        $can=$this->getDoctrine()->getRepository(Candidat::class)->findAll();
       
        return $this->render('home/detailedNews.html.twig',[
            'new'=> $new,
            "rec"=>$rec,
            "can"=>$can,
         
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
    public function testAction($id)
    {
        
    
       
        $Candidat= $this->getDoctrine()->getRepository(Candidat::class)->find($id);
      
        $Competance= $this->getDoctrine()->getRepository(Competance::class)->findComById($id);
    
        $Experience= $this->getDoctrine()->getRepository(Experience::class)->findExpById($id);
        // Retrieve the HTML generated in our twig file
        return $this->render('test.html.twig',[
            'cv'=> $Candidat,
            'Competances'=>$Competance,
            'Experiences'=>$Experience,
        ]);
      
    }
}
