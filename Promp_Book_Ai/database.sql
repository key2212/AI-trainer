CREATE DATABASE ai_platform;
USE ai_platform;

CREATE TABLE users(
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100),
    password VARCHAR(255),
    preference TEXT
);

CREATE TABLE ai_tools(
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100),
    description TEXT,
    guide TEXT,
    tags VARCHAR(255),
    rating DECIMAL(3,1),
    prompt_template TEXT,
    use_cases TEXT,
    exercises TEXT,
    weaknesses TEXT,
    official_url VARCHAR(255),
    api_url TEXT
);

CREATE TABLE conversations(
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    title VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE messages(
    id INT AUTO_INCREMENT PRIMARY KEY,
    conversation_id INT,
    sender VARCHAR(20),
    content TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE prompt_history(
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    prompt TEXT,
    score FLOAT,
    feedback TEXT
);

CREATE TABLE user_preferences(
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    goal VARCHAR(255),
    level VARCHAR(100),
    favorite_fields TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE user_exercise_results(
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    ai_tool_id INT NOT NULL,
    exercise_title VARCHAR(255),
    user_answer TEXT,
    score DECIMAL(4,1),
    feedback TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);