<?php

namespace App\Repository\Issuers;

use App\Entity\Issuers\Issuer;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Issuer>
 *
 * @method Issuer|null find($id, $lockMode = null, $lockVersion = null)
 * @method Issuer|null findOneBy(array $criteria, array $orderBy = null)
 * @method Issuer[]    findAll()
 * @method Issuer[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class IssuerRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Issuer::class);
    }

    public function add(Issuer $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Issuer $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function getByNip(string $nip): ?Issuer
    {
        return $this->createQueryBuilder('i')
            ->andWhere('i.nip = :nip')
            ->setParameter('nip', $nip)
            ->getQuery()
            ->getOneOrNullResult()
            ;
    }
}
