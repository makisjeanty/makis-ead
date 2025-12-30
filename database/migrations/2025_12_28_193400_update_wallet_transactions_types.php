<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // For SQLite, we need to recreate the table with the new enum values
        if (DB::getDriverName() === 'sqlite') {
            $this->recreateWalletTransactionsTableForSqlite();
        } else {
            // For MySQL and other databases, use the original approach
            DB::statement("ALTER TABLE wallet_transactions MODIFY COLUMN type ENUM(
                'credit',
                'debit', 
                'deposit',
                'withdraw',
                'purchase',
                'refund',
                'affiliate_commission',
                'transfer_in',
                'transfer_out'
            )");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (DB::getDriverName() === 'sqlite') {
            DB::statement("ALTER TABLE wallet_transactions MODIFY COLUMN type ENUM(
                'credit',
                'debit', 
                'deposit',
                'withdraw',
                'purchase',
                'refund'
            )");
        } else {
            DB::statement("ALTER TABLE wallet_transactions MODIFY COLUMN type ENUM(
                'credit',
                'debit', 
                'deposit',
                'withdraw',
                'purchase',
                'refund'
            )");
        }
    }
    
    /**
     * Recreate the wallet_transactions table for SQLite with new enum values
     */
    private function recreateWalletTransactionsTableForSqlite(): void
    {
        // Get the current data
        $oldData = DB::select('SELECT * FROM wallet_transactions');
        
        // Drop the old table
        Schema::drop('wallet_transactions');
        
        // Create the table again with new enum values
        Schema::create('wallet_transactions', function ($table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned();
            $table->enum('type', [
                'credit',
                'debit',
                'deposit', 
                'withdraw',
                'purchase',
                'refund',
                'affiliate_commission',
                'transfer_in',
                'transfer_out'
            ]);
            $table->enum('status', ['pending', 'completed', 'failed', 'cancelled']);
            $table->decimal('amount', 10, 2);
            $table->string('description')->nullable();
            $table->string('reference_type')->nullable();
            $table->integer('reference_id')->nullable();
            $table->timestamps();
            
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
        
        // Insert the old data back
        foreach ($oldData as $data) {
            DB::table('wallet_transactions')->insert([
                'user_id' => $data->user_id,
                'type' => $data->type,
                'status' => $data->status,
                'amount' => $data->amount,
                'description' => $data->description,
                'reference_type' => $data->reference_type,
                'reference_id' => $data->reference_id,
                'created_at' => $data->created_at,
                'updated_at' => $data->updated_at,
            ]);
        }
    }
};