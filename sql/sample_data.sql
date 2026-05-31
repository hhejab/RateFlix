INSERT IGNORE INTO dbProj_users
(role_id, username, email, password_hash)
VALUES
(1, 'admin1', 'admin@test.com', '123456'),
(2, 'creator1', 'creator@test.com', '123456'),
(3, 'viewer1', 'viewer@test.com', '123456');


INSERT IGNORE INTO dbProj_movies
(creator_id, category_id, title, short_description, description, status)
VALUES
(
    9,
    1,
    'John Wick',
    'Action movie',
    'An ex-hitman seeks revenge.',
    'published'
),
(
    9,
    5,
    'Interstellar',
    'Sci-Fi movie',
    'A journey through space and time.',
    'published'
);


INSERT IGNORE INTO dbProj_ratings
(movie_id, user_id, rating_value)
VALUES
(1, 1, 5),
(2, 1, 4);


INSERT IGNORE INTO dbProj_comments
(movie_id, user_id, comment_text)
VALUES
(1, 1, 'Amazing movie!'),
(2, 1, 'Very interesting story.');