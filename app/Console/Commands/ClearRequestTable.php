<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Request\Request;
use Carbon\Carbon;
use App\Models\User;
use App\Models\MailOtp;



class ClearRequestTable extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'clear:request';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clear Request Table Data Before 30 Days';

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
     * @return mixed
     */
    public function handle()
    {
        $date = Carbon::now()->subDays(30);

        $request = Request::where( 'created_at', '<', $date)->delete();

       // Delete inactive users with expired OTPs
        $usersToDelete = User::where('active', 0)
            ->where('email_confirmed', 0)
            ->get();

        foreach ($usersToDelete as $user) {
            $otpGenerationTime = User::where('email', $user->email)->first()->created_at;
            $currentTime = Carbon::now();
            $timeDifference = $currentTime->diffInMinutes($otpGenerationTime);

            if ($timeDifference > 5) {
                $user->delete();
                MailOtp::where('email', $user->email)->delete();
                $this->info('User deleted: ' . $user->email);
            }
        }

        $this->info('Records cleard ');
    }
}
