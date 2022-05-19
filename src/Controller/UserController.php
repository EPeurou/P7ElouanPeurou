<?php

namespace App\Controller;

use App\Entity\Token;
use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Serializer\Serializer;
use JMS\Serializer\SerializerInterface;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * @Route("/user")
 */
class UserController extends AbstractController
{
    /**
     * @Route("/", name="app_user_index", methods={"GET"})
     */
    public function index(UserRepository $userRepository,SerializerInterface $serializerInterface,CacheInterface $cache): Response
    {
        $strid = "";
        $user = $userRepository->findAll();
        foreach ($user as $singleUser){
            $ids = $singleUser->getId();
            $strid .= $ids;
        }
        // dd($strid);
        $data = $serializerInterface->serialize($user, 'json');
        $ToCache = $cache->get("data_index".$strid,function(ItemInterface $item) use($data){
            $item->expiresAfter(3600);
            // return $data;
            return $data;
        });
        $response = new Response($data);
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }

    /**
     * @Route("/new", name="app_user_new", methods={"GET", "POST"})
     */
    public function new(Request $request, UserRepository $userRepository,ManagerRegistry $doctrine): Response
    {
        $user = new User();
        $data = $request->getContent();
        $dataDecode = json_decode($data, true);
        
        if (!isset($dataDecode['userName'])) {
            return new JsonResponse([
                'erreur' => 'le champ nom d\'utilisateur n\'a pas ete transmit.'
            ], 400);
        } elseif (!isset($dataDecode['password'])) {
            return new JsonResponse([
                'erreur' => 'le champ mot de passe n\'a pas ete transmit.'
            ], 400);
        } elseif (isset($dataDecode['password']) && $dataDecode['password'] == "" || $dataDecode['password'] == null){
            return new JsonResponse([
                'erreur' => 'le champ mot de passe ne peut pas etre vide.'
            ], 400);
        } elseif (isset($dataDecode['userName']) && $dataDecode['userName'] == "" || $dataDecode['userName'] == null) {
            return new JsonResponse([
                'erreur' => 'le champ nom d\'utilisateur ne peut pas etre vide.'
            ], 400);
        } else {
            $hashedPassword = password_hash($dataDecode['password'], PASSWORD_DEFAULT);
            $serializer = new Serializer(array(new ObjectNormalizer()), array(new JsonEncoder()));
            $user = $this->container->get('serializer')->deserialize($data,"App\Entity\User", 'json');
            
            // $user = $serializer->deserialize($data, "App\Entity\User", "json");
            $user->setPassword($hashedPassword);
            try{
                $entityManager = $doctrine->getManager();
                $entityManager->persist($user);
                $entityManager->flush();
                return new Response('', Response::HTTP_CREATED);
            } catch(\Exception $e) {
                return new JsonResponse([
                    'erreur' => 'le nom d\'utilisateur existe deja.'
                ], 500);
            }
        }
          
        // return new Response('', Response::HTTP_UNAUTHORIZED);
        
    }

    /**
     * @Route("/{id}", name="app_user_show", methods={"GET"})
     */
    public function show(User $user, SerializerInterface $serializerInterface, CacheInterface $cache): Response
    {
        // $serializer = new Serializer(array(new ObjectNormalizer()), array(new JsonEncoder()));
        // $data = $serializer->serialize($user, "json");
        $userName = $user->getUserIdentifier();
        $data = $serializerInterface->serialize($user, 'json');
        
        $ToCache = $cache->get("data_show".$userName,function(ItemInterface $item) use($data){
            $item->expiresAfter(3600);
            // return $data;
            return $data;
        });
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
     * @Route("/delete/{id}", name="app_user_delete", methods={"POST"})
     */
    public function delete(Request $request, User $user, UserRepository $userRepository): Response
    {
        
            $userRepository->remove($user);
        

            return new Response('', Response::HTTP_OK);
    }
}
