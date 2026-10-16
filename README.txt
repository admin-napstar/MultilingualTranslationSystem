Multilingual Translation System

It runs using php for the user interface and python as an API, this system translate english to South African languagesmaking use of text and audio to translate.

Requirements 
	-Visual Studio Code
	-XAMPP
	-Python 3.0.1 +
	- Python extensions are in the requirements.txt
	
Make sure this checks are done before running

✅ PHP project created and working
✅ Composer/PSR-4 configured
✅ MySQL database connected
✅ Registration/login/dashboard working
✅ Translation UI completed
✅ Translation records saved to MySQL
✅ Flask API created
✅ PyTorch installed
✅ Transformers installed
✅ NLLB-200 downloaded and tested
✅ Real English → isiZulu translation working
✅ PHP connected to the Python API
✅ End-to-end translation working
✅ PHP project pushed to GitHub
✅ Python Translation API pushed to GitHub
✅ Both repositories clean
	
Following is the Database structure to create before running the application =>
DATABASE
Name = multilingual_translation

CREATE TABLE users (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'user') NOT NULL DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE languages (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    code VARCHAR(20) NOT NULL UNIQUE,
    is_active BOOLEAN NOT NULL DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE translations (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED NOT NULL,
    source_language_id INT UNSIGNED NOT NULL,
    target_language_id INT UNSIGNED NOT NULL,
    source_text LONGTEXT NOT NULL,
    translated_text LONGTEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_translation_user
        FOREIGN KEY (user_id) REFERENCES users(id),

    CONSTRAINT fk_translation_source_language
        FOREIGN KEY (source_language_id) REFERENCES languages(id),

    CONSTRAINT fk_translation_target_language
        FOREIGN KEY (target_language_id) REFERENCES languages(id)
);

CREATE TABLE documents (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED NOT NULL,
    source_language_id INT UNSIGNED NOT NULL,
    target_language_id INT UNSIGNED NOT NULL,
    original_filename VARCHAR(255) NOT NULL,
    translated_filename VARCHAR(255) NULL,
    file_path VARCHAR(500) NOT NULL,
    translated_file_path VARCHAR(500) NULL,
    status ENUM('uploaded', 'processing', 'completed', 'failed')
        NOT NULL DEFAULT 'uploaded',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_document_user
        FOREIGN KEY (user_id) REFERENCES users(id),

    CONSTRAINT fk_document_source_language
        FOREIGN KEY (source_language_id) REFERENCES languages(id),

    CONSTRAINT fk_document_target_language
        FOREIGN KEY (target_language_id) REFERENCES languages(id)
);

CREATE TABLE datasets (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    description TEXT NULL,
    file_path VARCHAR(500) NOT NULL,
    language_pair VARCHAR(100) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE activity_logs (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED NULL,
    action VARCHAR(100) NOT NULL,
    description TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_activity_user
        FOREIGN KEY (user_id) REFERENCES users(id)
        ON DELETE SET NULL
);

Languages

INSERT INTO languages (name, code) VALUES
('English', 'en'),
('Sesotho', 'st'),
('isiZulu', 'zu'),
('isiXhosa', 'xh'),
('Afrikaans', 'af'),
('Sepedi', 'nso'),
('Siswati', 'ss'),
('Tshivenda', 've'),
('itsonga', 'ts'),
('isiNdebele', 'nr');

UPDATE languages
SET code = CASE id
    WHEN 1 THEN 'eng_Latn'
    WHEN 2 THEN 'sot_Latn'
    WHEN 3 THEN 'zul_Latn'
    WHEN 4 THEN 'xho_Latn'
    WHEN 5 THEN 'afr_Latn'
    WHEN 6 THEN 'nso_Latn'
    WHEN 7 THEN 'ssw_Latn'
    WHEN 8 THEN 'ven_Latn'
    WHEN 9 THEN 'tso_Latn'
    WHEN 10 THEN 'nbl_Latn'
END;

.\venv\Scripts\python.exe -m pip install flask requests
To run it => .\venv\Scripts\python.exe app.py
.\venv\Scripts\python.exe -m pip install torch --index-url https://download.pytorch.org/whl/cpu
.\venv\Scripts\python.exe -m pip install transformers sentencepiece


git remote add origin https://github.com/admin-napstar/MultilingualTranslationSystem.git
git remote add origin https://github.com/admin-napstar/MultilingualTranslationAPI.git

http://localhost/MultilingualTranslationSystem/?utm_source=chatgpt.com

Install this command => composer require pragmarx/google2fa
Then run this in DB
ALTER TABLE users
    ADD COLUMN two_factor_enabled TINYINT(1) NOT NULL DEFAULT 0,
    ADD COLUMN two_factor_secret VARCHAR(255) NULL;
	
Install or open Google Authenticator or Microsoft Authenticator on your phone.
Choose the option to add an account manually.
Enter the secret key displayed on your setup page.
Select Time-based if the app asks you to choose between time-based and counter-based codes.
The app should generate a six-digit code that changes periodically.


Download and install this => https://developers.cloudflare.com/tunnel/downloads if you using windows download the msi file not exe

after installation make sure xammp is running and open a command prompt and enter
	=> cloudflared tunnel --url http://localhost:80
	Wait for Cloudflare to initialize the tunnel. You should see a public URL similar to:
	https://random-words.trycloudflare.com
	
	After you see the randown https link in your command prompt copy that and add /MultilingualTranslationSystem/login.php to it in the browser and you site will be live till you close the command prompt