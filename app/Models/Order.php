<?php

namespace App\Models;

use Database\Factories\OrderFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'kode_pesanan',
    'user_id',
    'nama_pelanggan',
    'atas_nama',
    'tipe_pesanan',
    'nomor_meja',
    'total_harga',
    'status_pesanan',
    'status_pembayaran',
    'bukti_pembayaran',
    'catatan',
])]
class Order extends Model
{
    /** @use HasFactory<OrderFactory> */
    use HasFactory;

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function payment()
    {
        return $this->hasOne(Payment::class);
    }

    public static function calculatePortionPrice($menu, $customizationLine)
    {
        $basePrice = $menu->harga;
        
        $menuNameLower = strtolower($menu->nama_menu);
        $isNasiBakar = str_contains($menuNameLower, 'nasi bakar') || str_contains($menuNameLower, 'nasbak');
        
        if ($isNasiBakar) {
            if (str_contains($menuNameLower, 'reguler') || str_contains($menuNameLower, 'regular')) {
                // Default base price for reguler is 10000 (standard lauk)
                $basePrice = 10000;
                
                if (preg_match('/Lauk:\s*([^|\]]+)/i', $customizationLine, $matches)) {
                    $laukPart = strtolower(trim($matches[1]));
                    if (str_contains($laukPart, '*')) {
                        $basePrice = 12000;
                    }
                }
            } elseif (str_contains($menuNameLower, 'mix')) {
                $basePrice = 12000;
            } elseif (str_contains($menuNameLower, 'jumbo')) {
                $basePrice = 15000;
            }
            
            // Add extra lauk prices (each extra item adds 3000)
            if (preg_match('/Ekstra:\s*([^|\]]+)/i', $customizationLine, $matches)) {
                $ekstraPart = trim($matches[1]);
                if (strtolower($ekstraPart) !== 'tidak ada') {
                    $extras = explode(',', $ekstraPart);
                    foreach ($extras as $extra) {
                        $extra = trim($extra);
                        if ($extra !== '') {
                            $basePrice += 3000;
                        }
                    }
                }
            }
        }
        
        return $basePrice;
    }
}
