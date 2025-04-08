</main>

  <footer class="bg-zinc-900 border-t border-zinc-800 py-8">
    <div class="container mx-auto px-4">
      <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        <div>
          <h3 class="text-lg font-semibold mb-4">О GameMarket</h3>
          <p class="text-gray-400 text-sm">
            GameMarket - это маркетплейс для покупки и продажи игровых товаров и услуг. 
            Мы предлагаем безопасную и удобную платформу для торговли игровыми аккаунтами, 
            внутриигровой валютой, предметами и услугами.
          </p>
        </div>
        <div>
          <h3 class="text-lg font-semibold mb-4">Информация</h3>
          <ul class="space-y-2 text-sm">
            <li><a href="/privacy.php" class="text-gray-400 hover:text-white transition-colors">Политика конфиденциальности</a></li>
            <li><a href="/terms-of-sale.php" class="text-gray-400 hover:text-white transition-colors">Условия продажи</a></li>
            <li><a href="/user-agreement.php" class="text-gray-400 hover:text-white transition-colors">Пользовательское соглашение</a></li>
          </ul>
        </div>
        <div>
          <h3 class="text-lg font-semibold mb-4">Контакты</h3>
          <ul class="space-y-2 text-sm">
            <li class="text-gray-400">Email: support@gamemarket.ru</li>
            <li class="text-gray-400">Телефон: +7 (999) 123-45-67</li>
            <li><a href="/chat.php" class="text-gray-400 hover:text-white transition-colors">Чат с поддержкой</a></li>
          </ul>
        </div>
      </div>
      <div class="mt-8 pt-6 border-t border-zinc-800 text-center text-sm text-gray-500">
        &copy; <?php echo date('Y'); ?> GameMarket. Все права защищены.
      </div>
    </div>
  </footer>

  <!-- Виджет чата -->
  <div id="chat-widget" class="fixed bottom-4 right-4 z-50">
    <!-- Кнопка чата -->
    <button id="chat-widget-button" class="bg-zinc-800 hover:bg-zinc-700 text-white rounded-full p-4 shadow-lg flex items-center justify-center transition-all duration-300">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
        </svg>
    </button>
    
    <!-- Окно чата -->
    <div id="chat-widget-window" class="hidden bg-zinc-900 border border-zinc-800 rounded-lg shadow-xl w-80 md:w-96 overflow-hidden absolute bottom-16 right-0">
        <!-- Заголовок чата -->
        <div class="bg-zinc-800 p-3 border-b border-zinc-700 flex items-center justify-between">
            <div class="font-medium text-white">Чат с поддержкой</div>
            <button id="chat-widget-close" class="text-gray-400 hover:text-white">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
        
        <?php if (isset($_SESSION['user'])): ?>
            <!-- Сообщение с ссылкой на полноценный чат -->
            <div class="p-4 text-center">
                <p class="text-gray-300 mb-4">Для удобного общения с поддержкой перейдите на страницу чата</p>
                <a href="/chat.php" class="inline-block px-4 py-2 bg-zinc-700 hover:bg-zinc-600 text-white rounded-lg transition-colors">
                    Открыть чат
                </a>
            </div>
        <?php else: ?>
            <!-- Сообщение для неавторизованных пользователей -->
            <div class="p-4 text-center">
                <p class="text-gray-300 mb-4">Для общения с поддержкой необходимо авторизоваться</p>
                <a href="/login.php?redirect=chat.php" class="inline-block px-4 py-2 bg-zinc-700 hover:bg-zinc-600 text-white rounded-lg transition-colors">
                    Войти
                </a>
            </div>
        <?php endif; ?>
    </div>
  </div>

  <script>
  document.addEventListener('DOMContentLoaded', function() {
      const chatButton = document.getElementById('chat-widget-button');
      const chatWindow = document.getElementById('chat-widget-window');
      const chatClose = document.getElementById('chat-widget-close');
      
      // Открытие/закрытие окна чата при клике на кнопку
      chatButton.addEventListener('click', function() {
          chatWindow.classList.toggle('hidden');
      });
      
      // Закрытие окна чата при клике на крестик
      chatClose.addEventListener('click', function() {
          chatWindow.classList.add('hidden');
      });
  });
  </script>

</body>
</html>

