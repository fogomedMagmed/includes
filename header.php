<?php
// Проверяем, начата ли сессия
if (session_status() === PHP_SESSION_NONE) {
   session_start();
}

// Подключаем файл с функциями, если он еще не подключен
if (!function_exists('isLoggedIn')) {
   require_once 'functions.php';
}

// Подключаемся к базе данных и получаем категории
$categories = [];
try {
   $pdo = require_once __DIR__ . '/../config/database.php';
   
   // Проверяем, что $pdo - это объект PDO, а не boolean или null
   if ($pdo instanceof PDO) {
       // Получаем все категории для меню
       $categories = getAllCategories($pdo);
   } else {
       // Если $pdo не является объектом PDO, используем временные данные
       $categories = [
           ['id' => 1, 'name' => 'Аккаунты', 'description' => 'Игровые аккаунты с прокачанными персонажами'],
           ['id' => 2, 'name' => 'Игровая валюта', 'description' => 'Внутриигровая валюта для различных игр'],
           ['id' => 3, 'name' => 'Услуги', 'description' => 'Услуги по прокачке, бусту и помощи в играх'],
           ['id' => 4, 'name' => 'Предметы', 'description' => 'Внутриигровые предметы, скины и другие виртуальные товары']
       ];
   }
} catch (Exception $e) {
   // В случае ошибки используем временные данные
   $categories = [
       ['id' => 1, 'name' => 'Аккаунты', 'description' => 'Игровые аккаунты с прокачанными персонажами'],
       ['id' => 2, 'name' => 'Игровая валюта', 'description' => 'Внутриигровая валюта для различных игр'],
       ['id' => 3, 'name' => 'Услуги', 'description' => 'Услуги по прокачке, бусту и помощи в играх'],
       ['id' => 4, 'name' => 'Предметы', 'description' => 'Внутриигровые предметы, скины и другие виртуальные товары']
   ];
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
 <meta charset="UTF-8">
 <meta name="viewport" content="width=device-width, initial-scale=1.0">
 <title>GameMarket - Маркетплейс игровых товаров</title>
 <meta name="description" content="Маркетплейс для покупки и продажи игровых товаров и услуг">
 
 <!-- Подключаем Tailwind CSS через CDN -->
 <script src="https://cdn.tailwindcss.com"></script>
 <script>
   tailwind.config = {
     darkMode: 'class',
     theme: {
       extend: {
         colors: {
           zinc: {
             700: '#3f3f46',
             800: '#27272a',
             900: '#18181b',
           }
         }
       }
     }
   }
 </script>
 
 <!-- Встроенные стили -->
 <style>
   body {
     background-color: black;
     color: white;
     font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, 'Open Sans', 'Helvetica Neue', sans-serif;
   }
   .dropdown {
     position: relative;
     display: inline-block;
   }
   .dropdown-content {
     display: none;
     position: absolute;
     background-color: #18181b;
     min-width: 160px;
     box-shadow: 0px 8px 16px 0px rgba(0,0,0,0.2);
     z-index: 1;
     border: 1px solid #27272a;
     border-radius: 0.375rem;
   }
   .dropdown:hover .dropdown-content {
     display: block;
   }
   .card {
     background-color: #18181b;
     border: 1px solid #27272a;
     border-radius: 0.5rem;
     overflow: hidden;
     transition: border-color 0.2s ease;
   }
   .card:hover {
     border-color: #3f3f46;
   }
   .card-body {
     padding: 1rem;
   }
   .card-img {
     width: 100%;
     height: 12rem;
     object-fit: cover;
   }
   .grid {
     display: grid;
     grid-template-columns: repeat(1, 1fr);
     gap: 1.5rem;
   }
   @media (min-width: 640px) {
     .grid {
       grid-template-columns: repeat(2, 1fr);
     }
   }
   @media (min-width: 768px) {
     .grid {
       grid-template-columns: repeat(3, 1fr);
     }
   }
   @media (min-width: 1024px) {
     .grid {
       grid-template-columns: repeat(4, 1fr);
     }
   }
 </style>
</head>
<body class="min-h-screen flex flex-col">
 <header class="border-b border-zinc-800">
   <div class="container mx-auto px-4 py-4">
     <div class="flex items-center justify-between">
       <a href="/index.php" class="text-2xl font-bold text-white">GameMarket</a>
       
       <div class="hidden md:flex items-center space-x-6">
         <div class="dropdown">
           <a href="/categories.php" class="text-gray-300 hover:text-white transition-colors">Категории</a>
           <div class="dropdown-content">
             <?php foreach ($categories as $category): ?>
               <a href="/category.php?id=<?php echo $category['id']; ?>" class="block px-4 py-2 text-gray-300 hover:bg-zinc-800 hover:text-white">
                 <?php echo $category['name']; ?>
               </a>
             <?php endforeach; ?>
           </div>
         </div>
         <a href="/products.php" class="text-gray-300 hover:text-white transition-colors">Товары</a>
         <a href="/sellers.php" class="text-gray-300 hover:text-white transition-colors">Продавцы</a>
         <?php if (isset($_SESSION['user']) && ($_SESSION['user']['role'] === 'seller' || $_SESSION['user']['role'] === 'admin')): ?>
           <a href="/seller/dashboard.php" class="text-gray-300 hover:text-white transition-colors">Панель продавца</a>
         <?php endif; ?>
       </div>
       
       <div class="flex items-center space-x-4">
         <div class="relative hidden md:block">
           <form action="/search.php" method="get">
             <input
               type="text"
               name="q"
               placeholder="Поиск игровых товаров..."
               class="pl-10 pr-4 py-2 border rounded-full w-64 bg-zinc-900 text-white border-zinc-700 placeholder-zinc-500 focus:outline-none focus:border-zinc-600"
             >
             <button type="submit" class="absolute left-3 top-2.5">
               <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-zinc-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                 <circle cx="11" cy="11" r="8"></circle>
                 <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
               </svg>
             </button>
           </form>
         </div>
         
         <a href="/cart.php" class="p-2 rounded-full hover:bg-zinc-800 transition-colors">
           <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
             <circle cx="9" cy="21" r="1"></circle>
             <circle cx="20" cy="21" r="1"></circle>
             <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path>
           </svg>
         </a>
         
         <?php if (isset($_SESSION['user'])): ?>
           <div class="dropdown">
             <button class="p-2 rounded-full hover:bg-zinc-800 transition-colors flex items-center">
               <span class="mr-2 text-white"><?php echo $_SESSION['user']['name']; ?></span>
               <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                 <path d="M19 9l-7 7-7-7"></path>
               </svg>
             </button>
             <div class="dropdown-content right-0">
               <a href="/profile.php" class="block px-4 py-2 text-gray-300 hover:bg-zinc-800 hover:text-white">Профиль</a>
               <a href="/orders.php" class="block px-4 py-2 text-gray-300 hover:bg-zinc-800 hover:text-white">Мои заказы</a>
               <a href="/chat.php" class="block px-4 py-2 text-gray-300 hover:bg-zinc-800 hover:text-white">Чат с поддержкой</a>
               <?php if ($_SESSION['user']['role'] === 'seller' || $_SESSION['user']['role'] === 'admin'): ?>
                 <a href="/seller/dashboard.php" class="block px-4 py-2 text-gray-300 hover:bg-zinc-800 hover:text-white">Панель продавца</a>
               <?php endif; ?>
               <?php if ($_SESSION['user']['role'] === 'admin'): ?>
                 <a href="/admin/dashboard.php" class="block px-4 py-2 text-gray-300 hover:bg-zinc-800 hover:text-white">Админ-панель</a>
                 <a href="/admin/chats.php" class="flex items-center px-4 py-2 text-gray-300 hover:bg-zinc-800 hover:text-white">
                   Чаты поддержки
                   <span id="chat-notifications-badge" class="ml-2 hidden bg-red-500 text-white text-xs px-2 py-1 rounded-full">0</span>
                 </a>
               <?php endif; ?>
               <a href="/logout.php" class="block px-4 py-2 text-gray-300 hover:bg-zinc-800 hover:text-white">Выйти</a>
             </div>
           </div>
         <?php else: ?>
           <a href="/login.php" class="p-2 rounded-full hover:bg-zinc-800 transition-colors">
             <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
               <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
               <circle cx="12" cy="7" r="4"></circle>
             </svg>
           </a>
         <?php endif; ?>
         
         <button class="md:hidden p-2 rounded-full hover:bg-zinc-800 transition-colors">
           <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
             <line x1="3" y1="12" x2="21" y2="12"></line>
             <line x1="3" y1="6" x2="21" y2="6"></line>
             <line x1="3" y1="18" x2="21" y2="18"></line>
           </svg>
         </button>
       </div>
     </div>
   </div>
 </header>

 <main class="flex-grow">
<?php if (isset($_SESSION['user']) && $_SESSION['user']['role'] === 'admin'): ?>
<script>
// Функция для проверки уведомлений чата
function checkChatNotifications() {
   fetch('/api/chat_notifications.php')
       .then(response => response.json())
       .then(data => {
           if (data.success) {
               const badge = document.getElementById('chat-notifications-badge');
               if (data.totalNotifications > 0) {
                   badge.textContent = data.totalNotifications;
                   badge.classList.remove('hidden');
               } else {
                   badge.classList.add('hidden');
               }
           }
       })
       .catch(error => console.error('Error checking chat notifications:', error));
}

// Проверяем уведомления при загрузке страницы и каждые 30 секунд
document.addEventListener('DOMContentLoaded', function() {
   checkChatNotifications();
   setInterval(checkChatNotifications, 30000);
});
</script>
<?php endif; ?>