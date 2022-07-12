<?php
/**
*génération des factures et devis au format pdf
*/
namespace App\Service;
 
 
//use App\Service\HTML2PDF;
 
 
use Spipu\Html2Pdf\Html2Pdf;
 
 
class T_HTML2PDF
{
 private $pdf;
 
 
public function create(
  $orientation=null,
  $format=null,
  $lang=null,
  $unicode=null,
  $encoding=null,
  $margin=null
  ):void{
$this->pdf = new \Spipu\Html2Pdf\Html2Pdf(
    $orientation ? $orientation : $this->orientation,
    $format ? $format : $this->format,
    $lang ? $lang : $this->lang,
   
   
   
  );
}
 
 
public function generatePdf($template, $name)
{
  $directory=$this->getParameter('images_directory');
  $this->pdf->writeHTML($template);
  return $this->pdf->Output($directory."/". $name);
}
  

 
}
 
 
// Fin génération des factures et devis au format pdf
