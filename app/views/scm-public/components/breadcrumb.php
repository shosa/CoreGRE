<?php if (!empty($breadcrumb)): ?>
<!-- Breadcrumb -->
<nav class="flex mb-6" aria-label="Breadcrumb">
    <ol class="inline-flex items-center space-x-1 md:space-x-3">
        <li class="inline-flex items-center">
            <a href="<?= $this->url('/scm/dashboard') ?>" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-blue-600 dark:text-gray-400 dark:hover:text-white transition-colors">
                <i class="fas fa-home w-4 h-4 mr-2"></i>
                Home
            </a>
        </li>
        
        <?php foreach ($breadcrumb as $index => $item): ?>
            <?php if (!empty($item['current']) && $item['current']): ?>
                <li aria-current="page">
                    <div class="flex items-center">
                        <i class="fas fa-chevron-right w-4 h-4 text-gray-400 mx-1"></i>
                        <span class="ml-1 text-sm font-medium text-gray-500 md:ml-2 dark:text-gray-400">
                            <?= htmlspecialchars($item['title']) ?>
                        </span>
                    </div>
                </li>
            <?php else: ?>
                <li>
                    <div class="flex items-center">
                        <i class="fas fa-chevron-right w-4 h-4 text-gray-400 mx-1"></i>
                        <a href="<?= $this->url($item['url']) ?>" class="ml-1 text-sm font-medium text-gray-700 hover:text-blue-600 md:ml-2 dark:text-gray-400 dark:hover:text-white transition-colors">
                            <?= htmlspecialchars($item['title']) ?>
                        </a>
                    </div>
                </li>
            <?php endif; ?>
        <?php endforeach; ?>
    </ol>
</nav>
<?php endif; ?>