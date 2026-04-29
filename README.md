# BookMyHotel
BookMyHotel is a PHP and MySQL hotel booking website built for a XAMPP-style local environment. It includes a public booking flow, user registration and login, payment confirmation, and an admin area for managing bookings, users, rooms, facilities, and settings.

## Features

- Responsive home, rooms, facilities, and contact pages
- User registration, login, and logout
- Room search by check-in date, check-out date, adults, and children
- Automatic total-price calculation by number of nights
- Booking handoff into a payment page
- Payment success and invoice-style confirmation screen
- Admin dashboard with booking, user, room, facility, and revenue summaries
- Contact form with validation and user feedback

## Tech Stack

- PHP
- MySQL / MariaDB
- Bootstrap 5
- Bootstrap Icons
- Swiper carousel
- Google Fonts

## Project Structure

```text
BookMyHotel/
├── admin/                 # Admin dashboard and management pages
├── assets/css/site.css    # Shared customer-facing styles
├── images/                # Carousel, room, facility, and user images
├── booking.php            # Validates booking details and stores them in session
├── config.php             # Main database connection
├── contact.php            # Contact page
├── facilities.php         # Facilities listing
├── index.php              # Home, login, and registration
├── payment.php            # Payment form and booking creation
├── payment_success.php    # Booking confirmation
├── rooms.php              # Room search and room cards
└── send_message.php       # Contact form handler
```

## Local Setup

1. Copy the project folder into your XAMPP web root:

```text
C:\xampp\htdocs\BookMyHotel
```

2. Start Apache and MySQL from the XAMPP Control Panel.

3. Create a database named `bookmyhotel` in phpMyAdmin.

4. Update database credentials in `config.php` if your local MySQL setup is different:

```php
$host = "localhost";
$dbname = "bookmyhotel";
$username = "root";
$password = "";
```

5. Open the site in your browser:

```text
http://localhost/BookMyHotel/
```

## Expected Database Tables

The app expects at least these tables.

```sql
CREATE TABLE users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  email VARCHAR(150) NOT NULL UNIQUE,
  phone VARCHAR(30) NOT NULL,
  address TEXT NOT NULL,
  pincode VARCHAR(20) NOT NULL,
  dob DATE NOT NULL,
  password VARCHAR(255) NOT NULL,
  picture VARCHAR(255),
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE bookings (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  room_type VARCHAR(50) NOT NULL,
  check_in DATE NOT NULL,
  check_out DATE NOT NULL,
  adults INT NOT NULL DEFAULT 1,
  children INT NOT NULL DEFAULT 0,
  total_amount DECIMAL(10,2) NOT NULL,
  status VARCHAR(30) NOT NULL DEFAULT 'pending',
  payment_method VARCHAR(50),
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id)
);

CREATE TABLE rooms (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(150) NOT NULL,
  description TEXT,
  price DECIMAL(10,2) NOT NULL,
  capacity INT NOT NULL,
  features TEXT,
  image VARCHAR(255),
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

Admin pages may also require an admin login table depending on your existing `admin/admin_login.php` implementation.

## Notes

- Uploaded user profile images are stored in `images/users/`.
- Room data shown on the public `rooms.php` page is currently hardcoded.
- The contact form validates input and shows a success message locally; it does not send email yet.
- Payment is a demo flow and does not integrate with a real payment gateway.
