USE learner_db;

-- Insert default admin, teacher, and student users
-- Passwords are bcrypt hashes for "password"

INSERT INTO users (name, email, password, role, created_at, updated_at)
VALUES
('Admin User', 'admin@learner.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', NOW(), NOW()),
('Teacher User', 'teacher@learner.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'teacher', NOW(), NOW()),
('Student User', 'student1@learner.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'student', NOW(), NOW());

-- Optional: create a default competency
INSERT INTO competencies (name, description, domain, level, max_score, created_at, updated_at)
VALUES ('Problem Solving', 'General problem-solving skills', 'Cognitive', 1, 100, NOW(), NOW());

-- Optional: create a sample challenge
INSERT INTO challenges (title, description, problem_statement, competency_id, difficulty, test_cases, hints, max_attempts, time_limit, points, is_active, created_at, updated_at)
VALUES (
  'Hello World',
  'Write a program that prints Hello World.',
  'Your task is to output the string "Hello World".',
  1,
  'easy',
  '[{"input": "", "output": "Hello World"}]',
  '["Use print(\\"Hello World\\")"]',
  3,
  30,
  10,
  true,
  NOW(),
  NOW()
);