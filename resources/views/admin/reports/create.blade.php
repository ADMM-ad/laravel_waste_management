@extends('layouts.master')

@section('title', 'Buat Laporan Baru - Warga')

@section('content')
<style>
    .segmented-container {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        background-color: #F1F5F9;
        padding: 4px;
        border-radius: 10px;
        border: 1px solid #E2E8F0;
        gap: 4px;
    }
    .segmented-button {
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 8px 12px;
        border-radius: 8px;
        font-size: 13px;
        font-weight: 500;
        cursor: pointer;
        color: #1E293B;
        transition: all 0.2s ease-in-out;
        user-select: none;
        text-align: center;
    }
    .segmented-button:hover:not(.active) {
        background-color: rgba(0, 0, 0, 0.05);
    }
    .segmented-button.active {
        background-color: #15803D !important;
        color: #FFFFFF !important;
        font-weight: 600;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.12);
    }
</style>

<div class="space-y-6 max-w-5xl mx-auto">
    <!-- Header Page Navigation -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-border pb-4">
        <div>
            <div class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-emerald-100 text-emerald-800 dark:bg-emerald-950/60 dark:text-emerald-400 mb-1.5">
                <i data-lucide="shield-check" class="h-3.5 w-3.5"></i>
                <span>Akses Warga Pelapor</span>
            </div>
            <h1 class="text-xl sm:text-2xl font-bold tracking-tight text-foreground">Buat Laporan Baru</h1>
            <p class="text-xs sm:text-sm text-muted-foreground">Laporkan timbunan sampah liar di lingkungan Anda untuk segera diverifikasi dan ditangani petugas kebersihan.</p>
        </div>
        <a href="{{ route('admin.reports.index') }}" class="inline-flex items-center justify-center gap-2 rounded-lg border border-border bg-card px-3.5 py-2 text-xs font-semibold text-foreground shadow-xs hover:bg-accent transition-colors">
            <i data-lucide="arrow-left" class="h-3.5 w-3.5"></i>
            <span>Kembali ke Daftar</span>
        </a>
    </div>

    <!-- Alert Messages (Server-side Global Summary) -->
    @if ($errors->any())
        <div class="rounded-xl border border-destructive/20 bg-destructive/10 p-4 text-destructive text-xs">
            <div class="flex items-center gap-2 font-semibold mb-1">
                <i data-lucide="alert-circle" class="h-4 w-4"></i>
                <span>Validasi formulir laporan belum sesuai:</span>
            </div>
            <ul class="list-disc list-inside space-y-0.5 text-[11px]">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.reports.store') }}" method="POST" enctype="multipart/form-data" id="reportForm" class="space-y-6" novalidate>
        @csrf

        <!-- 1. Foto Bukti & Deteksi AI Card (Equal Height on Laptop / LG screens) -->
        <div class="rounded-xl border border-border bg-card p-5 sm:p-6 shadow-xs space-y-4">
            <div class="flex items-center gap-2 border-b border-border pb-3">
                <i data-lucide="camera" class="h-5 w-5 text-emerald-600"></i>
                <h2 class="text-base font-bold text-foreground">1. Foto Bukti & Deteksi Cerdas AI</h2>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-5 items-stretch">
                <!-- Dropzone / File Picker (Left Column) -->
                <div class="flex flex-col h-full">
                    <label class="block text-xs font-semibold text-foreground mb-1.5">Unggah Foto Sampah <span class="text-destructive">*</span></label>
                    <div id="dropzone" class="flex-1 flex flex-col items-center justify-center border-2 border-dashed border-border hover:border-emerald-500 rounded-xl p-5 text-center cursor-pointer transition-all bg-muted/20 min-h-[240px]">
                        <input type="file" name="photo" id="photoInput" accept="image/jpeg,image/png,image/webp" class="hidden">
                        
                        <div id="dropzonePrompt" class="space-y-2 py-4">
                            <div class="w-12 h-12 mx-auto rounded-full bg-emerald-50 dark:bg-emerald-950/40 text-emerald-600 dark:text-emerald-400 flex items-center justify-center">
                                <i data-lucide="upload-cloud" class="h-6 w-6"></i>
                            </div>
                            <div class="text-xs font-semibold text-foreground">Ambil Foto Sampah</div>
                            <p class="text-[11px] text-muted-foreground max-w-xs mx-auto">Pilih foto, lalu gunakan tombol Cek Kategori AI atau input manual</p>
                            <span class="inline-block text-[10px] text-muted-foreground/80 bg-muted px-2 py-0.5 rounded">JPG, PNG, atau WebP (Maks. 5MB)</span>
                        </div>

                        <div id="dropzonePreview" class="hidden relative w-full h-full flex items-center justify-center">
                            <img id="previewImg" src="" alt="Preview Sampah" class="max-h-56 max-w-full rounded-lg object-contain shadow-xs">
                            <button type="button" id="btnRemovePhoto" title="Hapus Foto" class="absolute top-1 right-1 bg-destructive text-white p-1.5 rounded-full shadow-md hover:bg-destructive/90 transition-transform active:scale-95">
                                <i data-lucide="x" class="h-3.5 w-3.5"></i>
                            </button>
                        </div>
                    </div>
                    @error('photo')
                        <p class="text-[11px] text-destructive mt-1.5 font-medium flex items-center gap-1">
                            <i data-lucide="alert-circle" class="h-3 w-3"></i>
                            <span>{{ $message }}</span>
                        </p>
                    @enderror
                    <p id="clientErrorPhoto" class="hidden text-[11px] text-destructive mt-1.5 font-medium flex items-center gap-1">
                        <i data-lucide="alert-circle" class="h-3 w-3"></i>
                        <span>Foto bukti sampah wajib diunggah.</span>
                    </p>
                </div>

                <!-- AI Classification Panel (Right Column - Equal Height) -->
                <div class="rounded-xl border border-emerald-100 dark:border-emerald-900/30 bg-emerald-50/50 dark:bg-emerald-950/20 p-5 flex flex-col justify-between h-full space-y-4">
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <div class="flex items-center gap-1.5 text-xs font-bold text-emerald-700 dark:text-emerald-400">
                                <i data-lucide="sparkles" class="h-4 w-4"></i>
                                <span>Klasifikasi Otomatis AI (MobileNetV2)</span>
                            </div>
                            <span id="aiStatusBadge" class="text-[10px] px-2 py-0.5 rounded-full bg-muted text-muted-foreground font-medium">Siap</span>
                        </div>

                        <p class="text-[11px] text-muted-foreground leading-relaxed">
                            Sistem dapat mengenali jenis sampah (<strong>Organik</strong>, <strong>Plastik</strong>, <strong>Kertas</strong>) secara otomatis dari foto menggunakan model MobileNetV2.
                        </p>
                    </div>

                    <!-- AI Result Display Card (Mirrors Flutter AiResultCardWidget) -->
                    <div id="aiResultCard" class="hidden border border-emerald-200 dark:border-emerald-800 rounded-lg p-3.5 bg-white dark:bg-card space-y-2.5 shadow-xs">
                        <div class="flex items-center justify-between text-xs">
                            <span class="text-muted-foreground">Kategori Terdeteksi:</span>
                            <span id="aiPredictedText" class="font-bold text-emerald-600 dark:text-emerald-400 text-sm uppercase">-</span>
                        </div>
                        <div class="flex items-center justify-between text-xs">
                            <span class="text-muted-foreground">Tingkat Keyakinan:</span>
                            <span id="aiConfidenceText" class="font-bold text-foreground">0%</span>
                        </div>
                        <div class="w-full bg-muted rounded-full h-2 overflow-hidden">
                            <div id="aiConfidenceBar" class="bg-emerald-500 h-2 rounded-full transition-all duration-500" style="width: 0%"></div>
                        </div>

                        <!-- Class Probabilities Breakdown -->
                        <div id="aiProbabilitiesContainer" class="pt-2 border-t border-border/60 space-y-1.5 text-[11px]">
                            <!-- Populated dynamically via JS -->
                        </div>

                        <p class="text-[10px] text-emerald-600 dark:text-emerald-400 italic pt-1">
                            ✓ Kategori di formulir otomatis terpilih berdasarkan hasil AI di atas.
                        </p>
                    </div>

                    <div>
                        <button type="button" id="btnTriggerAi" class="w-full inline-flex items-center justify-center gap-2 py-2.5 px-4 rounded-lg bg-emerald-600 text-white text-xs font-semibold hover:bg-emerald-700 transition-colors shadow-xs disabled:opacity-50">
                            <i data-lucide="cpu" class="h-4 w-4"></i>
                            <span id="btnTriggerAiText">Cek Kategori AI</span>
                        </button>
                    </div>

                    <!-- Hidden AI Fields for Form Submission -->
                    <input type="hidden" name="ai_predicted_category" id="aiPredictedCategoryInput" value="{{ old('ai_predicted_category') }}">
                    <input type="hidden" name="ai_confidence_score" id="aiConfidenceScoreInput" value="{{ old('ai_confidence_score') }}">
                </div>
            </div>
        </div>

        <!-- 2. Detail Data Laporan Card -->
        <div class="rounded-xl border border-border bg-card p-5 sm:p-6 shadow-xs space-y-4">
            <div class="flex items-center gap-2 border-b border-border pb-3">
                <i data-lucide="file-text" class="h-5 w-5 text-emerald-600"></i>
                <h2 class="text-base font-bold text-foreground">2. Keterangan Laporan</h2>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <!-- Category Select (Mirrors Flutter SearchableDropdown) -->
                <div class="md:col-span-2">
                    <label class="block text-xs font-semibold text-foreground mb-1.5">Kategori Sampah <span class="text-destructive">*</span></label>
                    <select name="category_id" id="categorySelect" class="tom-select" placeholder="Cari atau pilih jenis sampah..." required>
                        <option value="">Cari atau pilih jenis sampah...</option>
                        @foreach ($categories as $cat)
                            <option value="{{ $cat->id }}" data-name="{{ strtolower($cat->name) }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>
                                {{ $cat->name }} - {{ $cat->description }}
                            </option>
                        @endforeach
                    </select>
                    @error('category_id')
                        <p class="text-[11px] text-destructive mt-1.5 font-medium flex items-center gap-1">
                            <i data-lucide="alert-circle" class="h-3 w-3"></i>
                            <span>{{ $message }}</span>
                        </p>
                    @enderror
                    <p id="clientErrorCategory" class="hidden text-[11px] text-destructive mt-1.5 font-medium flex items-center gap-1">
                        <i data-lucide="alert-circle" class="h-3 w-3"></i>
                        <span>Pilih salah satu kategori sampah.</span>
                    </p>
                </div>

                <!-- Description Input -->
                <div class="md:col-span-2">
                    <div class="flex justify-between items-center mb-1.5">
                        <label class="text-xs font-semibold text-foreground">Deskripsi Kondisi Sampah <span class="text-destructive">*</span></label>
                        <span id="descCount" class="text-[11px] text-muted-foreground">0 / 10 min</span>
                    </div>
                    <textarea name="description" id="descInput" rows="3" required minlength="10"
                              class="w-full rounded-lg border border-input bg-background p-3 text-xs text-foreground placeholder:text-muted-foreground focus:outline-none focus:ring-1 focus:ring-ring"
                              placeholder="Jelaskan kondisi tumpukan sampah, patokan jalan, dll...">{{ old('description') }}</textarea>
                    @error('description')
                        <p class="text-[11px] text-destructive mt-1.5 font-medium flex items-center gap-1">
                            <i data-lucide="alert-circle" class="h-3 w-3"></i>
                            <span>{{ $message }}</span>
                        </p>
                    @enderror
                    <p id="clientErrorDesc" class="hidden text-[11px] text-destructive mt-1.5 font-medium flex items-center gap-1">
                        <i data-lucide="alert-circle" class="h-3 w-3"></i>
                        <span id="clientErrorDescText">Deskripsi minimal 10 karakter.</span>
                    </p>
                </div>

                <!-- Scale Volume Selector (100% Mirrored from Flutter SegmentedButton) -->
                <div>
                    <label class="block text-xs font-semibold text-foreground mb-1.5">Skala Volume Sampah <span class="text-destructive">*</span></label>
                    <div class="segmented-container" id="scaleSegmentedContainer">
                        <label class="segmented-button {{ old('scale', 'small') == 'small' ? 'active' : '' }}" onclick="selectSegment('scale', 'small', this)">
                            <input type="radio" name="scale" value="small" {{ old('scale', 'small') == 'small' ? 'checked' : '' }} class="hidden">
                            <span>Kecil</span>
                        </label>
                        <label class="segmented-button {{ old('scale', 'small') == 'medium' ? 'active' : '' }}" onclick="selectSegment('scale', 'medium', this)">
                            <input type="radio" name="scale" value="medium" {{ old('scale', 'small') == 'medium' ? 'checked' : '' }} class="hidden">
                            <span>Sedang</span>
                        </label>
                        <label class="segmented-button {{ old('scale', 'small') == 'large' ? 'active' : '' }}" onclick="selectSegment('scale', 'large', this)">
                            <input type="radio" name="scale" value="large" {{ old('scale', 'small') == 'large' ? 'checked' : '' }} class="hidden">
                            <span>Besar</span>
                        </label>
                    </div>
                    @error('scale')
                        <p class="text-[11px] text-destructive mt-1.5 font-medium flex items-center gap-1">
                            <i data-lucide="alert-circle" class="h-3 w-3"></i>
                            <span>{{ $message }}</span>
                        </p>
                    @enderror
                </div>

                <!-- Urgency Selector (100% Mirrored from Flutter SegmentedButton) -->
                <div>
                    <label class="block text-xs font-semibold text-foreground mb-1.5">Tingkat Urgensi <span class="text-destructive">*</span></label>
                    <div class="segmented-container" id="urgencySegmentedContainer">
                        <label class="segmented-button {{ old('urgency', 'low') == 'low' ? 'active' : '' }}" onclick="selectSegment('urgency', 'low', this)">
                            <input type="radio" name="urgency" value="low" {{ old('urgency', 'low') == 'low' ? 'checked' : '' }} class="hidden">
                            <span>Rendah</span>
                        </label>
                        <label class="segmented-button {{ old('urgency', 'low') == 'medium' ? 'active' : '' }}" onclick="selectSegment('urgency', 'medium', this)">
                            <input type="radio" name="urgency" value="medium" {{ old('urgency', 'low') == 'medium' ? 'checked' : '' }} class="hidden">
                            <span>Sedang</span>
                        </label>
                        <label class="segmented-button {{ old('urgency', 'low') == 'high' ? 'active' : '' }}" onclick="selectSegment('urgency', 'high', this)">
                            <input type="radio" name="urgency" value="high" {{ old('urgency', 'low') == 'high' ? 'checked' : '' }} class="hidden">
                            <span>Darurat</span>
                        </label>
                    </div>
                    @error('urgency')
                        <p class="text-[11px] text-destructive mt-1.5 font-medium flex items-center gap-1">
                            <i data-lucide="alert-circle" class="h-3 w-3"></i>
                            <span>{{ $message }}</span>
                        </p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- 3. Titik Lokasi & Peta Card -->
        <div class="rounded-xl border border-border bg-card p-5 sm:p-6 shadow-xs space-y-4">
            <div class="flex items-center justify-between border-b border-border pb-3">
                <div class="flex items-center gap-2">
                    <i data-lucide="map-pin" class="h-5 w-5 text-emerald-600"></i>
                    <h2 class="text-base font-bold text-foreground">3. Lokasi Koordinat GPS & Peta</h2>
                </div>
                <button type="button" id="btnGps" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold rounded-lg bg-emerald-50 text-emerald-700 dark:bg-emerald-950/50 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800 hover:bg-emerald-100 transition-colors">
                    <i data-lucide="crosshair" class="h-3.5 w-3.5"></i>
                    <span>Deteksi GPS Saya</span>
                </button>
            </div>

            <!-- Interactive Leaflet Map -->
            <div class="space-y-1.5">
                <div class="text-[11px] text-muted-foreground">Klik atau geser pin merah pada peta di bawah ini untuk menandai titik lokasi sampah secara akurat:</div>
                <div id="mapContainer" class="h-64 w-full rounded-xl border border-border overflow-hidden z-10"></div>
            </div>

            <!-- Coordinates & Address Input -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-foreground mb-1">Latitude <span class="text-destructive">*</span></label>
                    <input type="text" name="latitude" id="latInput" value="{{ old('latitude', '-6.208800') }}" required readonly
                           class="w-full rounded-lg border border-input bg-muted/30 px-3 py-2 text-xs text-foreground font-mono focus:outline-none">
                    @error('latitude')
                        <p class="text-[11px] text-destructive mt-1 font-medium">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="block text-xs font-semibold text-foreground mb-1">Longitude <span class="text-destructive">*</span></label>
                    <input type="text" name="longitude" id="lngInput" value="{{ old('longitude', '106.845600') }}" required readonly
                           class="w-full rounded-lg border border-input bg-muted/30 px-3 py-2 text-xs text-foreground font-mono focus:outline-none">
                    @error('longitude')
                        <p class="text-[11px] text-destructive mt-1 font-medium">{{ $message }}</p>
                    @enderror
                </div>
                <div class="sm:col-span-2">
                    <label class="block text-xs font-semibold text-foreground mb-1">Nama Jalan / Patokan Alamat <span class="text-destructive">*</span></label>
                    <input type="text" name="address" id="addressInput" value="{{ old('address') }}" required
                           class="w-full rounded-lg border border-input bg-background px-3 py-2 text-xs text-foreground focus:outline-none focus:ring-1 focus:ring-ring"
                           placeholder="Jelaskan patokan jalan, nomor rumah, atau landmark terdekat...">
                    @error('address')
                        <p class="text-[11px] text-destructive mt-1.5 font-medium flex items-center gap-1">
                            <i data-lucide="alert-circle" class="h-3 w-3"></i>
                            <span>{{ $message }}</span>
                        </p>
                    @enderror
                    <p id="clientErrorAddress" class="hidden text-[11px] text-destructive mt-1.5 font-medium flex items-center gap-1">
                        <i data-lucide="alert-circle" class="h-3 w-3"></i>
                        <span>Alamat atau patokan jalan wajib diisi.</span>
                    </p>
                </div>
            </div>
            <p id="clientErrorGps" class="hidden text-[11px] text-destructive mt-1 font-medium flex items-center gap-1">
                <i data-lucide="alert-circle" class="h-3 w-3"></i>
                <span>Titik lokasi koordinat GPS belum terdeteksi. Silakan klik Deteksi GPS Saya atau klik pada peta.</span>
            </p>
        </div>

        <!-- Submit & Actions -->
        <div class="flex items-center justify-end gap-3 pt-2">
            <a href="{{ route('admin.reports.index') }}" class="px-5 py-2.5 rounded-lg border border-border bg-card text-xs font-semibold text-foreground hover:bg-accent transition-colors">
                Batal
            </a>
            <button type="submit" id="btnSubmitReport" class="inline-flex items-center justify-center gap-2 px-6 py-2.5 rounded-lg bg-primary text-primary-foreground text-xs font-semibold shadow-sm hover:bg-primary/90 transition-colors">
                <i data-lucide="send" class="h-4 w-4"></i>
                <span id="btnSubmitText">Kirim Laporan Sampah</span>
            </button>
        </div>
    </form>
</div>

<!-- Leaflet CSS & JS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

@push('scripts')
<script>

    function selectSegment(name, value, element) {
        const container = element.parentElement;
        container.querySelectorAll('.segmented-button').forEach(btn => btn.classList.remove('active'));
        element.classList.add('active');
        const input = element.querySelector('input[type="radio"]');
        if (input) {
            input.checked = true;
        }
    }

    // 1. Photo Dropzone & Preview
    const dropzone = document.getElementById('dropzone');
    const photoInput = document.getElementById('photoInput');
    const dropzonePrompt = document.getElementById('dropzonePrompt');
    const dropzonePreview = document.getElementById('dropzonePreview');
    const previewImg = document.getElementById('previewImg');
    const btnRemovePhoto = document.getElementById('btnRemovePhoto');
    const clientErrorPhoto = document.getElementById('clientErrorPhoto');

    dropzone.addEventListener('click', () => photoInput.click());
    photoInput.addEventListener('change', handleFileSelect);

    dropzone.addEventListener('dragover', (e) => {
        e.preventDefault();
        dropzone.classList.add('border-emerald-500', 'bg-emerald-50/20');
    });
    dropzone.addEventListener('dragleave', () => {
        dropzone.classList.remove('border-emerald-500', 'bg-emerald-50/20');
    });
    dropzone.addEventListener('drop', (e) => {
        e.preventDefault();
        dropzone.classList.remove('border-emerald-500', 'bg-emerald-50/20');
        if (e.dataTransfer.files.length) {
            photoInput.files = e.dataTransfer.files;
            handleFileSelect();
        }
    });

    btnRemovePhoto.addEventListener('click', (e) => {
        e.stopPropagation();
        photoInput.value = '';
        dropzonePreview.classList.add('hidden');
        dropzonePrompt.classList.remove('hidden');
        document.getElementById('aiResultCard').classList.add('hidden');
        document.getElementById('aiStatusBadge').textContent = 'Siap';
        document.getElementById('aiStatusBadge').className = 'text-[10px] px-2 py-0.5 rounded-full bg-muted text-muted-foreground font-medium';
        document.getElementById('aiPredictedCategoryInput').value = '';
        document.getElementById('aiConfidenceScoreInput').value = '';
    });

    function handleFileSelect() {
        const file = photoInput.files[0];
        if (!file) return;

        clientErrorPhoto.classList.add('hidden');
        dropzone.classList.remove('border-destructive');

        const reader = new FileReader();
        reader.onload = (e) => {
            previewImg.src = e.target.result;
            dropzonePrompt.classList.add('hidden');
            dropzonePreview.classList.remove('hidden');
            // Auto trigger AI classification
            triggerAiClassification();
        };
        reader.readAsDataURL(file);
    }

    // 2. Machine Learning AI Classification (Matches Flutter & FastAPI)
    const btnTriggerAi = document.getElementById('btnTriggerAi');
    const btnTriggerAiText = document.getElementById('btnTriggerAiText');
    const aiStatusBadge = document.getElementById('aiStatusBadge');
    const aiResultCard = document.getElementById('aiResultCard');
    const aiPredictedText = document.getElementById('aiPredictedText');
    const aiConfidenceText = document.getElementById('aiConfidenceText');
    const aiConfidenceBar = document.getElementById('aiConfidenceBar');
    const aiProbabilitiesContainer = document.getElementById('aiProbabilitiesContainer');
    const aiPredictedCategoryInput = document.getElementById('aiPredictedCategoryInput');
    const aiConfidenceScoreInput = document.getElementById('aiConfidenceScoreInput');

    btnTriggerAi.addEventListener('click', triggerAiClassification);

    async function triggerAiClassification() {
        const file = photoInput.files[0];
        if (!file) {
            clientErrorPhoto.classList.remove('hidden');
            dropzone.classList.add('border-destructive');
            dropzone.scrollIntoView({ behavior: 'smooth', block: 'center' });
            return;
        }

        btnTriggerAi.disabled = true;
        btnTriggerAiText.textContent = 'Menganalisis...';
        aiStatusBadge.textContent = 'Memproses...';
        aiStatusBadge.className = 'text-[10px] px-2 py-0.5 rounded-full bg-amber-100 text-amber-800 font-medium';

        const formData = new FormData();
        formData.append('image', file);
        formData.append('_token', '{{ csrf_token() }}');

        try {
            const res = await fetch('{{ route('admin.reports.classify') }}', {
                method: 'POST',
                body: formData,
            });
            const data = await res.json();

            if (data.success && data.data) {
                // Defensive extraction handling nested or direct prediction data
                const resData = (data.data && data.data.data) ? data.data.data : data.data;
                const predictedClass = resData.predicted_class || 'Unknown';
                const confidence = parseFloat(resData.confidence_score) || 0;
                const percent = Math.round(confidence * 100);

                aiPredictedText.textContent = predictedClass;
                aiConfidenceText.textContent = percent + '%';
                aiConfidenceBar.style.width = percent + '%';

                // Render probabilities breakdown if provided by model
                if (resData.probabilities && typeof resData.probabilities === 'object') {
                    let probHtml = '<div class="text-[10px] font-semibold text-muted-foreground uppercase tracking-wider mb-1">Distribusi Probabilitas Kelas:</div>';
                    for (const [cls, val] of Object.entries(resData.probabilities)) {
                        const pVal = Math.round((parseFloat(val) || 0) * 100);
                        const isWinner = cls.toLowerCase() === predictedClass.toLowerCase();
                        probHtml += `
                            <div class="flex items-center justify-between ${isWinner ? 'font-bold text-emerald-700 dark:text-emerald-400' : 'text-muted-foreground'}">
                                <span>${cls.charAt(0).toUpperCase() + cls.slice(1)}:</span>
                                <span>${pVal}%</span>
                            </div>
                        `;
                    }
                    aiProbabilitiesContainer.innerHTML = probHtml;
                } else {
                    aiProbabilitiesContainer.innerHTML = '';
                }

                aiResultCard.classList.remove('hidden');

                aiPredictedCategoryInput.value = predictedClass;
                aiConfidenceScoreInput.value = confidence;

                aiStatusBadge.textContent = 'Berhasil';
                aiStatusBadge.className = 'text-[10px] px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-800 font-medium';

                // Auto match category dropdown
                autoSelectCategory(predictedClass);
            } else {
                throw new Error(data.message || 'Gagal klasifikasi');
            }
        } catch (err) {
            aiStatusBadge.textContent = 'Offline / Gagal';
            aiStatusBadge.className = 'text-[10px] px-2 py-0.5 rounded-full bg-rose-100 text-rose-800 font-medium';
            console.warn('AI Classifier Error:', err);
        } finally {
            btnTriggerAi.disabled = false;
            btnTriggerAiText.textContent = 'Cek Kategori AI';
        }
    }

    function autoSelectCategory(predictedClass) {
        const categorySelect = document.getElementById('categorySelect');
        const searchWord = predictedClass.toLowerCase();

        for (let i = 0; i < categorySelect.options.length; i++) {
            const opt = categorySelect.options[i];
            const optText = (opt.text || '').toLowerCase();
            const optData = (opt.getAttribute('data-name') || '').toLowerCase();
            
            if (searchWord.includes('organ') && (optText.includes('organ') || optData.includes('organ'))) {
                categorySelect.selectedIndex = i;
                break;
            } else if (searchWord.includes('plast') && (optText.includes('plast') || optData.includes('plast'))) {
                categorySelect.selectedIndex = i;
                break;
            } else if (searchWord.includes('paper') && (optText.includes('kertas') || optData.includes('kertas') || optText.includes('paper'))) {
                categorySelect.selectedIndex = i;
                break;
            }
        }

        // If TomSelect initialized
        if (categorySelect.tomselect) {
            categorySelect.tomselect.setValue(categorySelect.value);
        }

        // Clear category error if any
        document.getElementById('clientErrorCategory').classList.add('hidden');
    }

    // 3. Description Count
    const descInput = document.getElementById('descInput');
    const descCount = document.getElementById('descCount');
    const clientErrorDesc = document.getElementById('clientErrorDesc');
    const clientErrorDescText = document.getElementById('clientErrorDescText');

    descInput.addEventListener('input', () => {
        const len = descInput.value.trim().length;
        descCount.textContent = len + ' / 10 min';
        if (len >= 10) {
            descCount.className = 'text-[11px] text-emerald-600 font-medium';
            clientErrorDesc.classList.add('hidden');
            descInput.classList.remove('border-destructive');
        } else {
            descCount.className = 'text-[11px] text-muted-foreground';
        }
    });

    // 4. Leaflet Map & GPS Geolocation
    let defaultLat = parseFloat(document.getElementById('latInput').value) || -6.2088;
    let defaultLng = parseFloat(document.getElementById('lngInput').value) || 106.8456;

    const map = L.map('mapContainer').setView([defaultLat, defaultLng], 14);
    L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '&copy; OpenStreetMap contributors'
    }).addTo(map);

    let marker = L.marker([defaultLat, defaultLng], { draggable: true }).addTo(map);

    function updateCoordinates(lat, lng) {
        document.getElementById('latInput').value = lat.toFixed(6);
        document.getElementById('lngInput').value = lng.toFixed(6);
        document.getElementById('clientErrorGps').classList.add('hidden');
        reverseGeocode(lat, lng);
    }

    marker.on('dragend', function (e) {
        const position = marker.getLatLng();
        updateCoordinates(position.lat, position.lng);
    });

    map.on('click', function (e) {
        marker.setLatLng(e.latlng);
        updateCoordinates(e.latlng.lat, e.latlng.lng);
    });

    async function reverseGeocode(lat, lng) {
        try {
            const res = await fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}`);
            const data = await res.json();
            if (data && data.display_name) {
                document.getElementById('addressInput').value = data.display_name;
                document.getElementById('clientErrorAddress').classList.add('hidden');
            }
        } catch (_) {
            if (!document.getElementById('addressInput').value) {
                document.getElementById('addressInput').value = `Titik koordinat (${lat.toFixed(5)}, ${lng.toFixed(5)})`;
                document.getElementById('clientErrorAddress').classList.add('hidden');
            }
        }
    }

    // GPS Button
    document.getElementById('btnGps').addEventListener('click', () => {
        if (!navigator.geolocation) {
            alert('Browser Anda tidak mendukung deteksi lokasi GPS.');
            return;
        }
        navigator.geolocation.getCurrentPosition(
            (pos) => {
                const lat = pos.coords.latitude;
                const lng = pos.coords.longitude;
                map.setView([lat, lng], 16);
                marker.setLatLng([lat, lng]);
                updateCoordinates(lat, lng);
            },
            (err) => {
                alert('Gagal mendeteksi lokasi GPS: ' + err.message);
            },
            { enableHighAccuracy: true, timeout: 10000 }
        );
    });

    // Address input clearing client error
    const addressInput = document.getElementById('addressInput');
    const clientErrorAddress = document.getElementById('clientErrorAddress');
    addressInput.addEventListener('input', () => {
        if (addressInput.value.trim().length > 0) {
            clientErrorAddress.classList.add('hidden');
            addressInput.classList.remove('border-destructive');
        }
    });

    // 5. Interactive Client-side Form Validation on Submit (Matches Flutter)
    const reportForm = document.getElementById('reportForm');
    const btnSubmitReport = document.getElementById('btnSubmitReport');
    const btnSubmitText = document.getElementById('btnSubmitText');

    reportForm.addEventListener('submit', function (e) {
        let isValid = true;
        let firstInvalidEl = null;

        // A. Validate Photo
        if (!photoInput.files || photoInput.files.length === 0) {
            isValid = false;
            clientErrorPhoto.classList.remove('hidden');
            dropzone.classList.add('border-destructive');
            if (!firstInvalidEl) firstInvalidEl = dropzone;
        } else {
            clientErrorPhoto.classList.add('hidden');
            dropzone.classList.remove('border-destructive');
        }

        // B. Validate Category
        const categoryVal = document.getElementById('categorySelect').value;
        const clientErrorCategory = document.getElementById('clientErrorCategory');
        if (!categoryVal) {
            isValid = false;
            clientErrorCategory.classList.remove('hidden');
            if (!firstInvalidEl) firstInvalidEl = clientErrorCategory;
        } else {
            clientErrorCategory.classList.add('hidden');
        }

        // C. Validate Description
        const descVal = descInput.value.trim();
        if (descVal.length === 0) {
            isValid = false;
            clientErrorDescText.textContent = 'Deskripsi wajib diisi.';
            clientErrorDesc.classList.remove('hidden');
            descInput.classList.add('border-destructive');
            if (!firstInvalidEl) firstInvalidEl = descInput;
        } else if (descVal.length < 10) {
            isValid = false;
            clientErrorDescText.textContent = `Deskripsi minimal 10 karakter (saat ini ${descVal.length}).`;
            clientErrorDesc.classList.remove('hidden');
            descInput.classList.add('border-destructive');
            if (!firstInvalidEl) firstInvalidEl = descInput;
        } else {
            clientErrorDesc.classList.add('hidden');
            descInput.classList.remove('border-destructive');
        }

        // D. Validate Coordinates
        const latVal = document.getElementById('latInput').value;
        const lngVal = document.getElementById('lngInput').value;
        const clientErrorGps = document.getElementById('clientErrorGps');
        if (!latVal || !lngVal || isNaN(parseFloat(latVal)) || isNaN(parseFloat(lngVal))) {
            isValid = false;
            clientErrorGps.classList.remove('hidden');
            if (!firstInvalidEl) firstInvalidEl = clientErrorGps;
        } else {
            clientErrorGps.classList.add('hidden');
        }

        // E. Validate Address
        const addrVal = addressInput.value.trim();
        if (addrVal.length === 0) {
            isValid = false;
            clientErrorAddress.classList.remove('hidden');
            addressInput.classList.add('border-destructive');
            if (!firstInvalidEl) firstInvalidEl = addressInput;
        } else {
            clientErrorAddress.classList.add('hidden');
            addressInput.classList.remove('border-destructive');
        }

        if (!isValid) {
            e.preventDefault();
            if (firstInvalidEl) {
                firstInvalidEl.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
            return false;
        }

        // Loading state
        btnSubmitReport.disabled = true;
        btnSubmitText.textContent = 'Mengirim Laporan...';
    });
</script>
@endpush
@endsection
