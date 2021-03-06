<?php

namespace Sdz\BlogBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
//use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Common\Collections\ArrayCollection;

use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;

use Sdz\BlogBundle\Entity\Article;
use Sdz\BlogBundle\Entity\Image;
use Sdz\BlogBundle\Entity\Commentaire;
use Sdz\BlogBundle\Entity\Categorie;
use Sdz\BlogBundle\Entity\ArticleCompetence;

use Sdz\BlogBundle\Form\ArticleType;
use Sdz\BlogBundle\Form\ArticleEditType;

class BlogController extends Controller
{
	public function articlesByCategorieAction($page, $cat)
	{
		$articles = $this->getDoctrine()
		                 ->getManager()
		                 ->getRepository('SdzBlogBundle:Article')
		                 ->getArticlesByCategory(2, $page, $cat);
		
		return $this->render('SdzBlogBundle:Blog:categorie.html.twig', array('articles' => $articles,
			                                                                       'page' => $page,
			                                                                       'cat' => $cat,
			                                                                       'accueil' => true,
			                                                                       'nombrePage' => ceil(count($articles)/2)));

	}

	public function articlesByPageAction(Request $request, $page)
	{

		//$request->setLocale('en_EN');

		/*$serializer = $this->get('jms_serializer');



		 $encoders = array(new XmlEncoder(), new JsonEncoder());
		 $normalizers = array(new GetSetMethodNormalizer());

		 $serializer = new Serializer($normalizers, $encoders);*/

		 $accueil = $request->query->get('accueil');
		//$request = $this->getRequest();
		if ($request->isXmlHttpRequest()) {
			// On récupère tous les articles de la base
		 //$page = $request->query->get('page');

			//$p = $page;
			$data = $this->getDoctrine()
			                 ->getManager()
			                 ->getRepository('SdzBlogBundle:Article')
			                 ->getArticlesAjax(2, $page);

			$articles = $data->getIterator();

			return $this->render('SdzBlogBundle:Blog:articles-ajax.html.twig', array('articles'   => $articles,
			                                                             'accueil'    => $accueil,
			                                                             'page'       => $page,
			                                                             'nombrePage' => ceil(count($articles)/2)
			                                                            ));
		}
			// $response = array();

			 // foreach ($data->getIterator() as $key => $article) {
    //      		$serializer->serialize($article, 'json');
    //      		var_dump($serializer);
			 //die('ok');

			// $response->setContent(json_encode($paginator->getIterator()));
			// $arts = array();
			// foreach ($paginator->getIterator() as $key => $article) {
			// 	$arts[$key] = new JsonResponse($article);
			// 	$response->setData(array('$key' => $article));
			// }

			
			//$response->setData(array('articles' => $articles[0], 'page' => $page, 'nombrePage' => ceil(count($articles)/2)));

			//$response = $arts;

			return $response;

		//}
				
		}


	public function indexAction($page) {
		
		// On récupère tous les articles de la base
		$articles = $this->getDoctrine()
		                 ->getManager()
		                 ->getRepository('SdzBlogBundle:Article')
		                 ->getArticles(2, $page);

		return $this->render('SdzBlogBundle:Blog:index.html.twig', array('articles'   => $articles,
			                                                             'page'       => $page,
			                                                             'nombrePage' => ceil(count($articles)/2)                         
			                                                            ));
	}

	public function voirAction($slug)
	{
		// On récupère le repository
		$em = $this->getDoctrine()
				   ->getManager();
				   //->find('SdzBlogBundle:Article', $id);

		// On récupère l'entité correspondante à l'id $id
		// $liste_artCommentsCatsImg = $em->getRepository('SdzBlogBundle:Article')->getArticleAvecCommentaires($id);
		$liste_artCommentsCatsImg = $em->getRepository('SdzBlogBundle:Article')->getArtCommentsCatsCompsImg($slug);
		
		// On recupère en résultat un array(objet) de type Article dans notre cas un seul résultat
		// d'où l'indice 0 pour récupèrer le premier élément du tableau
		$article = $liste_artCommentsCatsImg[0];

		if ($article === null) {
			throw $this->createNotFoundException('Article[slug='.$slug.'] inexistant');
		}

		// On récupère la liste des commentaires
		// la méthode utilisée recupère tous les commentaires de la table 'commentaire'
		$liste_commentaires = $article->getCommentaires();

		// l'equivalent d'un var_dump.
		//exit(\Doctrine\Common\Util\Debug::dump($liste_commentaires));

		// Récupère les articleCompétence d'un article, d'slug $article->getslug()
		// $liste_articleCompetence = $em->getRepository('SdzBlogBundle:ArticleCompetence')->findByArticle($article->getslug());
		$articleCompetences = $em->getRepository('SdzBlogBundle:Article')->getArticleCompetences($slug);
		$liste_articleCompetence = $articleCompetences[0]->getArticleCompetence();


		return $this->render('SdzBlogBundle:Blog:voir.html.twig', array(
							'article'            => $article,
							'liste_commentaires' => $liste_commentaires,
							'liste_articleCompetence' => $liste_articleCompetence));
	}

	public function ajouterAction() {
		$article = new Article;

		$form = $this->createForm(new ArticleType(), $article);

		// On récupère la requête 
		$request = $this->get('request');

		if( $request->getMethod() === 'POST') {
			// On fait le lien Requête <-> Formulaire
			// A partir de maintenant, la variable $article contient les valeurs
			// entrées dans le formulaire par le visiteur
			$form->bind($request);

			// On vérifie que les valeurs entrées sont correctes
			// (Nous verrons la validation des objets en détail dans le prochain chapitre)
			if ($form->isValid()) {

				$em = $this->getDoctrine()->getManager();
				$em->persist($article);

				var_dump($article->getImage()->getUrl().' et '.$article->getImage()->getAlt());
				
				$em->flush();

				// Ici, on s'occupera de la création et de la gestion du formulaire
				$this->get('session')->getFlashBag()->add('notice', 'Article bien enregistré');
				
				// contenu saisi par l'utilisateur
				$contenu = 'bla@hotmail.fr, bli@hotmail.com, johndoe@gmail.com';

				// On récupère le service antispam
				$antispam = $this->container->get('sdz_blog.antispam');

				if($antispam->isSpam($contenu)) {
					//throw new Exception('Votre message a été détecté comme spam !');
				}
				return $this->redirect($this->generateUrl('sdzblog_voir', array('slug' => $article->getSlug())));
			}
		}
		
		// A ce stade : 
		// - Soit la requête est de type GET, donc le visiteur vient d'arriver
		// sur la page et veut voir le formulaire
		// - Soit la requête est de type POST, mais le formulaire n'est pas valide, 
		// donc on l'affiche de nouveau


		return $this->render('SdzBlogBundle:Blog:ajouter.html.twig', array('form' => $form->createView()));
	}


	public function modifierAction(Article $article) {
		// On utilise le ArticleEditType
		$form = $this->createForm(new ArticleEditType(), $article);


		$request = $this->getRequest();

		if ($request->getMethod() == 'POST') {
			$form->bind($request);

			if ($form->isValid()) {
				// On récupère l'EntityManager
				$em = $this->getDoctrine()
				           ->getManager();
				$em->persist($article);
				$em->flush();

				// On définit un message flash
				$this->get('session')->getFlashBag()->add('info', 'Article bien modifié');

				return $this->redirect($this->generateUrl('sdzblog_voir', array('slug' => $article->getSlug())));
			}
		}

		return $this->render('SdzBlogBundle:Blog:modifier.html.twig', array('form'    => $form->createView(),
			                                                                'article' => $article));
	}

	public function supprimerAction(Article $article) {
		// On crée un formulaire vide, qui ne contiendra que le champ CSRF
		// Cela permet de protéger la suppression d'article contre cette faille
		$form = $this->createFormBuilder()->getForm();

		$request = $this->getRequest();

		if($request->getMethod('POST')) {
			$form->bind($request);

			if ($form->isValid()) {
				// On supprime l'article
				$em = $this->getDoctrine()
			    	       ->getManager();
			    $em->remove($article);
			    $em->flush();

			    // On définit un message flash
			    $this->get('session')->getFlashBag()->add('info', 'Article supprimé.');

			    return $this->redirect($this->generateUrl('sdzblog_accueil'));
			}
		}

		return $this->render('SdzBlogBundle:Blog:supprimer.html.twig', array('article' => $article,
			                                                                 'form'    => $form->createView()
			                                                                ));
	}

	// Méthode du controller appelé par le layout général
	public function menuAction($nombre)
	{
		$em = $this->getDoctrine()
		           ->getManager();
		// Récupère une liste d'article de taille $nombre et dans l'ordre décroissant
		$liste = $em->getRepository('SdzBlogBundle:Article')->findBy(array(),                 // Pas de critères
		 															 array('date' => 'desc'), // On trie par date décroissante
		 															 $nombre,                 // On sélectionne $nombre articles
		 															 0                        // A partir du premier
		 															);

	    return $this->render('SdzBlogBundle:Blog:menu.html.twig', array('liste_articles' => $liste));
	    // C'est ici tout l'intérêt : le contrôleur passe les variables nécessaire au template !
	}

	public function menuCategoriesAction()
	{
		$em = $this->getDoctrine()
		           ->getManager();

		$listeCats = $em->getRepository('SdzBlogBundle:Article')->getArticlesGroupByCategory();

		//var_dump($listeCats);
		//die();
		return $this->render('SdzBlogBundle:Blog:menu-categories.html.twig', array('liste_cats' => $listeCats));
	}

}
