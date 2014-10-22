<?php

namespace Sdz\BlogBundle\Entity;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;

/**
 * ArticleRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class ArticleRepository extends EntityRepository
{
	public function findByAuteurAndDate($auteur, $annee)
	{
		$qb = $this->createQueryBuilder('a');

		$qb->where('a.auteur = :auteur')
		    ->setParameter('auteur', $auteur)
		   ->andWhere('a.date < :annee')
		    ->setParameter('annee', $annee)
		   ->orderBy('a.date', 'DESC');

		return $qb->getQuery()
		          ->getSingleResult();

	}

	public function whereCurrentYear(\Doctrine\ORM\QueryBuilder $qb)
	{
		$qb->andWhere('a.date BETWEEN :debut AND :fin')
		    ->setParameter('debut', new \DateTime(date('Y').'-01-01'))
		    ->setParameter('fin', new \DateTime(date('Y').'-01-01'));

		    return $qb;
	}

	public function getArticleAvecCommentaires($slug)
	{
		// On récupère l'article dont l'slug est $slug ainsi que
		// les commentaires associés.
		$qb = $this->createQueryBuilder('a')
		           ->leftJoin('a.commentaires', 'c')
		           ->addSelect('c')
		           ->where('a.slug = :slug')
		            ->setParameter('slug', $slug);

		return $qb->getQuery()
		          ->getResult();
	}

	public function getArtCommentsCatsCompsImg($slug){
		$qb = $this->createQueryBuilder('a')
		           ->leftJoin('a.commentaires', 'c')
		           ->addSelect('c')
		           ->leftJoin('a.image', 'i')
		           ->addSelect('i')
		           ->leftJoin('a.categories', 'ca')
		           ->addSelect('ca')
		           ->where('a.slug = :slug')
		            ->setParameter('slug', $slug);

		return $qb->getQuery()
		          ->getResult();
	}

	public function getArticleCompetences($slug)
	{
		// On fait une double jointure entre les entités Article-ArticleCompetence-Competence
		$qb = $this->createQueryBuilder('a')
		           ->leftJoin('a.articleCompetence', 'ac')
		           ->addSelect('ac')
		           ->leftJoin('ac.competence', 'c')
		           ->addSelect('c')
		           ->where('a.slug = :slug')
		            ->setParameter('slug', $slug);

		return $qb->getQuery()
		          ->getResult();
	}

	public function getAvecCategories($noms_cats)
	{
		$qb = $this->createQueryBuilder('a')
		           ->leftJoin('a.categories', 'c')
		           ->addSelect('c')
		           ->where($qb->expr()->in('c.nom', $noms_cats)); // Puis on filtre sur le nom des catégories à l'aide d'un IN

		return $qb->getQuery()
		          ->getResult();
	}	


	public function myFind($auteur)
	{
		$qb = $this->createQueryBuilder('a');

		// On peut ajouter ce qu'on veut avant
		$qb->where('a.auteur = :auteur')
		    ->setParameter('auteur', $auteur);

		// On applique notre condition
		$qb = $this->whereCurrentYear($qb);

		// On peut ajouter ce qu'on veut après
		$qb->orderBy('a.date', 'DESC');

		return $qb->getQuery()
		          ->getResult();

	}

	public function findAllSortByDate($nombreParPage, $page)
	{	// deux manières de recupérer les articles triés de manière descendante
		// $result = $this->createQueryBuilder('a')
		//                ->orderBy('a.date', 'DESC')
		//                ->getQuery()
		//                ->getResult();
		// return $result;

		// Or
		return $this->findBy(array(), array('date' => 'DESC'));
	}

	// Les paramètres pour faire un pagination
	// Nombre total d'articles
	// Nombre d'articles à afficher par page
	// La page courante
	public function getArticles($nombreParPage, $page)
	{
		if ($page < 1) {
		// On déplace la vérification du numéro de la page dans la méthode
			throw new \InvalidArgumentException('L\'argument $page ne peut être inférieur à 1 (valeur : "'.$page.'").');
		}

		// La construction de la requête reste inchangée
		$query = $this->createQueryBuilder('a')
		              ->leftJoin('a.image', 'i')
		              ->addSelect('i')
		              ->leftJoin('a.categories', 'c')
		              ->addSelect('c')
		              ->orderBy('a.date', 'desc')
		              ->getQuery();

		// On définit l'article à partir duquel commencer la liste
		$query->setFirstResult(($page - 1) * $nombreParPage)
		// Ainsi que le nombre d'articles à afficher
		      ->setMaxResults($nombreParPage);

		return new Paginator($query);
	}
	public function myFindAllDQL()
	{
		$query = $this->_em->createQuery('SELECT a FROM SdzBlogBundle:Article a');

		$resultat = $query->getResult();

		return $resultat;
	}

	public function getSelectList()
	{
		$qb = $this->createQueryBuilder('a')
		           ->where('a.publication = 1');

		           return $qb; // On retourne le QueryBuilder, et non la Query
	}

}
