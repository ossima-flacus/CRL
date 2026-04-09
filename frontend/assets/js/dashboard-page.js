const API_BASE = '../backend/public/index.php';

const classListContainer = document.getElementById('classListContainer');
const studentListContainer = document.getElementById('studentListContainer');
const statClasses = document.getElementById('statClasses');
const statStudents = document.getElementById('statStudents');
const avgFillRate = document.getElementById('avgFillRate');
const levelSummaryPanel = document.getElementById('levelSummaryPanel');
const structureByLevel = document.getElementById('structureByLevel');
const filterClassesBadge = document.getElementById('filterClassesBadge');
const filterStudentsBadge = document.getElementById('filterStudentsBadge');

let classes = [];
let students = [];

function showToast(message, isError = false) {
    const toast = document.createElement('div');
    toast.className = 'toast-notif';
    toast.style.backgroundColor = isError ? '#dc2626' : '#1e88e5';
    toast.innerHTML = `<i class="fas ${isError ? 'fa-exclamation-triangle' : 'fa-check-circle'}"></i> ${message}`;
    document.body.appendChild(toast);
    setTimeout(() => toast.remove(), 2800);
}

function escapeHtml(value) {
    if (value === null || value === undefined) return '';
    return String(value).replace(/[&<>]/g, char => {
        if (char === '&') return '&amp;';
        if (char === '<') return '&lt;';
        if (char === '>') return '&gt;';
        return char;
    });
}

function getStudentCountByClass(classId) {
    return students.filter(student => Number(student.class_id) === Number(classId)).length;
}

function updateStatsAndViews() {
    if (statClasses) statClasses.textContent = String(classes.length);
    if (statStudents) statStudents.textContent = String(students.length);
    if (avgFillRate) {
        const avg = classes.length === 0 ? 0 : (students.length / classes.length).toFixed(1);
        avgFillRate.textContent = classes.length ? `${avg} élève(s)` : '0';
    }
    if (filterClassesBadge) filterClassesBadge.textContent = 'Tous niveaux';
    if (filterStudentsBadge) filterStudentsBadge.textContent = 'Tous niveaux';

    renderClassList();
    renderStudentList();
    renderLevelSummary();
    renderStructure();
}

function renderClassList() {
    if (!classListContainer) return;

    if (classes.length === 0) {
        classListContainer.innerHTML = '<div class="empty-state"><i class="fas fa-chalkboard"></i> Aucune classe disponible.</div>';
        return;
    }

    classListContainer.innerHTML = classes.map(c => {
        const studentCount = getStudentCountByClass(c.id);
        return `
            <div class="class-item-modern">
                <div class="class-row">
                    <div>
                        <div class="class-title">${escapeHtml(c.name)}</div>
                        <div class="student-info info-row">
                            <span><i class="fas fa-user-graduate"></i> ${studentCount} élève(s)</span>
                            <span class="badge-class">${escapeHtml(c.level)}</span>
                        </div>
                    </div>
                </div>
                ${c.description ? `<div class="secondary-text">📌 ${escapeHtml(c.description)}</div>` : ''}
            </div>
        `;
    }).join('');
}

function renderStudentList() {
    if (!studentListContainer) return;

    if (students.length === 0) {
        studentListContainer.innerHTML = '<div class="empty-state"><i class="fas fa-user-friends"></i> Aucun élève inscrit.</div>';
        return;
    }

    studentListContainer.innerHTML = students.map(s => {
        const className = classes.find(c => Number(c.id) === Number(s.class_id))?.name || 'Non affecté';
        return `
            <div class="student-item-modern">
                <div class="student-details">
                    <div>
                        <div class="student-name">${escapeHtml(s.first_name)} ${escapeHtml(s.last_name)}</div>
                        <div class="student-info">
                            <span><i class="fas fa-envelope"></i> ${escapeHtml(s.email || '—')}</span>
                            <span><i class="fas fa-phone"></i> ${escapeHtml(s.phone || '—')}</span>
                            <span><i class="fas fa-calendar-alt"></i> ${escapeHtml(s.birth_date)}</span>
                            <span class="badge-class">${escapeHtml(className)}</span>
                        </div>
                    </div>
                </div>
                ${s.notes ? `<div class="student-note-text"><i class="fas fa-pen"></i> ${escapeHtml(s.notes)}</div>` : ''}
            </div>
        `;
    }).join('');
}

function renderLevelSummary() {
    if (!levelSummaryPanel) return;

    const levels = ['Pré-primaire', 'Primaire', 'Lycée'];
    const html = levels.map(level => {
        const classCount = classes.filter(c => c.level === level).length;
        const studentCount = students.filter(s => classes.some(c => Number(c.id) === Number(s.class_id) && c.level === level)).length;
        return `<div class="level-stat"><span><strong>${level}</strong></span><span>${classCount} classe(s) · ${studentCount} élève(s)</span></div>`;
    }).join('');

    levelSummaryPanel.innerHTML = html || '<p>Aucune donnée</p>';
}

function renderStructure() {
    if (!structureByLevel) return;

    const levels = ['Pré-primaire', 'Primaire', 'Lycée'];
    structureByLevel.innerHTML = levels.map(level => {
        const classesInLevel = classes.filter(c => c.level === level);
        if (classesInLevel.length === 0) {
            return `
                <div class="structure-entry">
                    <div class="structure-level-title">${escapeHtml(level)}</div>
                    <span style="font-size:0.75rem;">Aucune classe</span>
                </div>
            `;
        }

        const chips = classesInLevel.map(c => {
            const studentCount = getStudentCountByClass(c.id);
            return `<span class="class-chip">${escapeHtml(c.name)} (${studentCount})</span>`;
        }).join('');

        return `
            <div class="structure-entry">
                <div class="structure-level-title">📌 ${escapeHtml(level)}</div>
                <div>${chips}</div>
            </div>
        `;
    }).join('');
}

async function fetchJson(route, options = {}) {
    const response = await fetch(`${API_BASE}?route=${route}`, options);
    return response.json();
}

async function fetchClasses() {
    const result = await fetchJson('class.list');
    return result.success ? result.classes : [];
}

async function fetchStudents() {
    const result = await fetchJson('student.list');
    return result.success ? result.students : [];
}

async function refreshDashboard() {
    try {
        const [loadedClasses, loadedStudents] = await Promise.all([fetchClasses(), fetchStudents()]);
        classes = Array.isArray(loadedClasses) ? loadedClasses : [];
        students = Array.isArray(loadedStudents) ? loadedStudents : [];
        updateStatsAndViews();
    } catch (error) {
        showToast('Impossible de charger les données du tableau de bord.', true);
        console.error('Dashboard load error:', error);
    }
}

window.addEventListener('DOMContentLoaded', refreshDashboard);

