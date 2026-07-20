<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\User;
use App\Http\Controllers\CustomerController;
use Illuminate\Http\RedirectResponse;
use Tests\TestCase;

class CustomerDeleteTest extends TestCase
{
    public function test_customer_delete_blocked_when_related_records_exist()
    {
        $customerMock = \Mockery::mock(Customer::class)->makePartial();

        $relationStub = new class {
            public function exists() { return true; }
        };

        $customerMock->shouldReceive('productionItems')->andReturn($relationStub);
        $customerMock->shouldReceive('shipments')->andReturn($relationStub);

        $controller = new CustomerController();
        $response = $controller->destroy($customerMock);

        $this->assertInstanceOf(RedirectResponse::class, $response);
        $this->assertStringContainsString('Pelanggan tidak dapat dihapus', session('error', ''));
    }

    public function test_customer_delete_allowed_when_no_transactions()
    {
        $customerMock = \Mockery::mock(Customer::class)->makePartial();

        $relationStub = new class {
            public function exists() { return false; }
        };

        $customerMock->shouldReceive('productionItems')->andReturn($relationStub);
        $customerMock->shouldReceive('shipments')->andReturn($relationStub);

        $customerMock->shouldReceive('delete')->andReturn(true);

        $controller = new CustomerController();
        $response = $controller->destroy($customerMock);

        $this->assertInstanceOf(RedirectResponse::class, $response);
        $this->assertStringContainsString('Data pelanggan berhasil dihapus', session('success', ''));
    }
}
