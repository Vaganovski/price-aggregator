
INSERT INTO `users` (`id`, `email`, `login`, `password_hash`, `password_salt`, `role`, `password_reset_code`, `password_reset_code_created_at`, `activated`, `activation_code`, `registered`, `registration_ip`, `location`, `created_at`, `updated_at`, `name`) VALUES
(3, 'admin@eprise.kz', 'admin', '7c037d405f6e3b17fc9c915dfe672401', 'v!7p/Re', 'administrator', NULL, NULL, NULL, 'd5907138', NULL, '127.0.1.1', 'KZ', '2010-02-12 18:39:39', '2010-02-12 18:39:39', 'Admin');




INSERT INTO `catalog_categories` (`id`, `root_id`, `lft`, `rgt`, `level`, `title`, `alias`, `short_description`, `description`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 46, 0, 'Root', '', '', '', '2010-01-29 14:38:29', '2010-01-29 14:38:29'),
(2, 1, 2, 17, 1, 'Компьютеры, периферия', 'computers', 'Компьютеры, периферия', 'Компьютеры, периферия', '2010-01-29 14:38:29', '2010-01-31 13:12:16'),
(3, 1, 18, 19, 1, 'Фото, видео', 'photo-video', 'Фото, видео', 'Фото, видео', '2010-01-29 14:51:28', '2010-01-31 13:13:07'),
(4, 1, 20, 21, 1, 'Авто', 'auto', 'Авто', 'Авто', '2010-01-31 13:13:44', '2010-01-31 13:13:44'),
(5, 1, 22, 25, 1, 'Бытовая техника', 'consumer-electronics', 'Бытовая техника', 'Бытовая техника', '2010-01-31 13:24:32', '2010-01-31 13:24:32'),
(6, 1, 26, 27, 1, 'Оргтехника', 'office-electronics', 'Оргтехника', 'Оргтехника', '2010-01-31 13:25:33', '2010-01-31 13:25:33'),
(7, 1, 28, 33, 1, 'Красота и здоровье', 'health-and-beauty', 'Красота и здоровье', 'Красота и здоровье', '2010-01-31 13:28:43', '2010-01-31 13:28:43'),
(8, 1, 34, 35, 1, 'Мобильные телефоны', 'cell-phones', 'Мобильные телефоны', 'Мобильные телефоны', '2010-01-31 13:30:07', '2010-01-31 13:30:07'),
(9, 1, 36, 41, 1, 'Спорт, дом', 'sports-house', 'Спорт, дом', 'Спорт, дом', '2010-01-31 13:31:26', '2010-01-31 13:31:26'),
(10, 1, 42, 43, 1, 'Книги, подарки', 'books-gifts', 'Книги, подарки', 'Книги, подарки', '2010-01-31 13:32:05', '2010-01-31 13:32:05'),
(11, 1, 44, 45, 1, 'Детские товары', 'baby-products', 'Детские товары', 'Детские товары', '2010-01-31 13:33:11', '2010-01-31 13:33:11'),
(12, 1, 3, 4, 2, 'Ноутбуки', 'laptops', 'Ноутбуки', 'Ноутбуки', '2010-01-31 13:34:19', '2010-01-31 13:34:19'),
(13, 1, 5, 8, 2, 'Комплектующие', 'components', 'Комплектующие', 'Комплектующие', '2010-01-31 13:34:49', '2010-01-31 13:34:49'),
(14, 1, 9, 10, 2, 'Мониторы', 'monitors', 'Мониторы', 'Мониторы', '2010-01-31 13:35:26', '2010-01-31 13:35:26'),
(15, 1, 11, 12, 2, 'Принтеры', 'printers', 'Принтеры', 'Принтеры', '2010-01-31 13:36:09', '2010-01-31 13:36:09'),
(16, 1, 13, 14, 2, 'Игровые приставки', 'game-consoles', 'Игровые приставки', 'Игровые приставки', '2010-01-31 13:40:32', '2010-01-31 13:40:38'),
(17, 1, 15, 16, 2, 'Карты памяти', 'memory-cards', 'Карты памяти', 'Карты памяти', '2010-01-31 13:41:20', '2010-01-31 13:41:20'),
(18, 1, 29, 30, 2, 'Парфюмерия', 'perfumes', 'Парфюмерия', 'Парфюмерия', '2010-01-31 13:41:56', '2010-01-31 13:41:56'),
(19, 1, 31, 32, 2, 'Косметика', 'cosmetics', 'Косметика', 'Косметика', '2010-01-31 13:42:49', '2010-01-31 13:42:49'),
(20, 1, 37, 38, 2, 'Велосипеды', 'bicycles', 'Велосипеды', 'Велосипеды', '2010-01-31 13:43:30', '2010-01-31 13:43:30'),
(21, 1, 39, 40, 2, 'Тренажеры', 'trainers', 'Тренажеры', 'Тренажеры', '2010-01-31 13:44:01', '2010-01-31 13:44:02'),
(22, 1, 23, 24, 2, 'DVD плееры', 'dvd-players', 'DVD плееры', 'DVD плееры', '2010-01-31 13:44:40', '2010-01-31 13:44:40'),
(23, 1, 6, 7, 3, 'Процессоры', 'processors', 'Процессоры', 'Процессоры', '2010-01-31 17:18:37', '2010-01-31 17:18:37');





INSERT INTO `catalog_products` (`id`, `image_filename`, `name`, `description`, `created_at`, `updated_at`, `visible`, `clicks`) VALUES
(1, 'eAwSla7l5a.png', 'Samsung LE-40B530', '\r\nПроцессор:\r\n    atom-1660 MHz,\r\nОперативная память:\r\n    DDR2-1024 Mb,\r\nРазмер экрана:\r\n    10.1",\r\nЖесткий диск:\r\n    160 Гб\r\nОптический привод:\r\n    DVD нет,\r\nВремя работы от аккумулятора:\r\n    6 ч.\r\nВес:\r\n    1.1 кг. ', '2010-02-11 18:01:20', '2010-02-11 18:17:15', 0, 0),
(2, '', 'Asus', '\r\nПроцессор:\r\n    atom-1660 MHz,\r\nОперативная память:\r\n    DDR2-1024 Mb,\r\nРазмер экрана:\r\n    10.1",\r\nЖесткий диск:\r\n    160 Гб\r\nОптический привод:\r\n    DVD нет,\r\nВремя работы от аккумулятора:\r\n    6 ч.\r\nВес:\r\n    1.1 кг. ', '2010-02-12 17:17:45', '2010-02-12 17:17:45', 1, 0),
(3, '', 'BenQ', '\r\nПроцессор:\r\n    atom-1660 MHz,\r\nОперативная память:\r\n    DDR2-1024 Mb,\r\nРазмер экрана:\r\n    10.1",\r\nЖесткий диск:\r\n    160 Гб\r\nОптический привод:\r\n    DVD нет,\r\nВремя работы от аккумулятора:\r\n    6 ч.\r\nВес:\r\n    1.1 кг. ', '2010-02-12 17:20:46', '2010-02-12 17:20:46', 1, 0);



INSERT INTO `catalog_categories_products` (`id`, `product_id`, `category_id`) VALUES
(1, 1, 13),
(2, 2, 19),
(3, 3, 3);
