<?php

namespace App\Controller;


use App\Entity\Token;
use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use Doctrine\Persistence\ManagerRegistry;
use Exception;
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
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use OpenApi\Annotations as OA;

/**
 * @Route("/user")
 */
class UserController extends AbstractController
{
    /**
     * @Route("/", name="app_user_index", methods={"GET"})
     * @OA\Response(
     *     response=200,
     *     description="Returns the list of users",
     *     @OA\JsonContent(
     *        type="array",
     *        @OA\Items(ref=@Model(type=User::class, groups={"full"})),
     *        example={
     *        "id": 4,
     *        "username": "jean",
     *        "roles": "",
     *        "password": "$2y$13$kylP7xftPMN9DexwiOP03ON.DYqzLlGZ6LzXbl.YmUI3G0MYE6.ha",
     *         "links": "all links to other routes for this user"        
     *        }
     *     )
     *     
     * )
     * @OA\Tag(name="User")
     * @Security(name="Bearer")
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
     * @Route("/new", name="app_user_new", methods={"POST"})
     * @OA\Response(
     *     response=201,
     *     description="Created",
     *     @OA\JsonContent(
     *        type="array",
     *        @OA\Items(ref=@Model(type=User::class, groups={"full"}))
     *     )  
     * )
     * @OA\Parameter(
     *     name="body",
     *     in="query",
     *     description="Create a new user",
     *     example={
     *      "userName": "username",
     *      "password": "password"
     *   },
     *     
     * )
     * @OA\Tag(name="User")
     * @Security(name="Bearer")
     */
    public function new(Request $request, UserRepository $userRepository,ManagerRegistry $doctrine): Response
    {
        $user = new User();
        $data = $request->getContent();
        $dataDecode = json_decode($data, true);
        
        if (!isset($dataDecode['userName'])) {
            return new JsonResponse([
                'code' => 400,
                'error' => 'The username field is not in your request.'
            ], 400);
        } elseif (!isset($dataDecode['password'])) {
            return new JsonResponse([
                'code' => 400,
                'error' => 'The password field is not in your request.'
            ], 400);
        } elseif (isset($dataDecode['password']) && $dataDecode['password'] == "" || $dataDecode['password'] == null){
            return new JsonResponse([
                'code' => 400,
                'error' => 'The password field can\'t be empty.'
            ], 400);
        } elseif (isset($dataDecode['userName']) && $dataDecode['userName'] == "" || $dataDecode['userName'] == null) {
            return new JsonResponse([
                'code' => 400,
                'error' => 'The username field can\'t be empty.'
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
                    'code' => 500,
                    'error' => 'The username already exist.'
                ], 500);
            }
        }
          
        // return new Response('', Response::HTTP_UNAUTHORIZED);
        
    }

    /**
     * @Route("/{id}", name="app_user_show", methods={"GET"})
     * @OA\Response(
     *     response=200,
     *     description="Return a user",
     *     @OA\JsonContent(
     *        type="array",
     *        @OA\Items(ref=@Model(type=User::class, groups={"full"})),
     *        example={
     *        "id": 5,
     *        "username": "tim",
     *        "roles": "",
     *        "password": "$2y$13$kylP7xftPMN9DexwiOP03ON.DYqzLlGZ6LzXbl.YmUI3G0MYE6.ha",
     *         "links": "all links to other routes for this user"        
     *        }
     *     )
     *     
     * )
     * @OA\Tag(name="User")
     * @Security(name="Bearer")
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
     * @Route("/delete/{id}", name="app_user_delete", methods={"POST"})
     * @OA\Response(
     *     response=200,
     *     description="Delete a user",
     * )
     * @OA\Tag(name="User")
     * @Security(name="Bearer")
     */
    public function delete(Request $request, User $user, UserRepository $userRepository): Response
    {
        
            $userRepository->remove($user);
        

            return new Response('', Response::HTTP_OK);
    }
}
