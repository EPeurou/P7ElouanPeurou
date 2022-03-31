<?php

namespace App\Controller;

use App\Entity\Token;
use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/user")
 */
class UserController extends AbstractController
{
    /**
     * @Route("/", name="app_user_index", methods={"GET"})
     */
    public function index(UserRepository $userRepository): Response
    {
        $user = $userRepository->findAll();

        $serializer = new Serializer(array(new ObjectNormalizer()), array(new JsonEncoder()));
        $data = $serializer->serialize($user, "json");
        $response = new Response($data);
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }

    /**
     * @Route("/new", name="app_user_new", methods={"GET", "POST"})
     */
    public function new(Request $request, UserRepository $userRepository,ManagerRegistry $doctrine): Response
    {
        $authorizationHeader = $request->headers->get('Authorization');
        // dd($authorizationHeader);
        if($authorizationHeader != null){
            $user = new User();
            $data = $request->getContent();
            $dataDecode = json_decode($data, true);
            // dd($dataDecode['password']);
            $hashedPassword = password_hash($dataDecode['password'], PASSWORD_DEFAULT);
            
            $serializer = new Serializer(array(new ObjectNormalizer()), array(new JsonEncoder()));
            $user = $serializer->deserialize($data, "App\Entity\User", "json");
            $user->setPassword($hashedPassword);
            $entityManager = $doctrine->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            return new Response('', Response::HTTP_CREATED);
        } else {
            return new Response('', Response::HTTP_UNAUTHORIZED);
        }
    }

    /**
     * @Route("/{id}", name="app_user_show", methods={"GET"})
     */
    public function show(User $user): Response
    {
        $serializer = new Serializer(array(new ObjectNormalizer()), array(new JsonEncoder()));
        $data = $serializer->serialize($user, "json");
        $response = new Response($data);
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }

    /**
     * @Route("/{id}/edit", name="app_user_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, User $user, UserRepository $userRepository): Response
    {
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $userRepository->add($user);
            return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('user/edit.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="app_user_delete", methods={"POST"})
     */
    public function delete(Request $request, User $user, UserRepository $userRepository): Response
    {
        
            $userRepository->remove($user);
        

            return new Response('', Response::HTTP_OK);
    }
}
