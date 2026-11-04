@extends('layouts.master')

@section('title', 'Evaluasi Model Machine Learning')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <div class="inline-flex items-center gap-2 px-2.5 py-1 rounded-full text-xs font-semibold {{ $aiServiceOnline ? 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400' : 'bg-rose-500/10 text-rose-600 dark:text-rose-400' }} mb-2">
                <span class="w-2 h-2 rounded-full {{ $aiServiceOnline ? 'bg-emerald-600 dark:bg-emerald-400' : 'bg-rose-600 dark:bg-rose-400' }}"></span>
                {{ $aiServiceOnline ? 'FastAPI Service Online' : 'FastAPI Service Offline / Belum Di-hosting' }}
            </div>
            <h1 class="text-2xl font-bold tracking-tight text-foreground">Evaluasi Model Machine Learning</h1>
            <p class="text-sm text-muted-foreground">Laporan performa model klasifikasi sampah, grafik konvergensi, dan matriks evaluasi.</p>
        </div>
        <div class="flex items-center gap-2">
            @if ($aiServiceOnline)
                <a href="{{ $fastApiUrl }}" target="_blank" class="inline-flex items-center gap-1.5 rounded-md bg-emerald-600 px-3.5 py-2 text-xs font-semibold text-white shadow-xs hover:bg-emerald-700 transition-colors">
                    <i data-lucide="external-link" class="h-3.5 w-3.5"></i>
                    <span>Dashboard FastAPI (Port 8000)</span>
                </a>
            @else
                <span class="inline-flex items-center gap-1.5 rounded-md bg-muted px-3 py-1.5 text-xs text-muted-foreground border border-border">
                    <i data-lucide="info" class="h-3.5 w-3.5"></i>
                    <span>Microservice Port 8000 Offline</span>
                </span>
            @endif
        </div>
    </div>

    <!-- Alert Status Hosting FastAPI Service -->
    @if (! $aiServiceOnline)
        <div class="rounded-xl border border-border bg-card p-4 sm:p-5 shadow-xs">
            <div class="flex items-center justify-between pb-2 mb-2 border-b border-border">
                <div class="flex items-center gap-2">
                    <i data-lucide="info" class="h-4 w-4 text-muted-foreground"></i>
                    <h3 class="text-sm font-semibold text-foreground">Status Service Machine Learning</h3>
                </div>
                <span class="text-xs text-muted-foreground font-medium">Offline</span>
            </div>
            <p class="text-xs text-muted-foreground leading-relaxed">
                Microservice AI FastAPI pada alamat <code class="px-1.5 py-0.5 rounded bg-muted font-mono text-foreground text-[11px]">{{ $fastApiUrl }}</code> sedang offline atau tidak di-hosting. Hal ini wajar apabila backend dideploy ke shared hosting sedangkan service Python dijalankan secara lokal atau terpisah.
            </p>
            <ul class="mt-2.5 text-xs text-muted-foreground space-y-1 list-disc list-inside">
                <li>Riwayat metrik akurasi, kurva pelatihan, dan confusion matrix tetap dapat dilihat di bawah ini.</li>
                <li>Untuk mengaktifkan kembali live testing: jalankan perintah <code class="px-1.5 py-0.5 rounded bg-muted font-mono text-foreground text-[11px]">uvicorn src.main:app --port 8000</code> di server/laptop Anda.</li>
            </ul>
        </div>
    @else
        <div class="rounded-xl border border-border bg-card p-4 shadow-xs flex items-center justify-between text-xs">
            <div class="flex items-center gap-2 text-muted-foreground">
                <i data-lucide="check-circle" class="h-4 w-4 text-emerald-600"></i>
                <span>Microservice FastAPI aktif dan siap menerima inferensi prediksi gambar di <strong class="text-foreground">{{ $fastApiUrl }}</strong>.</span>
            </div>
        </div>
    @endif

    <!-- 4 KPI Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="rounded-xl border border-border bg-card p-5 shadow-xs">
            <div class="text-xs font-semibold text-muted-foreground uppercase tracking-wider">Arsitektur Model</div>
            <div class="text-xl font-bold text-foreground mt-1">MobileNetV2</div>
            <p class="text-[11px] text-muted-foreground mt-1">Transfer Learning (ImageNet Weights)</p>
        </div>
        <div class="rounded-xl border border-border bg-card p-5 shadow-xs">
            <div class="text-xs font-semibold text-muted-foreground uppercase tracking-wider">Kategori Klasifikasi</div>
            <div class="text-xl font-bold text-emerald-600 dark:text-emerald-400 mt-1">3 Kelas</div>
            <p class="text-[11px] text-muted-foreground mt-1">Organic, Plastic, Paper</p>
        </div>
        <div class="rounded-xl border border-border bg-card p-5 shadow-xs">
            <div class="text-xs font-semibold text-muted-foreground uppercase tracking-wider">Target Akurasi PRD</div>
            <div class="text-xl font-bold text-blue-600 dark:text-blue-400 mt-1">&ge; 80.00%</div>
            <p class="text-[11px] text-muted-foreground mt-1">Standar kelulusan spesifikasi</p>
        </div>
        <div class="rounded-xl border border-border bg-card p-5 shadow-xs">
            <div class="text-xs font-semibold text-muted-foreground uppercase tracking-wider">Capaian Akurasi Test</div>
            <div class="text-xl font-bold text-emerald-600 dark:text-emerald-400 mt-1">96.67%</div>
            <p class="text-[11px] text-emerald-600 font-medium mt-1">&check; Target Terpenuhi (&ge; 80%)</p>
        </div>
    </div>

    <!-- Live Test Widget inside Laravel -->
    <div class="rounded-xl border border-border bg-card p-6 shadow-xs">
        <div class="flex items-center justify-between mb-1">
            <div class="flex items-center gap-2">
                <i data-lucide="sparkles" class="h-5 w-5 text-emerald-600"></i>
                <h2 class="text-base font-bold text-foreground">Uji Coba Prediksi Langsung (Live Inference)</h2>
            </div>
            @if (! $aiServiceOnline)
                <span class="inline-flex items-center gap-1 rounded-md bg-amber-500/10 px-2 py-0.5 text-[11px] font-semibold text-amber-600 border border-amber-500/20">
                    <i data-lucide="alert-triangle" class="h-3 w-3"></i>
                    Memerlukan FastAPI Aktif
                </span>
            @endif
        </div>
        <p class="text-xs text-muted-foreground mb-4">Pilih foto sampah dari komputer Anda untuk memicu inferensi model MobileNetV2 secara real-time via FastAPI.</p>

        <div class="flex flex-col sm:flex-row items-center gap-3">
            <input type="file" id="aiImageInput" accept="image/jpeg,image/png,image/webp"
                   class="block w-full text-xs text-muted-foreground file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border file:border-border file:text-xs file:font-semibold file:bg-muted file:text-foreground hover:file:bg-accent cursor-pointer">
            <button onclick="testAiInference()" id="btnAiPredict"
                    class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-primary text-primary-foreground text-xs font-semibold rounded-lg shadow-xs hover:bg-primary/90 transition-colors shrink-0">
                <i data-lucide="play" class="h-3.5 w-3.5"></i>
                <span>Jalankan Prediksi AI</span>
            </button>
        </div>

        <!-- Dynamic Error Banner on Inference Fail -->
        <div id="aiInferenceError" class="hidden mt-4 p-4 rounded-lg bg-rose-50 dark:bg-rose-950/30 border border-rose-200 dark:border-rose-900 text-rose-800 dark:text-rose-200 text-xs">
            <div class="flex items-start gap-2.5">
                <i data-lucide="alert-circle" class="h-4 w-4 text-rose-600 mt-0.5 shrink-0"></i>
                <div>
                    <strong class="font-bold">Gagal Menghubungi Server Machine Learning:</strong>
                    <p id="aiErrorMsg" class="mt-0.5 text-[11px] leading-relaxed">
                        Microservice FastAPI di <span class="font-mono">{{ $fastApiUrl }}</span> tidak merespons. Pastikan service Python dijalankan dengan <code>uvicorn src.main:app --port 8000</code>.
                    </p>
                </div>
            </div>
        </div>

        <!-- Result Box -->
        <div id="aiResultBox" class="hidden mt-4 p-4 rounded-lg bg-muted/40 border border-border">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                <div>
                    <span class="text-xs text-muted-foreground">Kategori Terprediksi:</span>
                    <div id="aiResClass" class="text-lg font-bold text-emerald-600">-</div>
                </div>
                <div class="sm:text-right">
                    <span class="text-xs text-muted-foreground">Confidence Score:</span>
                    <div id="aiResConfidence" class="text-lg font-bold text-foreground">-</div>
                </div>
            </div>
            <div class="mt-3 text-xs text-muted-foreground flex flex-col sm:flex-row sm:items-center justify-between border-t border-border pt-3 gap-2">
                <span>Waktu Inferensi: <strong id="aiResTime" class="text-foreground">-</strong></span>
                <span id="aiResProbs" class="font-mono text-[11px]"></span>
            </div>
        </div>
    </div>

    <!-- Evaluation Graphics (Side-by-Side) -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Training History Curve -->
        <div class="rounded-xl border border-border bg-card p-6 shadow-xs flex flex-col">
            <div class="pb-3 border-b border-border mb-4">
                <h3 class="font-semibold text-foreground text-sm">Kurva Pelatihan (Accuracy & Loss)</h3>
                <p class="text-xs text-muted-foreground">Grafik konvergensi nilai loss dan akurasi per epoch selama pelatihan.</p>
            </div>
            <div class="flex-1 flex items-center justify-center">
                @if ($historyImage)
                    <img src="{{ $historyImage }}" alt="Training History" class="w-full rounded-lg border border-border">
                @else
                    <div class="p-8 text-center text-xs text-muted-foreground">Grafik kurva pelatihan belum tersedia.</div>
                @endif
            </div>
        </div>

        <!-- Confusion Matrix Heatmap -->
        <div class="rounded-xl border border-border bg-card p-6 shadow-xs flex flex-col">
            <div class="pb-3 border-b border-border mb-4">
                <h3 class="font-semibold text-foreground text-sm">Confusion Matrix</h3>
                <p class="text-xs text-muted-foreground">Matriks perbandingan antara label aktual dan prediksi pada 180 data test (Real-World Dataset).</p>
            </div>
            <div class="flex-1 flex items-center justify-center">
                @if ($confusionImage)
                    <img src="{{ $confusionImage }}" alt="Confusion Matrix" class="w-full rounded-lg border border-border">
                @else
                    <div class="p-8 text-center text-xs text-muted-foreground">Confusion Matrix belum tersedia.</div>
                @endif
            </div>
        </div>
    </div>

    <!-- Classification Report Box -->
    <div class="rounded-xl border border-border bg-card p-6 shadow-xs">
        <div class="flex items-center justify-between pb-3 border-b border-border mb-3">
            <div>
                <h3 class="font-semibold text-foreground text-sm">Classification Report</h3>
                <p class="text-xs text-muted-foreground">Metrik Precision, Recall, F1-Score, dan Support pada data uji.</p>
            </div>
            <span class="inline-flex items-center rounded-md bg-emerald-500/10 px-2.5 py-1 text-xs font-semibold text-emerald-600">
                Accuracy: 96.67%
            </span>
        </div>
        <div class="rounded-lg bg-zinc-950 p-4 text-zinc-100 font-mono text-xs leading-relaxed overflow-x-auto shadow-inner">
            <pre>{{ $reportContent }}</pre>
        </div>
    </div>
</div>

@push('scripts')
<script>
    async function testAiInference() {
        const input = document.getElementById('aiImageInput');
        const btn = document.getElementById('btnAiPredict');
        const box = document.getElementById('aiResultBox');
        const errBox = document.getElementById('aiInferenceError');
        const errMsg = document.getElementById('aiErrorMsg');

        box.classList.add('hidden');
        errBox.classList.add('hidden');

        if (!input.files || input.files.length === 0) {
            alert('Silakan pilih file gambar sampah terlebih dahulu!');
            return;
        }

        btn.innerHTML = '<span class="inline-block animate-spin mr-1">&#9696;</span> Menganalisis...';
        btn.disabled = true;

        const formData = new FormData();
        formData.append('file', input.files[0]);

        try {
            const response = await fetch('{{ $fastApiUrl }}/predict', {
                method: 'POST',
                body: formData
            });
            const res = await response.json();

            if (res.success || res.status === 'success') {
                const d = res.data;
                document.getElementById('aiResClass').innerText = d.predicted_class;
                document.getElementById('aiResConfidence').innerText = (d.confidence_score * 100).toFixed(1) + '%';
                document.getElementById('aiResTime').innerText = (d.inference_time_ms ? d.inference_time_ms.toFixed(1) : '-') + ' ms';

                let probStr = '';
                if (d.probabilities) {
                    for (const [k, v] of Object.entries(d.probabilities)) {
                        probStr += `${k}: ${(v * 100).toFixed(1)}% | `;
                    }
                }
                document.getElementById('aiResProbs').innerText = probStr.slice(0, -3);
                box.classList.remove('hidden');
            } else {
                errBox.classList.remove('hidden');
                errMsg.innerText = res.message || 'Service mengembalikan galat inferensi.';
            }
        } catch (err) {
            errBox.classList.remove('hidden');
            errMsg.innerHTML = `Tidak dapat terhubung ke server FastAPI di <strong>{{ $fastApiUrl }}</strong>.<br>Keterangan: Microservice Machine Learning sedang offline atau belum di-hosting pada lingkungan produksi ini.`;
        } finally {
            btn.innerHTML = '<i data-lucide="play" class="h-3.5 w-3.5"></i><span>Jalankan Prediksi AI</span>';
            btn.disabled = false;
            lucide.createIcons();
        }
    }
</script>
@endpush
@endsection
