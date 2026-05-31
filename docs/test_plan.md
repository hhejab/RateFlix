# RateFlix Test Plan

## 1. Signup
Purpose: Verify that new users can register.
Steps:
1. Open signup.php
2. Enter username, email, password, and confirm password
3. Submit the form
Expected Output: Account created successfully.

## 2. Login
Purpose: Verify that registered users can login.
Steps:
1. Open login.php
2. Enter valid email and password
3. Submit the form
Expected Output: User is redirected to dashboard.php.

## 3. Movie Browsing
Purpose: Verify that visitors can view published movies.
Steps:
1. Open index.php
2. View movie list
3. Click View Details
Expected Output: Movie details page opens.

## 4. Search
Purpose: Verify that users can search by movie title.
Steps:
1. Enter a movie title in the search box
2. Click Search
Expected Output: Matching movies are displayed.

## 5. Creator Add Movie
Purpose: Verify that creators can add movies.
Steps:
1. Login as creator
2. Open creator/add_movie.php
3. Enter movie details
4. Submit
Expected Output: Movie is added successfully.

## 6. Creator Edit Movie
Purpose: Verify that creators can edit their own movies.
Steps:
1. Open creator/my_movies.php
2. Click Edit
3. Update movie details
4. Submit
Expected Output: Movie is updated successfully.

## 7. Admin Users
Purpose: Verify that admin can view users.
Steps:
1. Login as admin
2. Open admin/users.php
Expected Output: User list is displayed.

## 8. Admin Reports
Purpose: Verify that admin can generate reports.
Steps:
1. Login as admin
2. Open admin/reports.php
Expected Output: Popular movies and content by creator reports are displayed.

## 9. Access Control
Purpose: Verify role permissions.
Steps:
1. Login as viewer
2. Try opening admin/index.php
Expected Output: Access denied message appears.