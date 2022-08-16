<?php

namespace App\Repository\Invoices;

use App\Entity\Invoices\Invoice;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Invoice>
 *
 * @method Invoice|null find($id, $lockMode = null, $lockVersion = null)
 * @method Invoice|null findOneBy(array $criteria, array $orderBy = null)
 * @method Invoice[]    findAll()
 * @method Invoice[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class InvoiceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Invoice::class);
    }

    public function add(Invoice $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Invoice $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function getByExternal(int $external): ?Invoice
    {
        return $this->createQueryBuilder('i')
            ->andWhere('i.externalId = :external')
            ->setParameter('external', $external)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    public function getOneDose(int $page = 1, int $limit = 16)
    {
        $results = $this
            ->createQueryBuilder('i')
            ->select('i.id', 'i.number', 'i.externalId', 'i.saleDate', 'i.issueDate', 'i.issuerName',
                'i.currency', 'i.netValue', 'i.grossValue', 'i.vat');

        $paginator = $this->paginate($results, $page, $limit);
        $maxPages = ceil($paginator->count() / $limit);

        return [$maxPages, $paginator];
    }

    public function paginate($dql, $page = 1, $limit = 16): Paginator
    {
        $paginator = new Paginator($dql);
        $paginator->setUseOutputWalkers(false);
        $paginator->getQuery()
            ->setFirstResult($limit * ($page - 1))
            ->setMaxResults($limit);

        return $paginator;
    }
}
