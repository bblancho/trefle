<?php

namespace App\Repository;

use App\Entity\Recette;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Security;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @extends ServiceEntityRepository<Recette>
 *
 * @method Recette|null find($id, $lockMode = null, $lockVersion = null)
 * @method Recette|null findOneBy(array $criteria, array $orderBy = null)
 * @method Recette[]    findAll()
 * @method Recette[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RecetteRepository extends ServiceEntityRepository
{
    private $security;

    public function __construct(ManagerRegistry $registry, Security $security)
    {
        $this->security = $security;
        parent::__construct($registry, Recette::class);
    }

    public function save(Recette $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Recette $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findByLastRecette(): ?array
    {
        $user = $this->security->getUser() ;

        return $this->findBy( ["user" => $user ], ['createdAt' => 'DESC'] ) ;
    }

    /**
     * This method display all recettes based on number of recettes
     * 
     * @param integer $nbRecettes
     * 
     * @return array
     */
    public function findByRecettePublique( ?int $nbRecettes ): ?array
    {
        $queryBuilder = $this->createQueryBuilder('r')
            ->Where('r.isPublique = 1')
            ->orderBy('r.createdAt', 'DESC') ;
        
        if( $nbRecettes !== 0 || $nbRecettes !== null){
            $queryBuilder->setMaxResults( $nbRecettes ) ;
        }

        return $queryBuilder ->getQuery()
            ->getResult()
        ;
    }

}
