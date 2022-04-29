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
  CONSTRAINT fk_company_id FOREIGN KEY(company_id) REFERENCES company(id),
  CONSTRAINT fk_client_id FOREIGN KEY(client_id) REFERENCES company(id)
);

CREATE TABLE `story_hour_type` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `title` varchar(255),
  `is_hidden` boolean DEFAULT 0,
  `rate` INTEGER COMMENT 'In dollar cents, $150 = 15000'
);

CREATE TABLE `story_type` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `title` varchar(255)
);

CREATE TABLE `story_status` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `title` varchar(255),
  `is_complete_state` boolean DEFAULT 0,
  `is_billable_state` boolean DEFAULT 0,
  `color` varchar(10),
  `emoji` varchar(10)
);

CREATE TABLE `story_note` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `story_id` INTEGER,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `resolved_at` timestamp,
  `is_resolved` boolean DEFAULT 0,
  `note` text,
  CONSTRAINT fk_story_id FOREIGN KEY(story_id) REFERENCES story(id) ON DELETE CASCADE
);

CREATE TABLE `story_collection` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `ended_at` timestamp,
  `project_id` INTEGER,
  `title` varchar(255),
  `is_project_default` BOOLEAN default 0,
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
  CONSTRAINT fk_collection FOREIGN KEY(collection) REFERENCES story_collection(id), 
  CONSTRAINT fk_type FOREIGN KEY(type) REFERENCES story_type(id),
  CONSTRAINT fk_rate_type FOREIGN KEY(rate_type) REFERENCES story_hour_type(id)
);

CREATE TABLE `invoice` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `year` INTEGER,
  `region` varchar(255),
  `payment_order` INTEGER
);

CREATE TABLE `tax` (
  `year` INTEGER PRIMARY KEY,
  `strategies` TEXT
);

CREATE TABLE `tax_payment` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `amount` varchar(255),
  `year` INTEGER,
  `region` varchar(255),
  `payment_order` INTEGER,
  CONSTRAINT fk_tax_p_year FOREIGN KEY(year) REFERENCES tax(year) ON DELETE CASCADE
);

CREATE TABLE `tax_deduction` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `year` INTEGER,
  `title` varchar(255),
  `amount` INTEGER,
  CONSTRAINT fk_tax_d_year FOREIGN KEY(year) REFERENCES tax(year) ON DELETE CASCADE
);

CREATE TABLE `tax_adjustment` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `year` INTEGER,
  `title` varchar(255),
  `taxable_percent` INTEGER,
  `taxable_amount` INTEGER,
  CONSTRAINT fk_tax_a_year FOREIGN KEY(year) REFERENCES tax(year) ON DELETE CASCADE
);

INSERT INTO story_type (id, title) VALUES (1, 'Task'), (2, 'Chore'), (3, 'Meeting'), (4, 'Hand Off Item');

-- The primary items should not be removed and need
-- to remain in this order!
INSERT INTO story_status (id, title, emoji, color, is_complete_state, is_billable_state) VALUES
(1, 'Open', 'fi-sr-document', '#111111', 0, 0),
(2, 'Complete', 'fi-sr-checkbox', '#47F43E', 1, 1),
(3, 'Shipped', 'fi-sr-rocket-lunch', '#3fcce8', 1, 0),
(4, 'Closed', 'fi-sr-cross-circle', '#b82a36', 1, 0),
(5, 'Unpaid', 'fi-sr-time-fast', '#e1e1e1', 1, 0);

-- This is in dollar cents, so $50 = 5000.
INSERT INTO story_hour_type (id, title, rate, is_hidden) VALUES (1, 'Standard Rate', '5000', 0);

