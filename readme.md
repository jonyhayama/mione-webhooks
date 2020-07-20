# Mione Webhooks

## Database

```sql
CREATE TABLE `hooks` (
  `id` int(11) NOT NULL,
  `service` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `status` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'created',
  `payload` json NOT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

ALTER TABLE `hooks`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `hooks`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;


```