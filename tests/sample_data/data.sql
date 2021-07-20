SET FOREIGN_KEY_CHECKS = 0;
TRUNCATE TABLE `store_delivery_fixed_cost`;
INSERT INTO `store_delivery_fixed_cost` (`id`, `cost`) VALUES (NULL, '4.99');

TRUNCATE TABLE `store_delivery_methods`;
INSERT INTO `store_delivery_methods` (`id`, `description`, `price`) VALUES (NULL, '1st Class', '3.99'), (NULL, '2nd Class', '1.29'), (NULL, 'Next Day (Guaranteed)', '7.99');

TRUNCATE TABLE `store_delivery_value`;
INSERT INTO `store_delivery_value` (`id`, `min_price`, `max_price`, `price`) VALUES (NULL, '0.00', '49.98', '3.99'), (NULL, '49.99', '99.99', '0.00');

TRUNCATE TABLE `store_delivery_weight`;
INSERT INTO `store_delivery_weight` (`id`, `max_weight`, `price`) VALUES (NULL, '0.999', '0.00'), (NULL, '1.000', '1.00'), (NULL, '2.000', '2.00'), (NULL, '2.500', '2.50'), (NULL, '3.000', '3.00'), (NULL, '3.500', '3.50'), (NULL, '4.000', '4.00'), (NULL, '4.500', '4.50'), (NULL, '5.000', '5.00'), (NULL, '5.500', '5.50'), (NULL, '9999999.000', '6.00');

TRUNCATE TABLE `store_tax`;
INSERT INTO `store_tax` (`tax_id`, `percent`, `details`) VALUES ('1', '20', 'UK Standard VAT'), ('2', '0', 'Non VAT Items (Books, Food, Etc)');

TRUNCATE TABLE `store_categories`;
INSERT INTO `store_categories` (`id`, `name`, `description`, `url`, `image`, `order`, `noproducts`, `active`, `metatitle`, `metadesc`) VALUES (NULL, 'First Category', '', 'first-category', NULL, '1', '0', '1', 'This is the First Category', 'Some meta data'), (NULL, 'Another Category', '<p>This should be some text about this category</p>', 'some-random-url-string', NULL, '2', '0', '1', 'Yet Another 1', 'Some more meta data');

TRUNCATE TABLE `store_products`;
INSERT INTO `store_products` (`product_id`, `active`, `code`, `name`, `description`, `features`, `requirements`, `noimages`, `image`, `width`, `height`, `price`, `sale_price`, `related`, `digital`, `digitalloc`, `weight`, `tax_id`, `featured`, `homepage`, `date_added`, `custom_url`, `type`, `isbn`, `mpn`, `views`, `sales`) VALUES (NULL, '1', 'SAMPLE', 'Sample Product', '<p>Just some random description.</p>', NULL, NULL, '0', NULL, '0', '0', '9.99', NULL, NULL, '0', NULL, '1.000', '1', '0', '0', CURRENT_TIMESTAMP, NULL, NULL, NULL, NULL, '0', '0'),
(NULL, '1', 'SAMPLE2', 'Second Product', '<p>Yet another description.</p>', NULL, NULL, '0', NULL, '0', '0', '3.99', NULL, NULL, '1', NULL, '3.000', '1', '0', '0', CURRENT_TIMESTAMP, NULL, NULL, NULL, NULL, '0', '0');

TRUNCATE TABLE `store_product_category`;
INSERT INTO `store_product_category` (`product_id`, `category_id`, `main_category`) VALUES ('1', '2', '1'), ('2', '1', '1');

TRUNCATE TABLE `store_vouchers`;
INSERT INTO `store_vouchers` (`voucher_id`, `active`, `code`, `percent`, `amount`, `description`, `expire`, `selected_products`, `allowed`, `times_used`) VALUES (NULL, '1', 'DISC10', '10', NULL, '10% Discount', '2040-12-31 00:00:00', NULL, '50', '0'), (NULL, '1', 'FIXED1', NULL, '1.00', 'A fixed amount off', '2040-12-31 00:00:00', NULL, '100', '0');

TRUNCATE TABLE `store_gallery_images`;
INSERT INTO `store_gallery_images` (`img_id`, `image`, `caption`, `active`) VALUES (NULL, '/images/image1.jpg', 'This is the first image', 1), (NULL, '/images/image2.jpg', 'Another image', 1), (NULL, '/images/image3.jpg', 'Yet another image', 1), (NULL, '/images/image4.jpg', 'The final image', 1);

TRUNCATE TABLE `store_product_images`;
INSERT INTO `store_product_images` (`product_id`, `image_id`) VALUES ('1', '1'), ('1', '2'), ('1', '4');

TRUNCATE TABLE `users`;
INSERT INTO `users` (`id`, `title`, `firstname`, `lastname`, `add_1`, `add_2`, `town`, `county`, `postcode`, `phone`, `mobile`, `email`, `password`, `ipaddress`, `no_orders`, `regtime`, `last_login`, `require_pass`, `isactive`) VALUES (NULL, 'Mrs', 'Sample', 'Name', NULL, NULL, NULL, '56', NULL, NULL, NULL, 'sample.name@emaple.com', '$2y$10$AUrD3goyQLtOYkcn8UM/gOgyi6gNKjjwbEbBJPWvufW6riXXx2Xuy', NULL, '0', CURRENT_TIMESTAMP, NULL, '0', '1');

TRUNCATE TABLE `store_orders`;
TRUNCATE TABLE `store_order_products`;
TRUNCATE TABLE `store_reviews`;
TRUNCATE TABLE `store_serials`;
TRUNCATE TABLE `users_attempts`;
TRUNCATE TABLE `users_delivery_address`;
TRUNCATE TABLE `users_requests`;
TRUNCATE TABLE `users_sessions`;

SET FOREIGN_KEY_CHECKS = 1;