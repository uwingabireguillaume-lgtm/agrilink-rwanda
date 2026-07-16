-- AgriLink Rwanda — Seed Data
-- Sample admin, farmers, categories, and products for demo/evaluation.
-- Passwords below are all: Password123
-- (hash generated with PHP password_hash('Password123', PASSWORD_DEFAULT))

USE agrilink;

-- ============================
-- USERS
-- ============================
INSERT INTO users (full_name, email, password, phone, address, role) VALUES
('Admin User', 'admin@agrilink.rw', '$2y$10$2nhGK3Fm020OUa7dfxESOO3J4Tjqg.gGgVGf911RZNOUJhKYTzOpu', '0788000000', 'Kigali, Rwanda', 'admin'),
('Jean Bosco Habimana', 'jean.habimana@agrilink.rw', '$2y$10$2nhGK3Fm020OUa7dfxESOO3J4Tjqg.gGgVGf911RZNOUJhKYTzOpu', '0788111111', 'Musanze, Rwanda', 'farmer'),
('Marie Uwase', 'marie.uwase@agrilink.rw', '$2y$10$2nhGK3Fm020OUa7dfxESOO3J4Tjqg.gGgVGf911RZNOUJhKYTzOpu', '0788222222', 'Huye, Rwanda', 'farmer'),
('Emmanuel Nkurunziza', 'emmanuel.n@agrilink.rw', '$2y$10$2nhGK3Fm020OUa7dfxESOO3J4Tjqg.gGgVGf911RZNOUJhKYTzOpu', '0788333333', 'Rubavu, Rwanda', 'farmer'),
('Alice Mukamana', 'alice.mukamana@example.com', '$2y$10$2nhGK3Fm020OUa7dfxESOO3J4Tjqg.gGgVGf911RZNOUJhKYTzOpu', '0788444444', 'Kigali, Rwanda', 'consumer'),
('David Iradukunda', 'david.iradukunda@example.com', '$2y$10$2nhGK3Fm020OUa7dfxESOO3J4Tjqg.gGgVGf911RZNOUJhKYTzOpu', '0788555555', 'Kigali, Rwanda', 'consumer');

-- ============================
-- FARMER PROFILES
-- ============================
INSERT INTO farmer_profiles (user_id, farm_name, district, sector, bio, is_approved) VALUES
(2, 'Habimana Family Farm', 'Musanze', 'Muhoza', 'Growing Irish potatoes and vegetables in the fertile volcanic soils of Musanze for three generations.', 1),
(3, 'Uwase Organic Gardens', 'Huye', 'Ngoma', 'Certified organic vegetables and fruits, grown with sustainable farming practices.', 1),
(4, 'Lake Kivu Coffee Cooperative', 'Rubavu', 'Gisenyi', 'Premium arabica coffee beans grown on the shores of Lake Kivu.', 1);

-- ============================
-- CATEGORIES
-- ============================
INSERT INTO categories (name, slug, description) VALUES
('Vegetables', 'vegetables', 'Fresh vegetables sourced directly from local farms'),
('Fruits', 'fruits', 'Seasonal fruits grown across Rwanda'),
('Grains & Tubers', 'grains-tubers', 'Staple grains, potatoes, cassava, and more'),
('Coffee & Tea', 'coffee-tea', 'Premium Rwandan coffee and tea'),
('Dairy & Eggs', 'dairy-eggs', 'Fresh dairy products and eggs'),
('Herbs & Spices', 'herbs-spices', 'Fresh and dried herbs and spices');

-- ============================
-- PRODUCTS
-- ============================
INSERT INTO products (farmer_id, category_id, name, slug, description, price, unit, stock_quantity, image_url) VALUES
(1, 3, 'Irish Potatoes', 'irish-potatoes', 'Fresh Irish potatoes harvested from the volcanic soils of Musanze. Great for frying, boiling, or roasting.', 800.00, 'kg', 500, '/assets/images/product-placeholder.svg'),
(1, 1, 'Carrots', 'carrots', 'Crisp, sweet carrots grown without synthetic pesticides.', 700.00, 'kg', 300, '/assets/images/product-placeholder.svg'),
(1, 1, 'Cabbage', 'cabbage', 'Large, fresh green cabbage heads, perfect for family meals.', 500.00, 'piece', 200, '/assets/images/product-placeholder.svg'),
(2, 1, 'Tomatoes', 'tomatoes', 'Vine-ripened tomatoes, bursting with flavor, grown organically in Huye.', 900.00, 'kg', 250, '/assets/images/product-placeholder.svg'),
(2, 1, 'Green Peppers', 'green-peppers', 'Crunchy green bell peppers, hand-picked at peak freshness.', 1200.00, 'kg', 150, '/assets/images/product-placeholder.svg'),
(2, 2, 'Avocados', 'avocados', 'Creamy Hass avocados, perfect for salads or guacamole.', 1500.00, 'kg', 180, '/assets/images/product-placeholder.svg'),
(2, 2, 'Passion Fruit', 'passion-fruit', 'Sweet-tart passion fruit, freshly harvested.', 1800.00, 'kg', 120, '/assets/images/product-placeholder.svg'),
(3, 4, 'Arabica Coffee Beans', 'arabica-coffee-beans', 'Premium roasted arabica coffee beans from the shores of Lake Kivu.', 4500.00, 'kg', 100, '/assets/images/product-placeholder.svg'),
(3, 4, 'Green Tea Leaves', 'green-tea-leaves', 'Freshly picked green tea leaves from the highlands of Rubavu.', 3200.00, 'kg', 80, '/assets/images/product-placeholder.svg'),
(1, 3, 'Maize (Corn)', 'maize-corn', 'Dried maize kernels, ideal for flour or animal feed.', 600.00, 'kg', 400, '/assets/images/product-placeholder.svg'),
(2, 5, 'Free-Range Eggs', 'free-range-eggs', 'Farm-fresh eggs from free-range hens, sold by the tray of 30.', 3500.00, 'piece', 60, '/assets/images/product-placeholder.svg'),
(3, 6, 'Dried Red Chili', 'dried-red-chili', 'Sun-dried red chili peppers, packed with heat and flavor.', 2500.00, 'kg', 90, '/assets/images/product-placeholder.svg');
