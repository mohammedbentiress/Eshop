<?php
 
namespace App\DataFixtures;
 
use Faker\Factory;
use App\Entity\Order;
use App\Entity\Product;
use App\Entity\Category;
use App\Entity\Shipping;
use App\Entity\OrderLine;
use App\Repository\OrderRepository;
use App\Repository\OrderLineRepository;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;

class AppFixtures extends Fixture implements FixtureGroupInterface
{
 
    const NBR_PRODUCTS = 30;
    const NBR_CATEGORIES = 8;
    const NBR_SHIPPINGS = 7;
    const NBR_ORDERS = 7;
    
    /**
     *
     * @var $faker
     */
    private $faker;
 
    public function __construct()
    {
        $this->faker = Factory::create();
    }
 
    public function load(ObjectManager $manager)
    {
        $this->loadProducts($manager);
        $this->loadCategories($manager);
        $this->loadShippings($manager);
        $this->loadOrders($manager);

        $manager->flush();
    }
    
    /**
     * Create fake categories
     *
     * @param ObjectManager $manager
     * @return void
     */
    private function loadCategories(ObjectManager $manager)
    {
        for($i = 0; $i < self::NBR_CATEGORIES; $i++){
            $category = new Category();
            $category->setLabel($this->faker->sentence(5));
            for($j = 0; $j < rand(0,5); $j++)
            {
                $category->addProduct($this->getReference('product_'.rand(0,29)));
            }
            $manager->persist($category);
        }
    }
    

    /**
     * Create fake products
     *
     * @param ObjectManager $manager
     * @return void
     */
    private function loadProducts(ObjectManager $manager)
    {
        for($i = 0; $i < self::NBR_PRODUCTS; $i++){
            $product = new Product();
            $product->setLabel($this->faker->sentence(5))
                    ->setCover('https://picsum.photos/200/300')
                    ->setDescription($this->faker->paragraph(3))
                    ->setUnitPrice($this->faker->randomFloat(2,10,150))
                    ->setQuantity(rand(0,18));
            $manager->persist($product);

            $this->addReference("product_".$i,$product);
        }
    }

    /**
     * Create fake orders
     *
     * @return void
     */
    public function loadOrders(ObjectManager $manager)
    {
        for($i = 0; $i < self::NBR_SHIPPINGS; $i++)
        {
            $order = new Order();
            $order->setCreateAt(new \DateTime())
                ->setStatus(Order::ORDER_INITIATED)
                ->setShipping($this->getReference("shipping_".$i));
                for($j = 0; $j < rand(0,5);$j++)
                {
                    $orderLine = new OrderLine();
                    $orderLine->setQuantity($this->faker->numberBetween(1,10))
                            ->setProduct($this->getReference('product_'.rand(0,29)))
                            ->setCart($order);
                    $manager->persist($orderLine); 
                }   
                
            ;
            $manager->persist($order); 
        }
    }

    /**
     * Create fake shippings
     *
     * @return void
     */
    public function loadShippings(ObjectManager $manager)
    {
        for($i = 0; $i < self::NBR_SHIPPINGS; $i++)
        {
            $shipping = new Shipping();
            $shipping->setCity($this->faker->city())
                    ->setPostalCode($this->faker->postcode)
                    ->setAddress($this->faker->address)
                    ->setFirstName($this->faker->firstName)
                    ->setEmail($this->faker->email)
                    ->setMobileNumber($this->faker->phoneNumber)
                    ->setCountry($this->faker->country)
                    ->setState($this->faker->state)
                    ->setLastName($this->faker->lastName);
            $manager->persist($shipping);
            $this->addReference("shipping_".$i,$shipping);
        }
    }

    public static function getGroups(): array
    {
        return ['group1'];
    }
 
}