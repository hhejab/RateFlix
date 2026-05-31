CREATE TABLE dbProj_roles (
    role_id INT AUTO_INCREMENT PRIMARY KEY,
    role_name VARCHAR(30) NOT NULL UNIQUE
);

INSERT INTO dbProj_roles (role_name)
VALUES ('Admin'), ('Creator'), ('Viewer');


CREATE TABLE dbProj_users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    role_id INT NOT NULL,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (role_id)
    REFERENCES dbProj_roles(role_id)
);


CREATE TABLE dbProj_categories (
    category_id INT AUTO_INCREMENT PRIMARY KEY,
    category_name VARCHAR(50) NOT NULL UNIQUE
);

INSERT INTO dbProj_categories (category_name)
VALUES
('Action'),
('Drama'),
('Comedy'),
('Horror'),
('Sci-Fi');


CREATE TABLE dbProj_movies (
    movie_id INT AUTO_INCREMENT PRIMARY KEY,
    creator_id INT NOT NULL,
    category_id INT NOT NULL,
    title VARCHAR(150) NOT NULL,
    short_description VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    poster_image VARCHAR(255),
    media_file VARCHAR(255),
    status ENUM('draft','published','removed') DEFAULT 'draft',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (creator_id)
    REFERENCES dbProj_users(user_id),

    FOREIGN KEY (category_id)
    REFERENCES dbProj_categories(category_id)
);


CREATE TABLE dbProj_comments (
    comment_id INT AUTO_INCREMENT PRIMARY KEY,
    movie_id INT NOT NULL,
    user_id INT NOT NULL,
    comment_text TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (movie_id)
    REFERENCES dbProj_movies(movie_id),

    FOREIGN KEY (user_id)
    REFERENCES dbProj_users(user_id)
);


CREATE TABLE dbProj_ratings (
    rating_id INT AUTO_INCREMENT PRIMARY KEY,
    movie_id INT NOT NULL,
    user_id INT NOT NULL,
    rating_value INT NOT NULL CHECK (rating_value BETWEEN 1 AND 5),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    UNIQUE(movie_id, user_id),

    FOREIGN KEY (movie_id)
    REFERENCES dbProj_movies(movie_id),

    FOREIGN KEY (user_id)
    REFERENCES dbProj_users(user_id)
);

ALTER TABLE dbProj_movies
ADD FULLTEXT(title, short_description, description);

DELIMITER //

CREATE PROCEDURE dbProj_GetPopularMovies()
BEGIN
    SELECT 
        m.title,
        COUNT(r.rating_id) AS rating_count,
        IFNULL(AVG(r.rating_value), 0) AS avg_rating
    FROM dbProj_movies m
    LEFT JOIN dbProj_ratings r ON m.movie_id = r.movie_id
    WHERE m.status = 'published'
    GROUP BY m.movie_id
    ORDER BY avg_rating DESC, rating_count DESC;
END //

DELIMITER ;