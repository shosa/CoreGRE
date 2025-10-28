<footer class="bg-white border-t border-gray-200 mt-auto">
    <div class="max-w-7xl mx-auto py-4 px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-4">
                <p class="text-sm text-gray-500">
                    © <?= date('Y') ?> <?= APP_NAME ?> - Versione <?= APP_VERSION ?>
                </p>
                
                <?php if (APP_ENV === 'development'): ?>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                        <i class="fas fa-code mr-1"></i>
                        Development
                    </span>
                <?php endif; ?>
            </div>
            
            <div class="flex items-center space-x-4">
                <!-- System status -->
                <div class="flex items-center text-sm text-gray-500">
                    <div class="flex items-center">
                        <div class="h-2 w-2 bg-green-400 rounded-full mr-2"></div>
                        <span>Sistema Online</span>
                    </div>
                </div>
                
                <!-- Timestamp server -->
                <div class="text-xs text-gray-400">
                    Server Time: <?= date('d/m/Y H:i:s') ?>
                </div>
                
                <?php if (APP_DEBUG): ?>
                    <!-- Debug info -->
                    <div class="text-xs text-gray-400">
                        <?php
                        $memory = round(memory_get_usage() / 1024 / 1024, 2);
                        $peak = round(memory_get_peak_usage() / 1024 / 1024, 2);
                        ?>
                        Memoria: <?= $memory ?>MB / Peak: <?= $peak ?>MB
                    </div>
                <?php endif; ?>
            </div>
        </div>
        
        <?php if (APP_DEBUG): ?>
            <!-- Debug panel -->
            <div class="mt-3 pt-3 border-t border-gray-100">
                <details class="text-xs text-gray-500">
                    <summary class="cursor-pointer hover:text-gray-700">Debug Info</summary>
                    <div class="mt-2 grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <strong>Request:</strong><br>
                            Method: <?= $_SERVER['REQUEST_METHOD'] ?? 'N/A' ?><br>
                            URI: <?= $_SERVER['REQUEST_URI'] ?? 'N/A' ?><br>
                            User Agent: <?= substr($_SERVER['HTTP_USER_AGENT'] ?? 'N/A', 0, 50) ?>...
                        </div>
                        <div>
                            <strong>Session:</strong><br>
                            <?php if ($this->isAuthenticated()): ?>
                                User ID: <?= $_SESSION['user_id'] ?? 'N/A' ?><br>
                                Username: <?= $_SESSION['username'] ?? 'N/A' ?><br>
                                Admin Type: <?= $_SESSION['admin_type'] ?? 'N/A' ?>
                            <?php else: ?>
                                Non autenticato
                            <?php endif; ?>
                        </div>
                        <div>
                            <strong>Database:</strong><br>
                            Host: <?= DB_HOST ?><br>
                            Database: <?= DB_NAME ?><br>
                            Connection: OK
                        </div>
                    </div>
                </details>
            </div>
        <?php endif; ?>
    </div>
</footer>