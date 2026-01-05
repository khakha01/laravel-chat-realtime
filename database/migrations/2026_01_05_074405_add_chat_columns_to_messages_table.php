<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('messages', function (Blueprint $table) {

            // người gửi
            $table->foreignId('from_user_id')
                ->nullable()
                ->after('id') // chỉ định vị trí của cột mới trong database “Tạo cột này sau cột X”
                ->constrained('users') // tạo khóa ngoại (foreign key) trỏ tới bảng users
                ->cascadeOnDelete(); // Khi user bị xóa → message liên quan bị xóa theo

            // người nhận
            $table->foreignId('to_user_id')
                ->nullable()
                ->after('from_user_id')
                ->constrained('users')
                ->cascadeOnDelete();

            // trạng thái đọc
            $table->boolean('is_read')
                ->default(false)
                ->after('content');
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('messages', function (Blueprint $table) {
            $table->dropForeign(['from_user_id']);
            $table->dropForeign(['to_user_id']);

            $table->dropColumn([
                'from_user_id',
                'to_user_id',
                'is_read'
            ]);
        });
    }
};
