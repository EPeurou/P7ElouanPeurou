<?php

namespace App\Controller;

use App\Entity\Articles;
use App\Form\ArticlesType;
use App\Repository\ArticlesRepository;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use JMS\Serializer\SerializerInterface;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use OpenApi\Annotations as OA;

/**
 * @Route("/articles")
 */
class ArticlesController extends AbstractController
{
    /**
     * @Route("/show/{id}", name="article_show",methods={"GET"})
     * @OA\Response(
     *     response=200,
     *     description="Return a single article",
     *     @OA\JsonContent(
     *        type="array",
     *        @OA\Items(ref=@Model(type=Articles::class, groups={"full"})),
     *        example={
     *        "id": 1,
     *         "brand": "apple",
     *         "model": "iphone 8",
     *         "price": 1200.0,
     *         "links": "all links to other routes for this article"        
     *        }
     *     )
     *     
     * )
     * @OA\Tag(name="Article")
     * @Security(name="Bearer")
     */
    public function showAction(Articles $article,SerializerInterface $serializerInterface,CacheInterface $cache)
    {
        // $serializer = new Serializer(array(new ObjectNormalizer()), array(new JsonEncoder()));
        // dd($strid);
        $articlesId = $article->getId();
        $data = $serializerInterface->serialize($article, 'json');
        $ToCache = $cache->get("data_articles_show".$articlesId,function(ItemInterface $item) use($data){
            $item->expiresAfter(3600);
            // return $data;
            return $data;
        });
        
        // $data = $serializer->serialize($article, "json");
        $response = new Response($data);
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }

    /**
     * @Route("/", name="app_articles_index", methods={"GET"})
     * @OA\Response(
     *     response=200,
     *     description="Returns the list of articles",
     *     @OA\JsonContent(
     *        type="array",
     *        @OA\Items(ref=@Model(type=Articles::class, groups={"full"})),
     *        example={
     *         "id": 1,
     *         "brand": "apple",
     *         "model": "iphone 28",
     *         "price": 1200.0,
     *         "links": "all links to other routes for this article"        
     *        }
     *     )
     *     
     * )
     * @OA\Tag(name="Article")
     * @Security(name="Bearer")
     */
    public function index(ArticlesRepository $articlesRepository,SerializerInterface $serializerInterface,CacheInterface $cache): Response
    {
        $article = $articlesRepository->findAll();
        $strid = "";
        foreach ($article as $singleArticle){
            $ids = $singleArticle->getId();
            $strid .= $ids;
        }
        // dd($strid);
        $data = $serializerInterface->serialize($article, 'json');
        $ToCache = $cache->get("data_index".$strid,function(ItemInterface $item) use($data){
            $item->expiresAfter(3600);
            // return $data;
            return $data;
        });
        // $serializer = new Serializer(array(new ObjectNormalizer()), array(new JsonEncoder()));
        // $data = $serializer->serialize($article, "json");
        
        $response = new Response($data);
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }
}
