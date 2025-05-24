
# 🎵 Music Player Web Application

This is a dynamic PHP-based Music Player web application developed as a term project for the **CSE348 - Database Management Systems** course at Yeditepe University. The app provides user login, playlist management, music browsing, and artist exploration features — all connected to a MySQL database.

---

## ✨ Features

- 🔐 **User Authentication**
  - Secure login with format validation
  - Session-based access control

- 🎧 **Music Playback View**
  - Visual album cover display
  - Real-time progress bar and formatted duration

- 📁 **Playlist System**
  - Create and manage personal playlists
  - Add or remove songs easily

- 👩‍🎤 **Artist Pages**
  - Displays biography, country, genre, top albums and songs
  - Follow/unfollow artist interaction

- 📊 **Play History**
  - Tracks and displays most recently played songs
  - Personalized sidebar experience

- 🌎 **Country-Based Recommendations**
  - Lists most popular artists from the user’s country

---

## 🛠️ Tech Stack

- **Frontend:** HTML, CSS, Vanilla JavaScript
- **Backend:** PHP 8+
- **Database:** MySQL (phpMyAdmin)
- **Server Environment:** AMPPS (Apache, MySQL, PHP)
- **Version Control:** Git & GitHub

---






## 🚀 Getting Started

1. Clone the repository:
   ```bash
   git clone https://github.com/selimyilbas/musicplayer.git
   cd musicplayer
   ```

2. Move the project folder into your `AMPPS/www/` directory (if not already there).

3. Import the MySQL database:
   - Open `phpMyAdmin`
   - Create a new database (e.g., `mahmut_selim_yilbas`)
   - Import the `install.php` or `insert_data.sql` file

4. Start AMPPS and access:
   ```
   http://localhost/musicplayer/login.html
   ```


## 📁 Project Structure

```
musicplayer/
├── login.html / login.php
├── homepage.php
├── artistpage.php
├── currentmusic.php
├── playlistpage.php
├── install.php
├── /screenshots
└── /assets (images, text files)
```

---

## 👤 Author

**Mahmut Selim Yılbaş**  
🎓 Senior Computer Engineering Student @ Yeditepe University  
📫 [GitHub Profile](https://github.com/selimyilbas)

---

## 📄 License

This project is for educational purposes only.
