# Yii2 cart component

Yii2 cart component for eCommerce web applications

## Installation

Via Composer
```
composer require vsevolod-ryzhov/yii2-cart
```

## Usage

Use CombinedStorage if you want to use all provided functionality.

1. To use session as cart storage:

```php
// setup Cart component using included SessionStorage class
class Init implements BootstrapInterface
{
    public function bootstrap($app): void
    {
        $container = Yii::$container;

        $container->setSingleton(Cart::class, [], [
            new SessionStorage('cart', $app->session), new BaseCost()
        ]);
    }
}

// Product class must implement ProductInterface
class Product implements ProductInterface
{
    public function getId()
    {
        return $this->id;
    }

    public function getPrice()
    {
        return $this->price;
    }
}

// inject component in your controller
class CartController extends Controller
{
    /**
     * @var Cart
     */
    private $cart;

    public function __construct($id, $module, Cart $cart, $config = [])
    {
        parent::__construct($id, $module, $config);
        $this->cart = $cart;
    }

    public function actionAdd($id, $count = 1)
    {
        $product = Product::findOne($id);
        $this->cart->add($product,  $count);
    }
}
```

2. To use database storage (for authenticated users) and session storage (for guests):

```php
// create converter class which extends included AbstractProductConverter
// this class must convert your Product model class to CartItem class
// this class is used in Init class on DatabaseStorage initialization
class ProductToCartConverter extends AbstractProductConverter
{
    public function convertProductToCartItem($product, int $quantity): CartItem
    {
        /* @var $product Product */
        return new CartItem($product, $quantity);
    }
}
// setup Cart component using included SessionStorage class
class Init implements BootstrapInterface
{
    public function bootstrap($app): void
    {
        $container = Yii::$container;
        if ($app->user->isGuest) {
            $container->setSingleton(Cart::class, [], [
                new SessionStorage('cart', $app->session), new BaseCost()
            ]);
        } else {
            // you can specify database options for DatabaseStorage class (cartItemsTable & etc)
            $container->setSingleton(Cart::class, [], [
                new DatabaseStorage($app->user->id, $app->db, new ProductsQuery(Product::class), new ProductToCartConverter), new BaseCost()
            ]);
        }
    }
}

// Query class for Product model must extends included AbstractProductQuery
class ProductsQuery extends AbstractProductQuery
{
    /**
     * @return ProductsQuery
     */
    public function canBuy(): ActiveQuery
    {
        // query product which user can buy
        return $this->active()->available();
    }
}
```

3. To use CookieStorage:

```php
// use ProductToCartConverter from previews sample
class Init implements BootstrapInterface
{
    $container->setSingleton(Cart::class, [], [
        new CookieStorage(
            'cart',
            3600,
            $app->request->cookies,
            $app->response->cookies,
            new ProductsQuery(Product::class),
            new ProductToCartConverter
        ),
        new BaseCost()
    ]);
}
```

4. CombinedStorage (CookieStorage + DatabaseStorage):

```php
class Init implements BootstrapInterface
{
    public function bootstrap($app): void
    {
        $container = Yii::$container;
        $container->setSingleton(Cart::class, [], [
            new CombinedStorage(
                $app->user,
                $app->db,
                'cart',
                3600,
                $app->request->cookies,
                $app->response->cookies,
                new ProductsQuery(Product::class),
                new ProductToCartConverter
            ),
            new BaseCost()
        ]);
    }
}
```