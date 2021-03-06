<?php

namespace Sdz\BlogBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Competence
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Sdz\BlogBundle\Entity\CompetenceRepository")
 */
class Competence
{
    /**
     * @ORM\OneToMany(targetEntity="Sdz\BlogBundle\Entity\ArticleCompetence", mappedBy="competence")
     */
    private $articleCompetence;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="nom", type="string", length=255)
     */
    private $nom;

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set nom
     *
     * @param string $nom
     * @return Competence
     */
    public function setNom($nom)
    {
        $this->nom = $nom;

        return $this;
    }

    /**
     * Get nom
     *
     * @return string 
     */
    public function getNom()
    {
        return $this->nom;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->articleCompetence = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add articleCompetence
     *
     * @param \Sdz\BlogBundle\Entity\ArticleCompetence $articleCompetence
     * @return Competence
     */
    public function addArticleCompetence(\Sdz\BlogBundle\Entity\ArticleCompetence $articleCompetence)
    {
        $this->articleCompetence[] = $articleCompetence;
    }

    /**
     * Remove articleCompetence
     *
     * @param \Sdz\BlogBundle\Entity\ArticleCompetence $articleCompetence
     */
    public function removeArticleCompetence(\Sdz\BlogBundle\Entity\ArticleCompetence $articleCompetence)
    {
        $this->articleCompetence->removeElement($articleCompetence);
    }

    /**
     * Get articleCompetence
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getArticleCompetence()
    {
        return $this->articleCompetence;
    }
}
