<?php

namespace App\Console\Commands;

use App\Models\Customer;
use App\Models\MaterialOrder;
use App\Models\Supplier;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class TrimSupplierCustomerDummyData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:trim-supplier-customer-dummy-data {--keep=10 : Number of supplier/customer rows to keep} {--dry-run : Show what would be deleted without deleting}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Trim supplier and customer dummy data down to a fixed number of records.';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $keep = max(0, (int) $this->option('keep'));
        $dryRun = (bool) $this->option('dry-run');

        $supplierIdsToDelete = Supplier::orderBy('id_supplier')
            ->skip($keep)
            ->pluck('id_supplier')
            ->all();

        $customerIdsToDelete = Customer::orderBy('id_pelanggan')
            ->skip($keep)
            ->pluck('id_pelanggan')
            ->all();

        $this->info(sprintf('Keeping first %d supplier and customer rows by primary key order.', $keep));
        $this->line('Suppliers to delete: ' . count($supplierIdsToDelete));
        $this->line('Customers to delete: ' . count($customerIdsToDelete));

        if ($dryRun) {
            $this->warn('Dry run enabled, no rows were deleted.');

            return self::SUCCESS;
        }

        DB::transaction(function () use ($supplierIdsToDelete, $customerIdsToDelete): void {
            if ($supplierIdsToDelete !== []) {
                MaterialOrder::whereIn('id_supplier', $supplierIdsToDelete)->delete();
            }

            if ($supplierIdsToDelete !== []) {
                Supplier::whereIn('id_supplier', $supplierIdsToDelete)->delete();
            }

            if ($customerIdsToDelete !== []) {
                Customer::whereIn('id_pelanggan', $customerIdsToDelete)->delete();
            }
        });

        $this->info('Cleanup completed.');
        $this->line('Suppliers remaining: ' . Supplier::count());
        $this->line('Customers remaining: ' . Customer::count());

        return self::SUCCESS;
    }
}
