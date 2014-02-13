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
            'player'    => $player,
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
        $login_form = $this->createForm(new LoginType(), $login);
        
        $login_form->handleRequest($request);
        
        if ($login_form->isSubmitted()) {
            if ($login_form->isValid()) {
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
            'login_form'    => $login_form->createView(),
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
        $signup_form    = $this->createForm(new SignupType(), $signup);
        
        $signup_form->handleRequest($request);
        
        if ($signup_form->isSubmitted()) {
            if ($signup_form->isValid()) {
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
            'signup_form'   => $signup_form->createView(),
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
