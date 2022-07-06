<?php

namespace App\Controller;

use App\Entity\Customer;
use App\Repository\CustomerRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\Mapping\Id;
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
use PhpParser\Node\Expr\Cast\Int_;
use Symfony\Component\Serializer\Encoder\JsonDecode;
use Doctrine\ORM\Query\ResultSetMapping;

/**
 * @Route("/customer")
 */
class CustomerController extends AbstractController
{
    /**
     * @Route("/", name="app_customer", methods={"GET"})
     */
    public function index(Request $request,CustomerRepository $customerRepository,SerializerInterface $serializerInterface,CacheInterface $cache): Response
    {
        $getuser = $this->getUser();
        $body = $request->getContent();
        $bodyDecode = json_decode($body, true);
        $strid = "";
        $customer = $customerRepository->findBy(['idUser' =>$getuser]);
        if ($customer != null){
            foreach ($customer as $singleUser){
                $ids = $singleUser->getId();
                $strid .= $ids;
            }
            // dd($strid);
            $data = $serializerInterface->serialize($customer, 'json');
            $ToCache = $cache->get("data_index".$strid,function(ItemInterface $item) use($data){
                $item->expiresAfter(3600);
                // return $data;
                return $data;
            });
            $response = new Response($data);
            $response->headers->set('Content-Type', 'application/json');

            return $response;
        } else {
            return new JsonResponse([
                'code' => 404,
                'error' => 'Any customer for this user.'
            ], 404);
        }     
    }

    /**
     * @Route("/show/{id}", name="app_customer_show", methods={"GET"})
     */
    public function show(Customer $customer, SerializerInterface $serializerInterface, CacheInterface $cache, Request $request,CustomerRepository $customerRepository, int $id, ManagerRegistry $doctrine): Response
    {
        $getuser = $this->getUser();
        $body = $request->getContent();
        $bodyDecode = json_decode($body, true);
        // $userObj = $customerRepository->findByExampleField($id);
        
        $userObj = $customerRepository->findOneBy(['idUser' =>$getuser,'id'=>$id]);
        
        // $serializer = new Serializer(array(new ObjectNormalizer()), array(new JsonEncoder()));
        // $data = $serializer->serialize($user, "json");
        $userName = $customer->getUserName();
        if ($userObj != null){
            
            $data = $serializerInterface->serialize($userObj, 'json');
            
            $ToCache = $cache->get("data_show".$userName,function(ItemInterface $item) use($data){
                $item->expiresAfter(3600);
                // return $data;
                return $data;
            });
            // $jsonarray = Json_Decode($data);
            // $getIdUser = $jsonarray->id_user->id;
            // unset($jsonarray->id_user);
            // set($jsonarray->id_user = $getIdUser);
            // dd($jsonarray->id_user->id);
            $response = new Response($data);
            $response->headers->set('Content-Type', 'application/json');
            return $response;
        } else {
            return new JsonResponse([
                'code' => 404,
                'error' => 'The customer does not exist for this user.'
            ], 404);
        }        
    }
    /**
     * @Route("/new", name="app_customer_new", methods={"POST"})
     */
    public function new(UserRepository $userRepository,Request $request,ManagerRegistry $doctrine): Response
    {
        $getuser = $this->getUser();
        $customer = new Customer();
        $data = $request->getContent();
        $dataDecode = json_decode($data, true);
        // dd($userObj);
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
                'error' => 'The password field is empty.'
            ], 400);
        } elseif (isset($dataDecode['userName']) && $dataDecode['userName'] == "" || $dataDecode['userName'] == null) {
            return new JsonResponse([
                'code' => 400,
                'error' => 'The username field is empty.'
            ], 400);
        } else {
            $userObj = $userRepository->findOneBy(['id' => $getuser]);
            if($userObj != null){
                $hashedPassword = password_hash($dataDecode['password'], PASSWORD_DEFAULT);
                $serializer = new Serializer(array(new ObjectNormalizer()), array(new JsonEncoder()));
                $customer = $this->container->get('serializer')->deserialize($data,"App\Entity\Customer", 'json');
                //$user = $serializer->deserialize($data, "App\Entity\User", "json");
                $customer->setPassword($hashedPassword);
                $customer->setIdUser($userObj);
                try{
                    $entityManager = $doctrine->getManager();
                    $entityManager->persist($customer);
                    $entityManager->flush();
                    return new Response('', Response::HTTP_CREATED);
                } catch(\Exception $e) {
                    return new JsonResponse([
                        'code' => 500,
                        'error' => 'The username already exist.'
                    ], 500);
                    // dd($e);
                }
            } else {
                return new JsonResponse([
                    'code' => 404,
                    'error' => 'The iduser field is incorrect.'
                ], 404);
            }
        }
    }
    
    /**
     * @Route("/delete/{id}", name="app_customer_delete", methods={"DELETE"})
     * @OA\Response(
     *     response=200,
     *     description="Delete a user",
     * )
     * @OA\Tag(name="User")
     * @Security(name="Bearer")
     */
    public function delete(Request $request, Customer $customer, CustomerRepository $customerRepository,Int $id): Response
    {
        $getuser = $this->getUser();
        $body = $request->getContent();
        $bodyDecode = json_decode($body, true);
        if (!isset($bodyDecode['iduser'])) {
            return new JsonResponse([
                'code' => 400,
                'error' => 'The iduser field is not in your request.'
            ], 400);
        } elseif (isset($bodyDecode['iduser']) && $bodyDecode['iduser'] == "" || $bodyDecode['iduser'] == null) {
            return new JsonResponse([
                'code' => 400,
                'error' => 'The iduser field is empty.'
            ], 400);
        }
        $userObj = $customerRepository->findOneBy(['idUser' =>$getuser,'id'=>$id]);
        if ($userObj != null){
                $customerRepository->remove($customer);
            
                return new Response('', Response::HTTP_OK);
        } else {
            return new JsonResponse([
                'code' => 404,
                'error' => 'The iduser field is incorrect.'
            ], 404);
        }       
    }
}   
