const pmData = document.getElementById('pm-data');

const findings = pmData ? JSON.parse(pmData.dataset.findings || '{}') : {};
const spareparts = pmData ? JSON.parse(pmData.dataset.spareparts || '[]') : [];
const bigProblems = pmData ? JSON.parse(pmData.dataset.bigProblems || '[]') : [];
const users = pmData ? JSON.parse(pmData.dataset.users || '[]') : [];
const defaultManpowerPerson = pmData ? pmData.dataset.defaultPerson || '' : '';

let problemIndex = pmData ? Number(pmData.dataset.problemIndex || 0) : 0;
let sparepartIndex = pmData ? Number(pmData.dataset.sparepartIndex || 0) : 0;
let initialized = false;

function escapeHtml(value) {
    return String(value ?? '')
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#39;');
}

function buildProblemRow(index) {
    const options = bigProblems
        .map((problem) => {
            const category = escapeHtml((problem.category || '').trim().toLowerCase());
            const label = escapeHtml(problem.problem || '');

            return `<option value="${escapeHtml(problem.id)}" data-category="${category}">${label}</option>`;
        })
        .join('');

    return `
        <div class="problem-row mb-2 flex gap-2 max-sm:mb-3 max-sm:flex-col max-sm:rounded-xl max-sm:border max-sm:border-gray-200 max-sm:bg-gray-50 max-sm:p-3">
            <select name="problems[${index}][problem]" class="problem-select w-1/2 rounded-xl border border-gray-300 p-2 text-sm max-sm:w-full">
                <option value="">-- Select Problem --</option>
                ${options}
            </select>

            <select name="problems[${index}][finding]" class="finding-select flex-1 rounded-xl border border-gray-300 p-2 text-sm max-sm:w-full">
                <option value="">-- Finding --</option>
            </select>

            <select name="problems[${index}][severity]" class="w-1/5 rounded-xl border border-gray-300 p-2 text-sm max-sm:w-full">
                <option value="">-- Severity --</option>
                <option value="Low">Low</option>
                <option value="Medium">Medium</option>
                <option value="High">High</option>
            </select>

            <button type="button" onclick="removeProblem(this)" class="rounded-xl bg-red-500 px-3 py-2 text-white transition hover:bg-red-600 max-sm:w-full max-sm:py-2">
                X
            </button>
        </div>
    `;
}

function buildSparepartRow(index) {
    return `
        <div class="sparepart-row mb-2 flex gap-2 max-sm:mb-3 max-sm:flex-col max-sm:rounded-xl max-sm:border max-sm:border-gray-200 max-sm:bg-gray-50 max-sm:p-3">
            <select name="spareparts[${index}][sparepart_id]" placeholder="Select Sparepart" class="sparepart-select w-2/3 rounded-xl border border-gray-300 p-2 text-sm max-sm:w-full"></select>

            <input type="number" name="spareparts[${index}][qty]" placeholder="Qty" class="w-1/3 rounded-xl border border-gray-300 p-3 text-sm max-sm:w-full">

            <button type="button" onclick="removeSparepart(this)" class="rounded-xl bg-red-500 px-3 py-2 text-white transition hover:bg-red-600 max-sm:w-full max-sm:py-2">
                X
            </button>
        </div>
    `;
}

function formatMinutesAsDuration(minutes) {
    if (minutes === null || minutes === undefined || minutes === '') {
        return '';
    }

    const hours = Math.floor(minutes / 60);
    const mins = minutes % 60;
    return `${hours} Hours ${mins} Minutes`;
}

function formatMinutesAsDecimalHours(minutes) {
    if (minutes === null || minutes === undefined || minutes === '') {
        return '';
    }

    const hoursDecimal = (minutes / 60).toFixed(2);
    return `${hoursDecimal} MH`;
}

function parseTimeToMinutes(value) {
    if (!value || !value.includes(':')) {
        return null;
    }

    const [hour, minute] = value.split(':').map(num => parseInt(num, 10));
    if (Number.isNaN(hour) || Number.isNaN(minute)) {
        return null;
    }

    return hour * 60 + minute;
}

function calculateDurationFromTimes(start, end) {
    if (!start || !end) {
        return null;
    }

    const startMinutes = parseTimeToMinutes(start);
    const endMinutes = parseTimeToMinutes(end);

    if (startMinutes === null || endMinutes === null) {
        return null;
    }

    if (endMinutes < startMinutes) {
        return endMinutes + 24 * 60 - startMinutes;
    }

    return endMinutes - startMinutes;
}

function buildUserOptions(selected = '') {
    const options = ['<option value="">-- Select Person --</option>'];

    users.forEach((user) => {
        options.push(`
            <option value="${escapeHtml(user.name)}" ${user.name === selected ? 'selected' : ''}>
                ${escapeHtml(user.name)}
            </option>
        `);
    });

    return options.join('');
}

function buildManpowerRow(sessionIndex, index, manpower = {}) {
    const selectedPerson = manpower.person || defaultManpowerPerson || '';

    return `
        <div class="manpower-row mb-3 rounded-2xl border border-slate-200 bg-white p-4" data-manpower-index="${index}">
            <input data-manpower-field="sessions[{sessionIndex}][manpowers][{manpowerIndex}][id]" type="hidden" value="${manpower.id || ''}">

            <div class="grid grid-cols-1 gap-4 md:grid-cols-5">
                <div>
                    <label class="block mb-2 text-sm font-medium">Person</label>
                    <select data-manpower-field="sessions[{sessionIndex}][manpowers][{manpowerIndex}][person]" class="w-full rounded-2xl border border-gray-300 p-3 text-sm">
                        ${buildUserOptions(selectedPerson)}
                    </select>
                </div>
                <div>
                    <label class="block mb-2 text-sm font-medium">Start</label>
                    <input data-manpower-field="sessions[{sessionIndex}][manpowers][{manpowerIndex}][start_time]" type="time" value="${manpower.start_time || ''}" class="w-full rounded-2xl border border-gray-300 p-3 text-sm manpower-start">
                </div>
                <div>
                    <label class="block mb-2 text-sm font-medium">End</label>
                    <input data-manpower-field="sessions[{sessionIndex}][manpowers][{manpowerIndex}][end_time]" type="time" value="${manpower.end_time || ''}" class="w-full rounded-2xl border border-gray-300 p-3 text-sm manpower-end">
                </div>
                <div>
                    <label class="block mb-2 text-sm font-medium">Duration</label>
                    <input type="text" readonly value="${formatMinutesAsDuration(manpower.duration)}" class="w-full rounded-2xl border border-gray-300 bg-slate-100 p-3 text-sm manpower-duration">
                </div>
                <div>
                    <label class="block mb-2 text-sm font-medium">Man Hour</label>
                    <input type="text" readonly value="${formatMinutesAsDecimalHours(manpower.man_hour)}" class="w-full rounded-2xl border border-gray-300 bg-slate-100 p-3 text-sm manpower-man-hour">
                </div>
            </div>

            <div class="mt-4 flex justify-end">
                <button type="button" class="remove-manpower inline-flex items-center rounded-2xl bg-red-500 px-4 py-2 text-white hover:bg-red-600">
                    Remove
                </button>
            </div>
        </div>
    `;
}

function buildSessionRow(session = {}, index) {
    return `
        <div class="session-card rounded-3xl border border-slate-200 bg-white p-5" data-session-index="${index}">
            <div class="mb-4 flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
                <div>
                    <h4 class="text-lg font-semibold session-heading">Day ${index + 1}</h4>
                </div>
                <div class="flex items-center gap-2">
                    ${index > 0 ? '<button type="button" class="remove-session inline-flex items-center rounded-2xl bg-red-500 px-4 py-2 text-white hover:bg-red-600">Remove Day</button>' : ''}
                </div>
            </div>

            <input data-session-field="sessions[{index}][id]" type="hidden" value="${session.id || ''}">

            <div class="grid grid-cols-1 gap-4 md:grid-cols-4">
                <div>
                    <label class="block mb-2 text-sm font-medium">Actual Date</label>
                    <input data-session-field="sessions[{index}][actual_date]" type="date" value="${session.actual_date || ''}" class="w-full rounded-2xl border border-gray-300 p-3 text-sm session-actual-date">
                </div>
                <div>
                    <label class="block mb-2 text-sm font-medium">Start PM</label>
                    <input data-session-field="sessions[{index}][start_time]" type="time" value="${session.start_time || ''}" class="w-full rounded-2xl border border-gray-300 p-3 text-sm session-start-time">
                </div>
                <div>
                    <label class="block mb-2 text-sm font-medium">End PM</label>
                    <input data-session-field="sessions[{index}][end_time]" type="time" value="${session.end_time || ''}" class="w-full rounded-2xl border border-gray-300 p-3 text-sm session-end-time">
                </div>
                <div>
                    <label class="block mb-2 text-sm font-medium">Duration</label>
                    <input type="text" readonly value="${formatMinutesAsDuration(session.duration)}" class="w-full rounded-2xl border border-gray-300 bg-slate-100 p-3 text-sm session-duration">
                </div>
            </div>

            <div class="mt-6 rounded-2xl border border-slate-200 bg-slate-50 p-4">
                <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                    <div>
                        <p class="text-sm font-semibold">Manpower</p>
                        <p class="text-sm text-slate-500">Track each person assigned to this session.</p>
                    </div>
                    <button type="button" class="add-manpower inline-flex items-center rounded-2xl bg-blue-600 px-4 py-2 text-white hover:bg-blue-700" data-session-index="${index}">
                        + Add Manpower
                    </button>
                </div>

                <div class="manpower-wrapper mt-4"></div>

                <div class="mt-4 text-right text-sm font-medium">
                    Total Man Hour Day <span class="session-day-number">${index + 1}</span>: <span class="session-total-man-hour">0 MH</span>
                </div>
            </div>
        </div>
    `;
}

function setSessionInputNames(sessionCard, sessionIndex) {
    sessionCard.dataset.sessionIndex = sessionIndex;
    sessionCard.querySelector('.session-heading').textContent = `Day ${sessionIndex + 1}`;
    sessionCard.querySelector('.session-day-number').textContent = sessionIndex + 1;

    sessionCard.querySelectorAll('[data-session-field]').forEach((element) => {
        const template = element.getAttribute('data-session-field');
        element.name = template.replace(/\{index\}/g, sessionIndex);
    });

    sessionCard.querySelectorAll('.manpower-row').forEach((manpowerRow, manpowerIndex) => {
        manpowerRow.dataset.manpowerIndex = manpowerIndex;
        manpowerRow.querySelectorAll('[data-manpower-field]').forEach((element) => {
            const template = element.getAttribute('data-manpower-field');
            element.name = template
                .replace(/\{sessionIndex\}/g, sessionIndex)
                .replace(/\{manpowerIndex\}/g, manpowerIndex);
        });
    });

    sessionCard.querySelectorAll('.add-manpower').forEach((button) => {
        button.dataset.sessionIndex = sessionIndex;
    });
}

function refreshSessionIndexes() {
    const sessionCards = document.querySelectorAll('.session-card');
    sessionCards.forEach((sessionCard, sessionIndex) => {
        setSessionInputNames(sessionCard, sessionIndex);
    });
}

function updateSessionDuration(sessionCard) {
    const startInput = sessionCard.querySelector('.session-start-time');
    const endInput = sessionCard.querySelector('.session-end-time');
    const durationInput = sessionCard.querySelector('.session-duration');

    const duration = calculateDurationFromTimes(startInput.value, endInput.value);
    durationInput.value = formatMinutesAsDuration(duration);
    updateSessionManHourTotal(sessionCard);
    updateSummaryTotals();
}

function updateManpowerRow(manpowerRow) {
    const startInput = manpowerRow.querySelector('.manpower-start');
    const endInput = manpowerRow.querySelector('.manpower-end');
    const durationInput = manpowerRow.querySelector('.manpower-duration');
    const manHourInput = manpowerRow.querySelector('.manpower-man-hour');

    const duration = calculateDurationFromTimes(startInput.value, endInput.value);
    durationInput.value = formatMinutesAsDuration(duration);
    manHourInput.value = formatMinutesAsDecimalHours(duration);
}

function updateSessionManHourTotal(sessionCard) {
    const manpowerRows = sessionCard.querySelectorAll('.manpower-row');
    let total = 0;

    manpowerRows.forEach((row) => {
        const start = row.querySelector('.manpower-start')?.value;
        const end = row.querySelector('.manpower-end')?.value;
        const duration = calculateDurationFromTimes(start, end);

        if (duration !== null) {
            total += duration;
        }
    });

    const totalLabel = sessionCard.querySelector('.session-total-man-hour');
    totalLabel.textContent = total ? formatMinutesAsDecimalHours(total) : '0 MH';
}

function updateSummaryTotals() {
    const sessionCards = document.querySelectorAll('.session-card');
    let durationTotal = 0;
    let manHourTotal = 0;

    sessionCards.forEach((sessionCard) => {
        const start = sessionCard.querySelector('.session-start-time')?.value;
        const end = sessionCard.querySelector('.session-end-time')?.value;
        const duration = calculateDurationFromTimes(start, end);

        if (duration !== null) {
            durationTotal += duration;
        }

        const manpowerRows = sessionCard.querySelectorAll('.manpower-row');
        manpowerRows.forEach((row) => {
            const startMP = row.querySelector('.manpower-start')?.value;
            const endMP = row.querySelector('.manpower-end')?.value;
            const durationMP = calculateDurationFromTimes(startMP, endMP);

            if (durationMP !== null) {
                manHourTotal += durationMP;
            }
        });
    });

    document.getElementById('total-duration').textContent = durationTotal ? `${(durationTotal / 60).toFixed(2)} Hours` : '0 Hours 0 Minutes';
    document.getElementById('total-man-hour').textContent = manHourTotal ? formatMinutesAsDecimalHours(manHourTotal) : '0 MH';
}

function buildSession(sessionData, index) {
    const wrapper = document.createElement('div');
    wrapper.innerHTML = buildSessionRow(sessionData, index);
    const sessionCard = wrapper.firstElementChild;

    const manpowers = Array.isArray(sessionData.manpowers) ? sessionData.manpowers : [];
    const manpowerWrapper = sessionCard.querySelector('.manpower-wrapper');

    manpowers.forEach((manpower, manpowerIndex) => {
        const manpowerWrapperRow = document.createElement('div');
        manpowerWrapperRow.innerHTML = buildManpowerRow(index, manpowerIndex, manpower);
        manpowerWrapper.appendChild(manpowerWrapperRow.firstElementChild);
    });

    setSessionInputNames(sessionCard, index);
    updateSessionDuration(sessionCard);
    updateSessionManHourTotal(sessionCard);

    return sessionCard;
}

function initSessions() {
    const sessionsData = pmData ? JSON.parse(pmData.dataset.sessions || '[]') : [];
    const wrapper = document.getElementById('sessions-wrapper');
    wrapper.innerHTML = '';

    if (sessionsData.length === 0) {
        sessionsData.push({
            id: null,
            actual_date: '',
            start_time: '',
            end_time: '',
            duration: null,
            manpowers: [],
        });
    }

    sessionsData.forEach((session, index) => {
        wrapper.appendChild(buildSession(session, index));
    });

    refreshSessionIndexes();
    updateSummaryTotals();
}

window.addSession = function () {
    const wrapper = document.getElementById('sessions-wrapper');
    const index = wrapper.querySelectorAll('.session-card').length;
    wrapper.appendChild(buildSession({}, index));
    refreshSessionIndexes();
    updateSummaryTotals();
};

window.removeSession = function (button) {
    const sessionCard = button.closest('.session-card');
    if (!sessionCard) {
        return;
    }

    const wrapper = document.getElementById('sessions-wrapper');
    if (wrapper.querySelectorAll('.session-card').length <= 1) {
        return;
    }

    sessionCard.remove();
    refreshSessionIndexes();
    updateSummaryTotals();
};

window.addManpower = function (button) {
    const sessionIndex = Number(button.dataset.sessionIndex);
    const sessionCard = button.closest('.session-card');
    const manpowerWrapper = sessionCard.querySelector('.manpower-wrapper');
    const manpowerIndex = manpowerWrapper.querySelectorAll('.manpower-row').length;
    const row = document.createElement('div');
    row.innerHTML = buildManpowerRow(sessionIndex, manpowerIndex, {});
    manpowerWrapper.appendChild(row.firstElementChild);

    refreshSessionIndexes();
    updateSummaryTotals();
};

window.removeManpower = function (button) {
    const manpowerRow = button.closest('.manpower-row');
    if (!manpowerRow) {
        return;
    }

    manpowerRow.remove();
    const sessionCard = button.closest('.session-card');
    if (sessionCard) {
        refreshSessionIndexes();
        updateSessionManHourTotal(sessionCard);
        updateSummaryTotals();
    }
};

function initSessionsEventHandlers() {
    const wrapper = document.getElementById('sessions-wrapper');

    wrapper.addEventListener('click', function (event) {
        if (event.target.matches('.remove-session')) {
            window.removeSession(event.target);
            return;
        }

        if (event.target.matches('.add-manpower')) {
            window.addManpower(event.target);
            return;
        }

        if (event.target.matches('.remove-manpower')) {
            window.removeManpower(event.target);
            return;
        }
    });

    wrapper.addEventListener('change', function (event) {
        if (event.target.matches('.session-start-time') || event.target.matches('.session-end-time')) {
            const sessionCard = event.target.closest('.session-card');
            if (sessionCard) {
                updateSessionDuration(sessionCard);
            }
            return;
        }

        if (event.target.matches('.manpower-start') || event.target.matches('.manpower-end')) {
            const manpowerRow = event.target.closest('.manpower-row');
            const sessionCard = event.target.closest('.session-card');
            if (manpowerRow) {
                updateManpowerRow(manpowerRow);
            }
            if (sessionCard) {
                updateSessionManHourTotal(sessionCard);
                updateSummaryTotals();
            }
        }
    });
}

window.addProblem = function () {
    const wrapper = document.getElementById('problem-wrapper');

    if (!wrapper) {
        return;
    }

    wrapper.insertAdjacentHTML('beforeend', buildProblemRow(problemIndex));
    problemIndex++;
};

window.removeProblem = function (button) {
    const rows = document.querySelectorAll('.problem-row');

    if (rows.length > 1 && button?.parentElement) {
        button.parentElement.remove();
    }
};

function loadFinding(problemSelect) {
    const option = problemSelect.selectedOptions[0];

    if (!option || !option.dataset.category) {
        return;
    }

    const category = option.dataset.category.trim().toLowerCase();
    const row = problemSelect.closest('.problem-row');
    const findingSelect = row?.querySelector('.finding-select');

    if (!findingSelect) {
        return;
    }

    const oldFinding = findingSelect.dataset.oldFinding;

    findingSelect.innerHTML = '<option value="">-- Finding --</option>';

    if (findings[category]) {
        findings[category].forEach(function (item) {
            const selected = item.id == oldFinding ? 'selected' : '';

            findingSelect.innerHTML += `
                <option value="${item.id}" ${selected}>
                    ${item.finding}
                </option>
            `;
        });
    }
}

function initProblemFindings() {
    document.querySelectorAll('.problem-select').forEach(function (select) {
        if (select.value) {
            loadFinding(select);
        }
    });
}

function calculateDuration() {
    const startInput = document.getElementById('start_time');
    const endInput = document.getElementById('end_time');
    const durationInput = document.getElementById('duration');
    const errorText = document.getElementById('duration_error');

    if (!startInput || !endInput || !durationInput || !errorText) {
        return;
    }

    if (!startInput.value || !endInput.value) {
        return;
    }

    const start = startInput.value.split(':');
    const end = endInput.value.split(':');

    const startMinutes = parseInt(start[0], 10) * 60 + parseInt(start[1], 10);
    const endMinutes = parseInt(end[0], 10) * 60 + parseInt(end[1], 10);

    if (endMinutes < startMinutes) {
        durationInput.value = '';
        errorText.classList.remove('hidden');
        endInput.classList.add('border-red-500');
        return;
    }

    errorText.classList.add('hidden');
    endInput.classList.remove('border-red-500');

    const diff = endMinutes - startMinutes;
    const hours = Math.floor(diff / 60);
    const minutes = diff % 60;

    durationInput.value = `${hours} Hours ${minutes} Minutes`;
}

function initTomSelect(element) {
    if (!window.TomSelect) {
        return;
    }

    if (element.tomselect) {
        element.tomselect.destroy();
    }

    new window.TomSelect(element, {
        valueField: 'id',
        labelField: 'material_number',
        searchField: [
            'location',
            'material_number',
            'description',
            'remarks'
        ],
        options: spareparts,
        items: element.dataset.selected ? [element.dataset.selected] : [],
        create: false,
        maxOptions: 100,
        render: {
            option: function (item, escape) {
                return `
                    <div style="padding:8px">
                        <div style="font-size:12px;color:#6b7280">
                            📍 ${escape(item.location ?? '-')}
                        </div>
                        <div style="font-weight:700">
                            ${escape(item.material_number)}
                        </div>
                        <div>
                            ${escape(item.description)}
                        </div>
                        <div style="font-size:12px;color:#6b7280">
                            ${escape(item.remarks ?? '-')}
                        </div>
                    </div>
                `;
            },
            item: function (item, escape) {
                return `
                    <div>
                        ${escape(item.material_number)}
                        -
                        ${escape(item.description)}
                    </div>
                `;
            }
        }
    });
}

function initSpareparts() {
    document.querySelectorAll('.sparepart-select').forEach(function (el) {
        initTomSelect(el);
    });
}

window.addSparepart = function () {
    const wrapper = document.getElementById('sparepart-wrapper');

    if (!wrapper) {
        return;
    }

    wrapper.insertAdjacentHTML('beforeend', buildSparepartRow(sparepartIndex));

    const selects = wrapper.querySelectorAll('.sparepart-select');

    if (selects.length) {
        initTomSelect(selects[selects.length - 1]);
    }

    sparepartIndex++;
};

window.removeSparepart = function (button) {
    button?.closest('.sparepart-row')?.remove();
};

function loadTomSelectScript() {
    if (window.TomSelect) {
        return Promise.resolve();
    }

    return new Promise((resolve, reject) => {
        const script = document.createElement('script');
        script.src = 'https://cdn.jsdelivr.net/npm/tom-select/dist/js/tom-select.complete.min.js';
        script.onload = resolve;
        script.onerror = reject;
        document.head.appendChild(script);
    });
}

function initPage() {
    if (initialized) {
        return;
    }

    initialized = true;

    document.addEventListener('change', function (event) {
        if (event.target.classList.contains('problem-select')) {
            loadFinding(event.target);
        }
    });

    initProblemFindings();
    initSessions();
    initSessionsEventHandlers();

    const startInput = document.getElementById('start_time');
    const endInput = document.getElementById('end_time');

    startInput?.addEventListener('change', calculateDuration);
    endInput?.addEventListener('change', calculateDuration);

    initSpareparts();
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', function () {
        loadTomSelectScript().then(initPage).catch(() => {
            initPage();
        });
    });
} else {
    loadTomSelectScript().then(initPage).catch(() => {
        initPage();
    });
}
