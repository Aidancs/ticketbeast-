<?php

use App\Billing\FakePaymentGateway;
use App\Billing\PaymentGateway;
use App\Concert;
use App\Order;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class PurchaseTicketsTest extends TestCase
{
    use DatabaseMigrations;

    protected function setUp()
    {
        parent::setUp();

        $this->paymentGateway = new FakePaymentGateway;

        $this->app->instance(PaymentGateway::class, $this->paymentGateway);
    }

    private function orderTickets($concert, $params)
    {
        $this->json('POST', "/concerts/{$concert->id}/orders", $params);
    }

    private function assertValidationError($field)
    {
//        $this->assertResponseStatus(422);
//        $this->assertArrayHasKey($field, $this->decodeResponseJson);
    }

    /** @test */
    function customer_can_purchase_concert_tickets_to_a_published_concert()
    {
        $concert = factory(Concert::class)->states('published')->create(['ticket_price' => 3250]);

        $this->orderTickets($concert, [

            'email' => 'john@example.com',

            'ticket_quantity' => 3,

            'payment_token' => $this->paymentGateway->getValidTestToken(),
        ]);

//        $response = $this->get('/concerts/'.$concert->id.'/orders');
//
//        $response->assertStatus(201);

        $this->assertEquals(9750, $this->paymentGateway->totalCharges());

        $order = $concert->orders()->where('email', 'john@example.com')->first();

        $this->assertNotNull($order);

        $this->assertEquals(3, $order->tickets()->count());
    }

    /** @test */
    function cannot_purchase_tickets_to_an_unpublished_concert()
    {
        $concert = factory(Concert::class)->states('unpublished')->states('unpublished')->create();

        $this->orderTickets($concert, [

            'email' => 'john@example.com',

            'ticket_quantity' => 3,

            'payment_token' => $this->paymentGateway->getValidTestToken(),
        ]);

//        $this->assertResponseStatus(404);

        $this->assertEquals(0, $concert->orders()->count());

        $this->assertEquals(0, $this->paymentGateway->totalCharges());
    }

    /** @test */
    function an_order_is_not_created_if_payment_fails()
    {
        $concert = factory(Concert::class)->states('published')->create(['ticket_price' => 3250]);

        $this->orderTickets($concert, [

            'email' => 'john@example.com',

            'ticket_quantity' => 3,

            'payment_token' => 'invalid-payment-token',
        ]);

//        $this->assertResponseStatus(422);

        $order = $concert->orders()->where('email', 'john@example.com')->first();

        $this->assertNull($order);
    }

    /** @test */
    function email_is_required_to_purchase_tickets()
    {
        $concert = factory(Concert::class)->states('published')->create();

        $this->orderTickets($concert, [

            'ticket_quantity' => 3,

            'payment_token' => $this->paymentGateway->getValidTestToken(),
        ]);

//        $response = $this->get('/concerts/'.$concert->id.'/orders');

//        $this->assertValidationError('email');
    }

    /** @test */
    function email_must_be_valid_to_purchase_tickets()
    {
        $concert = factory(Concert::class)->states('published')->create();

        $this->orderTickets($concert, [
            'email' => 'not-an-email-address',
            'ticket_quantity' => 3,
            'payment_token' => $this->paymentGateway->getValidTestToken(),
        ]);

//        $this->assertValidationError('email');
    }

    /** @test */
    function ticket_quantity_is_required_to_purchase_tickets()
    {
        $concert = factory(Concert::class)->states('published')->create();

        $this->orderTickets($concert, [
            'email' => 'john@example.com',
            'payment_token' => $this->paymentGateway->getValidTestToken(),
        ]);

//        $this->assertValidationError('ticket_quantity');
    }

    /** @test */
    function ticket_quantity_must_be_at_least_1_to_purchase_tickets()
    {
        $concert = factory(Concert::class)->states('published')->create();

        $this->orderTickets($concert, [
            'email' => 'john@example.com',
            'ticket_quantity' => 0,
            'payment_token' => $this->paymentGateway->getValidTestToken(),
        ]);

//        $this->assertValidationError('ticket_quantity');
    }

    /** @test */
    function payment_token_is_required()
    {
        $concert = factory(Concert::class)->states('published')->create();

        $this->orderTickets($concert, [
            'email' => 'john@example.com',
            'ticket_quantity' => 3,
        ]);

//        $this->assertValidationError('payment_token');
    }
}

