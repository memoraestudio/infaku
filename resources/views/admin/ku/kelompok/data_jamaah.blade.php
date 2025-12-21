@extends('layouts.app')

@section('title', 'Data Jamaah')
@section('page-title', 'Data Jamaah')
@section('icon-page-title', 'bi-people')

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
            min-width: 1200px;
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
            font-size: 13px;
        }

        .data-table tr {
            font-size: small;
        }

        .data-table tbody tr:hover {
            background: #f8f9fa;
        }

        .btn {
            padding: 6px 12px;
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

        .btn-print {
            background: #17a2b8;
            color: white;
        }

        .btn-print:hover {
            background: #138496;
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
            padding: 6px 12px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 13px;
            transition: border-color 0.2s ease;
            color: #4d4d4d;
        }

        .form-control:focus {
            outline: none;
            border-color: #105a44;
            box-shadow: 0 0 0 2px rgba(16, 90, 68, 0.1);
        }

        .form-select {
            width: 100%;
            padding: 5px 12px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 13px;
            background: white;
            color: #4d4d4d;
        }

        .form-text {
            font-size: 13px;
            color: #666;
            margin-top: 4px;
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
            max-width: 700px;
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
            font-size: 13px;
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

        .badge-info {
            background: #d1ecf1;
            color: #0c5460;
        }

        .badge-warning {
            background: #fff3cd;
            color: #856404;
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
        }

        @media (max-width: 768px) {
            .form-row {
                grid-template-columns: 1fr;
            }
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
    </style>
@endpush

@section('content')
    <div class="master-container">
        <div class="card">
            <div class="card-header">
                <div style="display: flex;gap: 10px;justify-content: space-between;align-items: center;">
                    <h3 class="card-title">Data Jamaah</h3>
                    <div>
                        <button class="btn btn-print"
                            onclick="window.open('{{ route('admin.kelompok.data-jamaah.print') }}', '_blank')">
                            <i class="bi-printer"></i> Print
                        </button>
                        <button class="btn btn-primary" onclick="showCreateModal()">
                            <i class="bi-plus"></i> Tambah Jamaah
                        </button>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="table-controls">
                    <div style="display: flex; gap: 10px; align-items: center;">
                        <select id="perPageSelect" class="form-select"
                            style="width:auto;display:inline-block; padding:5px 0px; font-size:13px;">
                            <option value="10">10</option>
                            <option value="25">25</option>
                            <option value="50">50</option>
                            <option value="100">100</option>
                        </select>
                        <select id="filterAktif" class="form-select" style="width: 150px;">
                            <option value="">Semua Status</option>
                            <option value="1">Aktif</option>
                            <option value="0">Tidak Aktif</option>
                        </select>
                    </div>
                    <div class="search-box">
                        <input type="text" id="searchInput" class="search-input" placeholder="Cari nama, telepon...">
                        <i class="bi-search search-icon"></i>
                    </div>
                </div>

                <div class="table-container">
                    <div class="table-responsive">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th width="50">No</th>
                                    <th>Nama Lengkap</th>
                                    <th>Jenis Kelamin</th>
                                    <th>TTL</th>
                                    <th>Telepon</th>
                                    <th>Pekerjaan</th>
                                    <th>Status</th>
                                    <th>Dapuan</th>
                                    <th>Status Aktif</th>
                                    <th width="120">Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="tableBody">
                                <!-- Data will be loaded via AJAX -->
                                {{-- <td>Tidak Ada Data</td> --}}
                            </tbody>
                        </table>
                    </div>
                </div>

                <div id="emptyState" class="empty-state" style="display: none;">
                    <i class="bi-people" style="font-size: 3rem;"></i>
                    <h4>Tidak ada data jamaah</h4>
                </div>

                <div id="loadingState" class="empty-state">
                    <div style="height: 20px; width: 200px; margin: 0 auto 10px; background: #f0f0f0; border-radius: 4px;">
                    </div>
                    <div style="height: 20px; width: 150px; margin: 0 auto; background: #f0f0f0; border-radius: 4px;"></div>
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
        </div>
    </div>

    <!-- Create/Edit Modal -->
    <div class="modal" id="formModal">
        <div class="modal-dialog">
            <div class="modal-header">
                <h3 class="modal-title" id="modalTitle">Tambah Jamaah</h3>
                <button class="modal-close" onclick="hideModal()">&times;</button>
            </div>
            <div class="modal-body">
                <form id="jamaahForm">
                    <input type="hidden" id="editId">

                    <div class="form-group">
                        <label class="form-label" for="nama_lengkap">Nama Lengkap *</label>
                        <input type="text" class="form-control" id="nama_lengkap" name="nama_lengkap" required>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label" for="tempat_lahir">Tempat Lahir</label>
                            <input type="text" class="form-control" id="tempat_lahir" name="tempat_lahir">
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="tanggal_lahir">Tanggal Lahir</label>
                            <input type="date" class="form-control" id="tanggal_lahir" name="tanggal_lahir">
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label" for="jenis_kelamin">Jenis Kelamin *</label>
                            <select class="form-select" id="jenis_kelamin" name="jenis_kelamin" required>
                                <option value="">Pilih Jenis Kelamin</option>
                                <option value="L">Laki-laki</option>
                                <option value="P">Perempuan</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="golongan_darah">Golongan Darah</label>
                            <select class="form-select" id="golongan_darah" name="golongan_darah">
                                <option value="-">Tidak Tahu</option>
                                <option value="A">A</option>
                                <option value="B">B</option>
                                <option value="AB">AB</option>
                                <option value="O">O</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label" for="status_menikah">Status Menikah *</label>
                            <select class="form-select" id="status_menikah" name="status_menikah" required>
                                <option value="">Pilih Status</option>
                                <option value="BELUM_MENIKAH">Belum Menikah</option>
                                <option value="MENIKAH">Menikah</option>
                                <option value="JANDA">Janda</option>
                                <option value="DUDA">Duda</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="pekerjaan">Pekerjaan</label>
                            <input type="text" class="form-control" id="pekerjaan" name="pekerjaan">
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label" for="telepon">Telepon</label>
                            <input type="text" class="form-control" id="telepon" name="telepon" maxlength="15">
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="email">Email</label>
                            <input type="email" class="form-control" id="email" name="email">
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label" for="dapuan_id">Dapuan *</label>
                            <select class="form-select" id="dapuan_id" name="dapuan_id" required>
                                <option value="">Pilih Dapuan</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="is_aktif">Status Aktif *</label>
                            <select class="form-select" id="is_aktif" name="is_aktif" required>
                                <option value="1">Aktif</option>
                                <option value="0">Tidak Aktif</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="alamat">Alamat</label>
                        <textarea class="form-control" id="alamat" name="alamat" rows="3"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button class="btn" onclick="hideModal()">Batal</button>
                <button class="btn btn-success" id="submitBtn" onclick="submitForm()">Simpan</button>
            </div>
        </div>
    </div>

    <!-- Detail Modal -->
    <div class="modal" id="detailModal">
        <div class="modal-dialog">
            <div class="modal-header">
                <h3 class="modal-title">Detail Jamaah</h3>
                <button class="modal-close" onclick="hideDetailModal()">&times;</button>
            </div>
            <div class="modal-body" id="detailBody">
                <!-- Data will be loaded via JS -->
            </div>
        </div>
    </div>

    <!-- Toast Container -->
    <div class="toast-container" id="toastContainer"></div>
@endsection

@push('scripts')
    <script>
        let currentPage = 1;
        let totalPages = 1;
        let searchQuery = '';
        let aktifFilter = '';
        let perPage = 10;
        // let deleteId = null;
        let dapuanOptions = [];

        document.addEventListener('DOMContentLoaded', function() {
            loadDapuanOptions();
            loadData();
            setupEventListeners();
        });

        function setupEventListeners() {
            let searchTimeout;
            document.getElementById('searchInput').addEventListener('input', function(e) {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(() => {
                    searchQuery = e.target.value;
                    currentPage = 1;
                    loadData();
                }, 500);
            });

            document.getElementById('filterAktif').addEventListener('change', function(e) {
                aktifFilter = e.target.value;
                currentPage = 1;
                loadData();
            });

            document.getElementById('perPageSelect').addEventListener('change', function(e) {
                perPage = parseInt(e.target.value);
                currentPage = 1;
                loadData();
            });
        }

        async function loadDapuanOptions() {
            try {
                const response = await fetch('{{ route('admin.kelompok.api.jamaah.dapuan-options') }}');
                const data = await response.json();

                if (data.success) {
                    dapuanOptions = data.data;
                    updateDapuanDropdown();
                }
            } catch (error) {
                console.error('Error loading dapuan options:', error);
            }
        }

        function updateDapuanDropdown(selectedId = '') {
            const dropdown = document.getElementById('dapuan_id');
            dropdown.innerHTML = '<option value="">Pilih Dapuan</option>';

            dapuanOptions.forEach(dapuan => {
                const option = document.createElement('option');
                option.value = dapuan.dapuan_id;
                option.textContent = `${dapuan.nama_dapuan} `;
                option.selected = dapuan.dapuan_id === selectedId;
                dropdown.appendChild(option);
            });
        }

        async function loadData() {
            showLoading();
            try {
                let url =
                    `{{ route('admin.kelompok.api.jamaah.index') }}?page=${currentPage}&search=${encodeURIComponent(searchQuery)}&per_page=${perPage}`;
                if (aktifFilter !== '') {
                    url += `&is_aktif=${aktifFilter}`;
                }
                const response = await fetch(url);
                const data = await response.json();
                if (data.success) {
                    renderTable(data.data);
                    updatePagination(data);
                } else {
                    throw new Error(data.message);
                }
            } catch (error) {
                console.error('Error loading data:', error);
                showError('Gagal memuat data: ' + error.message);
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
                <td>${item.nama_lengkap}</td>
                <td>${item.jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan'}</td>
                <td>
                    ${item.tempat_lahir || '-'},
                   ${item.tanggal_lahir ? new Date(item.tanggal_lahir).toLocaleDateString('id-ID') : '-'}
                </td>
                <td>${item.telepon || '-'}</td>
                <td>${item.pekerjaan || '-'}</td>
                <td>
                    <span class="badge badge-info">${item.status_menikah.replace('_', ' ')}</span>
                </td>
                <td>${item.nama_dapuan || '-'}</td>
                <td>
                    ${item.is_aktif ? 
                        '<span class="badge badge-success">Aktif</span>' : 
                        '<span class="badge badge-danger">Tidak Aktif</span>'
                    }
                </td>
                <td>
                    <button class="btn btn-edit btn-sm" onclick="editItem('${item.jamaah_id}')" title="Edit">
                        <i class="bi-pencil"></i>
                    </button>
                    <button class="btn btn-primary btn-sm" onclick="showDetailModal('${item.jamaah_id}')" title="Detail">
                        <i class="bi-eye"></i>
                    </button>
                </td>
            </tr>
        `).join('');
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

        function showCreateModal() {
            document.getElementById('modalTitle').textContent = 'Tambah Jamaah';
            document.getElementById('editId').value = '';
            document.getElementById('jamaahForm').reset();
            updateDapuanDropdown();
            document.getElementById('formModal').classList.add('show');
        }

        async function editItem(id) {
            try {
                const response = await fetch(`{{ route('admin.kelompok.api.jamaah.show', '') }}/${id}`);
                const data = await response.json();

                if (data.success) {
                    const item = data.data;
                    document.getElementById('modalTitle').textContent = 'Edit Jamaah';
                    document.getElementById('editId').value = item.jamaah_id;

                    // Fill form fields
                    document.getElementById('nama_lengkap').value = item.nama_lengkap;
                    document.getElementById('tempat_lahir').value = item.tempat_lahir || '';
                    document.getElementById('tanggal_lahir').value = item.tanggal_lahir || '';
                    document.getElementById('jenis_kelamin').value = item.jenis_kelamin;
                    document.getElementById('golongan_darah').value = item.golongan_darah || '-';
                    document.getElementById('status_menikah').value = item.status_menikah;
                    document.getElementById('pekerjaan').value = item.pekerjaan || '';
                    document.getElementById('telepon').value = item.telepon || '';
                    document.getElementById('email').value = item.email || '';
                    document.getElementById('alamat').value = item.alamat || '';
                    document.getElementById('is_aktif').value = item.is_aktif ? '1' : '0';

                    updateDapuanDropdown(item.dapuan_id);

                    document.getElementById('formModal').classList.add('show');
                } else {
                    throw new Error(data.message);
                }
            } catch (error) {
                console.error('Error loading item:', error);
                showError('Gagal memuat data: ' + error.message);
            }
        }

        function hideModal() {
            document.getElementById('formModal').classList.remove('show');
        }

        async function submitForm() {
            const formData = new FormData(document.getElementById('jamaahForm'));
            const id = document.getElementById('editId').value;

            const data = {
                nama_lengkap: formData.get('nama_lengkap'),
                tempat_lahir: formData.get('tempat_lahir'),
                tanggal_lahir: formData.get('tanggal_lahir'),
                jenis_kelamin: formData.get('jenis_kelamin'),
                golongan_darah: formData.get('golongan_darah'),
                status_menikah: formData.get('status_menikah'),
                pekerjaan: formData.get('pekerjaan'),
                telepon: formData.get('telepon'),
                email: formData.get('email'),
                alamat: formData.get('alamat'),
                dapuan_id: formData.get('dapuan_id'),
                is_aktif: formData.get('is_aktif') === '1'
            };

            try {
                const url = id ? `{{ route('admin.kelompok.api.jamaah.update', '') }}/${id}` :
                    '{{ route('admin.kelompok.api.jamaah.store') }}';
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
                showError('Gagal menyimpan data: ' + error.message);
            }
        }


        async function showDetailModal(id) {
            try {
                const response = await fetch(`{{ route('admin.kelompok.api.jamaah.show', '') }}/${id}`);
                const data = await response.json();
                if (data.success) {
                    const item = data.data;
                    let html = `<div style=\"display:grid;grid-template-columns:150px 1fr;gap:12px;\">`;
                    html +=
                        `<div style=\"font-weight:500;color:#333;\">Nama Lengkap</div><div>${item.nama_lengkap}</div>`;
                    html +=
                        `<div style=\"font-weight:500;color:#333;\">Jenis Kelamin</div><div>${item.jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan'}</div>`;
                    html +=
                        `<div style=\"font-weight:500;color:#333;\">Tempat, Tanggal Lahir</div><div>${item.tempat_lahir || '-'}, ${item.tanggal_lahir ? new Date(item.tanggal_lahir).toLocaleDateString('id-ID') : '-'}</div>`;
                    html += `<div style=\"font-weight:500;color:#333;\">Telepon</div><div>${item.telepon || '-'}</div>`;
                    html += `<div style=\"font-weight:500;color:#333;\">Email</div><div>${item.email || '-'}</div>`;
                    html +=
                        `<div style=\"font-weight:500;color:#333;\">Pekerjaan</div><div>${item.pekerjaan || '-'}</div>`;
                    html +=
                        `<div style=\"font-weight:500;color:#333;\">Status Menikah</div><div>${item.status_menikah.replace('_', ' ')}</div>`;
                    html +=
                        `<div style=\"font-weight:500;color:#333;\">Dapuan</div><div>${item.nama_dapuan || '-'}</div>`;
                    html +=
                        `<div style=\"font-weight:500;color:#333;\">Status Aktif</div><div>${item.is_aktif ? 'Aktif' : 'Tidak Aktif'}</div>`;
                    html += `<div style=\"font-weight:500;color:#333;\">Alamat</div><div>${item.alamat || '-'}</div>`;
                    html += `</div>`;
                    document.getElementById('detailBody').innerHTML = html;
                    document.getElementById('detailModal').classList.add('show');
                } else {
                    showError('Gagal memuat detail: ' + data.message);
                }
            } catch (error) {
                showError('Gagal memuat detail: ' + error.message);
            }
        }

        function hideDetailModal() {
            document.getElementById('detailModal').classList.remove('show');
        }

        function showLoading() {
            document.getElementById('loadingState').style.display = 'block';
            document.getElementById('emptyState').style.display = 'none';
            document.getElementById('pagination').style.display = 'none';
        }

        function showToast(type, title, message) {
            const container = document.getElementById('toastContainer');
            const toast = document.createElement('div');
            const icons = {
                success: 'bi-check-circle',
                error: 'bi-exclamation-triangle',
                warning: 'bi-exclamation-circle',
                info: 'bi-info-circle'
            };

            toast.className = `toast ${type}`;

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

            const existingToasts = container.children.length - 1;
            const delay = existingToasts * 300; // 300ms delay between each toast

            setTimeout(() => {
                toast.classList.add('show');

                setTimeout(() => {
                    toast.classList.remove('show');
                    setTimeout(() => {
                        if (toast.parentNode) {
                            toast.remove();
                        }
                    }, 250);
                }, 3000); // Auto hide after 3 seconds
            }, delay);
        }

        function getToastIcon(type) {
            switch (type) {
                case 'success':
                    return 'bi-check-circle-fill';
                case 'error':
                    return 'bi-exclamation-triangle-fill';
                case 'warning':
                    return 'bi-exclamation-triangle-fill';
                case 'info':
                    return 'bi-info-circle-fill';
                default:
                    return 'bi-info-circle-fill';
            }
        }

        function showSuccess(message) {
            showToast('success', 'Berhasil', message);
        }

        function showError(message) {
            showToast('error', 'Error', message);
        }

        document.getElementById('formModal').addEventListener('click', function(e) {
            if (e.target === this) hideModal();
        });

        document.getElementById('detailModal').addEventListener('click', function(e) {
            if (e.target === this) hideDetailModal();
        });
    </script>
@endpush
