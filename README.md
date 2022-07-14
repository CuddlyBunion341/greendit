# Greendit
Greendit is a simple social media website similar to Reddit.
This social media site uses PHP and a MySQL database for the backend.

## Live Demo
This project will be deployed soon.

## Installation

The Project uses MySQL workbench and XAMPP but there are also many other possibilities. With the following steps you can install this social media site and run it on your own device

### MySQL
1. Install MySQL for [Windows](https://dev.mysql.com/downloads/installer/), [Mac](https://dev.mysql.com/doc/refman/5.7/en/macos-installation-pkg.html) or [Linux](https://dev.mysql.com/doc/mysql-linuxunix-excerpt/8.0/en/linux-installation-native.html)
2. Install the newest version of [MySQL Workbench](https://dev.mysql.com/downloads/workbench/)
3. Run the [database.sql](sql/database.sql) script to create the database
4. Run the [insert-data.sql](sql/insert-data.sql) script to insert some dummy data

### XAMPP
1. Install the newest version of [xampp](https://www.apachefriends.org/download.html)
2. Copy the repository to the `htdocs` folder of xampp
3. Start the Apache and MySQL server
4. Open [Greendit](http://localhost/greendit/index.php) in your browser
