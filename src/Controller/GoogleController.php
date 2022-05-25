<?php
namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;

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
    public function connectCheckAction(Request $request, ClientRegistry $clientRegistry)
    {
        
        if(!$this->getUser()){
            
            
            return new JsonResponse(array('status'=>false , 'message'=>"user not found!"));
            
        }else{
            if($this->getUser()->getExist()==1){
                return $this->redirectToRoute('listposthome');
            }else
                return $this->redirectToRoute('listposthome');
        }
    }
}