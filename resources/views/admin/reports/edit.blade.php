@extends('layouts.master')

@section('title', 'Edit Laporan ' . $report->tracking_code)

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

<div class="max-w-4xl mx-auto space-y-6">
    <!-- Breadcrumb & Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 text-xs text-muted-foreground mb-1">
                <a href="{{ route('admin.reports.index') }}" class="hover:text-foreground transition-colors">Laporan Sampah</a>
                <span>/</span>
                <span class="text-foreground font-medium">Edit Laporan {{ $report->tracking_code }}</span>
            </div>
            <h1 class="text-xl sm:text-2xl font-bold tracking-tight text-foreground">Edit Laporan Sampah</h1>
            <p class="text-xs sm:text-sm text-muted-foreground">Perbarui informasi laporan sampah Anda sebelum diverifikasi oleh petugas kebersihan.</p>
        </div>
        <a href="{{ route('admin.reports.index') }}" class="inline-flex items-center justify-center gap-2 rounded-lg border border-border bg-card px-3.5 py-2 text-xs font-semibold text-foreground shadow-xs hover:bg-accent transition-colors">
            <i data-lucide="arrow-left" class="h-3.5 w-3.5"></i>
            <span>Kembali ke Daftar</span>
        </a>
    </div>

    <!-- Alert Messages -->
    @if ($errors->any())
        <div class="rounded-xl border border-destructive/20 bg-destructive/10 p-4 text-destructive text-xs">
            <div class="flex items-center gap-2 font-semibold mb-1">
                <i data-lucide="alert-circle" class="h-4 w-4"></i>
                <span>Terdapat kesalahan pada formulir:</span>
            </div>
            <ul class="list-disc list-inside space-y-0.5 text-[11px]">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.reports.update', $report->id) }}" method="POST" enctype="multipart/form-data" id="reportForm" class="space-y-6">
        @csrf
        @method('PUT')

        <!-- 1. Foto Bukti Card -->
        <div class="rounded-xl border border-border bg-card p-5 sm:p-6 shadow-xs space-y-4">
            <div class="flex items-center gap-2 border-b border-border pb-3">
                <i data-lucide="camera" class="h-5 w-5 text-emerald-600"></i>
                <h2 class="text-base font-bold text-foreground">1. Foto Bukti Sampah</h2>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5 items-stretch">
                <div>
                    <label class="block text-xs font-semibold text-foreground mb-1.5">Foto Sampah Saat Ini / Ganti Foto Baru</label>
                    <div id="dropzone" class="border-2 border-dashed border-border hover:border-emerald-500 rounded-xl p-4 text-center cursor-pointer transition-all bg-muted/20">
                        <input type="file" name="photo" id="photoInput" accept="image/jpeg,image/png,image/webp" class="hidden">
                        <div id="dropzonePreview" class="relative">
                            <img id="previewImg" src="{{ asset('storage/' . $report->photo_path) }}" alt="Preview" class="max-h-56 mx-auto rounded-lg object-contain shadow-xs">
                            <p class="text-[11px] text-muted-foreground mt-2">Klik foto di atas jika ingin mengganti dengan foto baru.</p>
                        </div>
                    </div>
                </div>

                <!-- AI Classification Panel -->
                <div class="rounded-xl border border-emerald-100 dark:border-emerald-900/30 bg-emerald-50/50 dark:bg-emerald-950/20 p-4 space-y-3">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-1.5 text-xs font-bold text-emerald-700 dark:text-emerald-400">
                            <i data-lucide="sparkles" class="h-4 w-4"></i>
                            <span>Klasifikasi Otomatis AI (MobileNetV2)</span>
                        </div>
                        <span id="aiStatusBadge" class="text-[10px] px-2 py-0.5 rounded-full bg-muted text-muted-foreground font-medium">
                            {{ $report->ai_predicted_category ? 'Terkait AI' : 'Tersedia' }}
                        </span>
                    </div>

                    <button type="button" id="btnTriggerAi" class="w-full inline-flex items-center justify-center gap-2 py-2 px-3 rounded-lg bg-emerald-600 text-white text-xs font-semibold hover:bg-emerald-700 transition-colors shadow-xs disabled:opacity-50">
                        <i data-lucide="cpu" class="h-3.5 w-3.5"></i>
                        <span id="btnTriggerAiText">Uji Analisis AI untuk Foto Ini</span>
                    </button>

                    <div id="aiResultCard" class="{{ $report->ai_predicted_category ? '' : 'hidden' }} border border-emerald-200 dark:border-emerald-800 rounded-lg p-3 bg-white dark:bg-card space-y-2">
                        <div class="flex items-center justify-between text-xs">
                            <span class="text-muted-foreground">Kategori AI:</span>
                            <span id="aiPredictedText" class="font-bold text-emerald-600 uppercase">{{ $report->ai_predicted_category ?? '-' }}</span>
                        </div>
                        <div class="flex items-center justify-between text-xs">
                            <span class="text-muted-foreground">Tingkat Keyakinan:</span>
                            <span id="aiConfidenceText" class="font-bold text-foreground">{{ $report->ai_confidence_score ? round($report->ai_confidence_score * 100) . '%' : '-' }}</span>
                        </div>
                    </div>

                    <!-- Hidden AI Fields -->
                    <input type="hidden" name="ai_predicted_category" id="aiPredictedCategoryInput" value="{{ old('ai_predicted_category', $report->ai_predicted_category) }}">
                    <input type="hidden" name="ai_confidence_score" id="aiConfidenceScoreInput" value="{{ old('ai_confidence_score', $report->ai_confidence_score) }}">
                </div>
            </div>
        </div>

        <!-- 2. Detail Data Laporan Card -->
        <div class="rounded-xl border border-border bg-card p-5 sm:p-6 shadow-xs space-y-4">
            <div class="flex items-center gap-2 border-b border-border pb-3">
                <i data-lucide="file-text" class="h-5 w-5 text-emerald-600"></i>
                <h2 class="text-base font-bold text-foreground">2. Keterangan Laporan</h2>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Category Select -->
                <div class="md:col-span-2">
                    <label class="block text-xs font-semibold text-foreground mb-1.5">Kategori Sampah <span class="text-destructive">*</span></label>
                    <select name="category_id" id="categorySelect" class="tom-select" placeholder="Pilih kategori sampah..." required>
                        <option value="">Pilih Kategori Sampah...</option>
                        @foreach ($categories as $cat)
                            <option value="{{ $cat->id }}" data-name="{{ strtolower($cat->name) }}" {{ old('category_id', $report->category_id) == $cat->id ? 'selected' : '' }}>
                                {{ $cat->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Description -->
                <div class="md:col-span-2">
                    <div class="flex justify-between items-center mb-1.5">
                        <label class="text-xs font-semibold text-foreground">Deskripsi Kondisi Sampah <span class="text-destructive">*</span></label>
                        <span id="descCount" class="text-[11px] text-emerald-600 font-medium">{{ strlen($report->description) }} / 10 min</span>
                    </div>
                    <textarea name="description" id="descInput" rows="3" required minlength="10"
                              class="w-full rounded-lg border border-input bg-background p-3 text-xs text-foreground placeholder:text-muted-foreground focus:outline-none focus:ring-1 focus:ring-ring"
                              placeholder="Jelaskan kondisi tumpukan sampah, patokan jalan, dll...">{{ old('description', $report->description) }}</textarea>
                </div>

                <!-- Scale Volume (100% Mirrored from Flutter SegmentedButton) -->
                <div>
                    <label class="block text-xs font-semibold text-foreground mb-1.5">Skala Volume Sampah <span class="text-destructive">*</span></label>
                    <div class="segmented-container" id="scaleSegmentedContainer">
                        @foreach (['small' => 'Kecil', 'medium' => 'Sedang', 'large' => 'Besar'] as $val => $label)
                            <label class="segmented-button {{ old('scale', $report->scale) == $val ? 'active' : '' }}" onclick="selectSegment('scale', '{{ $val }}', this)">
                                <input type="radio" name="scale" value="{{ $val }}" {{ old('scale', $report->scale) == $val ? 'checked' : '' }} class="hidden">
                                <span>{{ $label }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>

                <!-- Urgency (100% Mirrored from Flutter SegmentedButton) -->
                <div>
                    <label class="block text-xs font-semibold text-foreground mb-1.5">Tingkat Urgensi <span class="text-destructive">*</span></label>
                    <div class="segmented-container" id="urgencySegmentedContainer">
                        @foreach (['low' => 'Rendah', 'medium' => 'Sedang', 'high' => 'Darurat'] as $val => $label)
                            <label class="segmented-button {{ old('urgency', $report->urgency) == $val ? 'active' : '' }}" onclick="selectSegment('urgency', '{{ $val }}', this)">
                                <input type="radio" name="urgency" value="{{ $val }}" {{ old('urgency', $report->urgency) == $val ? 'checked' : '' }} class="hidden">
                                <span>{{ $label }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <!-- 3. Titik Lokasi & Peta Card -->
        <div class="rounded-xl border border-border bg-card p-5 sm:p-6 shadow-xs space-y-4">
            <div class="flex items-center justify-between border-b border-border pb-3">
                <div class="flex items-center gap-2">
                    <i data-lucide="map-pin" class="h-5 w-5 text-emerald-600"></i>
                    <h2 class="text-base font-bold text-foreground">3. Titik Lokasi Koordinat & Peta</h2>
                </div>
                <button type="button" id="btnGps" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold rounded-lg bg-emerald-50 text-emerald-700 dark:bg-emerald-950/50 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800 hover:bg-emerald-100 transition-colors">
                    <i data-lucide="crosshair" class="h-3.5 w-3.5"></i>
                    <span>Deteksi GPS Saya</span>
                </button>
            </div>

            <div class="space-y-1.5">
                <div class="text-[11px] text-muted-foreground">Klik atau geser pin pada peta untuk mengubah posisi koordinat:</div>
                <div id="mapContainer" class="h-64 w-full rounded-xl border border-border overflow-hidden z-10"></div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-foreground mb-1">Latitude <span class="text-destructive">*</span></label>
                    <input type="text" name="latitude" id="latInput" value="{{ old('latitude', $report->latitude) }}" required readonly
                           class="w-full rounded-lg border border-input bg-muted/30 px-3 py-2 text-xs text-foreground font-mono focus:outline-none">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-foreground mb-1">Longitude <span class="text-destructive">*</span></label>
                    <input type="text" name="longitude" id="lngInput" value="{{ old('longitude', $report->longitude) }}" required readonly
                           class="w-full rounded-lg border border-input bg-muted/30 px-3 py-2 text-xs text-foreground font-mono focus:outline-none">
                </div>
                <div class="sm:col-span-2">
                    <label class="block text-xs font-semibold text-foreground mb-1">Nama Jalan / Patokan Alamat <span class="text-destructive">*</span></label>
                    <input type="text" name="address" id="addressInput" value="{{ old('address', $report->address) }}" required
                           class="w-full rounded-lg border border-input bg-background px-3 py-2 text-xs text-foreground focus:outline-none focus:ring-1 focus:ring-ring">
                </div>
            </div>
        </div>

        <!-- Submit & Actions -->
        <div class="flex items-center justify-end gap-3 pt-2">
            <a href="{{ route('admin.reports.index') }}" class="px-5 py-2.5 rounded-lg border border-border bg-card text-xs font-semibold text-foreground hover:bg-accent transition-colors">
                Batal
            </a>
            <button type="submit" id="btnSubmitReport" class="inline-flex items-center justify-center gap-2 px-6 py-2.5 rounded-lg bg-primary text-primary-foreground text-xs font-semibold shadow-sm hover:bg-primary/90 transition-colors">
                <i data-lucide="check" class="h-4 w-4"></i>
                <span>Simpan Perubahan Laporan</span>
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

    const dropzone = document.getElementById('dropzone');
    const photoInput = document.getElementById('photoInput');
    const previewImg = document.getElementById('previewImg');

    dropzone.addEventListener('click', () => photoInput.click());
    photoInput.addEventListener('change', () => {
        const file = photoInput.files[0];
        if (!file) return;
        const reader = new FileReader();
        reader.onload = (e) => {
            previewImg.src = e.target.result;
            triggerAiClassification();
        };
        reader.readAsDataURL(file);
    });

    // AI Classification
    const btnTriggerAi = document.getElementById('btnTriggerAi');
    const btnTriggerAiText = document.getElementById('btnTriggerAiText');
    const aiStatusBadge = document.getElementById('aiStatusBadge');
    const aiResultCard = document.getElementById('aiResultCard');
    const aiPredictedText = document.getElementById('aiPredictedText');
    const aiConfidenceText = document.getElementById('aiConfidenceText');

    btnTriggerAi.addEventListener('click', triggerAiClassification);

    async function triggerAiClassification() {
        const file = photoInput.files[0];
        if (!file) {
            alert('Silakan pilih foto baru terlebih dahulu untuk memicu analisis AI.');
            return;
        }

        btnTriggerAi.disabled = true;
        btnTriggerAiText.textContent = 'Menganalisis...';
        aiStatusBadge.textContent = 'Memproses...';

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
                const resData = (data.data && data.data.data) ? data.data.data : data.data;
                aiPredictedText.textContent = resData.predicted_class || 'Unknown';
                aiConfidenceText.textContent = Math.round((resData.confidence_score || 0) * 100) + '%';
                aiResultCard.classList.remove('hidden');

                document.getElementById('aiPredictedCategoryInput').value = resData.predicted_class;
                document.getElementById('aiConfidenceScoreInput').value = resData.confidence_score;

                aiStatusBadge.textContent = 'Selesai';
                aiStatusBadge.className = 'text-[10px] px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-800 font-medium';
            }
        } catch (err) {
            aiStatusBadge.textContent = 'Gagal';
        } finally {
            btnTriggerAi.disabled = false;
            btnTriggerAiText.textContent = 'Uji Analisis AI untuk Foto Ini';
        }
    }

    // Leaflet Map
    let reportLat = parseFloat(document.getElementById('latInput').value) || -6.2088;
    let reportLng = parseFloat(document.getElementById('lngInput').value) || 106.8456;

    const map = L.map('mapContainer').setView([reportLat, reportLng], 15);
    L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '&copy; OpenStreetMap contributors'
    }).addTo(map);

    let marker = L.marker([reportLat, reportLng], { draggable: true }).addTo(map);

    function updateCoordinates(lat, lng) {
        document.getElementById('latInput').value = lat.toFixed(6);
        document.getElementById('lngInput').value = lng.toFixed(6);
    }

    marker.on('dragend', function (e) {
        const position = marker.getLatLng();
        updateCoordinates(position.lat, position.lng);
    });

    map.on('click', function (e) {
        marker.setLatLng(e.latlng);
        updateCoordinates(e.latlng.lat, e.latlng.lng);
    });

    // GPS Button
    document.getElementById('btnGps').addEventListener('click', () => {
        if (!navigator.geolocation) return;
        navigator.geolocation.getCurrentPosition((pos) => {
            const lat = pos.coords.latitude;
            const lng = pos.coords.longitude;
            map.setView([lat, lng], 16);
            marker.setLatLng([lat, lng]);
            updateCoordinates(lat, lng);
        });
    });
</script>
@endpush
@endsection
