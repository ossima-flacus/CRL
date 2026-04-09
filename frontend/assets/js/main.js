// Handle form submission
const registrationForm = document.getElementById('registrationForm');

if (registrationForm) {
    registrationForm.addEventListener('submit', async (e) => {
        e.preventDefault();

        const formData = {
            first_name: document.getElementById('first_name').value,
            last_name: document.getElementById('last_name').value,
            gender: document.getElementById('gender').value,
            birth_date: document.getElementById('birth_date').value,
            birth_place: document.getElementById('birth_place').value,
            birth_certificate_number: document.getElementById('birth_certificate_number').value,
            previous_school: document.getElementById('previous_school').value || null,
            nationality: document.getElementById('nationality').value,
            class_id: parseInt(document.getElementById('class_id').value, 10),
            enrollment_type: document.querySelector('input[name="enrollment_type"]:checked').value,
            guardian: {
                full_name: document.getElementById('guardian_name').value,
                relationship: document.getElementById('guardian_relationship').value,
                phone: document.getElementById('guardian_phone').value,
                email: document.getElementById('guardian_email').value,
                address: document.getElementById('guardian_address').value,
                profession: document.getElementById('guardian_profession').value || null
            }
        };

        if (!formData.first_name || !formData.last_name || !formData.birth_date ||
            !formData.birth_place || !formData.birth_certificate_number || !formData.nationality ||
            !formData.class_id || !formData.guardian.full_name || !formData.guardian.relationship ||
            !formData.guardian.phone || !formData.guardian.email || !formData.guardian.address) {
            showToast('Veuillez remplir tous les champs obligatoires.', true);
            return;
        }

        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(formData.guardian.email)) {
            showToast('Veuillez entrer une adresse email valide.', true);
            return;
        }

        const submitButton = e.target.querySelector('button[type="submit"]');
        submitButton.disabled = true;
        submitButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Envoi en cours...';

        try {
            const response = await fetch('../backend/public/index.php?route=register', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(formData)
            });

            const result = await response.json();

            if (!response.ok || !result.success) {
                showToast(result.message || 'Erreur lors de l’envoi. Veuillez réessayer.', true);
                submitButton.disabled = false;
                submitButton.innerHTML = '<i class="fas fa-paper-plane"></i> Envoyer la pré-inscription';
                return;
            }

            showToast(`✅ Inscription réussie ! Numéro d'inscription: ${result.registration_id}`);
            registrationForm.reset();
            window.scrollTo({ top: 0, behavior: 'smooth' });
        } catch (error) {
            showToast('Erreur serveur. Veuillez réessayer plus tard.', true);
        } finally {
            submitButton.disabled = false;
            submitButton.innerHTML = '<i class="fas fa-paper-plane"></i> Envoyer la pré-inscription';
        }
    });
}

function showToast(message, isError = false) {
    const toast = document.getElementById('toast');
    if (!toast) return;
    toast.textContent = message;
    toast.classList.remove('hidden', 'bg-green-500', 'bg-red-500');
    toast.classList.add(isError ? 'bg-red-500' : 'bg-green-500');
    toast.classList.remove('hidden');
    setTimeout(() => {
        toast.classList.add('hidden');
    }, 5000);
}

async function loadRegistrationClasses() {
    const classSelect = document.getElementById('class_id');
    if (!classSelect) return;

    try {
        const response = await fetch('../backend/public/index.php?route=class.list');
        const result = await response.json();

        if (!response.ok || !result.success) {
            return;
        }

        classSelect.innerHTML = '<option value="">Sélectionnez une classe</option>';
        result.classes.forEach((classItem) => {
            const option = document.createElement('option');
            option.value = classItem.id;
            option.textContent = `${classItem.level} — ${classItem.name}`;
            classSelect.appendChild(option);
        });
    } catch (error) {
        console.warn('Impossible de charger les classes.', error);
    }
}

loadRegistrationClasses();
