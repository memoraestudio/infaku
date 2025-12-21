@extends('layouts.app')

@section('title', 'Anggota Keluarga')
@section('page-title', 'Anggota Keluarga')
@section('icon-page-title', 'bi-person-lines-fill')

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
            font-size: 0.9rem;
        }

        .form-control {
            width: 100%;
            padding: 8px 12px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 0.9rem;
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
            font-size: 0.9rem;
            background: white;
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
            padding: 15px 0;
        }

        .page-info {
            font-size: 0.9rem;
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
            padding: 40px 20px;
            color: #666;
        }

        .badge {
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 0.75rem;
            font-weight: 500;
        }

        .badge-primary {
            background: #d1ecf1;
            color: #0c5460;
        }

        .badge-success {
            background: #d4edda;
            color: #155724;
        }

        .badge-warning {
            background: #fff3cd;
            color: #856404;
        }

        .badge-info {
            background: #d1ecf1;
            color: #0c5460;
        }

        .badge-danger {
            background: #f8d7da;
            color: #721c24;
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
        }

        .family-info {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 6px;
            margin-bottom: 15px;
            border-left: 4px solid #105a44;
        }

        .family-name {
            font-weight: 600;
            color: #105a44;
            margin-bottom: 5px;
        }

        .family-details {
            font-size: 0.85rem;
            color: #666;
        }

        @media (max-width: 768px) {
            .form-row {
                grid-template-columns: 1fr;
            }
        }
    </style>
@endpush

@section('content')
    <div class="master-container">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Anggota Keluarga</h3>
            </div>
            <div class="card-body">
                <div class="table-controls">
                    <div class="search-box">
                        <input type="text" id="searchInput" class="search-input"
                            placeholder="Cari nama, keluarga, hubungan...">
                        <i class="bi-search search-icon"></i>
                    </div>
                    <button class="btn btn-primary" onclick="showCreateModal()">
                        <i class="bi-plus"></i> Tambah Anggota
                    </button>
                </div>

                <div class="table-container">
                    <div class="table-responsive">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th width="50">No</th>
                                    <th>Nama Keluarga</th>
                                    <th>Nama Anggota</th>
                                    <th>Jenis Kelamin</th>
                                    <th>Hubungan</th>
                                    <th>Urutan</th>
                                    <th>Tanggal Lahir</th>
                                    <th width="120">Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="tableBody">
                                <!-- Data will be loaded via AJAX -->
                            </tbody>
                        </table>
                    </div>
                </div>

                <div id="emptyState" class="empty-state" style="display: none;">
                    <i class="bi-people" style="font-size: 3rem;"></i>
                    <h4>Belum ada data anggota keluarga</h4>
                    <p>Mulai dengan menambahkan anggota keluarga pertama Anda.</p>
                    <button class="btn btn-primary" onclick="showCreateModal()">
                        <i class="bi-plus"></i> Tambah Anggota
                    </button>
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
                <h3 class="modal-title" id="modalTitle">Tambah Anggota Keluarga</h3>
                <button class="modal-close" onclick="hideModal()">&times;</button>
            </div>
            <div class="modal-body">
                <form id="anggotaForm">
                    <input type="hidden" id="editId">

                    <div class="form-group">
                        <label class="form-label" for="keluarga_id">Keluarga *</label>
                        <select class="form-select" id="keluarga_id" name="keluarga_id" required
                            onchange="loadKeluargaInfo(this.value)">
                            <option value="">Pilih Keluarga</option>
                        </select>
                    </div>

                    <div id="keluargaInfo" class="family-info" style="display: none;">
                        <div class="family-name" id="infoNamaKeluarga"></div>
                        <div class="family-details" id="infoKepalaKeluarga"></div>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="jamaah_id">Jamaah *</label>
                        <select class="form-select" id="jamaah_id" name="jamaah_id" required>
                            <option value="">Pilih Jamaah</option>
                        </select>
                        <div class="form-text">Hanya menampilkan jamaah yang belum menjadi anggota keluarga</div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label" for="status_hubungan">Hubungan Keluarga *</label>
                            <select class="form-select" id="status_hubungan" name="status_hubungan" required>
                                <option value="">Pilih Hubungan</option>
                                <option value="KEPALA_KELUARGA">Kepala Keluarga</option>
                                <option value="ISTRI">Istri</option>
                                <option value="ANAK">Anak</option>
                                <option value="MENANTU">Menantu</option>
                                <option value="CUCU">Cucu</option>
                                <option value="ORANGTUA">Orang Tua</option>
                                <option value="SAUDARA">Saudara</option>
                                <option value="LAINNYA">Lainnya</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="urutan">Urutan *</label>
                            <input type="number" class="form-control" id="urutan" name="urutan" min="1"
                                value="1" required>
                            <div class="form-text">Urutan dalam keluarga (1 untuk kepala keluarga)</div>
                        </div>
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
                <h3 class="modal-title">Hapus Anggota Keluarga</h3>
                <button class="modal-close" onclick="hideDeleteModal()">&times;</button>
            </div>
            <div class="modal-body">
                <p>Apakah Anda yakin ingin menghapus anggota keluarga <strong id="deleteItemName"></strong>?</p>
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
        let keluargaOptions = [];
        let jamaahOptions = [];

        document.addEventListener('DOMContentLoaded', function() {
            loadKeluargaOptions();
            loadJamaahOptions();
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
        }

        async function loadKeluargaOptions() {
            try {
                const response = await fetch('{{ route('admin.kelompok.api.anggota-keluarga.keluarga-options') }}');
                const data = await response.json();

                if (data.success) {
                    keluargaOptions = data.data;
                    updateKeluargaDropdown();
                }
            } catch (error) {
                console.error('Error loading keluarga options:', error);
            }
        }

        async function loadJamaahOptions() {
            try {
                const response = await fetch('{{ route('admin.kelompok.api.anggota-keluarga.jamaah-options') }}');
                const data = await response.json();

                if (data.success) {
                    jamaahOptions = data.data;
                    updateJamaahDropdown();
                }
            } catch (error) {
                console.error('Error loading jamaah options:', error);
            }
        }

        function updateKeluargaDropdown(selectedId = '') {
            const dropdown = document.getElementById('keluarga_id');
            dropdown.innerHTML = '<option value="">Pilih Keluarga</option>';

            keluargaOptions.forEach(keluarga => {
                const option = document.createElement('option');
                option.value = keluarga.keluarga_id;
                option.textContent = keluarga.nama_keluarga;
                option.selected = keluarga.keluarga_id === selectedId;
                dropdown.appendChild(option);
            });
        }

        function updateJamaahDropdown(selectedId = '') {
            const dropdown = document.getElementById('jamaah_id');
            dropdown.innerHTML = '<option value="">Pilih Jamaah</option>';

            jamaahOptions.forEach(jamaah => {
                const option = document.createElement('option');
                option.value = jamaah.jamaah_id;
                option.textContent = `${jamaah.nama_lengkap} (${jamaah.jenis_kelamin === 'L' ? 'L' : 'P'})`;
                option.selected = jamaah.jamaah_id === selectedId;
                dropdown.appendChild(option);
            });
        }

        async function loadKeluargaInfo(keluargaId) {
            if (!keluargaId) {
                document.getElementById('keluargaInfo').style.display = 'none';
                return;
            }

            try {
                const response = await fetch(
                    `{{ route('admin.kelompok.api.anggota-keluarga.by-keluarga', '') }}/${keluargaId}`);
                const data = await response.json();

                if (data.success) {
                    const anggota = data.data;
                    const keluarga = keluargaOptions.find(k => k.keluarga_id === keluargaId);

                    if (keluarga) {
                        const kepalaKeluarga = anggota.find(a => a.status_hubungan === 'KEPALA_KELUARGA');

                        document.getElementById('infoNamaKeluarga').textContent = keluarga.nama_keluarga;
                        document.getElementById('infoKepalaKeluarga').textContent = kepalaKeluarga ?
                            `Kepala Keluarga: ${kepalaKeluarga.nama_lengkap}` :
                            'Belum ada kepala keluarga';

                        document.getElementById('keluargaInfo').style.display = 'block';
                    }
                }
            } catch (error) {
                console.error('Error loading keluarga info:', error);
            }
        }

        async function loadData() {
            showLoading();

            try {
                const response = await fetch(
                    `{{ route('admin.kelompok.api.anggota-keluarga.index') }}?page=${currentPage}&search=${encodeURIComponent(searchQuery)}`
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
                <td>${index + 1 + ((currentPage - 1) * 10)}</td>
                <td>
                    <strong>${item.nama_keluarga}</strong>
                </td>
                <td>
                    <strong>${item.nama_lengkap}</strong>
                    <br>
                    <small class="text-muted">${item.jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan'}</small>
                </td>
                <td>${item.jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan'}</td>
                <td>
                    ${getStatusBadge(item.status_hubungan)}
                </td>
                <td align="center">${item.urutan}</td>
                <td>
                    ${item.tanggal_lahir ? new Date(item.tanggal_lahir).toLocaleDateString('id-ID') : '-'}
                </td>
                <td>
                    <button class="btn btn-edit btn-sm" onclick="editItem('${item.anggota_id}')" title="Edit">
                        <i class="bi-pencil"></i>
                    </button>
                    <button class="btn btn-delete btn-sm" onclick="showDeleteModal('${item.anggota_id}', '${item.nama_lengkap}')" title="Hapus">
                        <i class="bi-trash"></i>
                    </button>
                </td>
            </tr>
        `).join('');
        }

        function getStatusBadge(status) {
            const badges = {
                'KEPALA_KELUARGA': 'badge-primary',
                'ISTRI': 'badge-success',
                'ANAK': 'badge-info',
                'MENANTU': 'badge-warning',
                'CUCU': 'badge-info',
                'ORANGTUA': 'badge-warning',
                'SAUDARA': 'badge-info',
                'LAINNYA': 'badge-secondary'
            };

            const label = status.replace('_', ' ');
            const badgeClass = badges[status] || 'badge-secondary';

            return `<span class="badge ${badgeClass}">${label}</span>`;
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
            document.getElementById('modalTitle').textContent = 'Tambah Anggota Keluarga';
            document.getElementById('editId').value = '';
            document.getElementById('anggotaForm').reset();
            document.getElementById('keluargaInfo').style.display = 'none';
            updateKeluargaDropdown();
            updateJamaahDropdown();
            document.getElementById('formModal').classList.add('show');
        }

        async function editItem(id) {
            try {
                const response = await fetch(`{{ route('admin.kelompok.api.anggota-keluarga.show', '') }}/${id}`);
                const data = await response.json();

                if (data.success) {
                    const item = data.data;
                    document.getElementById('modalTitle').textContent = 'Edit Anggota Keluarga';
                    document.getElementById('editId').value = item.anggota_id;

                    // Fill form fields
                    document.getElementById('status_hubungan').value = item.status_hubungan;
                    document.getElementById('urutan').value = item.urutan;

                    // Load keluarga info
                    updateKeluargaDropdown(item.keluarga_id);
                    loadKeluargaInfo(item.keluarga_id);

                    // Disable keluarga and jamaah selection for edit
                    document.getElementById('keluarga_id').disabled = true;
                    document.getElementById('jamaah_id').disabled = true;

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
            // Re-enable dropdowns
            document.getElementById('keluarga_id').disabled = false;
            document.getElementById('jamaah_id').disabled = false;

            document.getElementById('formModal').classList.remove('show');
        }

        async function submitForm() {
            const formData = new FormData(document.getElementById('anggotaForm'));
            const id = document.getElementById('editId').value;

            const data = {
                keluarga_id: formData.get('keluarga_id'),
                jamaah_id: formData.get('jamaah_id'),
                status_hubungan: formData.get('status_hubungan'),
                urutan: parseInt(formData.get('urutan')) || 1
            };

            try {
                const url = id ? `{{ route('admin.kelompok.api.anggota-keluarga.update', '') }}/${id}` :
                    '{{ route('admin.kelompok.api.anggota-keluarga.store') }}';
                const method = id ? 'PUT' : 'POST';

                // For edit, remove keluarga_id and jamaah_id from data
                if (id) {
                    delete data.keluarga_id;
                    delete data.jamaah_id;
                }

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
                    showSuccess(id ? 'Data berhasil diupdate' : 'Data berhasil ditambahkan');
                } else {
                    throw new Error(result.message);
                }
            } catch (error) {
                console.error('Error submitting form:', error);
                showError('Gagal menyimpan data: ' + error.message);
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
                    `{{ route('admin.kelompok.api.anggota-keluarga.destroy', '') }}/${deleteId}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        }
                    });

                const result = await response.json();

                if (result.success) {
                    hideDeleteModal();
                    loadData();
                    showSuccess('Data berhasil dihapus');
                } else {
                    throw new Error(result.message);
                }
            } catch (error) {
                console.error('Error deleting item:', error);
                showError('Gagal menghapus data: ' + error.message);
            }
        }

        function showLoading() {
            document.getElementById('loadingState').style.display = 'block';
            document.getElementById('emptyState').style.display = 'none';
            document.getElementById('pagination').style.display = 'none';
        }

        function showSuccess(message) {
            alert('Sukses: ' + message);
        }

        function showError(message) {
            alert('Error: ' + message);
        }

        document.getElementById('formModal').addEventListener('click', function(e) {
            if (e.target === this) hideModal();
        });

        document.getElementById('deleteModal').addEventListener('click', function(e) {
            if (e.target === this) hideDeleteModal();
        });
    </script>
@endpush
