<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use App\Models\Absen;
use Carbon\Carbon;
class SetAbsenAlfa extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'set:absen-alfa';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Set absen alfa';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $today = Carbon::today();

    foreach (User::all() as $user) {

        $cek = Absen::where('user_id', $user->id)
            ->whereDate('tanggal', $today)
            ->first();

        if ($cek) continue;

        if ($today->isWeekend()) {
            $status = 'libur';
        } else {
            $status = 'alfa';
        }

        Absen::create([
            'user_id' => $user->id,
            'tanggal' => $today,
            'jam_masuk' => null,
            'jam_pulang' => null,
            'status' => $status
        ]);
    }
    }
}
