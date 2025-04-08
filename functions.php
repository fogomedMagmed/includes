<?php
// Включаем отображение ошибок для отладки
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Функции для работы с пользователями

// Регистрация нового пользователя
function registerUser($pdo, $name, $email, $password, $role = 'buyer') {
    try {
        // Проверяем, что $pdo - это объект PDO
        if (!($pdo instanceof PDO)) {
            return ['success' => false, 'message' => 'Ошибка подключения к базе данных'];
        }
        
        // Проверяем, существует ли уже пользователь с таким email
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            return ['success' => false, 'message' => 'Пользователь с таким email уже существует'];
        }
        
        // Хешируем пароль
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        
        // Добавляем пользователя в базу данных
        $stmt = $pdo->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)");
        $stmt->execute([$name, $email, $hashed_password, $role]);
        
        return ['success' => true, 'message' => 'Регистрация успешно завершена'];
    } catch (PDOException $e) {
        return ['success' => false, 'message' => 'Ошибка при регистрации: ' . $e->getMessage()];
    }
}

// Авторизация пользователя
function loginUser($pdo, $email, $password) {
    try {
        // Проверяем, что $pdo - это объект PDO
        if (!($pdo instanceof PDO)) {
            return ['success' => false, 'message' => 'Ошибка подключения к базе данных'];
        }
        
        // Ищем пользоватея по email
        $stmt = $pdo->prepare("SELECT id, name, email, password, role FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();
        
        if (!$user) {
            return ['success' => false, 'message' => 'Пользователь с таким email не найден'];
        }
        
        // Проверяем пароль
        if (password_verify($password, $user['password'])) {
            // Создаем сессию для пользователя
            $_SESSION['user'] = [
                'id' => $user['id'],
                'name' => $user['name'],
                'email' => $user['email'],
                'role' => $user['role']
            ];
            
            return ['success' => true, 'message' => 'Вход выполнен успешно'];
        } else {
            return ['success' => false, 'message' => 'Неверный пароль'];
        }
    } catch (PDOException $e) {
        return ['success' => false, 'message' => 'Ошибка при входе: ' . $e->getMessage()];
    }
}

// Выход пользователя
function logoutUser() {
    // Удаляем данные пользователя из сессии
    unset($_SESSION['user']);
    
    // Перенаправляем на главную страницу
    header('Location: index.php');
    exit;
}

// Проверка, авторизован ли пользователь
function isLoggedIn() {
    return isset($_SESSION['user']);
}

// Проверка, является ли пользователь продавцом
function isSeller() {
    return isLoggedIn() && ($_SESSION['user']['role'] === 'seller' || $_SESSION['user']['role'] === 'admin');
}

// Проверка, является ли пользователь администратором
function isAdmin() {
    return isLoggedIn() && $_SESSION['user']['role'] === 'admin';
}

// Получение текущего пользователя
function getCurrentUser() {
    return $_SESSION['user'] ?? null;
}

// Функции для работы с товарами

// Получение всех категорий
function getAllCategories($pdo) {
    try {
        // Проверяем, что $pdo - это объект PDO
        if (!($pdo instanceof PDO)) {
            return [];
        }
        
        // Проверяем, существует ли таблица categories
        $stmt = $pdo->query("SHOW TABLES LIKE 'categories'");
        if ($stmt->rowCount() === 0) {
            // Если таблица не существует, возвращаем временные данные
            return [
                ['id' => 1, 'name' => 'Аккаунты', 'description' => 'Игровые аккаунты с прокачанными персонажами'],
                ['id' => 2, 'name' => 'Игровая валюта', 'description' => 'Внутриигровая валюта для различных игр'],
                ['id' => 3, 'name' => 'Услуги', 'description' => 'Услуги по прокачке, бусту и помощи в играх'],
                ['id' => 4, 'name' => 'Предметы', 'description' => 'Внутриигровые предметы, скины и другие виртуальные товары']
            ];
        }
        
        $stmt = $pdo->query("SELECT * FROM categories ORDER BY name");
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        // В случае ошибки возвращаем временные данные
        return [
            ['id' => 1, 'name' => 'Аккаунты', 'description' => 'Игровые аккаунты с прокачанными персонажами'],
            ['id' => 2, 'name' => 'Игровая валюта', 'description' => 'Внутриигровая валюта для различных игр'],
            ['id' => 3, 'name' => 'Услуги', 'description' => 'Услуги по прокачке, бусту и помощи в играх'],
            ['id' => 4, 'name' => 'Предметы', 'description' => 'Внутриигровые предметы, скины и другие виртуальные товары']
        ];
    }
}

// Получение категории по ID
function getCategoryById($pdo, $id) {
    try {
        // Проверяем, что $pdo - это объект PDO
        if (!($pdo instanceof PDO)) {
            return null;
        }
        
        $stmt = $pdo->prepare("SELECT * FROM categories WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    } catch (PDOException $e) {
        return null;
    }
}

// Получение всех товаров
function getAllProducts($pdo, $limit = null, $offset = 0, $filters = []) {
   try {
       // Проверяем, что $pdo - это объект PDO
       if (!($pdo instanceof PDO)) {
           return [];
       }
       
       // Проверяем, существует ли таблица products
       $stmt = $pdo->query("SHOW TABLES LIKE 'products'");
       if ($stmt->rowCount() === 0) {
           // Если таблица не существует, возвращаем пустой массив
           return [];
       }
       
       $sql = "SELECT p.*, u.name as seller_name, c.name as category_name 
               FROM products p 
               JOIN users u ON p.seller_id = u.id 
               JOIN categories c ON p.category_id = c.id 
               WHERE p.status = 'active'";
       
       $params = [];
       
       // Применяем фильтры
       if (!empty($filters['category_id'])) {
           $sql .= " AND p.category_id = ?";
           $params[] = $filters['category_id'];
       }
       
       if (!empty($filters['seller_id'])) {
           $sql .= " AND p.seller_id = ?";
           $params[] = $filters['seller_id'];
       }
       
       if (!empty($filters['price_min'])) {
           $sql .= " AND p.price >= ?";
           $params[] = $filters['price_min'];
       }
       
       if (!empty($filters['price_max'])) {
           $sql .= " AND p.price <= ?";
           $params[] = $filters['price_max'];
       }
       
       if (!empty($filters['search'])) {
           $search = "%{$filters['search']}%";
           $sql .= " AND (p.name LIKE ? OR p.description LIKE ? OR c.name LIKE ? OR u.name LIKE ?)";
           $params[] = $search;
           $params[] = $search;
           $params[] = $search;
           $params[] = $search;
       }
       
       $sql .= " ORDER BY p.created_at DESC";
       
       if ($limit !== null) {
           $sql .= " LIMIT ? OFFSET ?";
           $params[] = $limit;
           $params[] = $offset;
       }
       
       $stmt = $pdo->prepare($sql);
       $stmt->execute($params);
       return $stmt->fetchAll();
   } catch (PDOException $e) {
       // В случае ошибки возвращаем пустой массив
       return [];
   }
}

// Получение товара по ID
function getProductById($pdo, $id) {
    try {
        // Проверяем, что $pdo - это объект PDO
        if (!($pdo instanceof PDO)) {
            return null;
        }
        
        $stmt = $pdo->prepare("SELECT p.*, u.name as seller_name, c.name as category_name 
                               FROM products p 
                               JOIN users u ON p.seller_id = u.id 
                               JOIN categories c ON p.category_id = c.id 
                               WHERE p.id = ? AND p.status = 'active'");
        $stmt->execute([$id]);
        return $stmt->fetch();
    } catch (PDOException $e) {
        return null;
    }
}

// Получение товаров по категории
function getProductsByCategory($pdo, $category_id, $limit = null) {
    try {
        // Проверяем, что $pdo - это объект PDO
        if (!($pdo instanceof PDO)) {
            return [];
        }
        
        $sql = "SELECT p.*, u.name as seller_name, c.name as category_name 
                FROM products p 
                JOIN users u ON p.seller_id = u.id 
                JOIN categories c ON p.category_id = c.id 
                WHERE p.category_id = ? AND p.status = 'active' 
                ORDER BY p.created_at DESC";
        
        if ($limit !== null) {
            $sql .= " LIMIT ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$category_id, $limit]);
        } else {
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$category_id]);
        }
        
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        return [];
    }
}

// Получение товаров продавца
function getProductsBySeller($pdo, $seller_id, $limit = null) {
    try {
        // Проверяем, что $pdo - это объект PDO
        if (!($pdo instanceof PDO)) {
            return [];
        }
        
        $sql = "SELECT p.*, u.name as seller_name, c.name as category_name 
                FROM products p 
                JOIN users u ON p.seller_id = u.id 
                JOIN categories c ON p.category_id = c.id 
                WHERE p.seller_id = ? AND p.status = 'active' 
                ORDER BY p.created_at DESC";
        
        if ($limit !== null) {
            $sql .= " LIMIT ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$seller_id, $limit]);
        } else {
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$seller_id]);
        }
        
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        return [];
    }
}

// Получение всех продавцов
function getAllSellers($pdo) {
    try {
        // Проверяем, что $pdo - это объект PDO
        if (!($pdo instanceof PDO)) {
            return [];
        }
        
        $stmt = $pdo->query("SELECT id, name, email FROM users WHERE role = 'seller' ORDER BY name");
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        return [];
    }
}

// Получение продавца по ID
function getSellerById($pdo, $id) {
    try {
        // Проверяем, что $pdo - это объект PDO
        if (!($pdo instanceof PDO)) {
            return null;
        }
        
        $stmt = $pdo->prepare("SELECT id, name, email FROM users WHERE id = ? AND role = 'seller'");
        $stmt->execute([$id]);
        return $stmt->fetch();
    } catch (PDOException $e) {
        return null;
    }
}

// Добавление товара
function addProduct($pdo, $name, $description, $price, $image, $seller_id, $category_id) {
    try {
        // Проверяем, что $pdo - это объект PDO
        if (!($pdo instanceof PDO)) {
            return ['success' => false, 'message' => 'Ошибка подключения к базе данных'];
        }
        
        $stmt = $pdo->prepare("INSERT INTO products (name, description, price, image, seller_id, category_id) 
                              VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$name, $description, $price, $image, $seller_id, $category_id]);
        return ['success' => true, 'id' => $pdo->lastInsertId()];
    } catch (PDOException $e) {
        return ['success' => false, 'message' => 'Ошибка при добавлении товара: ' . $e->getMessage()];
    }
}

// Обновление товара
function updateProduct($pdo, $id, $name, $description, $price, $image, $category_id) {
    try {
        // Проверяем, что $pdo - это объект PDO
        if (!($pdo instanceof PDO)) {
            return ['success' => false, 'message' => 'Ошибка подключения к базе данных'];
        }
        
        $sql = "UPDATE products SET name = ?, description = ?, price = ?, category_id = ?";
        $params = [$name, $description, $price, $category_id];
        
        if ($image) {
            $sql .= ", image = ?";
            $params[] = $image;
        }
        
        $sql .= " WHERE id = ?";
        $params[] = $id;
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        
        return ['success' => true];
    } catch (PDOException $e) {
        return ['success' => false, 'message' => 'Ошибка при обновлении товара: ' . $e->getMessage()];
    }
}

// Удаление товара
function deleteProduct($pdo, $id) {
    try {
        // Проверяем, что $pdo - это объект PDO
        if (!($pdo instanceof PDO)) {
            return ['success' => false, 'message' => 'Ошибка подключения к базе данных'];
        }
        
        $stmt = $pdo->prepare("UPDATE products SET status = 'inactive' WHERE id = ?");
        $stmt->execute([$id]);
        return ['success' => true];
    } catch (PDOException $e) {
        return ['success' => false, 'message' => 'Ошибка при удалении товара: ' . $e->getMessage()];
    }
}

// Функция для загрузки изображения
function uploadImage($file, $target_dir = "assets/images/") {
    // Проверяем, существует ли директория, если нет - создаем
    if (!file_exists($target_dir)) {
        mkdir($target_dir, 0777, true);
    }
    
    // Генерируем уникальное имя файла
    $imageFileType = strtolower(pathinfo($file["name"], PATHINFO_EXTENSION));
    $target_file = $target_dir . uniqid() . "." . $imageFileType;
    
    // Проверяем, является ли файл изображением
    $check = getimagesize($file["tmp_name"]);
    if ($check === false) {
        return ['success' => false, 'message' => 'Файл не является изображением'];
    }
    
    // Проверяем размер файла (максимум 5MB)
    if ($file["size"] > 5000000) {
        return ['success' => false, 'message' => 'Файл слишком большой'];
    }
    
    // Разрешаем только определенные форматы
    if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif") {
        return ['success' => false, 'message' => 'Разрешены только JPG, JPEG, PNG и GIF файлы'];
    }
    
    // Пытаемся загрузить файл
    if (move_uploaded_file($file["tmp_name"], $target_file)) {
        return ['success' => true, 'file_path' => $target_file];
    } else {
        return ['success' => false, 'message' => 'Ошибка при загрузке файла'];
    }
}
?>

