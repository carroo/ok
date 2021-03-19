<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateCartTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cart', function (Blueprint $table) {
            $table->id();
            $table->integer('id_produk');
            $table->string('nama');
            $table->integer('harga');
            $table->integer('jumlah');
            $table->integer('total');
            $table->timestamps();
        });
        DB::unprepared('CREATE TRIGGER stokkurang AFTER INSERT ON cart FOR EACH ROW
        BEGIN 
            UPDATE produk SET stok=stok-NEW.jumlah
            WHERE id = NEW.id_produk;
        END ');
        DB::unprepared('CREATE TRIGGER stoktambah AFTER DELETE ON cart FOR EACH ROW
        BEGIN 
            UPDATE produk SET stok=stok+OLD.jumlah
            WHERE id = OLD.id_produk;
        END ');
        DB::unprepared('CREATE TRIGGER stokupdate AFTER UPDATE ON cart FOR EACH ROW
        BEGIN 
            UPDATE produk SET stok=stok+OLD.jumlah-NEW.jumlah
            WHERE id = NEW.id_produk;
        END ');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cart');
        DB::uprepared('DROP TRIGGER `stokkurang`');
        DB::uprepared('DROP TRIGGER `stoktambah`');
        DB::uprepared('DROP TRIGGER `stokupdate`');
    }
}
