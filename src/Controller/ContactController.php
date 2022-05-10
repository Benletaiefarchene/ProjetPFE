<?php

namespace App\Controller;

use App\Form\ContactType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ContactController extends AbstractController
{
    /**
     * @Route("/contact", name="app_contact")
     */
    public function Contact(Request $request , \Swift_Mailer $mailer)
    {
        $form=$this->createForm(ContactType::class);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $contact = $form->getData();
           
            $message=(new \Swift_Message($contact['sujet']))
  
            ->setFrom($contact['email'])
           
            ->setTo('archene9@gmail.com')
            ->setBody(
                $this->renderView('emails/contactmail.html.twig', compact('contact')
            ),
                'text/html'
            )
            ;
            $mailer->send($message);
            return $this->redirectToRoute('listposthome');
          
        }
        return $this->render('contact/Contact.html.twig', [
            'contactForm' => $form->createView(),
        ]);
    }
}
