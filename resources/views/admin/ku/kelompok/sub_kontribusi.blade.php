@extends('layouts.app')

@section('title', 'Sub Kontribusi')
@section('page-title', 'Sub Kontribusi')
@section('icon-page-title', 'bi-list-ul')

@push('style')
    <style>
        .master-container {
            padding: 5px;
        }

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

        .table-container {
            overflow-x: auto;
        }

        .table-responsive {
            width: 100%;
            overflow-x: auto;
        }

        .data-table {
            min-width: 1000px;
            width: 100%;
            border-collapse: collapse;
        }

        .data-table th,
        .data-table td {
            white-space: nowrap;
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #e0e0e0;
        }

        .data-table th {
            background: #f8f9fa;
            color: #333;
            font-size: 0.9rem;
        }

        .data-table tr {
            font-size: small;
        }

        .data-table tbody tr:hover {
            background: #f8f9fa;
        }

        .btn {
            padding: 7px 12px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 0.8rem;
            font-weight: 500;
            transition: all 0.2s ease;
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }

        .btn-sm {
            padding: 4px 8px;
            font-size: 0.75rem;
        }

        .btn-primary {
            background: #105a44;
            color: white;
        }

        .btn-primary:hover {
            background: #0d8b66;
        }

        .btn-secondary {
            background: #6c757d;
            color: white;
        }

        .btn-secondary:hover {
            background: #5a6268;
        }

        .btn-edit {
            background: #ffc107;
            color: #212529;
        }

        .btn-edit:hover {
            background: #e0a800;
        }

        .btn-delete {
            background: #dc3545;
            color: white;
        }

        .btn-delete:hover {
            background: #c82333;
        }

        .btn-success {
            background: #28a745;
            color: white;
        }

        .btn-success:hover {
            background: #218838;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-label {
            display: block;
            margin-bottom: 5px;
            font-weight: 500;
            color: #333;
            font-size: 13px;
        }

        .form-control {
            width: 100%;
            padding: 8px 12px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 13px;
            color: #4d4d4d;
            transition: border-color 0.2s ease;
        }

        .form-control:focus {
            outline: none;
            border-color: #105a44;
            box-shadow: 0 0 0 2px rgba(16, 90, 68, 0.1);
        }

        .form-select {
            width: 100%;
            padding: 8px 12px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 13px;
            background: white;
            color: #757575;
        }

        .form-text {
            font-size: 13px;
            color: #666;
            margin-top: 4px;
        }

        .form-select-filter {
            width: 100%;
            padding: 5px 12px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 0.9rem;
            background: white;
            color: #757575;
        }

        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 1050;
            align-items: center;
            justify-content: center;
        }

        .modal.show {
            display: flex;
        }

        .modal-dialog {
            background: white;
            border-radius: 8px;
            width: 90%;
            max-width: 600px;
            max-height: 90vh;
            overflow-y: auto;
        }

        .modal-header {
            padding: 15px 20px;
            border-bottom: 1px solid #e0e0e0;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .modal-title {
            font-size: 1.1rem;
            font-weight: 600;
            margin: 0;
        }

        .modal-close {
            background: none;
            border: none;
            font-size: 1.2rem;
            cursor: pointer;
            color: #666;
        }

        .modal-body {
            padding: 20px;
        }

        .modal-footer {
            padding: 15px 20px;
            border-top: 1px solid #e0e0e0;
            display: flex;
            justify-content: flex-end;
            gap: 10px;
        }

        .pagination {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 10px;
            margin-top: 20px;
        }

        .page-info {
            font-size: 13px;
            color: #666;
        }

        .page-btn {
            padding: 6px 12px;
            border: 1px solid #ddd;
            background: white;
            border-radius: 4px;
            cursor: pointer;
            font-size: 0.9rem;
        }

        .page-btn:hover:not(:disabled) {
            background: #f8f9fa;
        }

        .page-btn:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        .table-controls {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
            gap: 15px;
            flex-wrap: wrap;
        }

        .filter-section {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 6px;
            margin-bottom: 20px;
            border: 1px solid #e0e0e0;
        }

        .filter-title {
            font-size: 1rem;
            font-weight: 600;
            margin-bottom: 10px;
            color: #333;
        }

        .filter-form {
            display: flex;
            gap: 15px;
            align-items: flex-end;
            flex-wrap: wrap;
        }

        .filter-group {
            flex: 1;
            min-width: 200px;
        }

        .search-box {
            position: relative;
            flex: 1;
            min-width: 250px;
        }

        .search-input {
            width: 100%;
            padding: 6px 35px 5px 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 0.9rem;
        }

        .search-icon {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            color: #666;
        }

        .empty-state {
            text-align: center;
            padding: 10px 20px;
            color: #666;
        }

        .empty-state h4,
        p {
            font-size: 13px;
        }

        .badge {
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 0.75rem;
            font-weight: 500;
        }

        .badge-success {
            background: #d4edda;
            color: #155724;
        }

        .badge-danger {
            background: #f8d7da;
            color: #721c24;
        }

        .badge-primary {
            background: #d1ecf1;
            color: #0c5460;
        }

        .badge-warning {
            background: #fff3cd;
            color: #856404;
        }

        .value-display {
            font-weight: 600;
            font-size: 0.9rem;
        }

        .percentage-value {
            color: #105a44;
        }

        .nominal-value {
            color: #28a745;
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
        }

        .value-input-group {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .value-prefix {
            font-weight: 500;
            color: #666;
            min-width: 40px;
        }

        .kategori-badge {
            background: #105a44;
            color: white;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 0.75rem;
            font-weight: 500;
        }

        /* Toast Styles */
        .toast-container {
            position: fixed;
            top: 20px;
            right: 320px;
            z-index: 1060;
            max-width: 400px;
        }

        .toast {
            background: white;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            margin-bottom: 10px;
            transform: translateX(100%);
            transition: transform 0.3s ease;
            border-left: 4px solid #17a2b8;
            display: flex;
            align-items: center;
            padding: 12px 16px;
            min-width: 300px;
        }

        .toast.show {
            transform: translateX(0);
        }

        .toast.hide {
            transform: translateX(100%);
        }

        .toast-icon {
            width: 24px;
            height: 24px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 12px;
            flex-shrink: 0;
        }

        .toast.success {
            border-left: 4px solid #28a745;
        }

        .toast.success .toast-icon {
            background: #d4edda;
            color: #155724;
        }

        .toast.error {
            border-left: 4px solid #dc3545;
        }

        .toast.error .toast-icon {
            background: #f8d7da;
            color: #721c24;
        }

        .toast.warning {
            border-left: 4px solid #ffc107;
        }

        .toast.warning .toast-icon {
            background: #fff3cd;
            color: #856404;
        }

        .toast.info {
            border-left: 4px solid #17a2b8;
        }

        .toast.info .toast-icon {
            background: #d1ecf1;
            color: #0c5460;
        }

        .toast-content {
            flex: 1;
        }

        .toast-title {
            font-weight: 600;
            font-size: 14px;
            margin-bottom: 2px;
        }

        .toast-message {
            font-size: 0.85rem;
            color: #666;
        }

        .toast-close {
            background: none;
            border: none;
            color: #999;
            font-size: 18px;
            cursor: pointer;
            padding: 0;
            margin-left: 12px;
            flex-shrink: 0;
        }

        .toast-close:hover {
            color: #666;
        }

        @media (max-width: 768px) {
            .form-row {
                grid-template-columns: 1fr;
            }

            .filter-form {
                flex-direction: column;
                align-items: stretch;
            }

            .filter-group {
                min-width: auto;
            }
        }
    </style>
@endpush

@section('content')
    <div class="master-container">


        <div class="card">
            <div class="card-header" style="display: flex; align-items: center; justify-content: space-between;">
                <h3 class="card-title">Sub Kontribusi</h3>
                {{-- <button class="btn btn-success" onclick="showCreateModal()" id="addBtn" disabled>
                    <i class="bi-plus"></i> Tambah Sub Kontribusi
                </button> --}}
            </div>
            <div class="card-body">
                <!-- Filter Section -->

                <!-- Table Controls -->
                <div class="table-controls" id="tableControls" style="display: flex; align-items: center; gap: 15px;">
                    <div>
                        <select id="perPageSelect" class="form-select"
                            style="width:auto;display:inline-block; padding:5px 0px; font-size:13px;">
                            <option value="10">10</option>
                            <option value="25">25</option>
                            <option value="50">50</option>
                            <option value="100">100</option>
                        </select>
                    </div>
                    <div class="filter-form">
                        <div class="filter-group">
                            <select class="form-select-filter" id="master_kontribusi_filter" name="master_kontribusi_id"
                                required>
                                <option value="">Pilih Kategori</option>
                            </select>
                        </div>
                    </div>
                    <div class="search-box">
                        <input type="text" id="searchInput" class="search-input" placeholder="Cari nama, keterangan...">
                        <i class="bi-search search-icon"></i>
                    </div>
                </div>

                <!-- Table Section -->
                <div class="table-container" id="tableSection" style="display: block;">
                    <div class="table-responsive">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th width="50">No</th>
                                    <th>Kategori</th>
                                    <th>Nama Sub Kontribusi</th>
                                    <th>Jenis</th>
                                    <th>Nilai</th>
                                    <th>Status</th>
                                    <th>Keterangan</th>
                                    <th width="90">Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="tableBody">
                                <!-- Data will be loaded via AJAX -->
                            </tbody>
                        </table>
                    </div>
                    <div id="emptyState" class="empty-state" style="display: none;">
                        <i class="bi-list-ul" style="font-size: 3rem;"></i>
                        <h4>Belum ada data sub kontribusi</h4>
                        <p>Mulai dengan menambahkan sub kontribusi untuk kategori ini.</p>
                    </div>
                </div>

                <!-- Table Section -->
                <div class="table-container" id="tableSection" style="display: none;">
                    <div class="table-responsive">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th width="50">No</th>
                                    <th>Kategori</th>
                                    <th>Nama Sub Kontribusi</th>
                                    <th>Jenis</th>
                                    <th>Nilai</th>
                                    <th>Keterangan</th>
                                    <th>Status</th>
                                    <th width="120">Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="tableBody">
                                <!-- Data will be loaded via AJAX -->
                            </tbody>
                        </table>
                    </div>

                    <div id="emptyState" class="empty-state" style="display: none;">
                        <i class="bi-list-ul" style="font-size: 3rem;"></i>
                        <h4>Belum ada data sub kontribusi</h4>
                        <p>Mulai dengan menambahkan sub kontribusi pertama untuk kategori ini.</p>
                        <button class="btn btn-primary" onclick="showCreateModal()">
                            <i class="bi-plus"></i> Tambah Sub Kontribusi
                        </button>
                    </div>

                    <div id="loadingState" class="empty-state">
                        <div
                            style="height: 20px; width: 200px; margin: 0 auto 10px; background: #f0f0f0; border-radius: 4px;">
                        </div>
                        <div style="height: 20px; width: 150px; margin: 0 auto; background: #f0f0f0; border-radius: 4px;">
                        </div>
                    </div>

                    <div class="pagination" id="pagination" style="display: none;">
                        <button class="page-btn" id="prevPage" onclick="changePage(currentPage - 1)">
                            <i class="bi-chevron-left"></i> Prev
                        </button>
                        <span class="page-info" id="pageInfo">Page 1 of 1</span>
                        <button class="page-btn" id="nextPage" onclick="changePage(currentPage + 1)">
                            Next <i class="bi-chevron-right"></i>
                        </button>
                    </div>
                </div>

                <!-- Initial State -->
                <div id="initialState" class="empty-state">
                    <i class="bi-filter-circle" style="font-size: 3rem;"></i>
                    <h4>Pilih Kategori Kontribusi Terlebih Dahulu</h4>
                </div>
            </div>
        </div>
    </div>

    <!-- Toast Container -->
    <div class="toast-container" id="toastContainer"></div>

    <!-- Create/Edit Modal -->
    <div class="modal" id="formModal">
        <div class="modal-dialog">
            <div class="modal-header">
                <h3 class="modal-title" id="modalTitle">Tambah Sub Kontribusi</h3>
                <button class="modal-close" onclick="hideModal()">&times;</button>
            </div>
            <div class="modal-body">
                <form id="kontribusiForm">
                    <input type="hidden" id="editId">
                    <input type="hidden" id="currentMasterId">

                    <div class="form-group">
                        <label class="form-label" for="master_kontribusi_id">Kategori Kontribusi *</label>
                        <select class="form-select" id="master_kontribusi_id" name="master_kontribusi_id" required>
                            <option value="">Pilih Kategori</option>
                        </select>
                        <div class="form-text">Pilih kategori kontribusi</div>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="nama_kontribusi">Nama Sub Kontribusi *</label>
                        <input type="text" class="form-control" id="nama_kontribusi" name="nama_kontribusi" required>
                        <div class="form-text">Contoh: Infaq Jumat, Sodaqoh Rutin, Zakat Fitrah</div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label" for="jenis">Jenis Nilai *</label>
                            <select class="form-select" id="jenis" name="jenis" required
                                onchange="toggleValuePrefix()">
                                <option value="">Pilih Jenis</option>
                                <option value="nominal">Nominal (Rp)</option>
                                <option value="percentage">Persentase (%)</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="value">Nilai *</label>
                            <div class="value-input-group">
                                <input type="number" class="form-control" id="value" name="value" step="0.0001"
                                    min="0" required>
                                <span class="value-prefix" id="valuePrefix">Rp</span>
                            </div>
                            <div class="form-text" id="valueHelp">
                                Masukkan nilai kontribusi
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="keterangan">Keterangan</label>
                        <textarea class="form-control" id="keterangan" name="keterangan" rows="3"
                            placeholder="Deskripsi atau penjelasan tentang sub kontribusi ini..."></textarea>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="is_active">Status *</label>
                        <select class="form-select" id="is_active" name="is_active" required>
                            <option value="1">Aktif</option>
                            <option value="0">Tidak Aktif</option>
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button class="btn" onclick="hideModal()">Batal</button>
                <button class="btn btn-success" id="submitBtn" onclick="submitForm()">Simpan</button>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal" id="deleteModal">
        <div class="modal-dialog">
            <div class="modal-header">
                <h3 class="modal-title">Hapus Sub Kontribusi</h3>
                <button class="modal-close" onclick="hideDeleteModal()">&times;</button>
            </div>
            <div class="modal-body">
                <p>Apakah Anda yakin ingin menghapus sub kontribusi <strong id="deleteItemName"></strong>?</p>
                <p class="form-text">Data yang dihapus tidak dapat dikembalikan.</p>
            </div>
            <div class="modal-footer">
                <button class="btn" onclick="hideDeleteModal()">Batal</button>
                <button class="btn btn-delete" onclick="confirmDelete()">Hapus</button>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        let currentPage = 1;
        let totalPages = 1;
        let searchQuery = '';
        let deleteId = null;
        let currentMasterId = null;
        let masterOptions = [];
        let perPage = 10;

        document.addEventListener('DOMContentLoaded', function() {
            loadMasterOptions();
            setupEventListeners();
            document.getElementById('perPageSelect').addEventListener('change', function() {
                perPage = parseInt(this.value);
                currentPage = 1;
                loadData();
            });
        });

        function setupEventListeners() {
            // Master filter change (langsung onchange)
            document.getElementById('master_kontribusi_filter').addEventListener('change', function() {
                const masterId = this.value;
                currentMasterId = masterId;
                document.getElementById('currentMasterId').value = masterId;
                if (masterId) {
                    document.getElementById('initialState').style.display = 'none';
                    document.getElementById('tableControls').style.display = 'flex';
                    document.getElementById('tableSection').style.display = 'block';
                    currentPage = 1;
                    loadData();
                } else {
                    document.getElementById('initialState').style.display = 'block';
                    document.getElementById('tableControls').style.display = 'flex';
                    document.getElementById('tableSection').style.display = 'none';
                }
            });

            // Search input with debounce
            let searchTimeout;
            document.getElementById('searchInput')?.addEventListener('input', function(e) {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(() => {
                    searchQuery = e.target.value;
                    currentPage = 1;
                    loadData();
                }, 500);
            });
        }

        async function loadMasterOptions() {
            try {
                const response = await fetch('{{ route('admin.kelompok.api.sub-kontribusi.master-options') }}');
                const data = await response.json();

                if (data.success) {
                    masterOptions = data.data;
                    updateMasterDropdowns();
                } else {
                    showToast('error', 'Error', 'Gagal memuat data kategori');
                }
            } catch (error) {
                console.error('Error loading master options:', error);
                showToast('error', 'Error', 'Gagal memuat data kategori');
            }
        }

        function updateMasterDropdowns() {
            // Update filter dropdown
            const filterDropdown = document.getElementById('master_kontribusi_filter');
            filterDropdown.innerHTML = '<option value="">Pilih Kategori</option>';

            // Update form dropdown
            const formDropdown = document.getElementById('master_kontribusi_id');
            formDropdown.innerHTML = '<option value="">Pilih Kategori</option>';

            masterOptions.forEach(master => {
                const option1 = document.createElement('option');
                option1.value = master.master_kontribusi_id;
                option1.textContent = `${master.nama_kontribusi} (${master.kode_kontribusi})`;
                filterDropdown.appendChild(option1);

                const option2 = document.createElement('option');
                option2.value = master.master_kontribusi_id;
                option2.textContent = `${master.nama_kontribusi} (${master.kode_kontribusi})`;
                formDropdown.appendChild(option2);
            });
        }

        function loadSubKontribusi() {
            const masterId = document.getElementById('master_kontribusi_filter').value;
            if (!masterId) {
                showToast('warning', 'Peringatan', 'Silakan pilih kategori kontribusi terlebih dahulu');
                return;
            }

            currentMasterId = masterId;

            // Show table section
            document.getElementById('initialState').style.display = 'none';
            document.getElementById('tableControls').style.display = 'flex';
            document.getElementById('tableSection').style.display = 'block';

            // Load data
            loadData();
        }

        async function loadData() {
            showLoading();

            try {
                const response = await fetch(
                    `{{ route('admin.kelompok.api.sub-kontribusi.index') }}?page=${currentPage}&per_page=${perPage}&search=${encodeURIComponent(searchQuery)}&master_id=${currentMasterId}`
                );
                const data = await response.json();

                if (data.success) {
                    renderTable(data.data);
                    updatePagination(data);
                } else {
                    throw new Error(data.message);
                }
            } catch (error) {
                console.error('Error loading data:', error);
                showToast('error', 'Error', 'Gagal memuat data: ' + error.message);
            }
        }

        function renderTable(data) {
            const tableBody = document.getElementById('tableBody');
            const emptyState = document.getElementById('emptyState');
            const loadingState = document.getElementById('loadingState');
            const pagination = document.getElementById('pagination');

            if (data.length === 0) {
                tableBody.innerHTML = '';
                emptyState.style.display = 'block';
                loadingState.style.display = 'none';
                pagination.style.display = 'none';
                return;
            }

            emptyState.style.display = 'none';
            loadingState.style.display = 'none';
            pagination.style.display = 'flex';

            tableBody.innerHTML = data.map((item, index) => `
            <tr>
                <td>${index + 1 + ((currentPage - 1) * perPage)}</td>
                <td>
                    <div style="font-size: 0.8rem; margin-top: 2px;">${item.nama_kontribusi}</div>
                </td>
                <td>
                    <strong>${item.nama_kontribusi}</strong>
                </td>
                <td>
                    ${item.jenis === 'percentage' ? 
                        '<span class="badge badge-primary">Persentase</span>' : 
                        '<span class="badge badge-success">Nominal</span>'
                    }
                </td>
                <td>
                    <div class="value-display ${item.jenis === 'percentage' ? 'percentage-value' : 'nominal-value'}">
                        ${item.jenis === 'percentage' ? 
                            `${parseFloat(item.value * 100).toFixed(2)}%` : 
                            `Rp ${formatNumber(parseFloat(item.value))}`
                        }
                    </div>
                </td>
                <td>${item.keterangan || '-'}</td>
                <td>
                    ${item.is_active ? 
                        '<span class="badge badge-success">Aktif</span>' : 
                        '<span class="badge badge-danger">Tidak Aktif</span>'
                    }
                </td>
                <td>
                    <button class="btn btn-edit btn-sm" onclick="editItem('${item.sub_kat_id}')" title="Edit">
                        <i class="bi-pencil"></i>
                    </button>
                    <button class="btn btn-delete btn-sm" onclick="showDeleteModal('${item.sub_kat_id}', '${item.nama_kontribusi}')" title="Hapus">
                        <i class="bi-trash"></i>
                    </button>
                </td>
            </tr>
        `).join('');
        }

        function formatNumber(num) {
            return new Intl.NumberFormat('id-ID').format(num);
        }

        function updatePagination(data) {
            currentPage = data.current_page;
            totalPages = data.last_page;

            document.getElementById('pageInfo').textContent = `Page ${currentPage} of ${totalPages}`;
            document.getElementById('prevPage').disabled = currentPage === 1;
            document.getElementById('nextPage').disabled = currentPage === totalPages;
        }

        function changePage(page) {
            if (page >= 1 && page <= totalPages) {
                currentPage = page;
                loadData();
            }
        }

        function resetFilter() {
            document.getElementById('master_kontribusi_filter').value = '';
            document.getElementById('searchInput').value = '';
            searchQuery = '';
            currentMasterId = null;

            document.getElementById('initialState').style.display = 'block';
            document.getElementById('tableControls').style.display = 'none';
            document.getElementById('tableSection').style.display = 'none';

            document.getElementById('loadBtn').disabled = true;
            document.getElementById('addBtn').disabled = true;
        }

        function toggleValuePrefix() {
            const jenis = document.getElementById('jenis').value;
            const prefix = document.getElementById('valuePrefix');
            const help = document.getElementById('valueHelp');
            const valueInput = document.getElementById('value');

            if (jenis === 'percentage') {
                prefix.textContent = '%';
                help.textContent = 'Masukkan persentase';
                valueInput.step = '0.0001';
            } else {
                prefix.textContent = 'Rp';
                help.textContent = 'Masukkan nominal dalam Rupiah';
                valueInput.step = '1';
            }
        }

        function showCreateModal() {
            if (!currentMasterId) {
                showToast('warning', 'Peringatan', 'Silakan pilih kategori kontribusi terlebih dahulu');
                return;
            }

            document.getElementById('modalTitle').textContent = 'Tambah Sub Kontribusi';
            document.getElementById('editId').value = '';
            document.getElementById('kontribusiForm').reset();
            document.getElementById('master_kontribusi_id').value = currentMasterId;
            toggleValuePrefix();
            document.getElementById('formModal').classList.add('show');
        }

        async function editItem(id) {
            try {
                const response = await fetch(`{{ route('admin.kelompok.api.sub-kontribusi.show', '') }}/${id}`);
                const data = await response.json();

                if (data.success) {
                    const item = data.data;
                    document.getElementById('modalTitle').textContent = 'Edit Sub Kontribusi';
                    document.getElementById('editId').value = item.sub_kat_id;
                    document.getElementById('currentMasterId').value = item.master_kontribusi_id;

                    // Fill form fields
                    document.getElementById('nama_kontribusi').value = item.nama_kontribusi;
                    document.getElementById('keterangan').value = item.keterangan || '';
                    document.getElementById('is_active').value = item.is_active ? '1' : '0';

                    // Set master dropdown
                    document.getElementById('master_kontribusi_id').value = item.master_kontribusi_id;

                    // Set jenis and value
                    document.getElementById('jenis').value = item.jenis;
                    document.getElementById('value').value = item.value;
                    toggleValuePrefix();

                    document.getElementById('formModal').classList.add('show');
                } else {
                    throw new Error(data.message);
                }
            } catch (error) {
                console.error('Error loading item:', error);
                showToast('error', 'Error', 'Gagal memuat data: ' + error.message);
            }
        }

        function hideModal() {
            document.getElementById('formModal').classList.remove('show');
        }

        async function submitForm() {
            const formData = new FormData(document.getElementById('kontribusiForm'));
            const id = document.getElementById('editId').value;
            const masterId = document.getElementById('currentMasterId').value;

            const data = {
                master_kontribusi_id: masterId || formData.get('master_kontribusi_id'),
                nama_kontribusi: formData.get('nama_kontribusi'),
                value: parseFloat(formData.get('value')),
                jenis: formData.get('jenis'),
                keterangan: formData.get('keterangan'),
                is_active: formData.get('is_active') === '1'
            };

            try {
                const url = id ? `{{ route('admin.kelompok.api.sub-kontribusi.update', '') }}/${id}` :
                    '{{ route('admin.kelompok.api.sub-kontribusi.store') }}';
                const method = id ? 'PUT' : 'POST';

                const response = await fetch(url, {
                    method: method,
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify(data)
                });

                const result = await response.json();

                if (result.success) {
                    hideModal();
                    loadData();
                    showToast('success', 'Berhasil', id ? 'Data berhasil diupdate' : 'Data berhasil ditambahkan');
                } else {
                    throw new Error(result.message);
                }
            } catch (error) {
                console.error('Error submitting form:', error);
                showToast('error', 'Error', 'Gagal menyimpan data: ' + error.message);
            }
        }

        function showDeleteModal(id, name) {
            deleteId = id;
            document.getElementById('deleteItemName').textContent = name;
            document.getElementById('deleteModal').classList.add('show');
        }

        function hideDeleteModal() {
            deleteId = null;
            document.getElementById('deleteModal').classList.remove('show');
        }

        async function confirmDelete() {
            try {
                const response = await fetch(
                    `{{ route('admin.kelompok.api.sub-kontribusi.destroy', '') }}/${deleteId}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        }
                    });

                const result = await response.json();

                if (result.success) {
                    hideDeleteModal();
                    loadData();
                    showToast('success', 'Berhasil', 'Data berhasil dihapus');
                } else {
                    throw new Error(result.message);
                }
            } catch (error) {
                console.error('Error deleting item:', error);
                showToast('error', 'Error', 'Gagal menghapus data: ' + error.message);
            }
        }

        function showLoading() {
            const loadingState = document.getElementById('loadingState');
            const emptyState = document.getElementById('emptyState');
            const pagination = document.getElementById('pagination');

            if (loadingState && emptyState && pagination) {
                loadingState.style.display = 'block';
                emptyState.style.display = 'none';
                pagination.style.display = 'none';
            }
        }

        // Toast Notification System
        function showToast(type, title, message) {
            const container = document.getElementById('toastContainer');
            const toast = document.createElement('div');
            toast.className = `toast ${type}`;

            const icons = {
                success: 'bi-check-circle',
                error: 'bi-x-circle',
                warning: 'bi-exclamation-triangle',
                info: 'bi-info-circle'
            };

            toast.innerHTML = `
            <div class="toast-icon">
                <i class="bi ${icons[type] || 'bi-info-circle'}"></i>
            </div>
            <div class="toast-content">
                <div class="toast-title">${title}</div>
                <div class="toast-message">${message}</div>
            </div>
            <button class="toast-close" onclick="this.parentElement.remove()">
                <i class="bi-x"></i>
            </button>
        `;

            container.appendChild(toast);

            // Stagger the show animation for neater display
            const existingToasts = container.children.length - 1;
            const delay = existingToasts * 300; // 300ms delay between each toast

            setTimeout(() => {
                toast.classList.add('show');

                // Auto remove after 5 seconds from when it started showing
                setTimeout(() => {
                    toast.classList.remove('show');
                    setTimeout(() => {
                        if (toast.parentNode) {
                            toast.remove();
                        }
                    }, 300);
                }, 5000);
            }, delay);
        }

        // Modal close handlers
        document.getElementById('formModal').addEventListener('click', function(e) {
            if (e.target === this) hideModal();
        });

        document.getElementById('deleteModal').addEventListener('click', function(e) {
            if (e.target === this) hideDeleteModal();
        });
    </script>
@endpush
