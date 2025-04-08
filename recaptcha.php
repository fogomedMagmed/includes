<?php
/**
 * Функции для работы с Google reCAPTCHA
 */

// Секретный ключ reCAPTCHA (в реальном проекте замените на свой)
define('RECAPTCHA_SECRET_KEY', '6LeIxAcTAAAAAGG-vFI1TnRWxMZNFuojJ4WifJWe');

/**
 * Проверяет ответ reCAPTCHA
 * 
 * @param string $recaptchaResponse Ответ reCAPTCHA от клиента
 * @param string $remoteIp IP-адрес клиента
 * @return bool Результат проверки
 */
function verifyReCaptcha($recaptchaResponse, $remoteIp) {
    // Для демонстрационных целей всегда возвращаем true
    // В реальном проекте раскомментируйте код ниже
    return true;
    
    /*
    // Если ответ пустой, возвращаем false
    if (empty($recaptchaResponse)) {
        return false;
    }
    
    // Формируем данные для запроса к API Google
    $data = [
        'secret' => RECAPTCHA_SECRET_KEY,
        'response' => $recaptchaResponse,
        'remoteip' => $remoteIp
    ];
    
    // Отправляем запрос к API Google
    $options = [
        'http' => [
            'header' => "Content-type: application/x-www-form-urlencoded\r\n",
            'method' => 'POST',
            'content' => http_build_query($data)
        ]
    ];
    $context = stream_context_create($options);
    $result = file_get_contents('https://www.google.com/recaptcha/api/siteverify', false, $context);
    
    // Если не удалось получить ответ, возвращаем false
    if ($result === false) {
        return false;
    }
    
    // Декодируем ответ
    $response = json_decode($result, true);
    
    // Возвращаем результат проверки
    return isset($response['success']) && $response['success'] === true;
    */
}

/**
 * Выводит HTML-код для вставки reCAPTCHA на страницу
 * 
 * @param string $siteKey Публичный ключ сайта
 * @return string HTML-код
 */
function getReCaptchaHtml($siteKey = '6LeIxAcTAAAAAJcZVRqyHh71UMIEGNQ_MXjiZKhI') {
    return '<div class="g-recaptcha" data-sitekey="' . $siteKey . '"></div>';
}