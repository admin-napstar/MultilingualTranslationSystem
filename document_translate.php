
<?php

session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
require __DIR__ . '/views/layouts/header.php';


$pageTitle = 'Document Translation';
$status = $_SESSION['document_status'] ?? '';
unset($_SESSION['document_status']);

$translatedFile = $_SESSION['translated_document'] ?? '';

$allowedLanguages = [
    'English',
    'Sesotho',
    'isiZulu',
    'isiXhosa',
    'Setswana',
    'Sepedi',
    'itsonga',
    'Tshivenda',
    'Siswati',
    'isiNdebele'
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Document Translation - Lingua-SA</title>

    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">

    <style>
   .document-content {
    max-width: 1100px;
    margin: 0 ;
    padding: 35px;
    color: #172554;
    transform: translateX(-30px);
}

        .document-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 20px;
            margin-bottom: 28px;
        }

        .document-header h1 {
            margin: 0 0 8px;
            font-size: 30px;
            letter-spacing: -0.7px;
        }

        .document-header p {
            margin: 0;
            color: #64748b;
            font-size: 14px;
            line-height: 1.7;
        }

        .document-header-icon {
            width: 58px;
            height: 58px;
            flex-shrink: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #4f46e5;
            font-size: 25px;
            border-radius: 17px;
            background: linear-gradient(135deg, #dbeafe, #ede9fe);
            box-shadow: 0 6px 18px rgba(79, 70, 229, 0.12);
        }

        .document-card {
            position: relative;
            overflow: hidden;
            padding: 27px;
            margin-bottom: 23px;
            border: 1px solid rgba(255, 255, 255, 0.95);
            border-radius: 18px;
            background: linear-gradient(135deg, #ffffff, #f7f8ff);
            box-shadow:
                0 8px 28px rgba(37, 99, 235, 0.07),
                0 2px 7px rgba(15, 23, 42, 0.035),
                inset 0 1px 0 #ffffff;
        }

        .document-card::before {
            content: "";
            position: absolute;
            inset: 0 0 auto;
            height: 4px;
            background: linear-gradient(90deg, #2563eb, #818cf8, #c084fc);
        }

        .document-card h2 {
            display: flex;
            align-items: center;
            gap: 11px;
            margin: 0 0 22px;
            font-size: 17px;
            color: #1e3a8a;
        }

        .document-card h2 i {
            color: #6366f1;
        }

        /* Upload area */
        .upload-area {
            position: relative;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 12px;
            min-height: 190px;
            padding: 24px;
            text-align: center;
            border: 2px dashed #c7d2fe;
            border-radius: 15px;
            background: linear-gradient(135deg, #f8faff, #f5f3ff);
            transition: border-color 0.2s, background 0.2s;
            cursor: pointer;
        }

        .upload-area:hover,
        .upload-area.drag-over {
            border-color: #6366f1;
            background: #eef2ff;
        }

        .upload-icon {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 58px;
            height: 58px;
            border-radius: 17px;
            color: #4f46e5;
            font-size: 25px;
            background: linear-gradient(135deg, #dbeafe, #e9d5ff);
            box-shadow: 0 5px 15px rgba(79, 70, 229, 0.12);
        }

        .upload-area h3 {
            margin: 0;
            font-size: 16px;
            color: #1e293b;
        }

        .upload-area p {
            margin: 0;
            color: #64748b;
            font-size: 13px;
        }

        .browse-button {
            display: inline-block;
            padding: 10px 17px;
            color: #ffffff;
            font-size: 13px;
            font-weight: 600;
            border-radius: 9px;
            background: linear-gradient(135deg, #2563eb, #4f46e5);
            box-shadow: 0 5px 13px rgba(79, 70, 229, 0.2);
            cursor: pointer;
        }

        .upload-area input[type="file"] {
            position: absolute;
            width: 1px;
            height: 1px;
            opacity: 0;
            overflow: hidden;
        }

        .upload-area:focus-within {
            outline: 3px solid rgba(99, 102, 241, 0.25);
            outline-offset: 3px;
        }

        .file-info {
            display: none;
            align-items: center;
            gap: 13px;
            padding: 15px;
            margin-top: 17px;
            border: 1px solid #dbeafe;
            border-radius: 12px;
            background: #f8faff;
        }

        .file-info.visible {
            display: flex;
        }

        .file-info-icon {
            color: #2563eb;
            font-size: 26px;
        }

        .file-details {
            flex: 1;
            min-width: 0;
        }

        .file-details strong {
            display: block;
            overflow-wrap: anywhere;
            font-size: 13px;
        }

        .file-details span {
            display: block;
            margin-top: 5px;
            color: #64748b;
            font-size: 12px;
        }

        .remove-file {
            border: none;
            background: transparent;
            color: #dc2626;
            font-size: 17px;
            cursor: pointer;
            padding: 8px;
        }

        /* Language selection */
        .language-grid {
            display: grid;
            grid-template-columns: 1fr 52px 1fr;
            align-items: end;
            gap: 16px;
        }

        .language-field label {
            display: block;
            margin-bottom: 9px;
            color: #334155;
            font-size: 13px;
            font-weight: 650;
        }

        .language-field select {
            width: 100%;
            padding: 13px;
            border: 1px solid #dbe4f0;
            border-radius: 10px;
            outline: none;
            background: linear-gradient(180deg, #ffffff, #f8faff);
            color: #1e293b;
            font-size: 14px;
        }

        .language-field select:focus {
            border-color: #6366f1;
            box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.1);
        }

        .swap-languages {
            width: 44px;
            height: 44px;
            margin-bottom: 1px;
            border: 1px solid #dbeafe;
            border-radius: 12px;
            background: #eff6ff;
            color: #4f46e5;
            cursor: pointer;
            transition: transform 0.2s;
        }

        .swap-languages:hover {
            transform: rotate(180deg);
        }

        .document-note {
            margin: 17px 0 0;
            color: #64748b;
            font-size: 12px;
            line-height: 1.7;
        }

        /* Main button */
        .translate-button,
        .download-button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            width: 100%;
            min-height: 48px;
            padding: 13px 20px;
            border: none;
            border-radius: 11px;
            background: linear-gradient(135deg, #2563eb, #4f46e5);
            color: #ffffff;
            font-size: 14px;
            font-weight: 650;
            cursor: pointer;
            box-shadow:
                0 6px 15px rgba(79, 70, 229, 0.2),
                inset 0 1px 0 rgba(255, 255, 255, 0.25);
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .translate-button:hover,
        .download-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 9px 20px rgba(79, 70, 229, 0.27);
        }

        .translate-button:disabled {
            opacity: 0.55;
            cursor: not-allowed;
            transform: none;
        }

        /* Status and result */
        .document-status {
            padding: 14px 16px;
            margin-bottom: 22px;
            border-radius: 10px;
            font-size: 13px;
            line-height: 1.6;
        }

        .document-status.success {
            color: #166534;
            background: #f0fdf4;
            border: 1px solid #bbf7d0;
        }

        .document-status.error {
            color: #991b1b;
            background: #fef2f2;
            border: 1px solid #fecaca;
        }

        .document-status.info {
            color: #1e40af;
            background: #eff6ff;
            border: 1px solid #bfdbfe;
        }

        .result-file {
            display: flex;
            align-items: center;
            gap: 14px;
            padding: 18px;
            border: 1px solid #dbeafe;
            border-radius: 13px;
            background: linear-gradient(135deg, #ffffff, #f5f8ff);
        }

        .result-file > i {
            color: #2563eb;
            font-size: 28px;
        }

        .result-details {
            flex: 1;
            min-width: 0;
        }

        .result-details strong {
            display: block;
            overflow-wrap: anywhere;
            font-size: 14px;
        }

        .result-details span {
            display: block;
            margin-top: 5px;
            color: #64748b;
            font-size: 12px;
        }

        .download-button {
            width: auto;
            min-height: auto;
            text-decoration: none;
            font-size: 12px;
            padding: 10px 14px;
        }

        .format-note {
            margin-top: 13px;
            color: #64748b;
            font-size: 12px;
            line-height: 1.7;
        }

        @media (max-width: 768px) {
            .document-content {
                padding: 20px 15px;
            }

            .document-header h1 {
                font-size: 25px;
            }

            .document-card {
                padding: 21px 17px;
            }

            .language-grid {
                grid-template-columns: 1fr;
                gap: 15px;
            }

            .swap-languages {
                justify-self: center;
                transform: rotate(90deg);
            }

            .swap-languages:hover {
                transform: rotate(270deg);
            }

            .result-file {
                flex-wrap: wrap;
            }

            .download-button {
                width: 100%;
            }
        }
    </style>
</head>

<body>



<main class="main-content">
    <div class="document-content">

        <header class="document-header">
            <div>
                <h1>Document Translation</h1>
                <p>
                    Upload a document, choose your languages,
                    and download the translated result.
                </p>
            </div>

        
        </header>

        <?php if ($status !== ''): ?>
            <div class="document-status
                <?= $status === 'success' ? 'success' : 'error' ?>"
                role="status">
                <?= htmlspecialchars($status === 'success'
                    ? 'Your document was translated successfully.'
                    : 'The document could not be translated. Please try again.') ?>
            </div>
        <?php endif; ?>

        <!-- Document upload -->
        <section class="document-card">
            <h2>
                <i class="fas fa-cloud-upload-alt"></i>
                Upload Document
            </h2>

            <form id="documentForm"
                  action="process_document.php"
                  method="POST"
                  enctype="multipart/form-data">

                <label class="upload-area" id="uploadArea"
                       for="documentFile">

                    <span class="upload-icon">
                        <i class="fas fa-file-arrow-up"></i>
                    </span>

                    <h3>Choose a document to translate</h3>

                    <p>
                        Drag and drop your file here or browse your computer.
                    </p>

                    <span class="browse-button">Browse Files</span>

                    <p>Supported formats depend on your system.</p>

                    <input type="file"
                           id="documentFile"
                           name="document"
                           accept=".docx,.txt,.pdf"
                           required>
                </label>

                <div class="file-info" id="fileInfo">
                    <i class="fas fa-file-word file-info-icon"></i>

                    <div class="file-details">
                        <strong id="fileName"></strong>
                        <span id="fileSize"></span>
                    </div>

                    <button type="button"
                            class="remove-file"
                            id="removeFile"
                            aria-label="Remove selected file">
                        <i class="fas fa-trash-alt"></i>
                    </button>
                </div>

                <!-- Translation language settings -->
                <div class="document-card" style="margin-top: 24px;">
                    <h2>
                  
                        Translation Languages
                    </h2>

                    <div class="language-grid">
                        <div class="language-field">
                            <label for="sourceLanguage">
                                Source Language
                            </label>

                            <select id="sourceLanguage"
                                    name="source_language" required>
                                <?php foreach ($allowedLanguages as $language): ?>
                                    <option value="<?= htmlspecialchars($language) ?>"
                                        <?= $language === 'English' ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($language) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <button type="button"
                                class="swap-languages"
                                id="swapLanguages"
                                aria-label="Swap languages">
                            <i class="fas fa-right-left"></i>
                        </button>

                        <div class="language-field">
                            <label for="targetLanguage">
                                Target Language
                            </label>

                            <select id="targetLanguage"
                                    name="target_language" required>
                                <?php foreach ($allowedLanguages as $language): ?>
                                    <option value="<?= htmlspecialchars($language) ?>"
                                        <?= $language === 'Sesotho' ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($language) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <p class="document-note">
                        Select the language of the original document and
                        the language you want to translate it into.
                    </p>
                </div>

                <button type="submit"
                        class="translate-button"
                        id="translateButton">
                  
                    Translate Document
                </button>
            </form>
        </section>

       
        <?php if ($status === 'success' && $translatedFile !== ''): ?>
            <section class="document-card">
                <h2>
                    <i class="fas fa-check-circle"></i>
                    Translation Result
                </h2>

                <div class="result-file">
                    <i class="fas fa-file-circle-check"></i>

                    <div class="result-details">
                        <strong>Translated Document</strong>
                        <span>Your translated file is ready to download.</span>
                    </div>

                    <a class="download-button"
                       href="download_document.php">
                        <i class="fas fa-download"></i>
                        Download
                    </a>
                </div>

                <p class="format-note">
                    Formatting preservation depends on the source file
                    and the document processing implementation.
                </p>
            </section>
        <?php endif; ?>

    </div>
</main>

<script>
const fileInput = document.getElementById('documentFile');
const uploadArea = document.getElementById('uploadArea');
const fileInfo = document.getElementById('fileInfo');
const fileName = document.getElementById('fileName');
const fileSize = document.getElementById('fileSize');
const removeFile = document.getElementById('removeFile');
const form = document.getElementById('documentForm');
const translateButton = document.getElementById('translateButton');

const maxFileSize = 10 * 1024 * 1024; // 10 MB
const allowedExtensions = ['docx', 'txt', 'pdf'];

function showFile(file) {
    if (!file) return;

    const extension = file.name.split('.').pop().toLowerCase();

    if (!allowedExtensions.includes(extension)) {
        alert('Please select a supported DOCX, TXT or PDF file.');
        fileInput.value = '';
        fileInfo.classList.remove('visible');
        return;
    }

    if (file.size > maxFileSize) {
        alert('The maximum file size is 10 MB.');
        fileInput.value = '';
        fileInfo.classList.remove('visible');
        return;
    }

    fileName.textContent = file.name;
    fileSize.textContent =
        (file.size / (1024 * 1024)).toFixed(2) + ' MB';

    fileInfo.classList.add('visible');
}

fileInput.addEventListener('change', function () {
    showFile(this.files[0]);
});

removeFile.addEventListener('click', function () {
    fileInput.value = '';
    fileInfo.classList.remove('visible');
});

['dragenter', 'dragover'].forEach(eventName => {
    uploadArea.addEventListener(eventName, function (event) {
        event.preventDefault();
        uploadArea.classList.add('drag-over');
    });
});

['dragleave', 'drop'].forEach(eventName => {
    uploadArea.addEventListener(eventName, function (event) {
        event.preventDefault();
        uploadArea.classList.remove('drag-over');
    });
});

uploadArea.addEventListener('drop', function (event) {
    const files = event.dataTransfer.files;

    if (!files.length) return;

    try {
        const transfer = new DataTransfer();
        transfer.items.add(files[0]);
        fileInput.files = transfer.files;
        showFile(files[0]);
    } catch (error) {
        alert('Please use Browse Files to select your document.');
    }
});

document.getElementById('swapLanguages')
    .addEventListener('click', function () {
        const source = document.getElementById('sourceLanguage');
        const target = document.getElementById('targetLanguage');

        const sourceValue = source.value;
        const targetValue = target.value;

        source.value = targetValue;
        target.value = sourceValue;
    });

form.addEventListener('submit', function (event) {
    if (!fileInput.files.length) {
        event.preventDefault();
        alert('Please select a document first.');
        return;
    }

    if (
        document.getElementById('sourceLanguage').value ===
        document.getElementById('targetLanguage').value
    ) {
        event.preventDefault();
        alert('Please choose different source and target languages.');
        return;
    }

    translateButton.disabled = true;
    translateButton.innerHTML =
        '<i class="fas fa-spinner fa-spin"></i> Processing Document...';
});
</script>

</body>
</html>
