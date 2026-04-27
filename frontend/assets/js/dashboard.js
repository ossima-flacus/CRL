const classForm = document.getElementById("classForm");
const studentForm = document.getElementById("studentForm");
const classList = document.getElementById("classList");
const studentList = document.getElementById("studentList");
const classesCount = document.getElementById("classesCount");
const studentsCount = document.getElementById("studentsCount");
const dashboardClassId = document.getElementById("dashboard_class_id");
const levelSummary = document.getElementById("levelSummary");
const classesByLevel = document.getElementById("classesByLevel");
const dashboardToast = document.getElementById("dashboardToast");

// Utiliser la configuration globale
const API_BASE = getApiBasePath
  ? getApiBasePath()
  : "../backend/public/index.php?route=";

function showDashboardToast(message, isError = false) {
  if (!dashboardToast) return;
  dashboardToast.textContent = message;
  dashboardToast.classList.remove("hidden", "bg-green-500", "bg-red-500");
  dashboardToast.classList.add(isError ? "bg-red-500" : "bg-green-500");
  dashboardToast.classList.remove("hidden");
  setTimeout(() => {
    dashboardToast.classList.add("hidden");
  }, 5000);
}

async function fetchClasses() {
  const response = await fetch(`${API_BASE}class.list`);
  const result = await response.json();
  return result.success ? result.classes : [];
}

async function fetchStudents() {
  const response = await fetch(`${API_BASE}student.list`);
  const result = await response.json();
  return result.success ? result.students : [];
}

function getStudentCountByClass(students, classId) {
  return students.filter(
    (student) => String(student.class_id) === String(classId),
  ).length;
}

function buildLevelSummary(classes) {
  const levels = ["Pré-primaire", "Primaire", "Lycée"];
  if (!levelSummary) return;
  levelSummary.innerHTML = levels
    .map((level) => {
      const count = classes.filter((item) => item.level === level).length;
      return `<div class="rounded-3xl bg-slate-50 border border-slate-200 p-5">
                    <p class="text-sm uppercase tracking-[0.24em] text-slate-500 font-semibold">${level}</p>
                    <p class="mt-3 text-3xl font-bold text-slate-900">${count}</p>
                    <p class="mt-2 text-slate-500 text-sm">${count === 0 ? "Aucune classe" : `${count} classe${count > 1 ? "s" : ""}`}</p>
                </div>`;
    })
    .join("");
}

function buildClassesByLevel(classes, students) {
  if (!classesByLevel) return;

  const grouped = classes.reduce((acc, item) => {
    acc[item.level] = acc[item.level] || [];
    acc[item.level].push(item);
    return acc;
  }, {});

  const levels = ["Pré-primaire", "Primaire", "Lycée"];
  classesByLevel.innerHTML = levels
    .map((level) => {
      const levelClasses = grouped[level] || [];
      if (!levelClasses.length) {
        return `<div class="rounded-3xl bg-slate-50 border border-slate-200 p-5">
                        <h3 class="text-lg font-semibold text-slate-900 mb-3">${level}</h3>
                        <p class="text-slate-500">Aucune classe créée pour ce niveau.</p>
                    </div>`;
      }

      const classCards = levelClasses
        .map((cls) => {
          const count = getStudentCountByClass(students, cls.id);
          return `<div class="rounded-3xl bg-white border border-slate-200 p-4 shadow-sm">
                        <div class="flex items-start justify-between gap-4">
                            <div>
                                <p class="text-base font-semibold text-slate-900">${cls.name}</p>
                                <p class="text-sm text-slate-500 mt-1">${cls.description || "Pas de description."}</p>
                            </div>
                            <span class="inline-flex items-center rounded-full bg-sky-100 text-sky-700 px-3 py-1 text-sm font-semibold">${count} élève${count > 1 ? "s" : ""}</span>
                        </div>
                    </div>`;
        })
        .join("");

      return `<div class="rounded-3xl bg-slate-50 border border-slate-200 p-5">
                    <h3 class="text-lg font-semibold text-slate-900 mb-4">${level}</h3>
                    <div class="space-y-4">${classCards}</div>
                </div>`;
    })
    .join("");
}

function renderClassList(classes, students) {
  if (!classList) return;
  if (!classes.length) {
    classList.innerHTML =
      '<p class="text-slate-500">Aucune classe créée pour l’instant.</p>';
    return;
  }

  classList.innerHTML = classes
    .map((cls) => {
      const studentCount = getStudentCountByClass(students, cls.id);
      return `<div class="rounded-3xl bg-slate-50 border border-slate-200 p-6 shadow-sm">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <p class="text-sm uppercase tracking-[0.24em] text-slate-500">${cls.level}</p>
                            <h3 class="text-xl font-semibold text-slate-900 mt-2">${cls.name}</h3>
                            <p class="mt-3 text-slate-600">${cls.description || "Description non renseignée."}</p>
                        </div>
                        <div class="text-right">
                            <p class="text-3xl font-bold text-slate-900">${studentCount}</p>
                            <p class="text-sm text-slate-500">élève${studentCount > 1 ? "s" : ""}</p>
                        </div>
                    </div>
                </div>`;
    })
    .join("");
}

function renderStudentList(students) {
  if (!studentList) return;
  if (!students.length) {
    studentList.innerHTML =
      '<p class="text-slate-500">Aucun élève ajouté pour l’instant.</p>';
    return;
  }

  studentList.innerHTML = students
    .slice(0, 10)
    .map((student) => {
      return `<div class="rounded-3xl bg-slate-50 border border-slate-200 p-5 shadow-sm">
                    <div class="flex items-center justify-between gap-4">
                        <div>
                            <p class="text-base font-semibold text-slate-900">${student.first_name} ${student.last_name}</p>
                            <p class="text-sm text-slate-500">Classe : ${student.class_name || "Non assignée"}</p>
                        </div>
                        <span class="text-sm uppercase tracking-[0.2em] text-slate-500">${student.gender}</span>
                    </div>
                    <p class="mt-3 text-slate-600 text-sm">${student.notes || "Pas de remarque."}</p>
                </div>`;
    })
    .join("");
}

function fillClassSelect(classes) {
  if (!dashboardClassId) return;
  dashboardClassId.innerHTML =
    '<option value="">Sélectionnez une classe</option>';
  classes.forEach((cls) => {
    const option = document.createElement("option");
    option.value = cls.id;
    option.textContent = `${cls.level} — ${cls.name}`;
    dashboardClassId.appendChild(option);
  });
}

async function refreshDashboard() {
  try {
    const [classes, students] = await Promise.all([
      fetchClasses(),
      fetchStudents(),
    ]);
    if (classesCount) classesCount.textContent = classes.length;
    if (studentsCount) studentsCount.textContent = students.length;
    renderClassList(classes, students);
    renderStudentList(students);
    buildLevelSummary(classes);
    buildClassesByLevel(classes, students);
    fillClassSelect(classes);
  } catch (error) {
    showDashboardToast("Impossible de charger le tableau de bord.", true);
    console.error(error);
  }
}

async function createClass(data) {
  const response = await fetch(`${API_BASE}class.create`, {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify(data),
  });
  return response.json();
}

async function createStudent(data) {
  const response = await fetch(`${API_BASE}student.create`, {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify(data),
  });
  return response.json();
}

if (classForm) {
  classForm.addEventListener("submit", async (e) => {
    e.preventDefault();
    const data = {
      name: document.getElementById("class_name").value.trim(),
      level: document.getElementById("class_level").value,
      description: document.getElementById("class_description").value.trim(),
    };

    if (!data.name || !data.level) {
      showDashboardToast("Le nom et le niveau de la classe sont requis.", true);
      return;
    }

    const result = await createClass(data);
    if (!result.success) {
      showDashboardToast(
        result.message || "Erreur lors de la création de la classe.",
        true,
      );
      return;
    }

    showDashboardToast("Classe créée avec succès.");
    classForm.reset();
    await refreshDashboard();
  });
}

if (studentForm) {
  studentForm.addEventListener("submit", async (e) => {
    e.preventDefault();
    const data = {
      first_name: document.getElementById("student_first_name").value.trim(),
      last_name: document.getElementById("student_last_name").value.trim(),
      gender: document.getElementById("student_gender").value,
      birth_date: document.getElementById("student_birth_date").value,
      email: document.getElementById("student_email")
        ? document.getElementById("student_email").value.trim() || null
        : null,
      phone: document.getElementById("student_phone")
        ? document.getElementById("student_phone").value.trim() || null
        : null,
      class_id: document.getElementById("dashboard_class_id").value,
      notes: document.getElementById("student_notes").value.trim() || null,
    };

    if (
      !data.first_name ||
      !data.last_name ||
      !data.gender ||
      !data.birth_date ||
      !data.class_id
    ) {
      showDashboardToast(
        "Veuillez remplir tous les champs obligatoires.",
        true,
      );
      return;
    }

    const result = await createStudent(data);
    if (!result.success) {
      showDashboardToast(
        result.message || "Erreur lors de l’ajout de l’élève.",
        true,
      );
      return;
    }

    showDashboardToast("Élève ajouté avec succès.");
    studentForm.reset();
    await refreshDashboard();
  });
}

refreshDashboard();
