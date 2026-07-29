const pmData = document.getElementById('pm-data');

const findings = pmData ? JSON.parse(pmData.dataset.findings || '{}') : {};
const spareparts = pmData ? JSON.parse(pmData.dataset.spareparts || '[]') : [];
const bigProblems = pmData ? JSON.parse(pmData.dataset.bigProblems || '[]') : [];

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
        <div class="problem-row flex gap-2 mb-2">
            <select name="problems[${index}][problem]" class="problem-select border p-2 w-1/2 rounded">
                <option value="">-- Select Problem --</option>
                ${options}
            </select>

            <select name="problems[${index}][finding]" class="finding-select border p-2 flex-1 rounded">
                <option value="">-- Finding --</option>
            </select>

            <select name="problems[${index}][severity]" class="border p-2 rounded w-1/5">
                <option value="">-- Severity --</option>
                <option value="Low">Low</option>
                <option value="Medium">Medium</option>
                <option value="High">High</option>
            </select>

            <button type="button" onclick="removeProblem(this)" class="bg-red-500 text-white px-3 rounded">
                X
            </button>
        </div>
    `;
}

function buildSparepartRow(index) {
    return `
        <div class="sparepart-row flex gap-2 mb-2">
            <select name="spareparts[${index}][sparepart_id]" placeholder="Select Sparepart" class="sparepart-select border p-2 w-2/3 rounded"></select>

            <input type="number" name="spareparts[${index}][qty]" placeholder="Qty" class="border p-3 w-1/3 rounded">

            <button type="button" onclick="removeSparepart(this)" class="bg-red-500 text-white px-3 rounded">
                X
            </button>
        </div>
    `;
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
