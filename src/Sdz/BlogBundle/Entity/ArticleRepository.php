<?php

namespace Sdz\BlogBundle\Entity;

use Doctrine\ORM\EntityRepository;

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

	public function getArticleAvecCommentaires($id)
	{
		// On récupère l'article dont l'id est $id ainsi que
		// les commentaires associés.
		$qb = $this->createQueryBuilder('a')
		           ->leftJoin('a.commentaires', 'c')
		           ->addSelect('c')
		           ->where('a.id = :id')
		            ->setParameter('id', $id);

		return $qb->getQuery()
		          ->getResult();
	}

	public function getArtWCommentsWCatsWImg($id){
		$qb = $this->createQueryBuilder('a')
		           ->leftJoin('a.commentaires', 'c')
		           ->addSelect('c')
		           ->leftJoin('a.image', 'i')
		           ->addSelect('i')
		           ->leftJoin('a.categories', 'ca')
		           ->addSelect('ca')
		           ->where('a.id = :id')
		            ->setParameter('id', $id);

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

	public function myFindAllDQL()
	{
		$query = $this->_em->createQuery('SELECT a FROM SdzBlogBundle:Article a');

		$resultat = $query->getResult();

		return $resultat;
	}
}
