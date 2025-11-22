CREATE TABLE `ucp_users` (
  `id` int(11) NOT NULL,
  `steamid` varchar(32) NOT NULL,
  `steamhex` varchar(64) DEFAULT NULL,
  `name` varchar(255) DEFAULT 'Tundmatu kasutaja',
  `role` varchar(50) DEFAULT 'Kasutaja',
  `registered_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

----
ALTER TABLE `players`
ADD COLUMN `steam` VARCHAR(255) NOT NULL;
