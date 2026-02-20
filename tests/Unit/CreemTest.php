<?php

namespace Creem\Laravel\Tests\Unit;

use Creem\Laravel\Creem;
use Creem\Laravel\CreemClient;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;

class CreemTest extends TestCase
{
    protected function createCreem(array $responses): Creem
    {
        $mock = new MockHandler($responses);
        $handlerStack = HandlerStack::create($mock);
        $httpClient = new Client(['handler' => $handlerStack]);

        $client = new CreemClient('creem_test_fake_key');
        $client->setHttpClient($httpClient);

        return new Creem($client);
    }

    // ---- Checkout Tests ----

    public function test_create_checkout(): void
    {
        $creem = $this->createCreem([
            new Response(200, [], json_encode([
                'checkout_url' => 'https://checkout.creem.io/abc',
                'checkout_id' => 'chk_123',
            ])),
        ]);

        $result = $creem->createCheckout('prod_123', [
            'success_url' => 'https://example.com/success',
        ]);

        $this->assertEquals('https://checkout.creem.io/abc', $result['checkout_url']);
        $this->assertEquals('chk_123', $result['checkout_id']);
    }

    public function test_get_checkout(): void
    {
        $creem = $this->createCreem([
            new Response(200, [], json_encode(['id' => 'chk_123', 'status' => 'completed'])),
        ]);

        $result = $creem->getCheckout('chk_123');
        $this->assertEquals('chk_123', $result['id']);
    }

    // ---- Product Tests ----

    public function test_create_product(): void
    {
        $creem = $this->createCreem([
            new Response(200, [], json_encode([
                'id' => 'prod_new',
                'name' => 'Premium Plan',
                'price' => 2999,
            ])),
        ]);

        $result = $creem->createProduct([
            'name' => 'Premium Plan',
            'price' => 2999,
            'currency' => 'USD',
            'billing_type' => 'recurring',
            'billing_period' => 'every-month',
        ]);

        $this->assertEquals('prod_new', $result['id']);
        $this->assertEquals(2999, $result['price']);
    }

    public function test_get_product(): void
    {
        $creem = $this->createCreem([
            new Response(200, [], json_encode(['id' => 'prod_123', 'name' => 'Basic'])),
        ]);

        $result = $creem->getProduct('prod_123');
        $this->assertEquals('Basic', $result['name']);
    }

    public function test_search_products(): void
    {
        $creem = $this->createCreem([
            new Response(200, [], json_encode([
                'items' => [
                    ['id' => 'prod_1', 'name' => 'Basic'],
                    ['id' => 'prod_2', 'name' => 'Pro'],
                ],
            ])),
        ]);

        $result = $creem->searchProducts(['limit' => 10]);
        $this->assertCount(2, $result['items']);
    }

    // ---- Customer Tests ----

    public function test_get_customer(): void
    {
        $creem = $this->createCreem([
            new Response(200, [], json_encode(['id' => 'cus_123', 'email' => 'test@example.com'])),
        ]);

        $result = $creem->getCustomer(['email' => 'test@example.com']);
        $this->assertEquals('cus_123', $result['id']);
    }

    public function test_list_customers(): void
    {
        $creem = $this->createCreem([
            new Response(200, [], json_encode([
                'items' => [['id' => 'cus_1'], ['id' => 'cus_2']],
            ])),
        ]);

        $result = $creem->listCustomers();
        $this->assertCount(2, $result['items']);
    }

    public function test_customer_billing_portal(): void
    {
        $creem = $this->createCreem([
            new Response(200, [], json_encode([
                'url' => 'https://billing.creem.io/portal/abc',
            ])),
        ]);

        $result = $creem->customerBillingPortal('cus_123');
        $this->assertStringContainsString('billing.creem.io', $result['url']);
    }

    // ---- Subscription Tests ----

    public function test_get_subscription(): void
    {
        $creem = $this->createCreem([
            new Response(200, [], json_encode(['id' => 'sub_123', 'status' => 'active'])),
        ]);

        $result = $creem->getSubscription('sub_123');
        $this->assertEquals('active', $result['status']);
    }

    public function test_search_subscriptions(): void
    {
        $creem = $this->createCreem([
            new Response(200, [], json_encode([
                'items' => [['id' => 'sub_1', 'status' => 'active']],
            ])),
        ]);

        $result = $creem->searchSubscriptions(['status' => 'active']);
        $this->assertCount(1, $result['items']);
    }

    public function test_update_subscription(): void
    {
        $creem = $this->createCreem([
            new Response(200, [], json_encode(['id' => 'sub_123', 'units' => 5])),
        ]);

        $result = $creem->updateSubscription('sub_123', ['units' => 5]);
        $this->assertEquals(5, $result['units']);
    }

    public function test_upgrade_subscription(): void
    {
        $creem = $this->createCreem([
            new Response(200, [], json_encode(['id' => 'sub_123', 'product_id' => 'prod_pro'])),
        ]);

        $result = $creem->upgradeSubscription('sub_123', 'prod_pro');
        $this->assertEquals('prod_pro', $result['product_id']);
    }

    public function test_cancel_subscription(): void
    {
        $creem = $this->createCreem([
            new Response(200, [], json_encode(['id' => 'sub_123', 'status' => 'scheduled_cancel'])),
        ]);

        $result = $creem->cancelSubscription('sub_123', 'scheduled');
        $this->assertEquals('scheduled_cancel', $result['status']);
    }

    public function test_pause_subscription(): void
    {
        $creem = $this->createCreem([
            new Response(200, [], json_encode(['id' => 'sub_123', 'status' => 'paused'])),
        ]);

        $result = $creem->pauseSubscription('sub_123');
        $this->assertEquals('paused', $result['status']);
    }

    public function test_resume_subscription(): void
    {
        $creem = $this->createCreem([
            new Response(200, [], json_encode(['id' => 'sub_123', 'status' => 'active'])),
        ]);

        $result = $creem->resumeSubscription('sub_123');
        $this->assertEquals('active', $result['status']);
    }

    // ---- Transaction Tests ----

    public function test_get_transaction(): void
    {
        $creem = $this->createCreem([
            new Response(200, [], json_encode(['id' => 'txn_123', 'amount' => 2999])),
        ]);

        $result = $creem->getTransaction('txn_123');
        $this->assertEquals(2999, $result['amount']);
    }

    public function test_search_transactions(): void
    {
        $creem = $this->createCreem([
            new Response(200, [], json_encode([
                'items' => [['id' => 'txn_1'], ['id' => 'txn_2']],
            ])),
        ]);

        $result = $creem->searchTransactions(['limit' => 10]);
        $this->assertCount(2, $result['items']);
    }

    // ---- License Tests ----

    public function test_activate_license(): void
    {
        $creem = $this->createCreem([
            new Response(200, [], json_encode([
                'instance_id' => 'inst_abc',
                'status' => 'activated',
            ])),
        ]);

        $result = $creem->activateLicense('ABCD-1234-EFGH', 'MacBook Pro');
        $this->assertEquals('inst_abc', $result['instance_id']);
    }

    public function test_validate_license(): void
    {
        $creem = $this->createCreem([
            new Response(200, [], json_encode(['valid' => true])),
        ]);

        $result = $creem->validateLicense('ABCD-1234-EFGH', 'inst_abc');
        $this->assertTrue($result['valid']);
    }

    public function test_deactivate_license(): void
    {
        $creem = $this->createCreem([
            new Response(200, [], json_encode(['status' => 'deactivated'])),
        ]);

        $result = $creem->deactivateLicense('ABCD-1234-EFGH', 'inst_abc');
        $this->assertEquals('deactivated', $result['status']);
    }

    // ---- Discount Tests ----

    public function test_create_discount(): void
    {
        $creem = $this->createCreem([
            new Response(200, [], json_encode([
                'id' => 'disc_123',
                'code' => 'LAUNCH20',
                'discount_type' => 'percentage',
                'discount_value' => 20,
            ])),
        ]);

        $result = $creem->createDiscount([
            'code' => 'LAUNCH20',
            'discount_type' => 'percentage',
            'discount_value' => 20,
        ]);

        $this->assertEquals('LAUNCH20', $result['code']);
    }

    public function test_get_discount(): void
    {
        $creem = $this->createCreem([
            new Response(200, [], json_encode(['id' => 'disc_123', 'code' => 'SAVE10'])),
        ]);

        $result = $creem->getDiscount(['code' => 'SAVE10']);
        $this->assertEquals('SAVE10', $result['code']);
    }

    public function test_delete_discount(): void
    {
        $creem = $this->createCreem([
            new Response(200, [], json_encode(['deleted' => true])),
        ]);

        $result = $creem->deleteDiscount('disc_123');
        $this->assertTrue($result['deleted']);
    }

    // ---- Client Access ----

    public function test_client_method_returns_creem_client(): void
    {
        $client = new CreemClient('creem_test_key');
        $creem = new Creem($client);

        $this->assertSame($client, $creem->client());
    }
}
