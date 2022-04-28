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
    // public $serializer = new Serializer();

    // public function __constructor($serializer)
    // {
    //     $this->serializer = $serializer;
    // }

    /**
     * @Route("/show/{id}", name="article_show")
     */
    public function showAction(Articles $article,SerializerInterface $serializerInterface,CacheInterface $cache)
    {
        // $serializer = new Serializer(array(new ObjectNormalizer()), array(new JsonEncoder()));
        // dd($strid);
        $articlesId = $article->getId();
        $data = $serializerInterface->serialize($article, 'json');
        $ToCache = $cache->get("data_articles_show".$articlesId,function() use($data){
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

    // /**
    //  * @Route("/new", name="app_articles_new", methods={"GET", "POST"})
    //  */
    // public function new(Request $request, ArticlesRepository $articlesRepository): Response
    // {
    //     $data = $request->getContent();
    //     $article = new Articles();
    //     $serializer = new Serializer(array(new ObjectNormalizer()), array(new JsonEncoder()));
    //     $articles = $serializer->deserialize($data, "App\Entity\Articles", "json");
    //     $articlesRepository->add($articles);

    //     return new Response('', Response::HTTP_CREATED);
    // }

    /**
     * @Route("/{id}", name="app_articles_show", methods={"GET"})
     */
    public function show(Articles $article): Response
    {
        return $this->render('articles/show.html.twig', [
            'article' => $article,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="app_articles_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, Articles $article, ArticlesRepository $articlesRepository): Response
    {
        $form = $this->createForm(ArticlesType::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $articlesRepository->add($article);
            return $this->redirectToRoute('app_articles_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('articles/edit.html.twig', [
            'article' => $article,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="app_articles_delete", methods={"POST"})
     */
    public function delete(Request $request, Articles $article, ArticlesRepository $articlesRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$article->getId(), $request->request->get('_token'))) {
            $articlesRepository->remove($article);
        }

        return $this->redirectToRoute('app_articles_index', [], Response::HTTP_SEE_OTHER);
    }
}
