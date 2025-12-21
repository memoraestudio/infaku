@extends('layouts.app')

@section('title', 'Input Pembayaran')
@section('page-title', 'Input Pembayaran')
@section('icon-page-title', 'bi-cash-coin')

@push('style')
    <style>
        .card {
            background: white;
            border-radius: 8px;
            border: 1px solid #e0e0e0;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
            margin-bottom: 20px;
        }

        .card-header {
            padding: 15px 20px;
            border-bottom: 1px solid #e0e0e0;
            background: #f8f9fa;
            border-radius: 8px 8px 0 0;
        }

        .card-title {
            font-size: 1.1rem;
            font-weight: 600;
            color: #333;
            margin: 0;
        }

        .card-body {
            padding: 20px;
        }

        .input-container {
            padding: 5px;
        }

        /* Master Card Styles - pertahankan ukuran font original */
        .master-card {
            background: white;
            border-radius: 8px;
            border: 1px solid #e0e0e0;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
            margin-bottom: 20px;
            overflow: hidden;
        }

        .master-card-header {
            padding: 5px 20px;
            border-bottom: 1px solid #e0e0e0;
            background: linear-gradient(135deg, #105a44 0%, #0d8b66 100%);
            position: relative;
            overflow: hidden;
        }

        .master-card-title {
            font-size: 1.1rem;
            font-weight: 600;
            color: white;
            margin: 0;
            position: relative;
            z-index: 1;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .master-card-subtitle {
            font-size: 12px;
            color: rgba(255, 255, 255, 0.9);
            margin-top: -5px;
            position: relative;
            z-index: 1;
        }

        .master-card-body {
            padding: 20px;
            background: white;
        }

        /* Grid untuk kontribusi cards - pertahankan ukuran original */
        .kontribusi-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 15px;
            margin-top: 10px;
        }

        /* Kontribusi Card Styles - pertahankan semua ukuran font original */
        .kontribusi-card {
            background: white;
            border-radius: 8px;
            border: 1px solid #e0e0e0;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
            transition: all 0.2s ease;
            cursor: pointer;
            overflow: hidden;
            height: 100%;
        }

        .kontribusi-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border-color: #105a44;
        }

        .kontribusi-card-header {
            padding: 15px 20px;
            border-bottom: 1px solid #e0e0e0;
            background: #f8f9fa;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .kontribusi-card-icon {
            width: 32px;
            height: 32px;
            border-radius: 6px;
            background: linear-gradient(135deg, #105a44 0%, #0d8b66 100%);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1rem;
            flex-shrink: 0;
        }

        .kontribusi-card-title-wrapper {
            flex: 1;
        }

        .kontribusi-card-title {
            font-size: 1rem;
            font-weight: 600;
            color: #333;
            margin: 0 0 2px 0;
            line-height: 1.3;
        }

        .kontribusi-card-code {
            font-size: 12px;
            color: #666;
            background: #f1f5f9;
            /* padding: 2px 6px; */
            border-radius: 3px;
            display: inline-block;
            font-family: 'Courier New', monospace;
        }

        .kontribusi-card-body {
            padding: 15px 20px;
        }

        .kontribusi-card-details {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .kontribusi-card-details li {
            display: flex;
            justify-content: space-between;
            padding: 6px 0;
            border-bottom: 1px solid #f1f5f9;
            font-size: 13px;
        }

        .kontribusi-card-details li:last-child {
            border-bottom: none;
        }

        .detail-label {
            color: #666;
            font-weight: 500;
        }

        .detail-value {
            color: #333;
            font-weight: 500;
            text-align: right;
        }

        .kontribusi-card-footer {
            padding: 12px 20px;
            background: #f8f9fa;
            border-top: 1px solid #e0e0e0;
            display: flex;
            justify-content: flex-end;
        }

        .select-btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 12px;
            background: #105a44;
            color: white;
            border: none;
            border-radius: 4px;
            font-size: 13px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .select-btn:hover {
            background: #0d8b66;
            transform: translateY(-1px);
        }

        .select-btn:active {
            transform: translateY(0);
        }

        /* Loading State - pertahankan ukuran original */
        .loading-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
            padding: 0px 20px;
            grid-column: 1 / -1;
        }

        .loading-spinner {
            width: 2rem;
            height: 2rem;
            border: 0.2em solid #f1f5f9;
            border-top: 0.2em solid #105a44;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin-bottom: 10px;
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }

        .loading-text {
            color: #666;
            font-size: 13px;
            margin: 0;
        }

        /* Empty State - pertahankan ukuran original */
        .empty-state {
            grid-column: 1 / -1;
            text-align: center;
            padding: 0px 20px;
        }


        .empty-title {
            color: #666;
            font-size: 1rem;
            font-weight: 600;
            margin-bottom: 5px;
        }

        .empty-subtitle {
            color: #999;
            font-size: 12px;
            margin: 0;
        }

        /* Responsive - pertahankan breakpoint original */
        @media (max-width: 768px) {
            .kontribusi-grid {
                grid-template-columns: 1fr;
                gap: 10px;
            }

            .master-card-header {
                padding: 12px 15px;
            }

            .master-card-body {
                padding: 15px;
            }

            .kontribusi-card-header,
            .kontribusi-card-body,
            .kontribusi-card-footer {
                padding: 12px 15px;
            }
        }

        /* Animation untuk card */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .kontribusi-card {
            animation: fadeInUp 0.3s ease forwards;
            opacity: 0;
        }

        .kontribusi-card:nth-child(1) {
            animation-delay: 0.1s;
        }

        .kontribusi-card:nth-child(2) {
            animation-delay: 0.15s;
        }

        .kontribusi-card:nth-child(3) {
            animation-delay: 0.2s;
        }

        .kontribusi-card:nth-child(4) {
            animation-delay: 0.25s;
        }

        .kontribusi-card:nth-child(5) {
            animation-delay: 0.3s;
        }

        .kontribusi-card:nth-child(6) {
            animation-delay: 0.35s;
        }
    </style>
@endpush

@section('content')
    <div class="input-container">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    Pilih Master Kontribusi
                </h3>
                {{-- <div class="card-subtitle">
                    Pilih salah satu jenis kontribusi untuk melanjutkan
                </div> --}}
            </div>
            <div class="card-body">
                <div class="kontribusi-grid" id="kontribusi-list">
                    <div class="loading-container" id="loadingKontribusi">
                        <div class="loading-spinner"></div>
                        <p class="loading-text">Memuat data kontribusi...</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            fetchKontribusi();
        });

        function fetchKontribusi() {
            fetch("{{ route('admin.kelompok.api.input-pembayaran.kontribusi-options') }}")
                .then(response => response.json())
                .then(data => {
                    const list = document.getElementById('kontribusi-list');
                    const loading = document.getElementById('loadingKontribusi');

                    if (!data.success || !data.data || data.data.length === 0) {
                        loading.innerHTML = `
                            <div class="empty-state">
                                    <i class="bi bi-inbox" style="font-size:3rem; color:#7a7a7a;"></i>
                                <h4 class="empty-title">Tidak ada data kontribusi</h4>
                            </div>
                        `;
                        return;
                    }

                    loading.style.display = 'none';
                    list.innerHTML = '';

                    data.data.forEach((item, index) => {
                        const card = document.createElement('div');
                        card.className = 'kontribusi-card';
                        card.style.animationDelay = `${(index + 1) * 0.05}s`;

                        // Menentukan ikon berdasarkan jenis kontribusi
                        let iconClass = 'bi-cash-coin';
                        let detailItems = [];

                        // Detail tambahan jika ada
                        if (item.deskripsi) {
                            detailItems.push({
                                label: 'Deskripsi',
                                value: item.deskripsi.length > 40 ? item.deskripsi.substring(0, 40) +
                                    '...' : item.deskripsi
                            });
                        }

                        if (item.nominal_default) {
                            const nominal = new Intl.NumberFormat('id-ID', {
                                style: 'currency',
                                currency: 'IDR',
                                minimumFractionDigits: 0
                            }).format(item.nominal_default);
                            detailItems.push({
                                label: 'Nominal Default',
                                value: nominal
                            });
                        }

                        card.innerHTML = `
                            <div class="kontribusi-card-header">
                                <div class="kontribusi-card-icon">
                                    <i class="bi ${iconClass}"></i>
                                </div>
                                <div class="kontribusi-card-title-wrapper">
                                    <h5 class="kontribusi-card-title">${item.nama_kontribusi}</h5>
                                    <div class="kontribusi-card-code">Kode: ${item.kode_kontribusi}</div>
                                </div>
                            </div>
                            ${detailItems.length > 0 ? `
                                                                                        <div class="kontribusi-card-body">
                                                                                            <ul class="kontribusi-card-details">
                                                                                                        ${detailItems.map(detail => `
                                            <li>
                                                <span class="detail-label">${detail.label}</span>
                                                <span class="detail-value">${detail.value}</span>
                                            </li>
                                        `).join('')}
                                                                                                    </ul>
                                                                                                </div>
                                                                                        ` : ''}
                            <div class="kontribusi-card-footer">
                                <button class="select-btn" onclick="goToInputPembayaran('${item.master_kontribusi_id}')">
                                    <i class="bi bi-arrow-right"></i>
                                    Pilih
                                </button>
                            </div>
                        `;

                        // Tambahkan event listener untuk seluruh card
                        card.addEventListener('click', function(e) {
                            if (!e.target.closest('.select-btn')) {
                                goToInputPembayaran(item.master_kontribusi_id);
                            }
                        });

                        list.appendChild(card);
                    });
                })
                .catch(error => {
                    console.error('Error fetching kontribusi:', error);
                    const list = document.getElementById('kontribusi-list');
                    const loading = document.getElementById('loadingKontribusi');

                    loading.innerHTML = `
                        <div class="empty-state">
                            <div class="empty-icon">
                                <i class="bi bi-exclamation-triangle"></i>
                            </div>
                            <h4 class="empty-title">Gagal memuat data</h4>
                            <p class="empty-subtitle">Terjadi kesalahan saat memuat data kontribusi</p>
                        </div>
                    `;
                });
        }

        function goToInputPembayaran(masterKontribusiId) {
            // Tambahkan animasi loading sebelum redirect
            const button = event?.target?.closest('.select-btn');
            if (button) {
                const originalContent = button.innerHTML;
                button.innerHTML = `
                    <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                    Memproses...
                `;
                button.disabled = true;
            }

            // Redirect setelah delay kecil
            setTimeout(() => {
                window.location.href =
                    `{{ route('admin.kelompok.input-pembayaran.create') }}?master_kontribusi_id=${masterKontribusiId}`;
            }, 200);
        }
    </script>
@endpush
