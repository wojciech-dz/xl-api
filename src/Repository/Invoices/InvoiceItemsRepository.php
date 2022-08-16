<?php

namespace App\Repository\Invoices;

use App\Entity\Invoices\Invoice;
use App\Entity\Invoices\InvoiceItems;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<InvoiceItems>
 *
 * @method InvoiceItems|null find($id, $lockMode = null, $lockVersion = null)
 * @method InvoiceItems|null findOneBy(array $criteria, array $orderBy = null)
 * @method InvoiceItems[]    findAll()
 * @method InvoiceItems[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class InvoiceItemsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, InvoiceItems::class);
    }

    public function add(InvoiceItems $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(InvoiceItems $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function getByExternalAndPosition(string $invoiceId, int $position): ?InvoiceItems
    {
        return $this->createQueryBuilder('i')
            ->andWhere('i.invoiceId = :invoiceId')
            ->andWhere('i.position = :position')
            ->setParameter('invoiceId', $invoiceId)
            ->setParameter('position', $position)
            ->getQuery()
            ->getOneOrNullResult()
            ;
    }

    public function getByInvoice(Invoice $invoice): ?array
    {
        return $this->createQueryBuilder('i')
            ->andWhere('i.invoice = :invoice')
            ->setParameter('invoice', $invoice)
            ->getQuery()
            ->getResult()
            ;
    }

    public function getByInvoiceAndPosition(Invoice $invoice, int $position): ?InvoiceItems
    {
        return $this->createQueryBuilder('i')
            ->andWhere('i.invoice = :invoice')
            ->andWhere('i.position = :position')
            ->setParameter('invoice', $invoice)
            ->setParameter('position', $position)
            ->getQuery()
            ->getOneOrNullResult()
            ;
    }
}
