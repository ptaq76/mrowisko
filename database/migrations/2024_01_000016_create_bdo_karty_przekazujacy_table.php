<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ══════════════════════════════════════════════════════════════════════
        // TABELA: karchem_klienci (lista NIP-ów do wykluczenia z widoku)
        // ══════════════════════════════════════════════════════════════════════
        Schema::create('karchem_klienci', function (Blueprint $table) {
            $table->id();
            $table->string('nip')->unique();
            $table->string('nazwa')->nullable();
            $table->timestamps();
        });

        // ══════════════════════════════════════════════════════════════════════
        // TABELA GŁÓWNA: bdo_karty_przekazujacy (karty odpadów - pozycja przekazującego)
        // ══════════════════════════════════════════════════════════════════════
        Schema::create('bdo_karty_przekazujacy', function (Blueprint $table) {
            $table->id();
            $table->string('kpo_id')->unique()->index();

            // Dane karty
            $table->string('card_number')->nullable()->index();
            $table->string('card_status')->nullable()->index();
            $table->string('card_status_code_name')->nullable();
            $table->string('waste_code')->nullable();
            $table->string('waste_code_description')->nullable();
            $table->string('waste_code_and_description')->nullable();
            $table->integer('calendar_year')->nullable()->index();

            // Masa odpadu
            $table->decimal('waste_mass', 12, 4)->nullable();
            $table->decimal('corrected_waste_mass', 12, 4)->nullable();

            // Daty i czasy planowane
            $table->date('planned_transport_date')->nullable();
            $table->datetime('planned_transport_time')->nullable();

            // Daty i czasy rzeczywiste
            $table->date('real_transport_date')->nullable()->index();
            $table->datetime('real_transport_time')->nullable();

            // Potwierdzenie przejęcia
            $table->date('receive_confirmation_date')->nullable();
            $table->datetime('receive_confirmation_time')->nullable();
            $table->string('receive_confirmed_by_user')->nullable();

            // Zatwierdzenie
            $table->date('approval_date')->nullable();
            $table->datetime('approval_time')->nullable();
            $table->string('approved_by_user')->nullable();

            // Potwierdzenie transportu
            $table->date('transport_confirmation_date')->nullable();
            $table->datetime('transport_confirmation_time')->nullable();
            $table->string('transport_confirmed_by_user')->nullable();

            // Odrzucenie
            $table->datetime('card_rejection_time')->nullable();
            $table->string('rejected_by_user_first_name_and_last_name')->nullable();

            // Dane przekazującego (sender)
            $table->string('sender_name_or_first_name_and_last_name')->nullable();
            $table->text('sender_address')->nullable();
            $table->string('sender_eup_number')->nullable();
            $table->string('sender_eup_name')->nullable();
            $table->text('sender_eup_address')->nullable();
            $table->string('sender_identification_number')->nullable();
            $table->string('sender_nip')->nullable()->index();

            // Dane przewoźnika (carrier)
            $table->string('carrier_name_or_first_name_and_last_name')->nullable();
            $table->text('carrier_address')->nullable();
            $table->string('carrier_identification_number')->nullable();
            $table->string('carrier_nip')->nullable();

            // Dane przejmującego (receiver)
            $table->string('receiver_name_or_first_name_and_last_name')->nullable();
            $table->text('receiver_address')->nullable();
            $table->string('receiver_identification_number')->nullable();
            $table->string('receiver_nip')->nullable();

            // Pojazd
            $table->string('vehicle_reg_number')->nullable();

            // Uwagi i dodatkowe informacje
            $table->text('remarks')->nullable();
            $table->text('additional_info')->nullable();

            // Data ostatniej modyfikacji w BDO
            $table->datetime('kpo_last_modified_at')->nullable()->index();

            $table->timestamps();
        });

        // ══════════════════════════════════════════════════════════════════════
        // TABELA SZCZEGÓŁÓW: bdo_karty_detale_przekazujacy
        // ══════════════════════════════════════════════════════════════════════
        Schema::create('bdo_karty_detale_przekazujacy', function (Blueprint $table) {
            $table->id();
            $table->string('kpo_id')->index();

            // Dane karty
            $table->string('card_number')->nullable();
            $table->string('card_status')->nullable();
            $table->integer('calendar_year')->nullable();

            // Masa odpadu
            $table->decimal('waste_mass', 12, 4)->nullable();
            $table->decimal('corrected_waste_mass', 12, 4)->nullable();

            // Daty i czasy
            $table->date('planned_transport_date')->nullable();
            $table->datetime('planned_transport_time')->nullable();
            $table->date('real_transport_date')->nullable();
            $table->datetime('real_transport_time')->nullable();
            $table->date('receive_confirmation_date')->nullable();
            $table->datetime('receive_confirmation_time')->nullable();
            $table->date('approval_date')->nullable();
            $table->datetime('approval_time')->nullable();
            $table->date('transport_confirmation_date')->nullable();
            $table->datetime('transport_confirmation_time')->nullable();

            // Użytkownicy
            $table->string('approved_by_user')->nullable();
            $table->string('transport_confirmed_by_user')->nullable();
            $table->string('receive_confirmed_by_user')->nullable();

            // Dane przekazującego
            $table->string('sender_name_or_first_name_and_last_name')->nullable();
            $table->text('sender_address')->nullable();
            $table->string('sender_eup_number')->nullable();
            $table->string('sender_eup_name')->nullable();
            $table->text('sender_eup_address')->nullable();
            $table->string('sender_identification_number')->nullable();
            $table->string('sender_nip')->nullable();

            // Dane przewoźnika
            $table->string('carrier_name_or_first_name_and_last_name')->nullable();
            $table->text('carrier_address')->nullable();
            $table->string('carrier_identification_number')->nullable();
            $table->string('carrier_nip')->nullable();

            // Dane przejmującego
            $table->string('receiver_name_or_first_name_and_last_name')->nullable();
            $table->text('receiver_address')->nullable();
            $table->string('receiver_identification_number')->nullable();
            $table->string('receiver_nip')->nullable();

            // Pozostałe
            $table->string('waste_code_and_description')->nullable();
            $table->string('vehicle_reg_number')->nullable();
            $table->text('remarks')->nullable();
            $table->text('additional_info')->nullable();

            $table->timestamps();

            $table->foreign('kpo_id')
                ->references('kpo_id')
                ->on('bdo_karty_przekazujacy')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bdo_karty_detale_przekazujacy');
        Schema::dropIfExists('bdo_karty_przekazujacy');
        Schema::dropIfExists('karchem_klienci');
    }
};
