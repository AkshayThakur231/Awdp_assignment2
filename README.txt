GadgetHub – PHP & MySQL E-Commerce (Advanced Web Development in PHP, Assignment 2)

SETUP (XAMPP / WAMP / Laragon, PHP 8.0+)
1. Copy the "gadgethub" folder into htdocs (XAMPP) or www (WAMP).
2. Start Apache and MySQL, open phpMyAdmin and import database.sql.
3. If your MySQL has a password, edit the $DB array at the top of config.php.
4. Make sure the "uploads" folder is writable.
5. Store:  http://localhost/gadgethub/
   Admin:  http://localhost/gadgethub/admin/login.php   (admin / admin123 – created on first visit)

FEATURES
Customer: home, categories, product list (search), product detail, cart (session), checkout,
payment (COD / UPI / card demo), order success, profile, order history & details, about, contact (saved in DB), login, register.
Admin: login, dashboard stats, users (search/view/edit/deactivate/delete), categories CRUD, products CRUD (+status, stock),
orders (view, update order/payment status; cancelling restores stock).
Security: PDO prepared statements, password_hash, CSRF tokens on every form, output escaping, image validation, session regeneration on login.
