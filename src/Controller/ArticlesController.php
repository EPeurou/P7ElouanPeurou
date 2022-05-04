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

/**
 * @Route("/articles")
 */
class ArticlesController extends AbstractController
{
    /**
     * @Route("/show/{id}", name="article_show")
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

    // /**
    //  * @Route("/create", name="article_create", methods={"POST"})
    //  */
    // public function createAction(Request $request)
    // {
    //     // dd("ok");
    //     $data = $request->getContent();
    //     $article = $this->get('jms_serializer')->deserialize($data, 'App\Entity\Article', 'json');

    //     $em = $this->getDoctrine()->getManager();
    //     $em->persist($article);
    //     $em->flush();

    //     return new Response('', Response::HTTP_CREATED);
    // }

    /**
     * @Route("/", name="app_articles_index", methods={"GET"})
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
