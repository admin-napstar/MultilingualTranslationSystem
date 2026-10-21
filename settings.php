<?php

session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$pageTitle = 'Settings';

require __DIR__ . '/views/layouts/header.php';

?>


    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">

    <style>
      
/* ===== SETTINGS PAGE: GLOSSY DESIGN ===== */

.settings-content {
    padding: 35px;
    max-width: 1050px;
    margin: 0 auto;
    color: #172554;
}

/* Page heading */
.settings-header {
    margin-bottom: 30px;
}

.settings-header h1 {
    font-size: 30px;
    font-weight: 750;
    letter-spacing: -0.8px;
    margin: 0 0 8px;
    color: #172554;
}

.settings-header p {
    color: #64748b;
    font-size: 14px;
    margin: 0;
}

/* Glossy settings cards */
.settings-card {
    position: relative;
    overflow: hidden;
    background: linear-gradient(
        135deg,
        rgba(255, 255, 255, 0.98),
        rgba(245, 248, 255, 0.94)
    );
    padding: 28px;
    margin-bottom: 24px;
    border: 1px solid rgba(255, 255, 255, 0.95);
    border-radius: 18px;
    box-shadow:
        0 8px 30px rgba(37, 99, 235, 0.07),
        0 2px 8px rgba(15, 23, 42, 0.04),
        inset 0 1px 0 rgba(255, 255, 255, 0.95);
    transition: transform 0.25s ease,
                box-shadow 0.25s ease;
}

/* Subtle glossy highlight */
.settings-card::before {
    content: "";
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 4px;
    background: linear-gradient(
        90deg,
        #2563eb,
        #818cf8,
        #c084fc
    );
}

.settings-card:hover {
    transform: translateY(-3px);
    box-shadow:
        0 16px 38px rgba(37, 99, 235, 0.11),
        0 5px 12px rgba(15, 23, 42, 0.04);
}

/* Section headings */
.settings-card h2 {
    display: flex;
    align-items: center;
    gap: 12px;
    font-size: 17px;
    font-weight: 700;
    color: #1e3a8a;
    margin: 0 0 25px;
}

.settings-card h2 i {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 42px;
    height: 42px;
    border-radius: 13px;
    color: #2563eb;
    background: linear-gradient(
        135deg,
        #eff6ff,
        #e0e7ff
    );
    box-shadow:
        inset 0 1px 2px rgba(255, 255, 255, 0.9),
        0 3px 8px rgba(37, 99, 235, 0.08);
}

/* Form labels */
.settings-form-group {
    margin-bottom: 21px;
}

.settings-form-group label {
    display: block;
    margin-bottom: 9px;
    color: #334155;
    font-size: 13px;
    font-weight: 650;
}

/* Glossy input fields */
.settings-form-group input,
.settings-form-group select {
    display: block;
    width: 100%;
    padding: 13px 15px;
    border: 1px solid #dbe4f0;
    border-radius: 11px;
    box-sizing: border-box;
    background: linear-gradient(
        180deg,
        #ffffff,
        #f8faff
    );
    color: #1e293b;
    font-family: inherit;
    font-size: 14px;
    outline: none;
    box-shadow:
        inset 0 1px 3px rgba(15, 23, 42, 0.025),
        0 2px 4px rgba(15, 23, 42, 0.02);
    transition: border-color 0.2s ease,
                box-shadow 0.2s ease,
                background 0.2s ease;
}

.settings-form-group input::placeholder {
    color: #94a3b8;
}

.settings-form-group input:hover,
.settings-form-group select:hover {
    border-color: #a5b4fc;
}

.settings-form-group input:focus,
.settings-form-group select:focus {
    border-color: #6366f1;
    background: #ffffff;
    box-shadow:
        0 0 0 4px rgba(99, 102, 241, 0.11),
        0 4px 12px rgba(37, 99, 235, 0.06);
}

/* Gradient save buttons */
.settings-save {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 9px;
    padding: 12px 21px;
    border: 1px solid rgba(255, 255, 255, 0.25);
    border-radius: 10px;
    background: linear-gradient(
        135deg,
        #2563eb,
        #4f46e5
    );
    color: #ffffff;
    font-family: inherit;
    font-size: 13px;
    font-weight: 650;
    cursor: pointer;
    box-shadow:
        0 5px 12px rgba(79, 70, 229, 0.22),
        inset 0 1px 0 rgba(255, 255, 255, 0.25);
    transition: transform 0.2s ease,
                box-shadow 0.2s ease,
                filter 0.2s ease;
}

.settings-save:hover {
    transform: translateY(-2px);
    filter: brightness(1.06);
    box-shadow:
        0 8px 18px rgba(79, 70, 229, 0.3),
        inset 0 1px 0 rgba(255, 255, 255, 0.25);
}

.settings-save:active {
    transform: translateY(0);
}

/* Helper text */
.settings-note {
    color: #64748b;
    font-size: 12px;
    line-height: 1.7;
    margin: 0 0 18px;
}

/* Responsive layout */
@media (max-width: 768px) {
    .settings-content {
        padding: 20px 15px;
    }

    .settings-header h1 {
        font-size: 25px;
    }

    .settings-card {
        padding: 21px 18px;
        border-radius: 15px;
    }

    .settings-card h2 {
        font-size: 16px;
    }

    .settings-save {
        width: 100%;
    }
}

    </style>
</head>

<body>



<main class="main-content">
    <div class="settings-content">

        <div class="settings-header">
         
        </div>

        <!-- Account Settings -->
        <section class="settings-card">
            <h2>
                <i class="fas fa-user-circle"></i>
                Account Settings
            </h2>

            <form action="update_settings.php" method="POST">
                <div class="settings-form-group">
                    <label for="fullname">Full Name</label>
                    <input type="text"
                           id="fullname"
                           name="fullname"
                           placeholder="Enter your full name">
                </div>

                <div class="settings-form-group">
                    <label for="email">Email Address</label>
                    <input type="email"
                           id="email"
                           name="email"
                           placeholder="Enter your email address">
                </div>

                <button type="submit" class="settings-save">
                    Save Account Settings
                </button>
            </form>
        </section>

   
        <section class="settings-card">
            <h2>
              
                Translation Preferences
            </h2>

            <form action="update_settings.php" method="POST">
                <div class="settings-form-group">
                    <label for="source_language">
                        Default Source Language
                    </label>

                    <select id="source_language" name="source_language">
                        <option value="">Select source language</option>
                        <option value="English">English</option>
                        <option value="Sesotho">Sesotho</option>
                        <option value="isiZulu">isiZulu</option>
                        <option value="isiXhosa">isiXhosa</option>
                        <option value="Setswana">Setswana</option>
                        <option value="Sepedi">Sepedi</option>
                        <option value="itsonga">itsonga</option>
                        <option value="Tshivenda">Tshivenda</option>
                        <option value="Siswati">Siswati</option>
                        <option value="isiNdebele">isiNdebele</option>
                    </select>
                </div>

                <div class="settings-form-group">
                    <label for="target_language">
                        Default Target Language
                    </label>

                    <select id="target_language" name="target_language">
                        <option value="">Select target language</option>
                        <option value="English">English</option>
                        <option value="Sesotho">Sesotho</option>
                        <option value="isiZulu">isiZulu</option>
                        <option value="isiXhosa">isiXhosa</option>
                        <option value="Setswana">Setswana</option>
                        <option value="Sepedi">Sepedi</option>
                        <option value="itsonga">itsonga</option>
                        <option value="Tshivenda">Tshivenda</option>
                        <option value="Siswati">Siswati</option>
                        <option value="isiNdebele">isiNdebele</option>
                    </select>
                </div>

                <p class="settings-note">
                    Your default languages can be used to preselect
                    languages on the translation page.
                </p>

                <button type="submit" class="settings-save">
                    Save Translation Preferences
                </button>
            </form>
        </section>

       
        <section class="settings-card">
            <h2>
                <i class="fas fa-file-alt"></i>
                Document Preferences
            </h2>

            <form action="update_settings.php" method="POST">
                <div class="settings-form-group">
                    <label for="output_format">
                        Preferred Output Format
                    </label>

                    <select id="output_format" name="output_format">
                        <option value="docx">Word Document (.docx)</option>
                        <option value="pdf">PDF (.pdf)</option>
                        <option value="txt">Text File (.txt)</option>
                    </select>
                </div>

                <p class="settings-note">
                    Available formats depend on the document translation
                    functionality supported by the system.
                </p>

                <button type="submit" class="settings-save">
                    Save Document Preferences
                </button>
            </form>
        </section>

    </div>
</main>

</body>
</html>
