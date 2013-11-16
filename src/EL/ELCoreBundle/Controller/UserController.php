<?php

namespace EL\ELCoreBundle\Controller;

use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use EL\ELCoreBundle\Entity\Player;
use EL\ELCoreBundle\Entity\Login;
use EL\ElCoreBundle\Form\LoginType;
use Symfony\Component\Form\FormError;

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
        $session = $this->get('el_core.session');
        
        $login = new Login();
        $login_form = $this->createForm(new LoginType(), $login);
        
        $login_form->handleRequest($this->getRequest());
        
        if ($login_form->isValid()) {
            $pseudo         = $login->getPseudo();
            $password       = $login->getPassword();
            $remember_me    = $login->getRememberMe();
            
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
        $request = $this->get('request')->request;
        $session = $this->get('el_core.session');
        $errors = array();
        
        if ($this->get('request')->isMethod('post')) {
            $pseudo = $request->get('player_pseudo');
            $password = $request->get('player_password');
            $password_repeat = $request->get('player_password_repeat');
            
            if (empty($pseudo)) {
                $errors []= $this->get('translator')->trans('Pseudo cannot be empty');
            }
            
            if (empty($password)) {
                $errors []= $this->get('translator')->trans('Password cannot be empty');
            }
            
            if ($password !== $password_repeat) {
                $errors []= $this->get('translator')->trans('Password repeat is not the same');
            }
            
            if (count($errors) == 0) {
                $result = $session->signup($pseudo, $password);
                
                switch ($result) {
                    case Player::ALREADY_LOGGED:
                        $errors []= $this->get('translator')->trans('You are already logged. Log out first');
                        break;
                    
                    case Player::PSEUDO_UNAVAILABLE:
                        $errors []= $this->get('translator')->trans('Pseudo %pseudo% is already taken', array('pseudo' => $pseudo));
                        break;
                    
                    case 0:
                        return $this->redirect($this->generateUrl('elcore_home'));
                        break;
                    
                    default:
                        $errors []= $this->get('translator')->trans('Unable to create account. Unknown error');
                        break;
                }
            } else if(!empty($pseudo)) {
                if ($session->pseudoExists($pseudo)) {
                    $errors []= $this->get('translator')->trans('Pseudo %pseudo% is already taken', array('pseudo' => $pseudo));
                }
            }
            
            if (count($errors) == 0) {
                $session->login($pseudo, $password);
            }
        }
        
        return $this->render('ELCoreBundle:User:sign-up.html.twig', array(
            'errors' => $errors,
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
