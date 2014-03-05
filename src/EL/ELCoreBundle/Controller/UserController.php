<?php

namespace EL\ELCoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\FormError;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use EL\ELCoreBundle\Model\ELUserException;
use EL\ELCoreBundle\Services\SessionService;
use EL\ELCoreBundle\Form\Entity\Signup;
use EL\ELCoreBundle\Form\Entity\Login;
use EL\ELCoreBundle\Form\Type\SignupType;
use EL\ELCoreBundle\Form\Type\LoginType;

class UserController extends Controller
{
    /**
     * 
     * @return array
     * 
     * @Template
     */
    public function indexAction()
    {
        $player = $this->get('el_core.session')->getPlayer();
        
        return array(
            'player' => $player,
        );
    }
    
    /**
     * @Route(
     *      "/player/login",
     *      name = "elcore_user_login"
     * )
     * @Template
     */
    public function loginAction()
    {
        $request    = $this->getRequest();
        $login      = new Login();
        $loginForm  = $this->createForm(new LoginType(), $login);
        
        $loginForm->handleRequest($request);
        
        if ($loginForm->isSubmitted()) {
            if ($loginForm->isValid()) {
                $session = $this->get('el_core.session');
                
                try {
                    $session->login($login->getPseudo(), $login->getPassword());
                    
                    return $this->redirect($this->generateUrl('elcore_home'));
                } catch (ELUserException $e) {
                    $e->addFlashMessage($this->get('session'));
                }
            }
        }
        
        return array(
            'error'         => null,
            'loginForm'    => $loginForm->createView(),
        );
    }
    
    /**
     * @Route(
     *      "/player/signup",
     *      name = "elcore_user_signup"
     * )
     * @Template
     */
    public function signupAction()
    {
        $request        = $this->getRequest();
        $signup         = new Signup();
        $signupForm     = $this->createForm(new SignupType(), $signup);
        
        $signupForm->handleRequest($request);
        
        if ($signupForm->isSubmitted()) {
            if ($signupForm->isValid()) {
                $session = $this->get('el_core.session');
                
                try {
                    $session->signup($signup->getPseudo(), $signup->getPassword());
                    
                    return $this->redirect($this->generateUrl('elcore_home'));
                } catch (ELUserException $e) {
                    $e->addFlashMessage($this->get('session'));
                }
            }
        }
        
        return array(
            'error'         => null,
            'signupForm'    => $signupForm->createView(),
        );
    }
    
    /**
     * @Route(
     *      "/player/logout",
     *      name = "elcore_user_logout"
     * )
     */
    public function logoutAction()
    {
        $this->get('el_core.session')->logout();
        
        return $this->redirect($this->generateUrl('elcore_home'));
    }
}
