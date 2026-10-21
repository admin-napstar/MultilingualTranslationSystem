
<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$pageTitle = 'Translation History';

require_once __DIR__ . '/views/layouts/header.php';

// Temporary sample records for the UI.
// Replace these with database records when the history table is ready.
$history = [
    [
        'type' => 'Text',
        'source' => 'English',
        'target' => 'isiZulu',
        'date' => '2026-10-21 14:30',
        'status' => 'Completed'
    ],
    [
        'type' => 'Document',
        'source' => 'isiXhosa',
        'target' => 'English',
        'date' => '2026-10-20 11:15',
        'status' => 'Completed'
    ],
    [
        'type' => 'Text',
        'source' => 'English',
        'target' => 'Sesotho',
        'date' => '2026-10-19 09:45',
        'status' => 'Completed'
    ],
];

$totalTranslations = count($history);
?>

<style>
.history-page {
    padding: 24px;
    color: var(--text-color, #1f2937);
}

.history-heading {
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 16px;
    margin-bottom: 24px;
}

.history-heading h1 {
    margin: 0 0 8px;
    font-size: 28px;
}

.history-heading p {
    margin: 0;
    color: #64748b;
}

.history-stats {
    display: grid;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    gap: 18px;
    margin-bottom: 24px;
}

.history-stat,
.history-panel {
    background: var(--card-bg, #ffffff);
    border: 1px solid var(--border-color, #e5e7eb);
    border-radius: 14px;
    padding: 20px;
}

.history-stat .stat-label {
    display: block;
    color: #64748b;
    font-size: 14px;
    margin-bottom: 10px;
}

.history-stat .stat-value {
    font-size: 27px;
    font-weight: 700;
}

.history-stat .stat-icon {
    display: inline-flex;
    margin-bottom: 12px;
    color: #4263eb;
    font-size: 23px;
}

.history-panel {
    overflow: hidden;
}

.history-toolbar {
    display: flex;
    flex-wrap: wrap;
    gap: 12px;
    margin: 18px 0;
}

.history-toolbar input,
.history-toolbar select {
    min-height: 42px;
    padding: 10px 12px;
    border: 1px solid #d1d5db;
    border-radius: 8px;
    background: var(--input-bg, #fff);
    color: inherit;
    font: inherit;
}

.history-toolbar input {
    flex: 1;
    min-width: 200px;
}

.history-toolbar select {
    min-width: 150px;
}

.history-table-wrap {
    width: 100%;
    overflow-x: auto;
}

.history-table {
    width: 100%;
    border-collapse: collapse;
    min-width: 700px;
}

.history-table th,
.history-table td {
    padding: 16px 14px;
    text-align: left;
    border-bottom: 1px solid var(--border-color, #e5e7eb);
}

.history-table th {
    color: #64748b;
    font-size: 12px;
    text-transform: uppercase;
    letter-spacing: .04em;
    background: var(--table-header-bg, #f8fafc);
}

.history-table tbody tr:hover {
    background: var(--table-hover-bg, #f8fafc);
}

.history-type {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    font-weight: 600;
}

.history-status {
    display: inline-block;
    padding: 5px 10px;
    border-radius: 20px;
    background: #dcfce7;
    color: #166534;
    font-size: 12px;
    font-weight: 600;
}

.history-empty {
    display: none;
    padding: 35px 15px;
    text-align: center;
    color: #64748b;
}

.history-empty i {
    display: block;
    font-size: 30px;
    margin-bottom: 10px;
}

.history-footer {
    padding-top: 16px;
    color: #64748b;
    font-size: 13px;
}

@media (max-width: 768px) {
    .history-page {
        padding: 15px;
    }

    .history-stats {
        grid-template-columns: 1fr;
        gap: 12px;
    }

    .history-heading h1 {
        font-size: 23px;
    }
}
</style>

<main class="history-page">

    <div class="history-heading">
        <div>
            <h1>Translation History</h1>
            <p>View your previous text and document translations.</p>
        </div>
    </div>

    <section class="history-stats">

        <div class="history-stat">
            <span class="stat-icon">
                <i class="fas fa-language"></i>
            </span>
            <span class="stat-label">Total Translations</span>
            <div class="stat-value" id="totalCount">
                <?= $totalTranslations ?>
            </div>
        </div>

        <div class="history-stat">
            <span class="stat-icon">
                <i class="fas fa-align-left"></i>
            </span>
            <span class="stat-label">Text Translations</span>
            <div class="stat-value" id="textCount">
                <?= count(array_filter($history, fn($item) => $item['type'] === 'Text')) ?>
            </div>
        </div>

        <div class="history-stat">
            <span class="stat-icon">
                <i class="fas fa-file-alt"></i>
            </span>
            <span class="stat-label">Document Translations</span>
            <div class="stat-value" id="documentCount">
                <?= count(array_filter($history, fn($item) => $item['type'] === 'Document')) ?>
            </div>
        </div>

    </section>

    <section class="history-panel">

        <h2>Recent Translations</h2>
        <p>Search and filter your previous translation activity.</p>

        <div class="history-toolbar">
            <input
                type="search"
                id="historySearch"
                placeholder="Search languages or translation type..."
                aria-label="Search translation history"
            >

            <select id="typeFilter" aria-label="Filter by translation type">
                <option value="">All Types</option>
                <option value="Text">Text</option>
                <option value="Document">Document</option>
            </select>

            <select id="languageFilter" aria-label="Filter by language">
                <option value="">All Languages</option>
                <option value="English">English</option>
                <option value="isiZulu">isiZulu</option>
                <option value="isiXhosa">isiXhosa</option>
                <option value="Sesotho">Sesotho</option>
            </select>
        </div>

        <div class="history-table-wrap">
            <table class="history-table">
                <thead>
                    <tr>
                        <th>Translation</th>
                        <th>Languages</th>
                        <th>Date</th>
                        <th>Status</th>
                    </tr>
                </thead>

                <tbody id="historyTableBody">
                    <?php foreach ($history as $item): ?>
                        <tr
                            data-type="<?= htmlspecialchars($item['type'], ENT_QUOTES, 'UTF-8') ?>"
                            data-source="<?= htmlspecialchars($item['source'], ENT_QUOTES, 'UTF-8') ?>"
                            data-target="<?= htmlspecialchars($item['target'], ENT_QUOTES, 'UTF-8') ?>"
                        >
                            <td>
                                <span class="history-type">
                                    <i class="fas <?= $item['type'] === 'Document'
                                        ? 'fa-file-alt'
                                        : 'fa-language' ?>"></i>

                                    <?= htmlspecialchars($item['type'], ENT_QUOTES, 'UTF-8') ?>
                                    Translation
                                </span>
                            </td>

                            <td>
                                <?= htmlspecialchars($item['source'], ENT_QUOTES, 'UTF-8') ?>
                                &rarr;
                                <?= htmlspecialchars($item['target'], ENT_QUOTES, 'UTF-8') ?>
                            </td>

                            <td>
                                <?= htmlspecialchars($item['date'], ENT_QUOTES, 'UTF-8') ?>
                            </td>

                            <td>
                                <span class="history-status">
                                    <?= htmlspecialchars($item['status'], ENT_QUOTES, 'UTF-8') ?>
                                </span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <div class="history-empty" id="historyEmpty">
                <i class="fas fa-history"></i>
                <strong>No translations found</strong>
                <p>Try changing your search or filters.</p>
            </div>
        </div>

        <div class="history-footer" id="historyResults"></div>

    </section>

</main>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const searchInput = document.getElementById('historySearch');
    const typeFilter = document.getElementById('typeFilter');
    const languageFilter = document.getElementById('languageFilter');
    const rows = Array.from(
        document.querySelectorAll('#historyTableBody tr')
    );
    const emptyMessage = document.getElementById('historyEmpty');
    const results = document.getElementById('historyResults');

    function filterHistory() {
        const search = searchInput.value.toLowerCase().trim();
        const selectedType = typeFilter.value.toLowerCase();
        const selectedLanguage = languageFilter.value.toLowerCase();

        let visibleCount = 0;

        rows.forEach(function (row) {
            const rowText = row.textContent.toLowerCase();
            const type = row.dataset.type.toLowerCase();
            const source = row.dataset.source.toLowerCase();
            const target = row.dataset.target.toLowerCase();

            const matchesSearch = rowText.includes(search);
            const matchesType = !selectedType || type === selectedType;
            const matchesLanguage =
                !selectedLanguage ||
                source === selectedLanguage ||
                target === selectedLanguage;

            const visible =
                matchesSearch && matchesType && matchesLanguage;

            row.style.display = visible ? '' : 'none';

            if (visible) {
                visibleCount++;
            }
        });

        emptyMessage.style.display =
            visibleCount === 0 ? 'block' : 'none';

        results.textContent =
            'Showing ' + visibleCount + ' of ' + rows.length +
            ' translations';
    }

    searchInput.addEventListener('input', filterHistory);
    typeFilter.addEventListener('change', filterHistory);
    languageFilter.addEventListener('change', filterHistory);

    filterHistory();
});
</script>

<?php require_once __DIR__ . '/views/layouts/footer.php'; ?>
