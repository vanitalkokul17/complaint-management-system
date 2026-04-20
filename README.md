📋 ComplaintDesk — Complaint Management System

A professional, full-stack Complaint Management System built with PHP + MariaDB + Apache (LAMP) stack. Deployed on AWS EC2 Amazon Linux 2023 with a modern dark UI.
________________________________________
🌐 Live Demo

•	App URL: http://13.233.143.97/complaint  #As if I stopped my instance the URL changes

•	Admin Login: admin@complaint.local

•	Password: Admin@12345
________________________________________
✨ Features

👤 User Portal

•	Register and login securely

•	Submit complaints with file attachments (jpg, png, pdf, doc)

•	Track complaint status with live timeline

•	Get real-time notifications when status changes

•	Filter complaints by status (open, in progress, resolved)

🛠️ Admin Panel

•	Dashboard with live stats (total, open, in-progress, resolved, critical)

•	View, filter and search all complaints

•	Update complaint status and priority

•	Add admin remarks and responses

•	Manage and activate/deactivate users

•	Full activity timeline per complaint

🔐 Security

•	PDO prepared statements (SQL injection safe)

•	Input sanitization with htmlspecialchars

•	bcrypt password hashing

•	Session-based authentication with role checks

•	File upload validation (type and extension check)
________________________________________

🖥️ Tech Stack

Layer	Technology

Backend	PHP 8.5

Database	MariaDB 10.5

Server	Apache 2.4 (httpd)

Frontend	HTML5, CSS3, Vanilla JS

Fonts	Syne + DM Sans (Google)

Hosting	AWS EC2 Amazon Linux 2023

Version	Git + GitHub
________________________________________

🗂️ Project Structure

complaint-management-system/

│

├── index.php                        # Login and Register page

├── logout.php                       # Session destroy

├── .htaccess                        # Apache config

├── setup_complaint_system.sh        # One-click EC2 installer

│

├── includes/

│   ├── config.php                   # DB connection and helper functions

│   ├── config.example.php           # Safe config template (no secrets)

│   ├── header.php                   # Sidebar and navigation layout

│   └── footer.php                   # Closing HTML tags

│

├── admin/

│   ├── dashboard.php                # Admin overview and stats

│   ├── complaints.php               # All complaints with filters

│   ├── view_complaint.php           # Manage single complaint

│   └── users.php                    # User management

│

├── user/

│   ├── dashboard.php                # User overview and recent complaints

│   ├── new_complaint.php            # Submit new complaint form

│   ├── my_complaints.php            # List all user complaints

│   └── view_complaint.php           # Track complaint timeline

│

├── assets/

│   ├── css/

│   │   └── style.css                # Dark theme stylesheet

│   └── uploads/                     # Uploaded complaint attachments

│

└── database/

    └── schema.sql                   # Full DB schema and seed data
________________________________________

🚀 How to Deploy on AWS EC2 (Amazon Linux 2023)

Step 1 — Launch EC2 Instance

•	Go to console.aws.amazon.com/ec2

•	Click Launch Instance

•	Choose Amazon Linux 2023

•	Instance type: t3.micro (free tier)

•	Create key pair → download .pem file

•	Security Group: open port 22 (SSH) and port 80 (HTTP)

•	Click Launch Instance

Step 2 — Connect via SSH (Windows PowerShell)

ssh -i C:\Users\YourName\Downloads\complaint-key.pem ec2-user@YOUR-EC2-IP

Step 3 — Fix the setup script for Amazon Linux 2023

sed -i 's/yum install -y httpd mariadb-server php php-mysqlnd php-mbstring php-xml php-gd/dnf install -y httpd mariadb105-server mariadb105 php php-mysqlnd php-mbstring php-xml php-gd php-json/g' ~/setup_complaint_system.sh

sed -i "s/ALTER USER 'root'@'localhost' IDENTIFIED WITH mysql_native_password BY/ALTER USER 'root'\@'localhost' IDENTIFIED BY/g" ~/setup_complaint_system.sh

Step 4 — Run the installer

sudo bash ~/setup_complaint_system.sh

Step 5 — Fix file permissions

sudo chown -R apache:apache /var/www/html/complaint

sudo systemctl restart httpd

Step 6 — Open your app

http://YOUR-EC2-IP/complaint
________________________________________

📤 How to Push to GitHub

Step 1 — Install Git

sudo dnf install -y git

Step 2 — Configure Git

sudo git config --global user.name "Your Name"

sudo git config --global user.email "your@email.com"

sudo git config --global --add safe.directory /var/www/html/complaint

Step 3 — Initialize and push

cd /var/www/html/complaint

sudo git init

sudo git add -A

sudo git commit -m "Initial commit: ComplaintDesk"

sudo git remote add origin https://github.com/YOUR_USERNAME/complaint-management-system.git

sudo git branch -M main

sudo git push -u origin main

When asked for password — paste your GitHub Personal Access Token (not your GitHub password).
________________________________________
🔄 Future Updates

Push changes from EC2 to GitHub

cd /var/www/html/complaint

sudo git add -A

sudo git commit -m "describe your change"

sudo git push origin main

Pull updates from GitHub to EC2

cd /var/www/html/complaint

sudo git pull origin main

sudo systemctl restart httpd
________________________________________
🗄️ Database Details

Setting	Value

DB Name	complaint_db

DB User	complaint_user

DB Pass	ComplaintDB@2024

Host	localhost

Tables

•	users — stores all user and admin accounts

•	complaints — all submitted complaints

•	categories — complaint categories (Technical, Billing, etc.)

•	complaint_updates — timeline of status changes

•	notifications — user notifications
________________________________________
🔑 Default Credentials

⚠️ Change these immediately after first login!

Role	Email	Password

Admin	admin@complaint.local	Admin@12345
________________________________________
📄 Pages

Page	Who	What it does

/complaint/	All	Login and Register

/complaint/user/dashboard.php	User	Stats and recent complaints

/complaint/user/new_complaint.php	User	Submit new complaint

/complaint/user/my_complaints.php	User	View and filter my complaints

/complaint/user/view_complaint.php	User	Track complaint timeline

/complaint/admin/dashboard.php	Admin	Overview dashboard

/complaint/admin/complaints.php	Admin	All complaints with filters

/complaint/admin/view_complaint.php	Admin	Manage complaint and update

/complaint/admin/users.php	Admin	Manage all users
________________________________________
⚠️ Production Checklist

•	Change default admin password

•	Set strong DB password in config.php

•	Enable HTTPS with SSL certificate

•	Set display_errors = Off in php.ini

•	Restrict SSH to your IP only

•	Set up Elastic IP to keep same IP after restart

•	Enable automated database backups
________________________________________
🛑 Stop and Start EC2

To stop (pauses server, data is safe):

•	EC2 Console → Instances → tick checkbox → Instance state → Stop

To start again:

•	EC2 Console → Instances → tick checkbox → Instance state → Start

•	Copy the new Public IPv4 (IP changes every restart unless you use Elastic IP)
________________________________________

👩‍💻 Built By

Vani — deployed on AWS EC2 Amazon Linux 2023
________________________________________

📄 License

MIT License — free to use, modify, and distribute.

Here, are some images of system output

<img width="1917" height="1019" alt="Screenshot 2026-04-19 182650" src="https://github.com/user-attachments/assets/0fc10e9c-d93b-4e41-8b2e-bc27018538d0" />

<img width="1918" height="1021" alt="Screenshot 2026-04-19 182617" src="https://github.com/user-attachments/assets/c06b9891-4edd-4543-8704-c30667789c27" />

<img width="1918" height="1016" alt="Screenshot 2026-04-19 183112" src="https://github.com/user-attachments/assets/019f3b7e-3054-4329-b89d-665ad3d13e9c" />

<img width="1919" height="1017" alt="Screenshot 2026-04-19 183127" src="https://github.com/user-attachments/assets/8bee9d47-ec6f-492a-87e2-68453f994895" />

<img width="1919" height="1024" alt="Screenshot 2026-04-19 183159" src="https://github.com/user-attachments/assets/be17c4f6-50f1-4500-9bad-daad72165111" />

<img width="1919" height="1023" alt="Screenshot 2026-04-19 183220" src="https://github.com/user-attachments/assets/7e842484-23b4-45e2-a298-a467bef2fd17" />
