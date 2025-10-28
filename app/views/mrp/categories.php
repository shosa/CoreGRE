<?php defined('APP_ROOT') or die('Access denied'); ?>

<!-- Dashboard Header -->
<div class="mb-8">
    <div class="sm:flex sm:items-center sm:justify-between">
        <div>
            <div class="flex items-center">
                <div>
                    <h1 class="text-title-md2 font-bold text-gray-900 dark:text-white">
                        Categorie MRP
                    </h1>
                    <p class="mt-2 text-gray-600 dark:text-gray-400">
                        Gestisci le traduzioni dei codici categoria
                    </p>
                </div>
            </div>
        </div>
        <div class="mt-4 sm:mt-0 flex items-center space-x-3">
            <button onclick="openModal()"
                    class="inline-flex items-center rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700">
                <i class="fas fa-plus mr-2"></i>
                Nuova Categoria
            </button>
        </div>
    </div>
</div>

<!-- Breadcrumb -->
<nav class="flex mb-6" aria-label="Breadcrumb">
    <ol class="inline-flex items-center space-x-1 md:space-x-3 text-sm text-gray-500 dark:text-gray-400">
        <li class="inline-flex items-center">
            <a href="<?= $this->url('/') ?>" class="hover:text-gray-700 dark:hover:text-gray-300">
                <i class="fas fa-home mr-2"></i>
                Dashboard
            </a>
        </li>
        <li>
            <div class="flex items-center">
                <i class="fas fa-chevron-right text-gray-400 mx-2"></i>
                <a href="<?= $this->url('/mrp') ?>" class="hover:text-gray-700 dark:hover:text-gray-300">MRP</a>
            </div>
        </li>
        <li>
            <div class="flex items-center">
                <i class="fas fa-chevron-right text-gray-400 mx-2"></i>
                <span class="text-gray-700 dark:text-gray-300">Categorie</span>
            </div>
        </li>
    </ol>
</nav>

<!-- Tabella -->
<div class="rounded-lg bg-white shadow-sm ring-1 ring-gray-900/5 dark:bg-gray-800 dark:ring-gray-700">
    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
        <h3 class="text-base font-semibold text-gray-900 dark:text-white">
            Elenco Categorie
        </h3>
        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
            Traduzioni dei codici categoria utilizzati nei materiali
        </p>
    </div>

    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="bg-gray-50 dark:bg-gray-700">
                <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-400">
                        Codice
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-400">
                        Nome
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-400">
                        Descrizione
                    </th>
                    <th scope="col" class="relative px-6 py-3">
                        <span class="sr-only">Azioni</span>
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-800 dark:divide-gray-700">
                <?php foreach ($categories as $category): ?>
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                            <?= htmlspecialchars($category->code) ?>
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                        <?= htmlspecialchars($category->name) ?>
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">
                        <?= htmlspecialchars($category->description ?? '') ?>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                        <div class="flex justify-end space-x-2">
                            <button onclick="editCategory(<?= $category->id ?>, '<?= htmlspecialchars($category->code, ENT_QUOTES) ?>', '<?= htmlspecialchars($category->name, ENT_QUOTES) ?>', '<?= htmlspecialchars($category->description ?? '', ENT_QUOTES) ?>')"
                                    class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button onclick="deleteCategory(<?= $category->id ?>)"
                                    class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal -->
<div id="categoryModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-99999">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white dark:bg-gray-800">
        <div class="mt-3">
            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4" id="modalTitle">Nuova Categoria</h3>
            <form id="categoryForm" method="POST">
                <input type="hidden" id="categoryId" name="id">

                <div class="mb-4">
                    <label for="code" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Codice
                    </label>
                    <input type="text" id="code" name="code" required maxlength="10"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                </div>

                <div class="mb-4">
                    <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Nome
                    </label>
                    <input type="text" id="name" name="name" required maxlength="100"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gamma-700 dark:border-gray-600 dark:text-white">
                </div>

                <div class="mb-6">
                    <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Descrizione
                    </label>
                    <textarea id="description" name="description" rows="3"
                              class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"></textarea>
                </div>

                <div class="flex justify-end space-x-3">
                    <button type="button" onclick="closeModal()"
                            class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">
                        Annulla
                    </button>
                    <button type="submit"
                            class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                        Salva
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function openModal() {
    document.getElementById('modalTitle').textContent = 'Nuova Categoria';
    document.getElementById('categoryForm').reset();
    document.getElementById('categoryId').value = '';
    document.getElementById('categoryForm').action = '<?= $this->url('/mrp/categories/store') ?>';
    document.getElementById('categoryModal').classList.remove('hidden');
}

function editCategory(id, code, name, description) {
    document.getElementById('modalTitle').textContent = 'Modifica Categoria';
    document.getElementById('categoryId').value = id;
    document.getElementById('code').value = code;
    document.getElementById('name').value = name;
    document.getElementById('description').value = description;
    document.getElementById('categoryForm').action = '<?= $this->url('/mrp/categories/update') ?>';
    document.getElementById('categoryModal').classList.remove('hidden');
}

function closeModal() {
    document.getElementById('categoryModal').classList.add('hidden');
}

function deleteCategory(id) {
    if (confirm('Sei sicuro di voler eliminare questa categoria?')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '<?= $this->url('/mrp/categories/delete') ?>';

        const idInput = document.createElement('input');
        idInput.type = 'hidden';
        idInput.name = 'id';
        idInput.value = id;

        form.appendChild(idInput);
        document.body.appendChild(form);

        // Submit con PJAX se disponibile
        if (typeof window.pjax !== 'undefined') {
            window.pjax.submitForm(form);
        } else {
            form.submit();
        }
    }
}

// Chiudi modal cliccando fuori
document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('categoryModal');
    if (modal) {
        modal.addEventListener('click', function(e) {
            if (e.target === this) {
                closeModal();
            }
        });
    }

    // Handle form submission con PJAX
    const categoryForm = document.getElementById('categoryForm');
    if (categoryForm && typeof window.pjax !== 'undefined') {
        categoryForm.addEventListener('submit', function(e) {
            e.preventDefault();
            window.pjax.submitForm(this);
            closeModal();
        });
    }
});
</script>