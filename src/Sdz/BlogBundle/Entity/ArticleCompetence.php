<?php

namespace Sdz\BlogBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
* @ORM\Entity
*/
class ArticleCompetence
{
	/**
	 * @ORM\Id
	 * @ORM\ManyToOne(targetEntity="Sdz\BlogBundle\Entity\Article", inversedBy="articleCompetence")
	 */
	private $article;

	/**
	 * @ORM\Id
	 * @ORM\ManyToOne(targetEntity="Sdz\BlogBundle\Entity\Competence", inversedBy="articleCompetence")
	 */
	private $competence;

	/**
	 * @ORM\Column()
	 */
	private $niveau; // attribut de la relation "niveau"
	
	// ArticleCompetence est l'entité propriétaire

    /**
     * Set niveau
     *
     * @param string $niveau
     * @return ArticleCompetence
     */
    public function setNiveau($niveau)
    {
        $this->niveau = $niveau;
    }

    /**
     * Get niveau
     *
     * @return string 
     */
    public function getNiveau()
    {
        return $this->niveau;
    }

    /**
     * Set article
     *
     * @param \Sdz\BlogBundle\Entity\Article $article
     * @return ArticleCompetence
     */
    public function setArticle(\Sdz\BlogBundle\Entity\Article $article)
    {
        $this->article = $article;
    }

    /**
     * Get article
     *
     * @return \Sdz\BlogBundle\Entity\Article 
     */
    public function getArticle()
    {
        return $this->article;
    }

    /**
     * Set competence
     *
     * @param \Sdz\BlogBundle\Entity\Competence $competence
     * @return ArticleCompetence
     */
    public function setCompetence(\Sdz\BlogBundle\Entity\Competence $competence)
    {
        $this->competence = $competence;
    }

    /**
     * Get competence
     *
     * @return \Sdz\BlogBundle\Entity\Competence 
     */
    public function getCompetence()
    {
        return $this->competence;
    }
}
