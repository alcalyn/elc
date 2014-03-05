<?php

namespace EL\CoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\FormError;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use EL\CoreBundle\Model\ELUserException;
use EL\CoreBundle\Services\SessionService;
use EL\CoreBundle\Form\Entity\Signup;
use EL\CoreBundle\Form\Entity\Login;
use EL\CoreBundle\Form\Type\SignupType;
use EL\CoreBundle\Form\Type\LoginType;

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
