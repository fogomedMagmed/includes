<?php
// Временные данные для локальной разработки
// В реальном проекте эти данные будут храниться в базе данных

// Товары
$products = [
  [
    'id' => '1',
    'name' => 'Аккаунт World of Warcraft (70 уровень)',
    'description' => 'Аккаунт с персонажем 70 уровня, полным комплектом эпического снаряжения и редкими маунтами',
    'price' => 5000,
    'image' => 'assets/images/placeholder.jpg',
    'seller' => 'WoWMaster',
    'category' => 'Аккаунты'
  ],
  [
    'id' => '2',
    'name' => '10000 золота в World of Warcraft',
    'description' => 'Внутриигровая валюта для World of Warcraft. Доставка в течение 24 часов.',
    'price' => 1500,
    'image' => 'assets/images/placeholder.jpg',
    'seller' => 'GoldFarm',
    'category' => 'Игровая валюта'
  ],
  [
    'id' => '3',
    'name' => 'Буст рейтинга в Dota 2 (1000-2000 MMR)',
    'description' => 'Профессиональный игрок поднимет ваш рейтинг с 1000 до 2000 MMR за 3-5 дней',
    'price' => 3000,
    'image' => 'assets/images/placeholder.jpg',
    'seller' => 'DotaBooster',
    'category' => 'Услуги'
  ],
  [
    'id' => '4',
    'name' => 'Скин AWP Dragon Lore (CS:GO)',
    'description' => 'Редкий скин для AWP в CS:GO. Минимальный износ, без царапин.',
    'price' => 120000,
    'image' => 'assets/images/placeholder.jpg',
    'seller' => 'SkinTrader',
    'category' => 'Предметы'
  ],
  [
    'id' => '5',
    'name' => 'Аккаунт League of Legends (Платина)',
    'description' => 'Аккаунт с платиновым рангом, 80+ чемпионов и 50+ скинов',
    'price' => 4500,
    'image' => 'assets/images/placeholder.jpg',
    'seller' => 'LoLMaster',
    'category' => 'Аккаунты'
  ],
  [
    'id' => '6',
    'name' => 'Прокачка персонажа в Diablo 4',
    'description' => 'Прокачка вашего персонажа до 50 уровня с прохождением всех сюжетных заданий',
    'price' => 2500,
    'image' => 'assets/images/placeholder.jpg',
    'seller' => 'DiabloGrinder',
    'category' => 'Услуги'
  ],
  [
    'id' => '7',
    'name' => '5000 V-Bucks для Fortnite',
    'description' => 'Внутриигровая валюта для Fortnite. Мгновенная доставка.',
    'price' => 1800,
    'image' => 'assets/images/placeholder.jpg',
    'seller' => 'VBuckShop',
    'category' => 'Игровая валюта'
  ],
  [
    'id' => '8',
    'name' => 'Аккаунт Genshin Impact (AR 55)',
    'description' => 'Аккаунт с Adventure Rank 55, множеством 5-звездочных персонажей и оружия',
    'price' => 8000,
    'image' => 'assets/images/placeholder.jpg',
    'seller' => 'GenshinTrader',
    'category' => 'Аккаунты'
  ],
  [
    'id' => '9',
    'name' => 'Прохождение рейда в Destiny 2',
    'description' => 'Профессиональная команда проведет вас через любой рейд в Destiny 2 с получением всех наград',
    'price' => 1200,
    'image' => 'assets/images/placeholder.jpg',
    'seller' => 'RaidMaster',
    'category' => 'Услуги'
  ],
  [
    'id' => '10',
    'name' => 'Набор редких предметов в Minecraft',
    'description' => 'Набор редких и труднодоступных предметов для вашего сервера Minecraft',
    'price' => 800,
    'image' => 'assets/images/placeholder.jpg',
    'seller' => 'MinecraftTrader',
    'category' => 'Предметы'
  ]
];

// Пользователи
$users = [
  [
    'id' => '1',
    'name' => 'Иван Петров',
    'email' => 'ivan@example.com',
    'password' => 'password123',
    'role' => 'buyer'
  ],
  [
    'id' => '2',
    'name' => 'Мария Сидорова',
    'email' => 'maria@example.com',
    'password' => 'password123',
    'role' => 'seller'
  ]
];
?>

