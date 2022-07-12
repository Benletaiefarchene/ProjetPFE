<?php
namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class GoogleController extends AbstractController
{
    /**
     * Link to this controller to start the "connect" process
     *
     * @Route("/connect/google", name="connect_google_start")
     * @param ClientRegistry $clientRegistry
     * @return \Symfony\Component\HttpFoundation\RedirectResponse ;
     */
    public function connectAction(ClientRegistry $clientRegistry,Request $request )
    {    
      
     
        return $clientRegistry
            ->getClient('google')
            ->redirect([], [
                'prompt' => 'consent',
            ]);
    }

    /**
     * After going to Facebook, you're redirected back here
     * because this is the "redirect_route" you configured
     * in config/packages/knpu_oauth2_client.yaml
     *
     * @Route("/connect/google/check", name="connect_google_check")
     * @param Request $request
     * @return JsonResponse|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function connectCheckAction(Request $request, ClientRegistry $clientRegistry ,UserPasswordEncoderInterface $userPasswordEncoder,EntityManagerInterface $em , \Swift_Mailer $mailer)
    {
       
        if(!$this->getUser()){
          
            
            return new JsonResponse(array('status'=>false , 'message'=>"user not found!"));
            
        }else{
            $alphabet = "abcdefghijklmnopqrstuwxyzABCDEFGHIJKLMNOPQRSTUWXYZ0123456789!@#$%^&*";
            $pass = array(); //remember to declare $pass as an array
            $alphaLength = strlen($alphabet) - 1; //put the length -1 in cache
            for ($i = 0; $i < 10; $i++) {
                $n = rand(0, $alphaLength);
                $pass[] = $alphabet[$n];
            }
            $pa= (implode($pass));
            $message=(new \Swift_Message('Mot de passe '))
            ->setFrom ('archene9@gmail.com')
            ->setTo ($this->getUser()->getEmail())
            ->setBody (
                "<p>Bonjour,</p><p>Votre Mot de Passe est :".$pa .'</p>','text/html'
            );
            $mailer->send($message);
            $this->getUser()->setPassword(
                $userPasswordEncoder->encodePassword(
                    $this->getUser(),
                    $pa
                    )
                );
            $role=['ROLE_CANDIDAT'];
            $this->getUser()->setRoles($role);
            $em->persist($this->getUser());
            $em->flush();
            if($this->getUser()->getExist()==1){
                return $this->redirectToRoute('homee');
            }else
                return $this->redirectToRoute('addcv');
        }
    }
   
    
}