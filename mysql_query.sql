CREATE TABLE users (
    id INT AUTO_INCREMENT,
    name VARCHAR(255) DEFAULT NULL,
    gender TINYINT UNSIGNED DEFAULT 0 COMMENT '0 - не указан, 1 - мужчина, 2 - женщина.',
    birth_date INT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Дата в unixtime.',
    PRIMARY KEY (id)
);

CREATE INDEX idx_gender ON users(gender);

CREATE TABLE phone_numbers (
    id INT AUTO_INCREMENT,
    user_id INT,
    phone VARCHAR(255) NOT NULL,
    PRIMARY KEY (id),
FOREIGN KEY (user_id) REFERENCES users(id)
);

CREATE INDEX idx_user_id ON phone_numbers(user_id);


/**
  Запрос по задаче
 */
SELECT
    u.name,
    count(pn.phone) as numbers
FROM users u
    INNER JOIN phone_numbers pn ON pn.user_id = u.id
WHERE
    u.gender = 2
    AND (YEAR(CURRENT_DATE)-YEAR(FROM_UNIXTIME(u.birth_date))) - (RIGHT(CURRENT_DATE,5)<RIGHT(FROM_UNIXTIME(u.birth_date),5)) BETWEEN 18 AND 21
GROUP BY pn.user_id;
