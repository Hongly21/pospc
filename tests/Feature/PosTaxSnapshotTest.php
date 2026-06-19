<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Inventory;
use App\Models\Order;
use App\Models\Product;
use App\Models\Receipt;
use App\Models\Role;
use App\Models\Tax;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\Hash;
use Tests\CreatesApplication;

class PosTaxSnapshotTest extends BaseTestCase
{
    use CreatesApplication;
    use RefreshDatabase;

    public function test_checkout_stamps_selected_tax_snapshot_on_receipt(): void
    {
        $role = Role::create([
            'RoleName' => 'Admin',
        ]);

        $user = User::create([
            'Username' => 'tester',
            'Email' => 'tester@example.com',
            'PasswordHash' => Hash::make('password'),
            'RoleID' => $role->RoleID,
            'Status' => 'Approved',
        ]);

        $category = Category::create([
            'Name' => 'Accessories',
            'status' => 1,
        ]);

        $tax = Tax::create([
            'Name' => 'VAT',
            'Rate' => 10.00,
            'Description' => 'Standard VAT',
            'Status' => 1,
        ]);

        $product = Product::create([
            'Name' => 'Keyboard',
            'CategoryID' => $category->CategoryID,
            'Brand' => 'Acme',
            'Model' => 'K-100',
            'CostPrice' => 40.00,
            'SellPrice' => 50.00,
            'Barcode' => 'KB-100',
            'Description' => 'Test keyboard',
            'WarrantyMonths' => 12,
            'Status' => 1,
        ]);

        Inventory::create([
            'ProductID' => $product->ProductID,
            'Quantity' => 5,
            'ReorderLevel' => 1,
        ]);

        $this->actingAs($user)
            ->postJson(route('pos.store'), [
                'payment_type' => 'Cash',
                'tax_id' => $tax->TaxID,
                'cart' => [
                    [
                        'id' => $product->ProductID,
                        'qty' => 2,
                        'name' => $product->Name,
                    ],
                ],
                'customer_id' => null,
            ])
            ->assertOk()
            ->assertJson([
                'status' => 'success',
            ]);

        $order = Order::firstOrFail();
        $receipt = Receipt::firstOrFail();

        $this->assertSame(110.00, (float) $order->TotalAmount);
        $this->assertSame(10.00, (float) $order->TotalTax);
        $this->assertSame($tax->TaxID, $receipt->TaxID);
        $this->assertSame(10.00, (float) $receipt->TaxRate);
        $this->assertSame(10.00, (float) $receipt->TaxAmount);
        $this->assertSame(110.00, (float) $receipt->PaidAmount);
        $this->assertSame(0.00, (float) $receipt->ChangeAmount);
    }
}
