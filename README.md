# Simple PHP & SQLite Blog

This is a lightweight, procedural-style blog application built with PHP and using a SQLite database. It serves as a great example for learning the basics of web development with PHP, including database interaction, handling user input, and a simple admin system.

## Features

- View a list of blog posts.
- Click to view a full individual post.
- Post comments on articles.
- An installation script to set up the database.
- A simple admin login system to manage content (functionality to be extended).

## Requirements

- A web server with PHP support (e.g., XAMPP, MAMP, or a standard LAMP/LEMP stack).
- The PHP PDO extension enabled, specifically the `pdo_sqlite` driver.

## Installation

1.  **Place the Files:** Ensure all the project files are in a directory accessible by your web server (e.g., inside the `htdocs` folder for XAMPP).

2.  **Set Permissions:** Make sure your web server has permission to create and write files in the `blog/data/` directory. The installation script needs to create the `data.sqlite` database file.

3.  **Run the Installer:** Open your web browser and navigate to the installation script. If your project is in a `blog` folder, the URL will be:
    ```
    http://localhost/blog/install.php
    ```

4.  **Save Your Password:** The installation script will set up the database and create an `admin` user with a **randomly generated password**. This password will be displayed on the screen upon successful installation. **Please copy and save this password immediately**, as you will need it to log in.

5.  **Delete the Database (for re-installation):** If you need to run the installer again, you must first manually delete the `blog/data/data.sqlite` file.

## How to Use

### Viewing the Blog

- The main page `/blog/index.php` lists all blog posts.
- Click on a post's "Read more..." link to go to the `view-post.php` page for that article, where you can also see and add comments.

### Admin Login

- Navigate to the login page at `/blog/login.php`.
- **Username:** `admin`
- **Password:** The password that was generated for you during the installation process.
