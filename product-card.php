<div class="card">
  <a href="product.php?id=<?php echo $product['id']; ?>">
    <div class="relative h-48 w-full bg-zinc-800">
      <img 
        src="<?php echo $product['image']; ?>" 
        alt="<?php echo $product['name']; ?>" 
        class="card-img"
      >
    </div>
    <div class="card-body">
      <h3 class="font-medium mb-1 truncate text-white"><?php echo $product['name']; ?></h3>
      <p class="text-zinc-400 text-sm mb-2 line-clamp-2"><?php echo $product['description']; ?></p>
      <div class="flex justify-between items-center">
        <span class="font-bold text-lg text-white"><?php echo $product['price']; ?> ₽</span>
        <span class="text-sm text-zinc-500"><?php echo $product['seller_name'] ?? $product['seller']; ?></span>
      </div>
    </div>
  </a>
</div>

