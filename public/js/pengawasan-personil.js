// Data pegawai dari server (akan di-inject dari blade)
// pegawaiData is declared in blade template before this script loads

// Track selected personil
let selectedPersonil = {
    penanggung_jawab: null,
    pengendali_teknis: null,
    ketua_tim: null,
    anggota: []
};

// Current role being selected in modal
let currentRole = null;

// Role titles for modal
const roleTitles = {
    'penanggung_jawab': 'Pilih Penanggung Jawab',
    'pengendali_teknis': 'Pilih Pengendali Teknis',
    'ketua_tim': 'Pilih Ketua Tim'
};

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM Loaded');
    console.log('pegawaiData:', pegawaiData);
    console.log('pegawaiData length:', pegawaiData.length);
    initializeFromExistingData();
    console.log('selectedPersonil after init:', selectedPersonil);
    updateAllDisplays();

    // Listen to jenis_penugasan changes
    const jenisPenugasanSelect = document.getElementById('jenis_penugasan');
    if (jenisPenugasanSelect) {
        jenisPenugasanSelect.addEventListener('change', function() {
            handleJenisPenugasanChange();
        });
        // Trigger on page load if value exists
        if (jenisPenugasanSelect.value) {
            handleJenisPenugasanChange();
        }
    }

    // Form validation before submit
    const form = document.getElementById('editPengawasanForm') || document.getElementById('pengawasanForm');
    if (form) {
        form.addEventListener('submit', function(e) {
            console.log('Form submit triggered');

            // Check if Perjalanan Dinas - skip PJ, PT, KT validation
            const jenisPenugasan = document.getElementById('jenis_penugasan');
            const isPerjalananDinas = jenisPenugasan && jenisPenugasan.value === 'Perjalanan Dinas Luar Daerah';

            // Check if Plt. Inspektur is selected for Perjalanan Dinas
            if (isPerjalananDinas) {
                const hasPltInspektur = checkIfPltInspekturSelected();
                if (hasPltInspektur) {
                    const totalPersonil = getTotalPersonilCount();
                    if (totalPersonil > 1) {
                        e.preventDefault();
                        alert('Jika Plt. Inspektur dipilih untuk Perjalanan Dinas Luar Daerah, tidak boleh ada pegawai lain yang dipilih!');
                        return false;
                    }
                }
            }

            if (!isPerjalananDinas) {
                // Check required roles for non-Perjalanan Dinas
                if (!selectedPersonil.penanggung_jawab) {
                    e.preventDefault();
                    alert('Penanggung Jawab harus dipilih!');
                    return false;
                }

                if (!selectedPersonil.ketua_tim) {
                    e.preventDefault();
                    alert('Ketua Tim harus dipilih!');
                    return false;
                }
            }

            // Check for duplicates
            const selectedIds = [];
            if (selectedPersonil.penanggung_jawab) {
                selectedIds.push(selectedPersonil.penanggung_jawab.id);
            }
            if (selectedPersonil.pengendali_teknis) {
                selectedIds.push(selectedPersonil.pengendali_teknis.id);
            }
            if (selectedPersonil.ketua_tim) {
                selectedIds.push(selectedPersonil.ketua_tim.id);
            }
            selectedPersonil.anggota.forEach(a => selectedIds.push(a.id));

            const uniqueIds = [...new Set(selectedIds)];
            if (selectedIds.length !== uniqueIds.length) {
                e.preventDefault();
                alert('Personil tidak boleh duplikat dalam satu Surat Tugas!');
                return false;
            }

            console.log('Form validation passed');
            return true;
        });
    }
});

// Initialize from existing data (for edit mode)
function initializeFromExistingData() {
    // Get values from hidden inputs
    const pjId = document.getElementById('penanggung_jawab_id').value;
    const ptId = document.getElementById('pengendali_teknis_id').value;
    const ktId = document.getElementById('ketua_tim_id').value;

    if (pjId) {
        selectedPersonil.penanggung_jawab = pegawaiData.find(p => p.id == pjId);
    }
    if (ptId) {
        selectedPersonil.pengendali_teknis = pegawaiData.find(p => p.id == ptId);
    }
    if (ktId) {
        selectedPersonil.ketua_tim = pegawaiData.find(p => p.id == ktId);
    }

    // Get anggota from hidden inputs
    const anggotaInputs = document.querySelectorAll('#anggotaHiddenInputs input[name="anggota[]"]');
    anggotaInputs.forEach(input => {
        const pegawai = pegawaiData.find(p => p.id == input.value);
        if (pegawai && !selectedPersonil.anggota.find(a => a.id == pegawai.id)) {
            selectedPersonil.anggota.push(pegawai);
        }
    });
}

// Open modal for role selection (PJ, PT, KT)
function openRoleModal(role) {
    console.log('openRoleModal called with role:', role);
    currentRole = role;
    const modal = document.getElementById('modalRole');
    const titleElement = document.getElementById('modalRoleTitleText');
    console.log('modal element:', modal);
    console.log('titleElement:', titleElement);
    
    titleElement.textContent = roleTitles[role];
    modal.classList.add('show');
    document.body.style.overflow = 'hidden';
    
    populateRolePersonilList();
    
    setTimeout(() => {
        document.getElementById('searchRoleInput').focus();
    }, 100);
}

// Close role modal
function closeRoleModal() {
    const modal = document.getElementById('modalRole');
    modal.classList.remove('show');
    document.body.style.overflow = '';
    currentRole = null;
    document.getElementById('searchRoleInput').value = '';
}

// Populate role personil list
function populateRolePersonilList(searchTerm = '') {
    const listContainer = document.getElementById('rolePersonilSearchList');
    
    if (!pegawaiData || pegawaiData.length === 0) {
        listContainer.innerHTML = '<div class="no-results">Data pegawai tidak tersedia</div>';
        return;
    }

    const term = searchTerm.toLowerCase();
    const filtered = pegawaiData.filter(p => {
        const nama = (p.nama || '').toLowerCase();
        const nip = (p.nip || '').toLowerCase();
        const jabatan = (p.jabatan || '').toLowerCase();
        return nama.includes(term) || nip.includes(term) || jabatan.includes(term);
    });

    if (filtered.length === 0) {
        listContainer.innerHTML = '<div class="no-results">Tidak ada personil yang ditemukan</div>';
        return;
    }

    let html = '';
    filtered.forEach(p => {
        // Check if already selected in other roles
        const isDisabled = isPersonilUsedInOtherRoles(p.id, currentRole);
        const isSelected = selectedPersonil[currentRole]?.id == p.id;
        const disabledClass = isDisabled ? 'disabled' : '';
        const selectedIcon = isSelected ? '<span class="checkmark">✓</span>' : '';
        
        html += `
            <div class="personil-search-item ${disabledClass}"
                 onclick="${isDisabled ? '' : `selectRolePersonil(${p.id})`}">
                <div class="personil-search-info">
                    <div class="personil-search-name">${p.nama} ${selectedIcon}</div>
                    <div class="personil-search-details">${p.nip} • ${p.jabatan}</div>
                </div>
            </div>
        `;
    });

    listContainer.innerHTML = html;
}

// Filter role personil
function filterRolePersonil() {
    const searchTerm = document.getElementById('searchRoleInput').value;
    populateRolePersonilList(searchTerm);
}

// Select role personil
function selectRolePersonil(pegawaiId) {
    console.log('selectRolePersonil called with pegawaiId:', pegawaiId);
    const pegawai = pegawaiData.find(p => p.id == pegawaiId);
    console.log('Found pegawai:', pegawai);
    if (!pegawai) return;

    // Check if Perjalanan Dinas with Plt. Inspektur restriction
    const jenisPenugasan = document.getElementById('jenis_penugasan');
    const isPerjalananDinas = jenisPenugasan && jenisPenugasan.value === 'Perjalanan Dinas Luar Daerah';

    if (isPerjalananDinas) {
        const isPltInspektur = pegawai.nama.includes('BOTTEN TANDIPADA');
        const hasPltInspektur = checkIfPltInspekturSelected();
        const totalPersonil = getTotalPersonilCount();

        if (isPltInspektur && totalPersonil > 0) {
            alert('Jika Plt. Inspektur dipilih untuk Perjalanan Dinas Luar Daerah, tidak boleh ada pegawai lain yang dipilih!');
            return;
        }

        if (!isPltInspektur && hasPltInspektur) {
            alert('Plt. Inspektur sudah dipilih untuk Perjalanan Dinas Luar Daerah. Tidak boleh menambah pegawai lain!');
            return;
        }
    }

    selectedPersonil[currentRole] = pegawai;
    console.log('Updated selectedPersonil[' + currentRole + ']:', selectedPersonil[currentRole]);

    // Update hidden input
    document.getElementById(currentRole + '_id').value = pegawaiId;

    // Update display
    updateAllDisplays();

    // Close modal
    closeRoleModal();
}

// Remove role personil
function removeRolePersonil(role) {
    selectedPersonil[role] = null;
    document.getElementById(role + '_id').value = '';
    updateAllDisplays();
}

// Open modal for anggota selection
function openPersonilModal() {
    const modal = document.getElementById('modalPersonil');
    modal.classList.add('show');
    document.body.style.overflow = 'hidden';

    populatePersonilList();

    setTimeout(() => {
        document.getElementById('searchPersonilInput').focus();
    }, 100);
}

// Close anggota modal
function closePersonilModal() {
    const modal = document.getElementById('modalPersonil');
    modal.classList.remove('show');
    document.body.style.overflow = '';
    document.getElementById('searchPersonilInput').value = '';
}

// Populate anggota personil list
function populatePersonilList(searchTerm = '') {
    const listContainer = document.getElementById('personilSearchList');

    if (!pegawaiData || pegawaiData.length === 0) {
        listContainer.innerHTML = '<div class="no-results">Data pegawai tidak tersedia</div>';
        return;
    }

    const term = searchTerm.toLowerCase();
    const filtered = pegawaiData.filter(p => {
        const nama = (p.nama || '').toLowerCase();
        const nip = (p.nip || '').toLowerCase();
        const jabatan = (p.jabatan || '').toLowerCase();
        return nama.includes(term) || nip.includes(term) || jabatan.includes(term);
    });

    if (filtered.length === 0) {
        listContainer.innerHTML = '<div class="no-results">Tidak ada personil yang ditemukan</div>';
        return;
    }

    let html = '';
    filtered.forEach(p => {
        // Check if already selected in roles
        const isDisabled = isPersonilUsedInRoles(p.id);
        const isChecked = selectedPersonil.anggota.find(a => a.id == p.id) ? 'checked' : '';
        const disabledClass = isDisabled ? 'disabled' : '';

        html += `
            <div class="personil-search-item ${disabledClass}">
                <label class="personil-checkbox-label">
                    <input
                        type="checkbox"
                        value="${p.id}"
                        ${isChecked}
                        ${isDisabled ? 'disabled' : ''}
                        onclick="togglePersonilSelection(${p.id})"
                    >
                    <div class="personil-search-info">
                        <div class="personil-search-name">${p.nama}</div>
                        <div class="personil-search-details">${p.nip} • ${p.jabatan}</div>
                    </div>
                </label>
            </div>
        `;
    });

    listContainer.innerHTML = html;
}

// Filter anggota personil
function filterPersonil() {
    const searchTerm = document.getElementById('searchPersonilInput').value;
    populatePersonilList(searchTerm);
}

// Toggle anggota selection
function togglePersonilSelection(pegawaiId) {
    console.log('togglePersonilSelection called with pegawaiId:', pegawaiId);
    const pegawai = pegawaiData.find(p => p.id == pegawaiId);
    console.log('Found pegawai:', pegawai);
    if (!pegawai) return;

    const index = selectedPersonil.anggota.findIndex(a => a.id == pegawaiId);

    if (index > -1) {
        // Remove from selection
        selectedPersonil.anggota.splice(index, 1);
        console.log('Removed from anggota');
    } else {
        // Check if Perjalanan Dinas with Plt. Inspektur restriction
        const jenisPenugasan = document.getElementById('jenis_penugasan');
        const isPerjalananDinas = jenisPenugasan && jenisPenugasan.value === 'Perjalanan Dinas Luar Daerah';

        if (isPerjalananDinas) {
            const isPltInspektur = pegawai.nama.includes('BOTTEN TANDIPADA');
            const hasPltInspektur = checkIfPltInspekturSelected();
            const totalPersonil = getTotalPersonilCount();

            if (isPltInspektur && totalPersonil > 0) {
                alert('Jika Plt. Inspektur dipilih untuk Perjalanan Dinas Luar Daerah, tidak boleh ada pegawai lain yang dipilih!');
                // Uncheck the checkbox
                event.target.checked = false;
                return;
            }

            if (!isPltInspektur && hasPltInspektur) {
                alert('Plt. Inspektur sudah dipilih untuk Perjalanan Dinas Luar Daerah. Tidak boleh menambah pegawai lain!');
                // Uncheck the checkbox
                event.target.checked = false;
                return;
            }
        }

        // Add to selection
        selectedPersonil.anggota.push(pegawai);
        console.log('Added to anggota');
    }

    console.log('Current anggota:', selectedPersonil.anggota);
    updateAllDisplays();
}

// Remove anggota
function removeAnggota(pegawaiId) {
    const index = selectedPersonil.anggota.findIndex(a => a.id == pegawaiId);
    if (index > -1) {
        selectedPersonil.anggota.splice(index, 1);
        updateAllDisplays();
    }
}

// Update anggota hidden inputs
function updateAnggotaHiddenInputs() {
    const container = document.getElementById('anggotaHiddenInputs');
    let html = '';

    selectedPersonil.anggota.forEach(pegawai => {
        html += `<input type="hidden" name="anggota[]" value="${pegawai.id}">`;
    });

    container.innerHTML = html;
}

// Handle jenis penugasan change
function handleJenisPenugasanChange() {
    const jenisPenugasan = document.getElementById('jenis_penugasan');
    if (!jenisPenugasan) return;

    const isPerjalananDinas = jenisPenugasan.value === 'Perjalanan Dinas Luar Daerah';

    if (isPerjalananDinas) {
        // Clear PJ, PT, KT selections
        selectedPersonil.penanggung_jawab = null;
        selectedPersonil.pengendali_teknis = null;
        selectedPersonil.ketua_tim = null;

        // Clear hidden inputs
        document.getElementById('penanggung_jawab_id').value = '';
        document.getElementById('pengendali_teknis_id').value = '';
        document.getElementById('ketua_tim_id').value = '';
    }

    updateAllDisplays();
}

// Check if Perjalanan Dinas is selected
function isPerjalananDinas() {
    const jenisPenugasan = document.getElementById('jenis_penugasan');
    return jenisPenugasan && jenisPenugasan.value === 'Perjalanan Dinas Luar Daerah';
}

// Update all displays with new layout (similar to detail page)
function updateAllDisplays() {
    console.log('updateAllDisplays called');
    const container = document.getElementById('personilListEdit');
    console.log('personilListEdit container:', container);
    if (!container) {
        console.error('personilListEdit container not found!');
        return;
    }

    const isPerjalananDinasMode = isPerjalananDinas();
    let html = '';

    // Penanggung Jawab
    if (!isPerjalananDinasMode) {
        const pj = selectedPersonil.penanggung_jawab;
        if (pj) {
            html += `
                <div class="personil-item-edit">
                    <div class="personil-role-edit">PJ</div>
                    <div class="personil-details-edit">
                        <div class="personil-name-edit">${pj.nama}</div>
                        <div class="personil-info-edit">${pj.jabatan} • ${pj.golongan || pj.nip}</div>
                    </div>
                    <button type="button" class="btn-remove-personil-edit" onclick="removeRolePersonil('penanggung_jawab')" title="Hapus">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/>
                        </svg>
                    </button>
                </div>
            `;
        } else {
            html += `
                <div class="personil-item-edit empty" onclick="openRoleModal('penanggung_jawab')">
                    <span>+ Pilih Penanggung Jawab</span>
                </div>
            `;
        }
    } else {
        html += `
            <div class="personil-item-edit disabled-role">
                <div class="personil-role-edit">PJ</div>
                <div class="personil-details-edit">
                    <div class="personil-name-edit" style="color: #94a3b8; font-style: italic;">Tidak diperlukan untuk Perjalanan Dinas</div>
                </div>
            </div>
        `;
    }

    // Pengendali Teknis
    if (!isPerjalananDinasMode) {
        const pt = selectedPersonil.pengendali_teknis;
        if (pt) {
            html += `
                <div class="personil-item-edit">
                    <div class="personil-role-edit">PT</div>
                    <div class="personil-details-edit">
                        <div class="personil-name-edit">${pt.nama}</div>
                        <div class="personil-info-edit">${pt.jabatan} • ${pt.golongan || pt.nip}</div>
                    </div>
                    <button type="button" class="btn-remove-personil-edit" onclick="removeRolePersonil('pengendali_teknis')" title="Hapus">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/>
                        </svg>
                    </button>
                </div>
            `;
        } else {
            html += `
                <div class="personil-item-edit empty" onclick="openRoleModal('pengendali_teknis')">
                    <span>+ Pilih Pengendali Teknis</span>
                </div>
            `;
        }
    } else {
        html += `
            <div class="personil-item-edit disabled-role">
                <div class="personil-role-edit">PT</div>
                <div class="personil-details-edit">
                    <div class="personil-name-edit" style="color: #94a3b8; font-style: italic;">Tidak diperlukan untuk Perjalanan Dinas</div>
                </div>
            </div>
        `;
    }

    // Ketua Tim
    if (!isPerjalananDinasMode) {
        const kt = selectedPersonil.ketua_tim;
        if (kt) {
            html += `
                <div class="personil-item-edit">
                    <div class="personil-role-edit">KETUA TIM</div>
                    <div class="personil-details-edit">
                        <div class="personil-name-edit">${kt.nama}</div>
                        <div class="personil-info-edit">${kt.jabatan} • ${kt.golongan || kt.nip}</div>
                    </div>
                    <button type="button" class="btn-remove-personil-edit" onclick="removeRolePersonil('ketua_tim')" title="Hapus">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/>
                        </svg>
                    </button>
                </div>
            `;
        } else {
            html += `
                <div class="personil-item-edit empty" onclick="openRoleModal('ketua_tim')">
                    <span>+ Pilih Ketua Tim</span>
                </div>
            `;
        }
    } else {
        html += `
            <div class="personil-item-edit disabled-role">
                <div class="personil-role-edit">KETUA TIM</div>
                <div class="personil-details-edit">
                    <div class="personil-name-edit" style="color: #94a3b8; font-style: italic;">Tidak diperlukan untuk Perjalanan Dinas</div>
                </div>
            </div>
        `;
    }

    // Anggota
    if (selectedPersonil.anggota.length > 0) {
        selectedPersonil.anggota.forEach((anggota) => {
            html += `
                <div class="personil-item-edit">
                    <div class="personil-role-edit">ANGGOTA</div>
                    <div class="personil-details-edit">
                        <div class="personil-name-edit">${anggota.nama}</div>
                        <div class="personil-info-edit">${anggota.jabatan} • ${anggota.golongan || anggota.nip}</div>
                    </div>
                    <button type="button" class="btn-remove-personil-edit" onclick="removeAnggota(${anggota.id})" title="Hapus">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/>
                        </svg>
                    </button>
                </div>
            `;
        });
    }

    // Add button for anggota
    html += `
        <div class="personil-item-edit empty" onclick="openPersonilModal()">
            <span>+ Tambah Anggota</span>
        </div>
    `;

    container.innerHTML = html;
    updateAnggotaHiddenInputs();
}

// Check if personil is used in roles (PJ, PT, KT)
function isPersonilUsedInRoles(pegawaiId) {
    return selectedPersonil.penanggung_jawab?.id == pegawaiId ||
           selectedPersonil.pengendali_teknis?.id == pegawaiId ||
           selectedPersonil.ketua_tim?.id == pegawaiId;
}

// Check if personil is used in other roles (excluding current role)
function isPersonilUsedInOtherRoles(pegawaiId, excludeRole) {
    const roles = ['penanggung_jawab', 'pengendali_teknis', 'ketua_tim'];

    for (let role of roles) {
        if (role !== excludeRole && selectedPersonil[role]?.id == pegawaiId) {
            return true;
        }
    }

    // Also check in anggota
    return selectedPersonil.anggota.find(a => a.id == pegawaiId) !== undefined;
}

// Close modal when clicking outside
window.onclick = function(event) {
    const modalRole = document.getElementById('modalRole');
    const modalPersonil = document.getElementById('modalPersonil');

    if (event.target === modalRole) {
        closeRoleModal();
    }
    if (event.target === modalPersonil) {
        closePersonilModal();
    }
}

// Close modal with ESC key
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        closeRoleModal();
        closePersonilModal();
    }
});

// Helper function to check if Plt. Inspektur is selected
function checkIfPltInspekturSelected() {
    // Check in all roles
    if (selectedPersonil.penanggung_jawab && selectedPersonil.penanggung_jawab.nama.includes('BOTTEN TANDIPADA')) {
        return true;
    }
    if (selectedPersonil.pengendali_teknis && selectedPersonil.pengendali_teknis.nama.includes('BOTTEN TANDIPADA')) {
        return true;
    }
    if (selectedPersonil.ketua_tim && selectedPersonil.ketua_tim.nama.includes('BOTTEN TANDIPADA')) {
        return true;
    }
    // Check in anggota
    for (let anggota of selectedPersonil.anggota) {
        if (anggota.nama.includes('BOTTEN TANDIPADA')) {
            return true;
        }
    }
    return false;
}

// Helper function to get total personil count
function getTotalPersonilCount() {
    let count = 0;
    if (selectedPersonil.penanggung_jawab) count++;
    if (selectedPersonil.pengendali_teknis) count++;
    if (selectedPersonil.ketua_tim) count++;
    count += selectedPersonil.anggota.length;
    return count;
}



