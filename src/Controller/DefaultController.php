<?php

namespace App\Controller;

use App\ExceptionApplication;
use App\Parameter;
use App\Security\Action;
use App\Security\Role;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class DefaultController extends BaseFrontController
{

 #[Route('/x', name: 'x')]
    public function x(Parameter $parameter): Response
    {
       
       return new Response() 
    }


    #[Route('/', name: 'homepage')]
    public function index(Parameter $parameter): Response
    {
        $this->denyAccessUnlessGranted(Action::HOMEPAGE);
        $this->logDebug('Home page');

        return $this->render(
            'home.html.twig'
        );
    }

    #[Route('/info', name: 'info')]
    public function info(Parameter $parameter): Response
    {
        $this->denyAccessUnlessGranted(Action::HOMEPAGE);
        $roles = Role::getRoles();
        foreach ($roles as $key => $role) {
            if (!$this->isGranted($role)) {
                unset($roles[$key]);
            }
        }

        return $this->render(
            'info.html.twig',
        );
    }

    #[Route('/todo', name: 'todo')]
    public function todo(): Response
    {
        $this->logWarning('page todo');

        return $this->render('todo.html.twig');
    }

    #[Route('/editor', name: 'editor')]
    public function editor(): Response
    {
        $this->denyAccessUnlessGranted(Role::ALL);
        $this->logInfo('Page editor');

        return $this->render('editor.html.twig');
    }

    /*
     * Error simulation
     *
     * @throws ExceptionApplication
     */
    #[Route('/500', name: '500')]
    public function e500(): Response
    {
        $this->logError('Erreur 500');
        throw new ExceptionApplication('error');
    }
}
