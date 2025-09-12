INSERT INTO chats (buyer_name, buyer_email, order_code, subject, status, last_message_at)
VALUES ('Raka Pratama', 'raka@example.com', 'ABC12345', 'Top-up MLBB 172 Diamonds', 'open', NOW());
INSERT INTO chat_messages (chat_id, sender, message) VALUES
(LAST_INSERT_ID(), 'user', 'Halo admin, saya sudah bayar untuk order ABC12345.'),
(LAST_INSERT_ID(), 'admin', 'Halo kak Raka, kami cek dulu ya 😊'),
(LAST_INSERT_ID(), 'user', 'Siap terima kasih.');