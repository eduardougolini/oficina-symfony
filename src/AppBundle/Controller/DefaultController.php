<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;

use AppBundle\Entity\Usuario;
use AppBundle\Entity\Endereco;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request) {
        return $this->render('form.html.twig');
    }
    
    /**
     * @Route("/registerNewUser", name="newUser")
     * @param Request $request
     */
    public function registerNewUser(Request $request) {
        $username = $request->get('name');
        $street = $request->get('street');
        
        $em = $this->getDoctrine()->getManager();
        
        $usuario = new Usuario();
        $usuario->setNome($username);
        
        $em->persist($usuario);
        
        $endereco = new Endereco();
        $endereco->setRua($street);
        $endereco->setUsuario($usuario);
        
        $em->persist($endereco);
        
        $em->flush();
        
        return new RedirectResponse($this->generateUrl('listUsers'));
    }
    
    /**
     * @Route("/listUsers", name="listUsers")
     * @return Response
     */
    public function listRegisteredUsers() {
        $em = $this->getDoctrine()->getManager();
        
        $users = $em->createQuery(
                "SELECT u.nome, e.rua "
                . "FROM AppBundle:Usuario u "
                . "JOIN AppBundle:Endereco e "
                    . "WITH e.usuario = u "
                . "WHERE u.id IS NOT NULL")
                ->getResult();
        
        return $this->render('listUsers.html.twig', ['users' => $users]);
    }
}
