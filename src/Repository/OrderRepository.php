<?php

namespace App\Repository;

use App\Entity\Order;
use App\Entity\Shipping;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Order|null find($id, $lockMode = null, $lockVersion = null)
 * @method Order|null findOneBy(array $criteria, array $orderBy = null)
 * @method Order[]    findAll()
 * @method Order[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OrderRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Order::class);
    }

    // /**
    //  * @return Order[] Returns an array of Order objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('o.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Order
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */

    /**
     * get all orders filtred by name 
     *
     * @param string $name
     * @return void
     */
    public function getOrdersByName(string $name)
    {
        // $qb = $this->createQueryBuilder('o');


        // return $qb->where('shipping.firstName LIKE :searchTerm OR shipping.lastName LIKE :searchTerm')->innerJoin('o.shipping','shipping')
        //         ->setParameter('searchTerm',$name)
        //         ->getQuery()->getResult();
        $qb = $this->createQueryBuilder('o');
 
        $expr = $qb->expr();
 
        return  $qb->Join('o.shipping','s')
                ->where($expr->like('s.firstName',$expr->literal('%'.$name.'%')))
                ->orWhere($expr->like('s.lastName',$expr->literal('%'.$name.'%')))
                ->getQuery()
                ->getResult()
        ;
    }
}
