<?php

namespace App\Controller;

use App\Entity\Token;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\User;
use App\Repository\TokenRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;

class SecurityController extends AbstractController
{
    // /**
    //  * @Route("api/login_check", name="api_login_check")
    //  */
    // public function apilogincheck()
    // {    
    //     // return $this->redirectToRoute('api_token_check', [], Response::HTTP_SEE_OTHER);
    //     // dd('ok');
    // }

    /**
     * @Route("api/token_check", name="api_token_check", methods={"GET","POST"})
     */
    public function tokencheck(Request $request)
    {   
        // return $this->redirectToRoute('api_login_check', [], Response::HTTP_SEE_OTHER);
        // $url = "http://127.0.0.1/P7ElouanPeurou/P7ElouanPeurou/public/api/login_check";

        // $opts = array('http' =>
        //     array(
        //         'method' => 'POST',
        //         'max_redirects' => '0',
        //         'ignore_errors' => '1',
        //         'body' => '{
        //             "username": "elouan",
        //             "password": "test"
        //        }' 
        //     )
        // );

        // $context = stream_context_create($opts);
        // $stream = fopen($url, 'r', false, $context);
        // dd(stream_get_contents($stream));
        $tokenstr = $request->getContent();
        $session = new Session();
        $session->start();
        $session->set('token', $tokenstr);
        $token = $session->get('token');
        dd($token);
        
        // return $this->json([
        //     'username' => $user->getUsername(),
        //     'email' => $user->getEmail()
        // ]);
    }
}
