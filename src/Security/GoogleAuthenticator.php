<?php
namespace App\Security;

use App\Entity\User; // your user entity
use Doctrine\ORM\EntityManagerInterface;
use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use KnpU\OAuth2ClientBundle\Secutity\Authenticator\SocialAuthenticator;
use KnpU\OAuth2ClientBundle\Security\Authenticator\OAuth2Authenticator;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;


class GoogleAuthenticator extends OAuth2Authenticator
{
    private $clientRegistry;
    private $entityManager;
    private $router;

    public function __construct(ClientRegistry $clientRegistry, EntityManagerInterface $entityManager, RouterInterface $router)
    {
        $this->clientRegistry = $clientRegistry;
        $this->entityManager = $entityManager;
        $this->router = $router;
    }

    public function supports(Request $request): ?bool
    {
        // continue ONLY if the current ROUTE matches the check ROUTE
        return $request->getPathInfo() == '/connect/google/check/' && $request->isMethod('GET');
       
    }
    public function getCredentials(Request $request)
    {
        return $this->fetchAccessToken($this->getGoogleClient()); 
    }
    public function getUser($credtials,UserProviderInterface $userProvider){
         $googleUser=$this>getGoogleClient()
         ->fetchUserFromToken($credtials);
         $email=$googleUser->getEmail();
         $user= $this->em->getRepository('App:User')
         ->findOneBy(['email'=>$email]);
         if(!$user){
             $user = new User();
             $user = setEmail($googleUser->getEmail());
             $user = setNom($googleUser->getName());
             $this->persist($user);
             $this->flush();

         }
         return $user;
    }
/**
 * @return \knpU\OAuth2ClientBundle\Client\OAuth2Client
 */
    private function getGoogleClient(){
        return $this->clientRegistry
        ->getClient('google');
    }
// /**
//  * Undocumented function
//  *
//  * @param Request $request
//  * @param  \Symfony\Component\Security\core\Exception\AuthenticationException $authException 
//  * @return \Symfony\Component\HttpFoundation\Response
//  */public function start(Request $request, AuthenticationException $authException){
//             return new RedirectResponse('/login');
//  }
   

//     public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
//     {
//         // // change "app_homepage" to some route in your app
//         // $targetUrl = $this->router->generate('listposthome');

//         // return new RedirectResponse($targetUrl);
    
//         // // or, on success, let the request continue to be handled by the controller
//         //return null;
//     }

//     public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
//     {
//         // $message = strtr($exception->getMessageKey(), $exception->getMessageData());

//         // return new Response($message, Response::HTTP_FORBIDDEN);
//     }
}