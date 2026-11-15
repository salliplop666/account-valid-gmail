const translations = {
    en: {
        'sign_in': 'Sign in',
        'to_continue_to_gmail': 'to continue to Gmail',
        'email_or_phone': 'Email or phone',
        'forgot_email': 'Forgot email?',
        'next': 'Next',
        'not_your_computer': 'Not your computer? Use a Private Window to sign in.',
        'learn_more': 'Learn more about using Guest mode',
        'create_account': 'Create account',
        'help': 'Help',
        'privacy': 'Privacy',
        'terms': 'Terms',
        'welcome': 'Welcome',
        'enter_your_password': 'Enter your password',
        'show_password': 'Show password',
        'try_another_way': 'Try another way',
        'forgot_password': 'Forgot password?',
        'wrong_password': 'Wrong password. Try again or click Forgot password to reset it.',
        'verifying_your_identity': 'Verifying your identity',
        'please_wait': 'Please wait while we verify your account.',
        'two_step_verification': '2-Step Verification',
        'enter_code_authenticator': 'Enter the code from your Google Authenticator app',
        'enter_6digit_code': 'Enter 6-digit code',
        'to_help_keep_safe': 'To help keep your account safe, Google wants to make sure it\'s really you trying to sign in',
        'verification_code_sent': 'Google sent a verification code to your phone',
        'enter_the_code': 'Enter the code',
        'resend_it': 'Resend it',
        'verifying': 'Verifying...',
        'loading': 'Loading...'
    },
    es: {
        'sign_in': 'Iniciar sesión',
        'to_continue_to_gmail': 'para continuar a Gmail',
        'email_or_phone': 'Correo electrónico o teléfono',
        'forgot_email': '¿Olvidaste tu correo?',
        'next': 'Siguiente',
        'not_your_computer': '¿No es tu computadora? Usa una ventana privada para iniciar sesión.',
        'learn_more': 'Más información sobre el modo de invitado',
        'create_account': 'Crear cuenta',
        'help': 'Ayuda',
        'privacy': 'Privacidad',
        'terms': 'Términos',
        'welcome': 'Bienvenido',
        'enter_your_password': 'Ingresa tu contraseña',
        'show_password': 'Mostrar contraseña',
        'try_another_way': 'Probar otro método',
        'forgot_password': '¿Olvidaste tu contraseña?',
        'wrong_password': 'Contraseña incorrecta. Intenta de nuevo o haz clic en Olvidaste tu contraseña para restablecerla.',
        'verifying_your_identity': 'Verificando tu identidad',
        'please_wait': 'Por favor espera mientras verificamos tu cuenta.',
        'two_step_verification': 'Verificación en 2 pasos',
        'enter_code_authenticator': 'Ingresa el código de tu aplicación Google Authenticator',
        'enter_6digit_code': 'Ingresa código de 6 dígitos',
        'to_help_keep_safe': 'Para ayudar a proteger tu cuenta, Google quiere asegurarse de que realmente eres tú quien intenta iniciar sesión',
        'verification_code_sent': 'Google envió un código de verificación a tu teléfono',
        'enter_the_code': 'Ingresa el código',
        'resend_it': 'Reenviar',
        'verifying': 'Verificando...',
        'loading': 'Cargando...'
    }
};

function detectLanguage() {
    let lang = 'en';
    
    // Check cookie
    const cookies = document.cookie.split(';');
    for (let cookie of cookies) {
        if (cookie.trim().startsWith('lang=')) {
            lang = cookie.trim().substring(5, 7);
            break;
        }
    }
    
    // Check URL
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.has('lang')) {
        lang = urlParams.get('lang').substring(0, 2);
        document.cookie = `lang=${lang};path=/;max-age=31536000`;
    }
    
    // Check browser language
    if (lang === 'en' && navigator.language) {
        const browserLang = navigator.language.substring(0, 2).toLowerCase();
        if (browserLang === 'es') lang = 'es';
    }
    
    return lang;
}

const currentLang = detectLanguage();

function t(key) {
    return translations[currentLang]?.[key] || translations.en[key] || key;
}

function setLanguage(lang) {
    document.cookie = `lang=${lang};path=/;max-age=31536000`;
    location.reload();
}
