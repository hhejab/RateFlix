INSERT INTO dbProj_users
(role_id, username, email, password_hash)
VALUES
(1, 'admin1', 'admin@test.com', '123456'),
(2, 'creator1', 'creator@test.com', '123456'),
(3, 'viewer1', 'viewer@test.com', '123456');


INSERT INTO dbProj_movies
(creator_id, category_id, title, short_description, description, status)
VALUES
(
    2,
    1,
    'John Wick',
    'Action movie',
    'An ex-hitman seeks revenge.',
    'published'
),
(
    2,
    5,
    'Interstellar',
    'Sci-Fi movie',
    'A journey through space and time.',
    'published'
);


INSERT INTO dbProj_ratings
(movie_id, user_id, rating_value)
VALUES
(1, 3, 5),
(2, 3, 4);


INSERT INTO dbProj_comments
(movie_id, user_id, comment_text)
VALUES
(1, 3, 'Amazing movie!'),
(2, 3, 'Very interesting story.');