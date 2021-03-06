<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\ResetPassType;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;

use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\HttpFoundation\JsonResponse;
class SecurityController extends AbstractController
{
    /**
     * @Route("/login", name="app_login")
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        if ($this->getUser()) {
           
             return $this->render('registration/register.html.twig');
           
        }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
       // return $this->redirectToRoute('homee');
    }

    /**
     * @Route("/logout", name="app_logout")
     */
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
        
    }
    /**
     * @Route("/oubli-pass", name="app_forgotten_password")
     */
    public function forgottenPass(Request $request , UserRepository $userRepo , \Swift_Mailer $mailer ,TokenGeneratorInterface $tokenGenerator ){
            //On cr??e le formulaire
            $form=$this->createForm(ResetPassType::class);
            // On traite  le formulaire 
            $form->handleRequest($request);

            //Si le formulaire est valide
            if($form->isSubmitted() && $form->isValid()){
                // On r??cup??re les donn??es 
                $donnees = $form->getData();
                // On cherche si un utilisateur a cet email
                $user=$userRepo->findOneByEmail($donnees['email']);
                
                //si l'utilisateur n'existe pas 
                if(!$user){
                    $this->addFlash('danger','Cette adresse n\'existe pas');
                  return  $this->redirectToRoute('app_login');

                }
                // On g??nere un token 
                $token = $tokenGenerator->generateToken();

                try {
                    $user->setResetToken($token);
                    $entityManager = $this->getDoctrine()->getManager();
                    $entityManager->persist($user);
                    $entityManager->flush();
                } catch (\Exception $e) {

                    $this->addFlash('warning', 'Une erreur est survenue :',$e->getMessage());
                    return  $this->redirectToRoute('app_login');
                }
                $url=$this->generateUrl('app_reset_password',['token' => $token],UrlGeneratorInterface::ABSOLUTE_URL); 

                //On envoi le Message

                $message=(new \Swift_Message('Mot de passe oubli??'))
                ->setFrom ('archene9@gmail.com')
                ->setTo ($donnees['email'])
                ->setBody (
                    "<p>Bonjour,</p><p>Une demande de r??initialisation de mot de passe a ??t?? effectu??e .
                    Veuillez cliquer sur  le lien suivant :".$url .'</p>','text/html'
                );
                $mailer->send($message);

                $this->addFlash('message',  'Un email de r??initialisation de mot de passe vous a ??t?? envoy?? ');

                return   $this->redirectToRoute('app_login');

            }

            //On envoie vers la page de demande de l'e-mail
            return $this->render('security/pass_oublier.html.twig',['emailForm'=>$form->createView()]);
    }
/**
 * 
 *@Route("/reset-pass/{token}",name="app_reset_password")
 */
    public function resetPassword($token , Request $request , UserPasswordEncoderInterface $passwordEncoder ){
        // On cherche  l'utilisateur avec le token fourni 
        $user = $this->getDoctrine()->getRepository(User::class)->findOneBy(['resetToken'=>$token]);
        if(!$user){
            $this->addFlash('danger','Token Inconnu ');
            return $this->redirectToRoute('app_login');
        }
      
        if($request->isMethod('POST')){

            $user->setResetToken(null);

            $user->setPassword($passwordEncoder->encodePassword($user, $request->request->get('password')));
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();
            
            $this->addFlash('message','Mot de passe modifi?? avec succ??s');
            return $this->redirectToRoute('app_login');
        }else {
            return $this->render('security/reset_password.html.twig',['token'=>$token]);
        }

    }

        /**
     * @Route("/mobile/aff", name="affmobcategory")
     */
    public function affmobcategory(NormalizerInterface $normalizer)
    {
        $med=$this->getDoctrine()->getRepository(User::class)->findAll();

        $normalizer = new ObjectNormalizer();
        $normalizer->setCircularReferenceLimit(1);
        $normalizer->setCircularReferenceHandler(function ($med) {
            return $med->getId();
        });
        $encoders = [new JsonEncoder()];
        $normalizers = array($normalizer);
        $serializer = new Serializer($normalizers,$encoders);
        $formatted = $serializer->normalize($med);
        
        return new JsonResponse($formatted);
 
      //  $jsonContent = $normalizer->normalize($med,'json',['categorie'=>'post:read']);
       // return new Response(json_encode($jsonContent));
    }

}
