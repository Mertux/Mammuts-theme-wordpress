# Mammuts – WordPress Theme für American Football

Ein modernes, dunkles WordPress-Theme speziell für American Football Clubs,
mit tiefer **SportsPress**-Integration.

---

## Features

- **Dark & Bold Design** – Dunkler Hintergrund, rote Akzentfarbe, aggressive Typografie
- **SportsPress Integration** – Match Center, Roster Grid, Standings, Spielerprofile
- **Customizer** – Hero-Bereich, Vereinsdaten, Farben, Social Media alles konfigurierbar
- **Responsive** – Mobile-optimiert mit Hamburger-Menü
- **Performance** – Keine jQuery-Abhängigkeit, reines Vanilla JS
- **Widget Areas** – Sidebar, 3x Footer, Sponsoren-Leiste

---

## Installation

1. Lade `mammuts-theme.zip` in WordPress hoch:
   **Design → Themes → Neues Theme hinzufügen → Theme hochladen**
2. Theme aktivieren
3. SportsPress-Plugin installieren & aktivieren: https://wordpress.org/plugins/sportspress/

---

## Einrichtung

### 1. Customizer (Design → Customizer)

- **Hero Section**: Hintergrundbild, Titel-Zeilen, CTA-Button
- **Club Information**: Vereinsname, Adresse, Kontakt, Gründungsjahr
- **Social Media**: Facebook, Instagram, Twitter/X, YouTube, TikTok
- **Colors**: Akzentfarbe ändern (Standard: #c8102e)

### 2. SportsPress einrichten

- **Teams** anlegen (eigenes Team + Gegner)
- **Positionen** anlegen (QB, RB, WR, LB, DB, OL, DL, etc.)
- **Spieler** anlegen mit Fotos, Nummern und Positionen
- **Events** (Spiele) erstellen mit Teams und Ergebnissen
- **League Tables** für Standings

### 3. Menü erstellen

Unter **Design → Menüs** ein Menü für "Primary Navigation" erstellen.
Empfohlene Seiten: Home, Our Team, Schedule, News, Academy, Contact

### 4. Startseite

Unter **Einstellungen → Lesen** → "Eine statische Seite" wählen.
Eine leere Seite als "Startseite" zuweisen – das Theme nutzt `front-page.php`.

### 5. Sponsoren

Im Widget-Bereich "Sponsors Bar" können Bild-Widgets oder ein
SportsPress-Sponsor-Widget eingefügt werden.

---

## Theme-Struktur

```
mammuts-theme/
├── style.css              # Haupt-CSS + Theme-Metadaten
├── functions.php          # Theme-Setup, Customizer, SportsPress-Helfer
├── header.php             # Header mit Navigation
├── footer.php             # Footer mit Widgets & Social Links
├── front-page.php         # Startseite (Hero, Match Center, Roster, News)
├── index.php              # Blog-Übersicht
├── page.php               # Standard-Seiten
├── single.php             # Einzelne Beiträge
├── single-sp_player.php   # Spieler-Profil (SportsPress)
├── archive.php            # Archiv-Seiten
├── search.php             # Suchergebnisse
├── 404.php                # 404-Fehlerseite
├── sidebar.php            # Sidebar
├── inc/
│   └── template-tags.php  # Hilfs-Funktionen (Player Cards, Match Cards, etc.)
├── assets/
│   ├── js/
│   │   └── main.js        # Vanilla JS (Scroll, Menu, Filter, Animationen)
│   └── css/               # (für zusätzliche CSS-Dateien)
└── README.md
```

---

## Akzentfarbe anpassen

Die Standard-Akzentfarbe ist Rot (#c8102e).
Im Customizer unter "Colors" kann sie geändert werden.
Alle CSS-Variablen passen sich automatisch an.

---

## Anforderungen

- WordPress 6.0+
- PHP 7.4+
- SportsPress Plugin (empfohlen)

---

## Lizenz

GNU General Public License v2 or later
