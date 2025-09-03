-- Fix migration duplications by marking them as already run
INSERT IGNORE INTO migrations (migration, batch) VALUES 
('2025_07_13_000000_create_karenderias_table', 2),
('2025_07_14_064533_create_karenderias_table', 2),
('2025_08_12_000002_add_recipe_id_to_menu_items_table', 2);
