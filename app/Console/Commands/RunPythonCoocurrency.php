<?php

namespace App\Console\Commands;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Symfony\Component\Process\Process;


#[Signature('app:run-python-coocurrency')]
#[Description('Command description')]
class RunPythonCoocurrency extends Command
{
    /**
     * Execute the console command.
     */
    public function handle()
{
    // 1. Laravel mengambil semua data vektor dari DB
    $rows = DB::table('movie_genre_vector')->pluck('vector');
    
    // Konversi koleksi database menjadi format array murni agar bisa dipahami Python
    $vectors = $rows->map(function ($item) {
        return is_string($item) ? json_decode($item, true) : $item;
    })->toArray();

    $pythonPath = 'C:\Users\muham\AppData\Local\Programs\Python\Python313\python.exe';

    // 2. Siapkan proses eksekusi Python
    $scriptPath = base_path('\data\scripts\getGenreCoocurrence.py'); // Sesuaikan posisi file python Anda
    $process = new Process([$pythonPath, $scriptPath, '20']); // '20' adalah argumen total_genres
    
    // 3. SEMPROT DATA LANGSUNG KE PYTHON (Menggantikan fungsi query DB di Python)
    $process->setInput(json_encode($vectors));
    
    $process->run();

    if (!$process->isSuccessful()) {
        $this->error("Gagal mengeksekusi Python: " . $process->getErrorOutput());
        return;
    } 

    $this->info($process->getOutput());
}
}
