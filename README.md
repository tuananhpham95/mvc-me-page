# Me-sida för MVC-kursen

![Skärmbild av min me-sida](images/me-page.png)

## Om projektet

Detta är min personliga me-sida för kursen MVC. Här presenterar jag mig själv, kursen, mina redovisningstexter och en enkel JSON API. Projektet är byggt med Symfony och använder Twig för templating samt Encore för asset-hantering.

## Kom igång

Följ dessa steg för att klona och köra webbplatsen lokalt:

### Förutsättningar

- **PHP** 8.1 eller senare
- **Composer** (för PHP-beroenden)
- **Node.js och npm** (för front-end assets)
- **Symfony CLI** (för att köra servern)

### Installation

1. **Klona repot:**
   ```bash
   git clone git@github.com:tuananhpham95/mvc-me-page.git
   cd mvc-me-page
   ```
2. **Installera PHP-beroenden:**
   ```bash
   composer install
   ```
3. **Installera och bygg front-end assets:**
   ```bash
    npm install
    npm run build
   ```
4. **Starta den lokala servern:**
   ```bash
   symfony server:start
   ```
5. **Besök webbplatsen i webbläsaren:**
   http://127.0.0.1:8000
