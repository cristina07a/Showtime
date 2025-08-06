## Introduction and tools
This application is designed to enable booking tickets for festivals with ease. The architecture follows MVC pattern.
The project was created using PHP with Symfony framework, MariaDB as the database, XAMPP for hosting a local server, Twig for frontend templates, and DaisyUI for styling.

## Recommendation system
The core feature of this application is a recommendation system for artists, implemented using [Spotify Playlists Dataset on Kaggle](https://www.kaggle.com/datasets/andrewmvd/spotify-playlists/data?select=spotify_dataset.csv).
This dataset contains songs, the artists who perform them, and the playlists they belong to. If two artists appear in the same playlist, an association is made between them; if you listen to artist A, you might also enjoy artist B.

In this application, logged in users can add artists to their wishlist. Then, when loading the wishlist page, another artists will be recommended.

Only the first 500 artists as popularity (count of how many playlists an artist is featured) have been kept in order to maintain the application manageable. The music genre of every kept artist has been randomized. The obtained artists and their respective music genres have then been added with an SQL query into the database.

After collecting the top 500 artists and their playlists (excluding duplicates), the recommendation system was implemented using a similarity matrix.

If a festival has more than 3 artists an user has in its wishlist, is then added to the section **Festivals you must attend**, thus raising sales for that festival.
## Other functionalities:

1. Login with email and password through Symfony Security.
2. CRUD operations for entities: Bands (artists), Festival, Ticket, Booking, Code (sale code).
3. 2 roles for logged in users: ADMIN and USER.
5. Music Genres are stored in an enum.
6. CRUD pages are restricted, only admins having access.
7. Usage of forms for: booking and and performing CRUD operations.
8. Authenticated and unauthenticated users can buy tickets.
9. When buying tickets, if authenticated, the user won't be asked to enter its email in booking form.
10. Possibility of adding discount codes when booking, which are stored in the current session so they don't stack.
11. Search bar for bands/artists and festivals.
12. Updating the wishlist asynchronously using AJAX (add/remove without page reload).

## Database structure

<img width="1356" height="841" alt="image" src="https://github.com/user-attachments/assets/e6d4ef0f-fa2d-41fd-bb5d-b517d0a67f12" />

## Application screenshots:

Bands/artists page:

<img width="1588" height="966" alt="127 0 0 1_8000_booking_create_7" src="https://github.com/user-attachments/assets/bc7de537-f9ee-4a30-bb74-acb91f4650ee" />

User home page:

<img width="1588" height="2643" alt="user_bands_page" src="https://github.com/user-attachments/assets/479302f2-6a8a-446c-a41d-209f7d6a5bf6" />

Wishlist page:

<img width="1897" height="875" alt="image" src="https://github.com/user-attachments/assets/c9fccfb0-32dd-44a8-b9f7-d8cb23e5c048" />

Tickets page (for a festival):

<img width="1588" height="966" alt="tickets for a festival" src="https://github.com/user-attachments/assets/816c8225-56c5-4e59-9206-f2b8da78fd51" />

Booking a ticket:

<img width="1588" height="874" alt="booking a ticket" src="https://github.com/user-attachments/assets/e6a40261-cb68-42e4-8c2c-ee08265b5d9a" />

Admin dashboard:

<img width="1902" height="874" alt="admin dashboard" src="https://github.com/user-attachments/assets/70d15aac-bbc1-43ed-a24d-a85fccff1c3b" />

CRUD operations example:

<img width="1894" height="872" alt="CRUD festival" src="https://github.com/user-attachments/assets/13aa53f7-de9f-47b4-8654-d315eb398c10" />


