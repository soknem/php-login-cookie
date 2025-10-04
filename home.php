<?php
require 'auth.php';
require 'sakila_db.php';

if (!checkAuth()) {
    header('Location: index.php');
    exit;
}

if (isset($_POST['logout'])) {
    logout();
    header('Location: index.php');
    exit;
}

// Handle customer deletion
if (isset($_POST['delete_customer'])) {
    $customer_id = $_POST['customer_id'];
    $stmt = $sakila_pdo->prepare("DELETE FROM customer WHERE customer_id = ?");
    $stmt->execute([$customer_id]);
}

// Handle customer update
if (isset($_POST['update_customer'])) {
    $customer_id = $_POST['customer_id'];
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $email = $_POST['email'];
    $address_id = $_POST['address_id'];
    
    $stmt = $sakila_pdo->prepare("UPDATE customer SET first_name = ?, last_name = ?, email = ?, address_id = ? WHERE customer_id = ?");
    $stmt->execute([$first_name, $last_name, $email, $address_id, $customer_id]);
}


// Pagination settings
$records_per_page = isset($_GET['records_per_page']) ? (int)$_GET['records_per_page'] : 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $records_per_page;

// Fetch users from test database
global $pdo;
$stmt = $pdo->query("SELECT username, password, remember_token FROM users");
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch customers from sakila database with joined details and pagination
$stmt = $sakila_pdo->query("SELECT COUNT(*) FROM customer");
$total_records = $stmt->fetchColumn();
$total_pages = ceil($total_records / $records_per_page);

$stmt = $sakila_pdo->prepare("
    SELECT c.customer_id, c.first_name, c.last_name, c.email, 
           a.address, ci.city, co.country, s.store_id, ci_store.city AS store_city
    FROM customer c
    JOIN address a ON c.address_id = a.address_id
    JOIN city ci ON a.city_id = ci.city_id
    JOIN country co ON ci.country_id = co.country_id
    JOIN store s ON c.store_id = s.store_id
    JOIN address a_store ON s.address_id = a_store.address_id
    JOIN city ci_store ON a_store.city_id = ci_store.city_id
    LIMIT :limit OFFSET :offset
");
$stmt->bindValue(':limit', $records_per_page, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$customers = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<script>
    // Define closeModal in the global scope
    function closeModal(modalId) {
        document.getElementById(modalId).style.display = 'none';
    }

    document.addEventListener('DOMContentLoaded', () => {
        const actionButtons = document.querySelectorAll('.action-btn');
        const menus = document.querySelectorAll('.action-menu');
        const detailsModal = document.getElementById('detailsModal');
        const detailsContent = document.getElementById('detailsContent');
        const updateModal = document.getElementById('updateModal');
        const deleteModal = document.getElementById('deleteModal');

        actionButtons.forEach(button => {
            button.addEventListener('click', (e) => {
                e.preventDefault();
                const index = button.getAttribute('data-index');
                const menu = document.getElementById(`action-menu-${index}`);
                menus.forEach(m => m.classList.remove('active'));
                menu.classList.toggle('active');
                menu.style.top = `${button.offsetTop + button.offsetHeight}px`;
                menu.style.left = `${button.offsetLeft - 100}px`; // Adjust position to the left
            });
        });

        document.addEventListener('click', (e) => {
            if (!e.target.closest('.action-btn') && !e.target.closest('.action-menu')) {
                menus.forEach(m => m.classList.remove('active'));
            }
        });

        document.querySelectorAll('.detail-action').forEach(link => {
            link.addEventListener('click', (e) => {
                e.preventDefault();
                const index = link.getAttribute('data-index');
                const customer = <?php echo json_encode($customers); ?>[index];
                if (customer) {
                    detailsContent.innerHTML = `
                        <p><strong>First Name:</strong> ${customer.first_name}</p>
                        <p><strong>Last Name:</strong> ${customer.last_name}</p>
                        <p><strong>Email:</strong> ${customer.email}</p>
                        <p><strong>Address:</strong> ${customer.address}</p>
                        <p><strong>City:</strong> ${customer.city}</p>
                        <p><strong>Country:</strong> ${customer.country}</p>
                        <p><strong>Store City:</strong> ${customer.store_city}</p>
                    `;
                    detailsModal.style.display = 'block';
                } else {
                    alert('Error loading customer details.');
                }
            });
        });

        document.querySelectorAll('.update-action').forEach(link => {
            link.addEventListener('click', (e) => {
                e.preventDefault();
                const index = link.getAttribute('data-index');
                const customer = <?php echo json_encode($customers); ?>[index];
                if (customer) {
                    document.getElementById('update_customer_id').value = customer.customer_id;
                    document.getElementById('update_first_name').value = customer.first_name;
                    document.getElementById('update_last_name').value = customer.last_name;
                    document.getElementById('update_email').value = customer.email;
                    document.getElementById('update_address_id').value = customer.address_id || 1; // Use actual address_id
                    updateModal.style.display = 'block';
                } else {
                    alert('Error loading customer data for update.');
                }
            });
        });

        document.querySelectorAll('.delete-action').forEach(link => {
            link.addEventListener('click', (e) => {
                e.preventDefault();
                const index = link.getAttribute('data-index');
                const customer = <?php echo json_encode($customers); ?>[index];
                if (customer) {
                    document.getElementById('delete_customer_id').value = customer.customer_id;
                    deleteModal.style.display = 'block';
                } else {
                    alert('Error loading customer data for deletion.');
                }
            });
        });

        window.addEventListener('click', (e) => {
            if (e.target == detailsModal || e.target == updateModal || e.target == deleteModal) {
                closeModal('detailsModal');
                closeModal('updateModal');
                closeModal('deleteModal');
            }
        });

        function changeRecordsPerPage() {
            const records = document.getElementById('records_per_page').value;
            window.location.href = `?section=customers&page=1&records_per_page=${records}`;
        }
    });
</script>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="styles.css">
    <style>
        .modal { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.6); z-index: 1000; }
        .modal-content { 
            background: white; 
            margin: 10% auto; 
            padding: 0; 
            width: 90%; 
            max-width: 500px; 
            border-radius: 12px; 
            box-shadow: 0 10px 25px rgba(0,0,0,0.2); 
            overflow: hidden; 
        }
        .modal-header { 
            background: linear-gradient(to right, #4b5563, #6b7280); 
            padding: 16px; 
            color: white; 
            font-size: 1.25rem; 
            font-weight: 600; 
        }
        .modal-body { padding: 20px; }
        .modal-body label { display: block; color: #1f2937; font-weight: 500; margin-bottom: 8px; }
        .modal-body input, .modal-body select { 
            width: 100%; 
            padding: 10px; 
            margin-bottom: 16px; 
            border: 1px solid #d1d5db; 
            border-radius: 6px; 
            transition: border-color 0.3s ease; 
        }
        .modal-body input:focus, .modal-body select:focus { border-color: #3b82f6; }
        .modal-footer { 
            padding: 16px; 
            background: #f9fafb; 
            text-align: right; 
            border-top: 1px solid #e5e7eb; 
        }
        .modal-footer button { margin-left: 8px; }
        .sidebar { position: fixed; top: 0; left: 0; height: 100vh; width: 48; z-index: 20; }
        .sidebar a:hover { background: rgba(255,255,255,0.1); }
        .pagination { 
            position: sticky; 
            bottom: 0; 
            background: #f9fafb; 
            padding: 16px; 
            border-top: 1px solid #e5e7eb; 
            z-index: 10; 
            width: calc(100% - 48px); 
            margin-left: 48px; 
        }
        .action-menu { 
            display: none; 
            position: absolute; 
            background: white; 
            min-width: 120px; 
            box-shadow: 0 8px 16px rgba(0,0,0,0.2); 
            border-radius: 6px; 
            z-index: 30; 
        }
        .action-menu.active { display: block; }
        .action-menu a { 
            display: block; 
            padding: 8px 16px; 
            color: #1f2937; 
            text-decoration: none; 
            transition: background 0.2s ease; 
        }
        .action-menu a:hover { background: #f3f4f6; }
    </style>
</head>
<body class="bg-gradient-to-br from-gray-200 to-gray-600 min-h-screen font-['Inter']">
    <div class="flex min-h-screen">
        <!-- Sidebar -->
        <div class="sidebar bg-white/10 w-48 border-r border-gray-400">
            <div class="p-6">
                <h2 class="text-xl font-bold text-red-800 mb-6">Menu</h2>
                <a href="?section=users" class="block py-2 px-4 text-gray-700 hover:text-red-800 <?php echo (!isset($_GET['section']) || $_GET['section'] === 'users') ? 'bg-gray-100/10' : ''; ?>">Users</a>
                <a href="?section=customers" class="block py-2 px-4 text-gray-700 hover:text-red-800 <?php echo (isset($_GET['section']) && $_GET['section'] === 'customers') ? 'bg-gray-100/10' : ''; ?>">Customers</a>
            </div>
        </div>

        <!-- Main Content -->
        <div class="flex-1 ml-48">
            <header class="bg-gray-800 border-b border-gray-400 sticky top-0 z-10">
                <div class="container mx-auto px-6 py-4 flex justify-between items-center">
                    <h1 class="text-2xl font-bold text-red-800">Dashboard</h1>
                    <form method="POST">
                        <button type="submit" name="logout" class="bg-red-500 text-white px-6 py-2 rounded-lg hover:bg-red-600 transition-all duration-300 transform hover:scale-105">Sign Out</button>
                    </form>
                </div>
            </header>
            <main class="container mx-auto px-6 py-8">
                <div class="overflow-x-auto relative">
                    <?php if (!isset($_GET['section']) || $_GET['section'] === 'users'): ?>
                        <!-- Users Table -->
                        <table class="min-w-full bg-white/10 rounded-xl shadow-2xl border border-gray-400">
                            <thead>
                                <tr class="bg-gray-700 text-white">
                                    <th class="py-3 px-4 text-left">Username</th>
                                    <th class="py-3 px-4 text-left">Password (Hashed)</th>
                                    <th class="py-3 px-4 text-left">Remember Token</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($users as $user): ?>
                                    <tr class="border-b border-gray-400 hover:bg-gray-100/10">
                                        <td class="py-3 px-4 text-gray-700"><?php echo htmlspecialchars($user['username']); ?></td>
                                        <td class="py-3 px-4 text-gray-700"><?php echo htmlspecialchars($user['password']); ?></td>
                                        <td class="py-3 px-4 text-left"><?php echo htmlspecialchars($user['remember_token'] ?? 'N/A'); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php else: ?>
                        <!-- Customers Table -->
                        <table class="min-w-full bg-white/10 rounded-xl shadow-2xl border border-gray-400">
                            <thead>
                                <tr class="bg-gray-700 text-white">
                                    <th class="py-3 px-4 text-left">First Name</th>
                                    <th class="py-3 px-4 text-left">Last Name</th>
                                    <th class="py-3 px-4 text-left">Email</th>
                                    <th class="py-3 px-4 text-left">Address</th>
                                    <th class="py-3 px-4 text-left">City</th>
                                    <th class="py-3 px-4 text-left">Country</th>
                                    <th class="py-3 px-4 text-left">Store City</th>
                                    <th class="py-3 px-4 text-left">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($customers as $index => $customer): ?>
                                    <tr class="border-b border-gray-400 hover:bg-gray-100/10">
                                        <td class="py-3 px-4 text-gray-700"><?php echo htmlspecialchars($customer['first_name']); ?></td>
                                        <td class="py-3 px-4 text-gray-700"><?php echo htmlspecialchars($customer['last_name']); ?></td>
                                        <td class="py-3 px-4 text-gray-700"><?php echo htmlspecialchars($customer['email']); ?></td>
                                        <td class="py-3 px-4 text-gray-700"><?php echo htmlspecialchars($customer['address']); ?></td>
                                        <td class="py-3 px-4 text-gray-700"><?php echo htmlspecialchars($customer['city']); ?></td>
                                        <td class="py-3 px-4 text-gray-700"><?php echo htmlspecialchars($customer['country']); ?></td>
                                        <td class="py-3 px-4 text-gray-700"><?php echo htmlspecialchars($customer['store_city']); ?></td>
                                        <td class="py-3 px-4 text-gray-700 relative">
                                            <button type="button" class="action-btn text-gray-700 hover:text-red-800 focus:outline-none" data-index="<?php echo $index; ?>">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01"></path>
                                                </svg>
                                            </button>
                                            <div class="action-menu" id="action-menu-<?php echo $index; ?>">
                                                <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 detail-action" data-index="<?php echo $index; ?>">Details</a>
                                                <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 update-action" data-index="<?php echo $index; ?>">Update</a>
                                                <a href="#" class="block px-4 py-2 text-sm text-red-600 hover:bg-gray-100 delete-action" data-index="<?php echo $index; ?>">Delete</a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                        <!-- Pagination -->
                        <div class="pagination">
                            <div>
                                <label for="records_per_page" class="text-gray-700">Records per page:</label>
                                <select id="records_per_page" onchange="changeRecordsPerPage()" class="ml-2 p-2 border border-gray-400 rounded">
                                    <?php foreach ([10, 15, 20, 25, 30, 50, 100] as $option): ?>
                                        <option value="<?php echo $option; ?>" <?php echo $records_per_page == $option ? 'selected' : ''; ?>><?php echo $option; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="flex space-x-2">
                                <a href="?section=customers&page=1&records_per_page=<?php echo $records_per_page; ?>" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">First</a>
                                <a href="?section=customers&page=<?php echo max(1, $page - 1); ?>&records_per_page=<?php echo $records_per_page; ?>" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">Previous</a>
                                <span class="text-gray-700 py-2">Page <?php echo $page; ?> of <?php echo $total_pages; ?></span>
                                <a href="?section=customers&page=<?php echo min($total_pages, $page + 1); ?>&records_per_page=<?php echo $records_per_page; ?>" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">Next</a>
                                <a href="?section=customers&page=<?php echo $total_pages; ?>&records_per_page=<?php echo $records_per_page; ?>" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">Last</a>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </main>
            <footer class="bg-white/10 border-t border-gray-400 py-4 mt-12">
                <div class="container mx-auto px-6 text-center text-gray-700">
                    <p>&copy; 2025 Your App. All rights reserved.</p>
                </div>
            </footer>
        </div>
    </div>

    <!-- Modals -->
    <div id="detailsModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">Customer Details</div>
            <div class="modal-body" id="detailsContent"></div>
            <div class="modal-footer">
                <button onclick="closeModal('detailsModal')" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">Close</button>
            </div>
        </div>
    </div>

    <div id="updateModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">Update Customer</div>
            <div class="modal-body">
                <form method="POST">
                    <input type="hidden" name="customer_id" id="update_customer_id">
                    <label>First Name:</label>
                    <input type="text" name="first_name" id="update_first_name" required>
                    <label>Last Name:</label>
                    <input type="text" name="last_name" id="update_last_name" required>
                    <label>Email:</label>
                    <input type="email" name="email" id="update_email" required>
                    <label>Address ID:</label>
                    <input type="number" name="address_id" id="update_address_id" required>
                    <div class="modal-footer">
                        <button type="submit" name="update_customer" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">Save</button>
                        <button type="button" onclick="closeModal('updateModal')" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div id="deleteModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">Confirm Deletion</div>
            <div class="modal-body">
                <p>Are you sure you want to delete this customer?</p>
                <form method="POST">
                    <input type="hidden" name="customer_id" id="delete_customer_id">
                    <div class="modal-footer">
                        <button type="submit" name="delete_customer" class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600">Delete</button>
                        <button type="button" onclick="closeModal('deleteModal')" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>


</body>
</html>