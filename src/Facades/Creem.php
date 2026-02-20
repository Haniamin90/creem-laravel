<?php

namespace Creem\Laravel\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static array createCheckout(string $productId, array $params = [])
 * @method static array getCheckout(string $checkoutId)
 * @method static array createProduct(array $params)
 * @method static array getProduct(string $productId)
 * @method static array searchProducts(array $params = [])
 * @method static array getCustomer(array $params)
 * @method static array listCustomers(array $params = [])
 * @method static array customerBillingPortal(string $customerId)
 * @method static array getSubscription(string $subscriptionId)
 * @method static array searchSubscriptions(array $params = [])
 * @method static array updateSubscription(string $subscriptionId, array $params)
 * @method static array upgradeSubscription(string $subscriptionId, string $newProductId)
 * @method static array cancelSubscription(string $subscriptionId, string $mode = 'scheduled')
 * @method static array pauseSubscription(string $subscriptionId)
 * @method static array resumeSubscription(string $subscriptionId)
 * @method static array getTransaction(string $transactionId)
 * @method static array searchTransactions(array $params = [])
 * @method static array activateLicense(string $key, string $instanceName)
 * @method static array validateLicense(string $key, string $instanceId)
 * @method static array deactivateLicense(string $key, string $instanceId)
 * @method static array createDiscount(array $params)
 * @method static array getDiscount(array $params)
 * @method static array deleteDiscount(string $discountId)
 * @method static \Creem\Laravel\CreemClient client()
 *
 * @see \Creem\Laravel\Creem
 */
class Creem extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \Creem\Laravel\Creem::class;
    }
}
