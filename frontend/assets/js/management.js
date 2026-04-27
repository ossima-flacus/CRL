const API_BASE = '../backend/public/index.php';

function showToast(message, isError = false) {
    const toast = document.createElement('div');
    toast.className = 'toast-notif';
    toast.style.backgroundColor = isError ? '#dc2626' : '#1e88e5';
    toast.style.right = '24px';
    toast.style.bottom = '24px';
    toast.style.position = 'fixed';
    toast.style.zIndex = '1000';
    toast.innerHTML = `<i class="fas ${isError ? 'fa-exclamation-triangle' : 'fa-check-circle'}"></i> ${message}`;
    document.body.appendChild(toast);
    setTimeout(() => toast.remove(), 3000);
}

async function apiJson(route, options = {}) {
    try {
        const response = await fetch(`${API_BASE}?route=${route}`, options);
        
        if (!response.ok) {
            throw new Error(`HTTP ${response.status}: ${response.statusText}`);
        }
        
        const contentType = response.headers.get('content-type');
        if (!contentType || !contentType.includes('application/json')) {
            throw new Error('Réponse serveur invalide (HTML reçu au lieu de JSON)');
        }
        
        return response.json();
    } catch (error) {
        console.error('API Error:', error);
        showToast(`Erreur API: ${error.message}`, true);
        throw error;
    }
}

async function fetchClasses() {
    const result = await apiJson('class.list');
    return result.success ? result.classes : [];
}

async function fetchStudents() {
    const result = await apiJson('student.list');
    return result.success ? result.students : [];
}

document.addEventListener('DOMContentLoaded', () => {
    const toggleButtons = document.querySelectorAll('[data-target]');
    const panels = document.querySelectorAll('.management-panel');
    const classesList = document.getElementById('classesList');
    const classForm = document.getElementById('classForm');
    const studentForm = document.getElementById('studentForm');
    const classLevelInput = document.getElementById('class_level');
    const studentClassSelect = document.getElementById('student_class_id');

    const currentLevel = classLevelInput ? classLevelInput.value : '';

    function hideAllPanels() {
        panels.forEach((panel) => panel.classList.add('hidden'));
    }

    function showPanel(id) {
        hideAllPanels();
        const panel = document.getElementById(id);
        if (panel) panel.classList.remove('hidden');
    }

    toggleButtons.forEach((button) => {
        button.addEventListener('click', () => {
            const target = button.dataset.target;
            if (target) showPanel(target);
        });
    });

    function renderClasses(classes, students) {
        if (!classesList) return;

        const levelClasses = classes.filter(cls => cls.level === currentLevel);
        if (!levelClasses.length) {
            classesList.innerHTML = '<p class="text-slate-600">Aucune classe créée pour ce niveau. Créez une classe pour afficher le détail.</p>';
            return;
        }

        classesList.innerHTML = levelClasses.map(cls => {
            const count = students.filter(student => Number(student.class_id) === Number(cls.id)).length;
            return `
                <div class="rounded-3xl bg-white border border-slate-200 p-6 shadow-sm">
                    <h3 class="text-xl font-semibold text-slate-900">${cls.name}</h3>
                    <p class="mt-2 text-slate-600">${cls.description || 'Pas de description fournie.'}</p>
                    <p class="mt-4 text-sm text-slate-500"><strong>${count}</strong> élève${count > 1 ? 's' : ''} assigné${count > 1 ? 's' : ''}</p>
                </div>`;
        }).join('');
    }

    function populateClassSelect(classes) {
        if (!studentClassSelect) return;
        const levelClasses = classes.filter(cls => cls.level === currentLevel);
        studentClassSelect.innerHTML = '<option value="">Sélectionnez une classe</option>';

        levelClasses.forEach(cls => {
            const option = document.createElement('option');
            option.value = cls.id;
            option.textContent = `${cls.name}`;
            studentClassSelect.appendChild(option);
        });

        if (!levelClasses.length) {
            const option = document.createElement('option');
            option.value = '';
            option.disabled = true;
            option.textContent = 'Créez d’abord une classe pour ce niveau';
            studentClassSelect.appendChild(option);
        }
    }

    async function refreshManagement() {
        try {
            const [classes, students] = await Promise.all([fetchClasses(), fetchStudents()]);
            renderClasses(classes, students);
            populateClassSelect(classes);
        } catch (error) {
            showToast('Impossible de charger les classes.', true);
            console.error(error);
        }
    }

    async function submitClass(event) {
        event.preventDefault();
        if (!classForm) return;

        const name = document.getElementById('class_name').value.trim();
        const level = document.getElementById('class_level').value;
        const description = document.getElementById('class_description').value.trim();

        if (!name || !level) {
            showToast('Le nom et le niveau de la classe sont requis.', true);
            return;
        }

        const result = await apiJson('class.create', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ name, level, description }),
        });

        if (!result.success) {
            showToast(result.message || 'Erreur lors de la création de la classe.', true);
            return;
        }

        showToast('Classe créée avec succès.');
        classForm.reset();
        if (classLevelInput) classLevelInput.value = currentLevel;
        await refreshManagement();
    }

    async function submitStudent(event) {
        event.preventDefault();
        if (!studentForm) return;

        const firstName = document.getElementById('student_first_name').value.trim();
        const lastName = document.getElementById('student_last_name').value.trim();
        const gender = document.getElementById('student_gender').value;
        const birthDate = document.getElementById('student_birth_date').value;
        const classId = document.getElementById('student_class_id').value;

        if (!firstName || !lastName || !gender || !birthDate || !classId) {
            showToast('Veuillez remplir tous les champs obligatoires.', true);
            return;
        }

        const result = await apiJson('student.create', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                first_name: firstName,
                last_name: lastName,
                gender,
                birth_date: birthDate,
                email: null,
                phone: null,
                class_id: parseInt(classId, 10),
                notes: null,
            }),
        });

        if (!result.success) {
            showToast(result.message || 'Erreur lors de l’ajout de l’élève.', true);
            return;
        }

        showToast('Élève ajouté avec succès.');
        studentForm.reset();
        if (studentClassSelect) studentClassSelect.selectedIndex = 0;
        await refreshManagement();
    }

    if (classForm) classForm.addEventListener('submit', submitClass);
    if (studentForm) studentForm.addEventListener('submit', submitStudent);

    refreshManagement();
});
