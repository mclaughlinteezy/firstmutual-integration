<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('medicalpushed', function (Blueprint $table) {
            $table->string('source')->default('api')->after('status'); // 'api' or 'manual'
        });
    }

    public function down()
    {
        Schema::table('medicalpushed', function (Blueprint $table) {
            $table->dropColumn('source');
        });
    }
};
