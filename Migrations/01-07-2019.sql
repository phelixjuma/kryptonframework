CREATE TABLE `password_reset_logs` (
 `id` int(11) NOT NULL AUTO_INCREMENT,
 `user_id` int(11) NOT NULL,
 `previous_password` varchar(255) NOT NULL,
 `status` enum('success','failed') NOT NULL,
 `created_at` datetime NOT NULL
 PRIMARY KEY (`id`)
);