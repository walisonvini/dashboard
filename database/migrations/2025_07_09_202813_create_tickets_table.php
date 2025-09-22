<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

use App\Enums\TicketStatus\TicketStatus;
use App\Enums\TicketStatus\TicketPriority;

return new class extends Migration
{
    public function up(): void {
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->enum('status', array_column(TicketStatus::cases(), 'value'))->default(TicketStatus::Open->value);
            $table->enum('priority', array_column(TicketPriority::cases(), 'value'))->default(TicketPriority::Low->value);
            $table->foreignId('category_id')->nullable()->constrained('ticket_categories');
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('tickets');
    }
};
