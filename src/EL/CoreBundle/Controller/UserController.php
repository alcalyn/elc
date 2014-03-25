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
     * )
     * @Route(
     *      "/player/login",
     *      name = "elcore_user_login",
     *      requirements = {
     *          "_method" = "GET",
     *          "_scheme" = "http"
     *      }
     * )
     * @Route(
     *      "/player/login",
     *      name = "elcore_user_login_post",
     *      requirements = {
     *          "_method" = "POST",
     *          "_scheme" = "https"
     *      }
     * )
     * @Template
     */
    public function loginAction()
    {
        $request    = $this->getRequest();
        $login      = new Login();
        $formAction = $this->generateUrl('elcore_user_login_post');
        $loginForm  = $this->createForm(new LoginType(), $login, array('action' => $formAction));
        
        $loginForm->handleRequest($request);
        
        if ($loginForm->isSubmitted()) {
            if ($loginForm->isValid()) {
                $session = $this->get('el_core.session');
                
                try {
                    $session->login($login->getPseudo(), $login->getPassword());
                    
                    return $this->redirect($this->generateUrl('elcore_home', array('ju')));
                } catch (ELUserException $e) {
                    $e->addFlashMessage($this->get('session'));
                }
            }
        }
        
        return array(
            'error'     => null,
            'loginForm' => $loginForm->createView(),
        );
    }
    
    /**
     * @Route(
     *      "/player/signup",
     *      name = "elcore_user_signup",
     *      requirements = {
     *          "_method" = "GET",
     *          "_scheme" = "http"
     *      }
     * )
     * @Route(
     *      "/player/signup",
     *      name = "elcore_user_signup_post",
     *      requirements = {
     *          "_method" = "POST",
     *          "_scheme" = "https"
     *      }
     * )
     * @Template
     */
    public function signupAction()
    {
        $request        = $this->getRequest();
        $signup         = new Signup();
        $formAction     = $this->generateUrl('elcore_user_signup_post');
        $signupForm     = $this->createForm(new SignupType(), $signup, array('action' => $formAction));
        
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
