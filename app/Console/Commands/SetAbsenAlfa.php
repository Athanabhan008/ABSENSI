<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use App\Models\Absen;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
class SetAbsenAlfa extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'absen:set-alfa';

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

        $users = User::whereIn('role', ['staff', 'admin'])->get();

        $userColumn = Schema::hasColumn('absens', 'id_user') ? 'id_user' : 'user_id';
        $dateColumn = Schema::hasColumn('absens', 'tgl_absen') ? 'tgl_absen' : 'tanggal';

        foreach ($users as $user) {
            $cek = DB::table('absens')
                ->where($userColumn, $user->id)
                ->whereDate($dateColumn, $today)
                ->first();

            if ($cek) {
                continue;
            }

            $status = $today->isWeekend() ? 'libur' : 'alfa';

            $payload = [
                $userColumn     => $user->id,
                $dateColumn     => $today,
                'jam_masuk'     => null,
                'jam_keluar'    => null,
                'lokasi_masuk'  => null,
                'lokasi_keluar' => null,
                'foto_masuk'    => null,
                'foto_keluar'   => null,
                'status'        => $status,
                'keterangan'    => null,
            ];

            DB::table('absens')->insert($payload);
        }

        $this->info('Absen alfa/libur berhasil dibuat untuk staff & admin');

        return self::SUCCESS;
    }
}
