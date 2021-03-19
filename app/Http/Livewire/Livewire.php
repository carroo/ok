<?php

namespace App\Http\Livewire;

use App\Models\Cart;
use App\Models\Produk;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class Livewire extends Component
{
    // untuk fungsi endpoint post/insert ke database
    public $nama, $total_bayar, $kembalian;

    // $jb = jumlah barang yang mau dimasukkan cart
    // $tharga = total harga
    public $jb, $produk, $tharga, $bayar, $cart;

    public function render()
    {
        $produk = Produk::get();
        $th = 0;
        foreach ($produk as $key => $value) {
            if (isset($this->jb[$value->id])) {
                if ($this->jb[$value->id] >= 0) {
                    $th += $this->jb[$value->id] * $value->harga;
                    //buat ngisi cart
                    Cart::updateOrCreate(
                        ['id_produk' => $value->id],
                        [   'nama' => $value->nama, 
                            'harga' => $value->harga,
                            'jumlah' => $this->jb[ $value->id], 
                            'total' => $value->harga * $this->jb[ $value->id],
                        ]
                    );
                   
                }
            }
        }
        $this->kembalian = $this->total_bayar-$this->tharga;
        $this->cart = Cart::get();
        $this->produk = Produk::get();
        $this->tharga = $th;
        return view('livewire.livewire');
    }

    public function store()
    {
        DB::table('transaction')->insert([
            'nama' => $this->nama,
            'total_harga' => $this->tharga,
            'total_bayar' => $this->total_bayar
        ]);

        foreach($this->cart as $key => $value) {
            DB::table('produk')->where('id', $value['id'])->update([
                'stok' => $value['stok'] - $value['jumlah']
            ]);
        }

        $this->nama = NULL;
        $this->total_bayar = NULL;
        $this->jb = NULL;
        $this->tharga = NULL;
        $this->bayar = NULL;
        $this->cart = NULL;
    }

    public function hapuscart($id)
    {
        $this->jb[$id] = NULL;
        Cart::where('id_produk',$id)->delete();
    }

    public function cek()
    {
        dd($this->cart);
    }
}
