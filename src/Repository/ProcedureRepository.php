<?php

namespace App\Repository;

use App\Entity\Procedure;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Procedure|null find($id, $lockMode = null, $lockVersion = null)
 * @method Procedure|null findOneBy(array $criteria, array $orderBy = null)
 * @method Procedure[]    findAll()
 * @method Procedure[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProcedureRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Procedure::class);
    }

    public function save(Procedure $procedure): void
    {
        $this->_em->persist($procedure);
        $this->_em->flush();
    }
}
