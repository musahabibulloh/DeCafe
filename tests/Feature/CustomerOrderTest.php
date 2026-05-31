<?php

namespace Tests\Feature;

use App\Models\Menu;
use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CustomerOrderTest extends TestCase
{
    use RefreshDatabase;

    public function test_customer_can_create_order_with_items_and_pay(): void
    {
        // 1. Create a customer user
        $customer = User::create([
            'name' => 'Pelanggan Test',
            'email' => 'customer_test@decafe.test',
            'password' => bcrypt('password'),
            'role' => 'customer',
        ]);

        // 2. Create some menu items from different categories
        $menu1 = Menu::create([
            'nama_menu' => 'Nasi Goreng',
            'kategori' => 'makanan',
            'deskripsi' => 'Nasi goreng enak',
            'harga' => 20000,
            'stok' => 10,
            'status' => 'tersedia',
        ]);

        $menu2 = Menu::create([
            'nama_menu' => 'Es Teh',
            'kategori' => 'minuman',
            'deskripsi' => 'Es teh manis',
            'harga' => 5000,
            'stok' => 20,
            'status' => 'tersedia',
        ]);

        $menu3 = Menu::create([
            'nama_menu' => 'Kentang Goreng',
            'kategori' => 'makanan',
            'deskripsi' => 'Kentang goreng renyah',
            'harga' => 12000,
            'stok' => 15,
            'status' => 'tersedia',
        ]);

        // 3. Post order as customer
        $response = $this->actingAs($customer)
            ->post(route('customer.orders.store'), [
                'nomor_meja' => '05',
                'atas_nama' => 'Budi',
                'tipe_pesanan' => 'dine_in',
                'items' => [
                    $menu1->id => [
                        'menu_id' => $menu1->id,
                        'jumlah' => 2,
                    ],
                    $menu2->id => [
                        'menu_id' => $menu2->id,
                        'jumlah' => 3,
                    ],
                    $menu3->id => [
                        'menu_id' => $menu3->id,
                        'jumlah' => 0,
                    ],
                ],
            ]);

        // Expect redirect to customer orders index
        $response->assertRedirect(route('customer.orders.index'));

        // Verify order in database
        $this->assertDatabaseHas('orders', [
            'user_id' => $customer->id,
            'nomor_meja' => '05',
            'total_harga' => 55000, // (20000 * 2) + (5000 * 3)
            'status_pesanan' => 'menunggu',
            'status_pembayaran' => 'belum_bayar',
        ]);

        $order = Order::first();
        $this->assertNotNull($order);

        // Verify order items
        $this->assertDatabaseHas('order_items', [
            'order_id' => $order->id,
            'menu_id' => $menu1->id,
            'jumlah' => 2,
            'subtotal' => 40000,
        ]);
        $this->assertDatabaseHas('order_items', [
            'order_id' => $order->id,
            'menu_id' => $menu2->id,
            'jumlah' => 3,
            'subtotal' => 15000,
        ]);
        $this->assertDatabaseMissing('order_items', [
            'order_id' => $order->id,
            'menu_id' => $menu3->id,
        ]);

        // Verify stock decremented
        $this->assertEquals(8, $menu1->fresh()->stok);
        $this->assertEquals(17, $menu2->fresh()->stok);

        // 4. Pay order as customer (digital payment)
        $payResponse = $this->actingAs($customer)
            ->post(route('customer.orders.pay', $order));

        $payResponse->assertRedirect(route('customer.orders.qris', $order));

        // Simulate paying QRIS
        $simResponse = $this->actingAs($customer)
            ->post(route('customer.orders.simulate-qris-pay', $order));
        $simResponse->assertJson(['success' => true]);

        // Verify status is updated to selesai and lunas
        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'status_pesanan' => 'selesai',
            'status_pembayaran' => 'lunas',
        ]);

        // Verify payment is recorded
        $this->assertDatabaseHas('payments', [
            'order_id' => $order->id,
            'kasir_id' => null,
            'metode_pembayaran' => 'qris',
            'total_bayar' => 55000,
            'status' => 'lunas',
        ]);
    }
}
