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
        $login = new Login();
        $login_form = $this->createForm(new LoginType(), $login);
        
        $login_form->handleRequest($this->getRequest());
        
        if ($login_form->isValid()) {
            $pseudo         = $login->getPseudo();
            $password       = $login->getPassword();
            $remember_me    = $login->getRememberMe();
            
            $session = $this->get('el_core.session');
            $success = $session->login($pseudo, $password);
            
            if ($success != 0) {
                $login_form->addError(new FormError('Login error'));
            } else {
                return $this->redirect($this->generateUrl('elcore_home'));
            }
        }
        
        return $this->render('ELCoreBundle:User:log-in.html.twig', array(
            'login_form'    => $login_form->createView(),
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
                                $translator->trans('Pseudo %pseudo% is already taken', array(
                                    '%pseudo%'  => $signup->getPseudo(),
                                ))));
                        break;
                    
                    case SessionService::ALREADY_LOGGED:
                        $signup_form
                            ->addError(new FormError(
                                $translator->trans('You are already logged. Log out first')));
                        break;
                    
                    case 0:
                        $session->login($signup->getPseudo(), $signup->getPassword());
                        return $this->redirect($this->generateUrl('elcore_home'));
                        
                    default:
                        $signup_form
                            ->addError(new FormError(
                                $translator->trans('Unable to create account. Unknown error')));
                        break;
                }
            } else {
                $signup_form
                        ->get('password_repeat')
                        ->addError(new FormError(
                            $translator->trans('Password repeat is not the same')));
                
                if ($session->pseudoExists($signup->getPseudo())) {
                    $signup_form
                            ->get('pseudo')
                            ->addError(new FormError(
                                $translator->trans('Pseudo %pseudo% is already taken', array(
                                    '%pseudo%'  => $signup->getPseudo(),
                                ))));
                }
            }
        }
        
        return $this->render('ELCoreBundle:User:sign-up.html.twig', array(
            'signup_form'   => $signup_form->createView(),
        ));
    }
    
    /**
     * @Route(
     *      "/player/logout",
     *      name = "elcore_user_logout"
     * )
     */
    public function logoutAction()
    {
        $session = $this->get('el_core.session');
        
        $session->logout();
        
        return $this->redirect($this->generateUrl('elcore_home'));
    }
}
