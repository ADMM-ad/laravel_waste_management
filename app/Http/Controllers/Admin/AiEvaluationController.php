<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;

class AiEvaluationController extends Controller
{
    /**
     * Display AI Evaluation and Model Metrics page.
     */
    public function index(): View
    {
        $evaluationPath = base_path('../machinelearning_waste_management/evaluation');
        
        $reportContent = 'Hasil evaluasi belum ditemukan. Pastikan file evaluation/classification_report.txt tersedia.';
        $reportFile = $evaluationPath . '/classification_report.txt';
        if (File::exists($reportFile)) {
            $reportContent = File::get($reportFile);
        }

        // Encode image files to base64 so they render directly in Blade
        $historyBase64 = null;
        $historyFile = $evaluationPath . '/training_history.png';
        if (File::exists($historyFile)) {
            $historyBase64 = 'data:image/png;base64,' . base64_encode(File::get($historyFile));
        }

        $confusionBase64 = null;
        $confusionFile = $evaluationPath . '/confusion_matrix.png';
        if (File::exists($confusionFile)) {
            $confusionBase64 = 'data:image/png;base64,' . base64_encode(File::get($confusionFile));
        }

        // Probe FastAPI Service status (Port 8000)
        $aiServiceOnline = false;
        $aiServiceError = null;
        $fastApiUrl = rtrim(config('services.fastapi.url', 'http://127.0.0.1:8000'), '/');

        try {
            $response = Http::timeout(1.5)->get("{$fastApiUrl}/health");
            if ($response->successful()) {
                $aiServiceOnline = true;
            } else {
                $aiServiceError = "Server merespons dengan status {$response->status()}";
            }
        } catch (\Throwable $e) {
            $aiServiceError = "Tidak dapat terhubung ke server FastAPI di {$fastApiUrl}. Service Machine Learning sedang offline atau belum di-hosting.";
        }

        return view('admin.ai.index', [
            'reportContent' => $reportContent,
            'historyImage' => $historyBase64,
            'confusionImage' => $confusionBase64,
            'aiServiceOnline' => $aiServiceOnline,
            'aiServiceError' => $aiServiceError,
            'fastApiUrl' => $fastApiUrl,
        ]);
    }
}
