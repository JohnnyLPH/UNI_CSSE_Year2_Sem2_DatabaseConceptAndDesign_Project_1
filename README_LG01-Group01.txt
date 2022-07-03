DB Lecture Group 01 - Group 01
Member:
    James Po Kin Hock
    Lau Jia Shao
    Lau Pikk Heang
    Yuki Chung Pei Ying

# Guide on how to setup database and website:
    # NOTE: Check dbConnection.php and modify if needed.

    1. Create a new database in MySQL with the following name or any name but make sure it's in dbConnection.php:
        db_assignment_1

    2. Import all the tables into the database using the SQL file in DB_Export folder.

    3. Modify dbConnection.php accordingly (MySQL account username and password with DB access, and database name if necessary).

    4. Use localhost to host the website, ensure root path is set to the website folder.

    5. Visit localhost/index.php in your browser. If using specific port (e.g., 4000), then localhost:4000/index.php.

    6. Available User accounts for login:
        -Admin:
            Username = adminA
            Password = 123
        -Company:
            Username = companyA
            Password = 123
        -Staff:
            Username = staffA
            Password = 123
        -Client:
            Username = clientA
            Password = 123

    7. To log in other user accounts, just refer the Username in Admin pages (Manage Admin, Manage Company, Manage Staff, Manage Client) and the password for almost all users is either 123 or ABC.
