<?php

namespace App\Console\Commands;

use App\Mail\OrderExpiryMail;
use App\Models\Admin\AdminDetail;
use App\Models\Admin\Order;
use App\Models\Admin\Subscription;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class OrderExpired extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'order:expire';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $expiredOrder = Order::where('active', 1)->get();

        foreach ($expiredOrder as $order) {
            $admin = AdminDetail::whereHas('user')->where('user_id',$order->user_id)->first();
            $package = Subscription::where('id',$order->package_id)->first();
            $expiryDate = $order->getOriginal('end_date');
            $startDate = $order->getOriginal('start_date');
            $today = Carbon::today()->toDateString();
            if (Carbon::parse($expiryDate)->subDays(1)->toDateString() < $today) {
                $order->update(['active' => 2]);

                $data = [
                    'name' => $admin->user->name,
                    'package_name' => $package->package_name,
                    'orderDate' => $startDate,
                    'expiryDate' => $expiryDate,
                ];

                Mail::to($admin->email)->send(new OrderExpiryMail($data));
            }
        }
    }
}
