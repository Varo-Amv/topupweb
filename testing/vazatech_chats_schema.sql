CREATE TABLE IF NOT EXISTS chats (
  id INT AUTO_INCREMENT PRIMARY KEY,
  buyer_name VARCHAR(100) NOT NULL,
  buyer_email VARCHAR(120),
  order_code VARCHAR(16),
  subject VARCHAR(150),
  status ENUM('open','pending','closed') DEFAULT 'open',
  last_message_at TIMESTAMP NULL DEFAULT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
CREATE TABLE IF NOT EXISTS chat_messages (
  id INT AUTO_INCREMENT PRIMARY KEY,
  chat_id INT NOT NULL,
  sender ENUM('user','admin') NOT NULL,
  message TEXT NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (chat_id) REFERENCES chats(id) ON DELETE CASCADE
);
CREATE INDEX idx_chats_status ON chats(status);
CREATE INDEX idx_msg_chat ON chat_messages(chat_id);