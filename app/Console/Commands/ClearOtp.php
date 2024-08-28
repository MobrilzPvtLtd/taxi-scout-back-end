<?php

namespace App\Console\Commands;
use Carbon\Carbon;
use App\Models\MailOtp;
use AWS\CRT\Log;
use Illuminate\Console\Command;
use Symfony\Polyfill\Intl\Idn\Info;

class ClearOtp extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
   protected $signature = 'clear:otp';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '1 hours completed OTP Deleted';

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
        $currentTime = Carbon::now();
        $timeLimit = $currentTime->copy()->subMinutes(60);
        $otps = MailOtp::where('updated_at', '<', $timeLimit)->get();

        foreach ($otps as $otp) {
            $otp->delete();
        }

       $this->info(' OTP Records cleard ');
    }
}
