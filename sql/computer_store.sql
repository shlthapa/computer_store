-- Table: users [cite: 30]
CREATE TABLE users (
    id INT(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    is_admin TINYINT(1) DEFAULT 0 
);

-- Table: products [cite: 31]
CREATE TABLE products (
    id INT(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    price DECIMAL(10, 2) NOT NULL,
    image_url VARCHAR(255),
    category VARCHAR(100),
    stock INT(11) NOT NULL DEFAULT 0
);

-- Table: cart (Used for active shopping carts) [cite: 32]
CREATE TABLE cart (
    id INT(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,
    user_id INT(11) NOT NULL,
    product_id INT(11) NOT NULL,
    quantity INT(11) NOT NULL DEFAULT 1,
    -- Ensure a user can only have one entry for the same product in their cart
    UNIQUE KEY unique_cart_item (user_id, product_id),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
);

-- Table: orders [cite: 33]
CREATE TABLE orders (
    id INT(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,
    user_id INT(11) NOT NULL,
    total_price DECIMAL(10, 2) NOT NULL,
    order_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    -- Add a status field (e.g., Pending, Shipped) later if needed
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE RESTRICT
);

-- Table: order_items (Crucial to know WHAT was ordered)
CREATE TABLE order_items (
    id INT(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,
    order_id INT(11) NOT NULL,
    product_id INT(11) NOT NULL,
    quantity INT(11) NOT NULL,
    price_at_purchase DECIMAL(10, 2) NOT NULL, 
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE RESTRICT
);

-- Insert a sample Admin user (Password is 'adminpassword' hashed)
INSERT INTO users (name, email, password, is_admin) VALUES 
('Admin User', 'admin@store.com', '$2y$10$i/R1/c/t4/n.gO8zQxG9/uC1l5pYqG3g/g.tV0r7jF.P6u.qF/V9', 1);

-- Insert some sample products
INSERT INTO products (name, description, price, category, stock) VALUES
('UltraGamer Pro', 'Top-tier desktop designed for ultimate performance.', 1999.99, 'desktops', 15)
('Alienware Predator 13', 'Sleek, high-performance laptop designed for gaming and professionals.', 1199.50, 'laptops', 22)
('ASUS ROG Astral GeForce RTX 5080', 'High-end ASUS GPU delivering ultra-smooth graphics.', 899.99, 'graphic cards', 25);