<?php
/**
 * Класс для управления изображениями
 */
class ImageManager {
    /**
     * Загрузка изображения
     * 
     * @param array $file Файл из $_FILES
     * @param string $targetDir Директория для сохранения
     * @param array $allowedTypes Разрешенные типы файлов
     * @param int $maxSize Максимальный размер файла в байтах
     * @return array Результат загрузки
     */
    public static function upload($file, $targetDir = 'assets/images/', $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'], $maxSize = 5242880) {
        // Проверяем, существует ли директория, если нет - создаем
        if (!file_exists($targetDir)) {
            mkdir($targetDir, 0777, true);
        }
        
        // Проверяем, является ли файл изображением
        if (!in_array($file['type'], $allowedTypes)) {
            return [
                'success' => false,
                'message' => 'Разрешены только JPG, PNG и GIF файлы'
            ];
        }
        
        // Проверяем размер файла
        if ($file['size'] > $maxSize) {
            return [
                'success' => false,
                'message' => 'Файл слишком большой (максимум ' . self::formatSize($maxSize) . ')'
            ];
        }
        
        // Генерируем уникальное имя файла
        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $fileName = uniqid() . '.' . $extension;
        $targetFile = $targetDir . $fileName;
        
        // Пытаемся загрузить файл
        if (move_uploaded_file($file['tmp_name'], $targetFile)) {
            return [
                'success' => true,
                'file_path' => $targetFile,
                'file_name' => $fileName
            ];
        } else {
            return [
                'success' => false,
                'message' => 'Ошибка при загрузке файла'
            ];
        }
    }
    
    /**
     * Удаление изображения
     * 
     * @param string $filePath Путь к файлу
     * @return bool Результат удаления
     */
    public static function delete($filePath) {
        if (file_exists($filePath)) {
            return unlink($filePath);
        }
        return false;
    }
    
    /**
     * Изменение размера изображения
     * 
     * @param string $filePath Путь к файлу
     * @param int $width Новая ширина
     * @param int $height Новая высота
     * @param string $targetFile Путь для сохранения нового файла
     * @return array Результат изменения размера
     */
    public static function resize($filePath, $width, $height, $targetFile = null) {
        // Проверяем, существует ли файл
        if (!file_exists($filePath)) {
            return [
                'success' => false,
                'message' => 'Файл не найден'
            ];
        }
        
        // Если путь для сохранения не указан, перезаписываем исходный файл
        if ($targetFile === null) {
            $targetFile = $filePath;
        }
        
        // Получаем информацию о файле
        $info = getimagesize($filePath);
        if ($info === false) {
            return [
                'success' => false,
                'message' => 'Не удалось получить информацию о файле'
            ];
        }
        
        // Создаем изображение в зависимости от типа
        switch ($info[2]) {
            case IMAGETYPE_JPEG:
                $image = imagecreatefromjpeg($filePath);
                break;
            case IMAGETYPE_PNG:
                $image = imagecreatefrompng($filePath);
                break;
            case IMAGETYPE_GIF:
                $image = imagecreatefromgif($filePath);
                break;
            default:
                return [
                    'success' => false,
                    'message' => 'Неподдерживаемый тип изображения'
                ];
        }
        
        // Создаем новое изображение
        $newImage = imagecreatetruecolor($width, $height);
        
        // Сохраняем прозрачность для PNG и GIF
        if ($info[2] === IMAGETYPE_PNG || $info[2] === IMAGETYPE_GIF) {
            imagecolortransparent($newImage, imagecolorallocate($newImage, 0, 0, 0));
            imagealphablending($newImage, false);
            imagesavealpha($newImage, true);
        }
        
        // Изменяем размер
        imagecopyresampled($newImage, $image, 0, 0, 0, 0, $width, $height, $info[0], $info[1]);
        
        // Сохраняем изображение
        $result = false;
        switch ($info[2]) {
            case IMAGETYPE_JPEG:
                $result = imagejpeg($newImage, $targetFile, 90);
                break;
            case IMAGETYPE_PNG:
                $result = imagepng($newImage, $targetFile, 9);
                break;
            case IMAGETYPE_GIF:
                $result = imagegif($newImage, $targetFile);
                break;
        }
        
        // Освобождаем память
        imagedestroy($image);
        imagedestroy($newImage);
        
        if ($result) {
            return [
                'success' => true,
                'file_path' => $targetFile
            ];
        } else {
            return [
                'success' => false,
                'message' => 'Ошибка при сохранении изображения'
            ];
        }
    }
    
    /**
     * Создание миниатюры изображения
     * 
     * @param string $filePath Путь к файлу
     * @param int $width Ширина миниатюры
     * @param int $height Высота миниатюры
     * @param string $targetDir Директория для сохранения
     * @return array Результат создания миниатюры
     */
    public static function createThumbnail($filePath, $width, $height, $targetDir = null) {
        // Если директория не указана, используем директорию исходного файла
        if ($targetDir === null) {
            $targetDir = dirname($filePath) . '/thumbnails/';
        }
        
        // Проверяем, существует ли директория, если нет - создаем
        if (!file_exists($targetDir)) {
            mkdir($targetDir, 0777, true);
        }
        
        // Генерируем имя файла миниатюры
        $fileName = pathinfo($filePath, PATHINFO_FILENAME);
        $extension = pathinfo($filePath, PATHINFO_EXTENSION);
        $thumbnailFile = $targetDir . $fileName . '_thumb.' . $extension;
        
        // Изменяем размер изображения
        return self::resize($filePath, $width, $height, $thumbnailFile);
    }
    
    /**
     * Форматирование размера файла
     * 
     * @param int $size Размер в байтах
     * @return string Отформатированный размер
     */
    private static function formatSize($size) {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $i = 0;
        while ($size >= 1024 && $i < count($units) - 1) {
            $size /= 1024;
            $i++;
        }
        return round($size, 2) . ' ' . $units[$i];
    }
}

// Обновляем функцию uploadImage для использования класса ImageManager
function uploadImage($file, $targetDir = 'assets/images/') {
    return ImageManager::upload($file, $targetDir);
}

