<?php

namespace EL\ELCoreBundle\Controller;

use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use EL\ELCoreBundle\Entity\Player;
use EL\ELCoreBundle\Form\Entity\Login;
use EL\ElCoreBundle\Form\Type\LoginType;
use Symfony\Component\Form\FormError;
use EL\ELCoreBundle\Form\Entity\Signup;
use EL\ElCoreBundle\Form\Type\SignupType;
use EL\ELCoreBundle\Services\SessionService;
use Symfony\Component\Security\Core\SecurityContext;

class UserController extends Controller
{
    public function indexAction()
    {
        $player = $this->get('el_core.session')->getPlayer();
        
        return $this->render('ELCoreBundle:User:login-bar.html.twig', array(
            'player'    => $player,
        ));
    }
    
    /**
     * @Route(
     *      "/player/login",
     *      name = "elcore_user_login"
     * )
     */
    public function loginAction()
    {
        if ($this->get('security.context')->isGranted('ROLE_PLAYER')) {
            return $this->redirect($this->generateUrl('elcore_home'));
        }
        
        $request = $this->getRequest();
        $session = $request->getSession();
        // On vérifie s'il y a des erreurs d'une précédente soumission du formulaire
        if ($request->attributes->has(SecurityContext::AUTHENTICATION_ERROR)) {
            $error = $request->attributes->get(SecurityContext::AUTHENTICATION_ERROR);
        } else {
            $error = $session->get(SecurityContext::AUTHENTICATION_ERROR);
            $session->remove(SecurityContext::AUTHENTICATION_ERROR);
        }
        return $this->render('ELCoreBundle:User:log-in.html.twig', array(
            // Valeur du précédent nom d'utilisateur entré par l'internaute
            'last_username' => $session->get(SecurityContext::LAST_USERNAME),
            'error'         => $error,
        ));
    }
    
    
    
    /**
     * @Route(
     *      "/player/signup",
     *      name = "elcore_user_signup"
     * )
     */
    public function signupAction()
    {
        if ($this->get('security.context')->isGranted('ROLE_PLAYER')) {
            $flashbag = $this->get('session')->getFlashBag();
            $flashbag->add(
                    'danger',
                    'You are already logged. Logout first to signup'
            );
            return $this->redirect($this->generateUrl('elcore_home'));
        }
        
        $signup = new Signup();
        $signup_form = $this->createForm(new SignupType(), $signup);
        $translator = $this->get('translator');
        $session = $this->get('el_core.session');
        
        $signup_form->handleRequest($this->getRequest());
        
        if ($signup_form->isValid()) {
            if ($signup->isValid()) {
                $success = $session->signup($signup->getPseudo(), $signup->getPassword());
                
                switch ($success) {
                    case SessionService::PSEUDO_UNAVAILABLE:
                        $signup_form
                            ->get('pseudo')
                            ->addError(new FormError(
                                $translator->trans('%pseudo%.already.taken', array(
                                    '%pseudo%'  => $signup->getPseudo(),
                                ))));
                        break;
                    
                    case SessionService::ALREADY_LOGGED:
                        $signup_form
                            ->addError(new FormError(
                                $translator->trans('you.are.already.logged')));
                        break;
                    
                    case 0:
                        return $this->redirect($this->generateUrl('elcore_home'));
                        
                    default:
                        $signup_form
                            ->addError(new FormError(
                                $translator->trans('unableto.create.account')));
                        break;
                }
            } else {
                $signup_form
                        ->get('password_repeat')
                        ->addError(new FormError(
                            $translator->trans('password.repeat.isnotthesmae')));
                
                if ($session->pseudoExists($signup->getPseudo())) {
                    $signup_form
                            ->get('pseudo')
                            ->addError(new FormError(
                                $translator->trans('%pseudo%.already.taken', array(
                                    '%pseudo%'  => $signup->getPseudo(),
                                ))));
                }
            }
        }
        
        return $this->render('ELCoreBundle:User:sign-up.html.twig', array(
            'signup_form'   => $signup_form->createView(),
        ));
    }
}
