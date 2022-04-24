CREATE TABLE `company` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `title` varchar(255),
  `logo_url` varchar(255),
  `address` varchar(255),
  `phone` varchar(255),
  `email` varchar(255),
  `url` varchar(255),
  `instructions` text
);

CREATE TABLE `project` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `ended_at` timestamp,
  `company_id` INTEGER,
  `client_id` INTEGER,
  `title` varchar(255),
  `code` varchar(2),
  CONSTRAINT fk_company_id FOREIGN KEY(company_id) REFERENCES company(id) ON DELETE SET null,
  CONSTRAINT fk_client_id FOREIGN KEY(client_id) REFERENCES company(id) ON DELETE SET null
);

CREATE TABLE `story_hour_type` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `title` varchar(255),
  `rate` INTEGER COMMENT 'In dollar cents, $150 = 15000'
);

CREATE TABLE `story_type` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `title` varchar(255)
);

CREATE TABLE `story_status` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `title` varchar(255),
  `is_complete_state` boolean DEFAULT false,
  `emoji` varchar(10)
);

CREATE TABLE `story_note` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `story_id` INTEGER,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `resolved_at` timestamp,
  `is_resolved` boolean DEFAULT false,
  `note` text,
  CONSTRAINT fk_story_id FOREIGN KEY(story_id) REFERENCES story(id) ON DELETE CASCADE
);

CREATE TABLE `story_collection` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `ended_at` timestamp,
  `project_id` INTEGER,
  `title` varchar(255),
  `is_project_default` BOOLEAN default false,
  `goals` text,
  CONSTRAINT fk_project_id FOREIGN KEY(project_id) REFERENCES project(id) ON DELETE CASCADE
);

CREATE TABLE `story` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `show_id` varchar(10),
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `due_at` timestamp,
  `ended_at` timestamp,
  `hours` INTEGER,
  `collection` INTEGER,
  `rate_type` INTEGER DEFAULT "1",
  `type` INTEGER,
  `status` INTEGER,
  `title` varchar(255),
  CONSTRAINT fk_collection FOREIGN KEY(collection) REFERENCES story_collection(id) ON DELETE SET null, 
  CONSTRAINT fk_type FOREIGN KEY(type) REFERENCES story_type(id) ON DELETE SET null,
  CONSTRAINT fk_rate_type FOREIGN KEY(rate_type) REFERENCES story_hour_type(id) ON DELETE SET null
);

-- Update this before running!
INSERT INTO company (title, logo_url, address, phone, email)
VALUES ('Your Company Name', 'http://yoursite.com/logo.png', '123 Sesame St<Br />New York, New York, 12345', '1-555-123-4567', 'invoices@yourcompany.com');

INSERT INTO story_type (id, title) VALUES (1, 'Story'), (2, 'Chore'), (3, 'Meeting');

-- The primary items should not be removed and need
-- to remain in this order!
INSERT INTO story_status (id, title, emoji. is_complete_state) VALUES (1, 'Open', '-', false), (2, 'Complete', '✅', true), (3, 'Shipped', '🚀', true), (4, 'Closed', '⛔️', true), (5, 'Superseded', '➰', true);

-- This is in dollar cents, so $50 = 5000.
INSERT INTO story_hour_type (id, title, rate) VALUES (1, 'Standard Rate', '5000');

