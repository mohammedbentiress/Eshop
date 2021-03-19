<?php

namespace App\Repository;

use App\Entity\Product;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Product|null find($id, $lockMode = null, $lockVersion = null)
 * @method Product|null findOneBy(array $criteria, array $orderBy = null)
 * @method Product[]    findAll()
 * @method Product[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProductRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Product::class);
    }

    // /**
    //  * @return Product[] Returns an array of Product objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Product
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
     
    /**
     * Get all related products to a given product based on categories
     *
     * @param Product $product
     * @return array
     */
    public function getRelatedProducts(Product $product):array
    {
        $stmt = $this->getEntityManager()
                    ->getConnection()
                    ->prepare('SELECT * FROM product 
                    WHERE id IN
                    (
                        SELECT product_id  FROM category_product
                        WHERE category_id IN
                        (
                            SELECT category_id FROM category_product WHERE product_id = :p))
                            AND id <> :p'
                    );
        $stmt->execute([
            'p'=>$product->getId(),
        ]);
        return $stmt->fetchAllAssociative();
    }

    /**
     *  Gets all products with quantity criteria.
     *
     * @param integer $min the minimum quantity. -1 if you wish to disable the criteria
     * @param integer $max the maximum quantity. -1 if you wish to disable the criteria
     * 
     * @return array<mixed>
     */
    public function getProductsWithQuantity(int $min = -1, int $max = -1)
    {
        $qb = $this->createQueryBuilder('p');

        if ($min > $max) {
            $tmp = $max;
            $max = $min;
            $min = $tmp;
        }
        if ($min > 0) {
            $qb->where('p.quantity > :min')
                ->setParameter('min', $min);
        }

        if ($max > 0) {
            $qb->where('p.quantity < :max')
                ->setParameter('max', $max);
        }

        return $qb
            ->getQuery()
            ->getResult();
    }
    
    /**
     * Searches products bu given criteria
     *
     * @param array $criteria
     * 
     * @return array<mixed> the resulting data.
     */
    public function search(array $criteria)
    {
        
        $qb = $this->createQueryBuilder('p');
        $expr = $qb->expr();

        return $qb->where($expr->like('p.label', $expr->literal('%'.$criteria['term'].'%')))
            ->getQuery()
            ->getResult();
    }
}
