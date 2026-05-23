<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Customer;
use App\Models\Loan;
use App\Models\Payment;
use App\Models\Collateral;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $customer = Customer::create([
            'name' => 'Juan Pérez',
            'identification_id' => '001-0012345-1',
            'phone' => '809-555-1234',
            'address' => 'Av. Winston Churchill #123, Santo Domingo',
            'email' => 'juan.perez@email.com',
        ]);

        $loan = Loan::create([
            'customer_id' => $customer->id,
            'amount' => 50000,
            'interest_rate' => 10,
            'interest_type' => 'simple',
            'payment_modality' => 'mensual',
            'start_date' => now()->subMonths(2),
            'estimated_end_date' => now()->addMonths(10),
            'status' => 'active',
            'balance' => 55000, // 50000 + 10%
        ]);

        Payment::create([
            'loan_id' => $loan->id,
            'amount' => 5000,
            'payment_date' => now()->subMonth(),
            'payment_method' => 'Transferencia',
            'observations' => 'Primer pago mensual',
        ]);

        Collateral::create([
            'loan_id' => $loan->id,
            'type' => 'Vehículo',
            'description' => 'Toyota Corolla 2015, Placa A123456',
            'estimated_value' => 450000,
            'status' => 'active',
        ]);
    }
}
