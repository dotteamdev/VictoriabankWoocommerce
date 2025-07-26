# WooCommerce Victoriabank Payments

Contributors: Dan Oistric
Tags: WooCommerce, Moldova, Victoriabank, VB, bank, payment, gateway, visa, mastercard, credit card
Requires at least: 6.0
Tested up to: 6.3.1
Stable tag: 1.1.0
Requires PHP: 7.2
License: GPLv3 or later
License URI: <https://www.gnu.org/licenses/gpl-3.0.html>

Plugin pentru plăți Victoriabank în magazinele WooCommerce.

1. **Card Visa/Mastercard (acceptă toate cardurile Visa sau Mastercard)**
2. **Star Card Rate (acceptă doar cardurile Star emise de BC Victoriabank)**
3. **Star Points (acceptă doar cardurile Star emise de BC Victoriabank)**

Scopul este să faciliteze procesul de integrare în sistemul de plăți BC Victoriabank.

* Tipuri de tranzacții de card `Authorization` și `Charge`
* Tranzacții `Reversal` – rambursări parțiale sau complete
* Tranzacții `Sales completion` – finalizează tranzacțiile autorizate
* Email de confirmare a comenzii cu detaliile tranzacției de card
* Gratuit de utilizat – [Licență open-source GPL-3.0 pe GitHub](https://github.com/cyberink-co/vb-payment-plugin)

### Cerințe Preliminare

* WordPress 6.0 sau mai nou
* WooCommerce plugin instalat și activat
* PHP 7.2 sau mai nou
* Cont de comerciant la Victoriabank

### Pasul 1: Descărcarea Plugin-ului

În acest repozitoriu găsiți fișierul `woocommerce-victoriabank-payments.zip` care conține plugin-ul WordPress complet pregătit pentru instalare.

### Pasul 2: Instalarea în WordPress

**Metoda 1 - Prin interfața WordPress (Recomandat):**

1. Conectați-vă la panoul de administrare WordPress
2. Navigați la **Plugins > Add New Plugin**
3. Faceți clic pe **Upload Plugin**
4. Selectați fișierul `woocommerce-victoriabank-payments.zip` din acest repozitoriu
5. Faceți clic pe **Install Now**
6. După instalare, faceți clic pe **Activate Plugin**

**Metoda 2 - Manual prin FTP:**

1. Extrageți conținutul fișierului `woocommerce-victoriabank-payments.zip`
2. Încărcați folderul extras în directorul `/wp-content/plugins/` de pe serverul dumneavoastră
3. În panoul de administrare WordPress, navigați la **Plugins > Installed Plugins**
4. Găsiți "Victoriabank payment" și faceți clic pe **Activate**

### Pasul 3: Generarea Cheilor RSA

Pentru siguranța tranzacțiilor, plugin-ul necesită chei RSA publice și private. În acest repozitoriu găsiți utilitea `RSAKeyGenerator.exe` care vă va genera automat aceste chei.

**Utilizarea RSAKeyGenerator:**

1. Descărcați `RSAKeyGenerator.exe` din acest repozitoriu
2. Executați aplicația pe computerul dumneavoastră Windows
3. Aplicația va genera automat:
   - **Cheia privată** (private key) - păstrați-o în siguranță, nu o distribuiți
   - **Cheia publică** (public key) - aceasta va fi trimisă către Victoriabank

⚠️ **Important:** Păstrați cheia privată în siguranță și nu o distribuiți niciodată. Cheia publică va fi furnizată băncii pentru configurarea contului dumneavoastră.

### Pasul 4: Configurarea Plugin-ului

1. În WordPress, navigați la **Settings > VB payments settings**
2. Completați următoarele secțiuni:

**Merchant Data:**
- Merchant ID (furnizat de Victoriabank)
- Terminal ID (furnizat de Victoriabank)
- Alte date specifice comerciantului

**Connection Settings:**
- URL-urile de conectare (furnizate de Victoriabank)
- Setările de mediu (Test/Production)

**Payment Settings:**
- Tipul de tranzacție (Authorization/Charge)
- Setările pentru fiecare metodă de plată

### Pasul 5: Configurarea Metodelor de Plată WooCommerce

1. Navigați la **WooCommerce > Settings > Payments**
2. Veți vedea următoarele metode de plată disponibile:
   - **Victoriabank Visa/Mastercard**
   - **Victoriabank Star Card Rate**  
   - **Victoriabank Star Points**
3. Activați și configurați fiecare metodă de plată după necesități

### Pasul 6: Configurarea Email-urilor

Pentru a utiliza șabloanele de email personalizate ale plugin-ului:

1. Navigați la **WooCommerce > Settings > Emails**
2. Dezactivați următoarele șabloane standard WooCommerce:
   - **New order**
   - **Processing order**
   - **Completed order**

### Pasul 7: Testarea

1. Creați o comandă de test în magazinul dumneavoastră
2. Selectați una dintre metodele de plată Victoriabank
3. Verificați că redirecționarea către sistemul bancar funcționează corect
4. Testați fluxul complet de plată

### Pasul 8: Trecerea în Producție

Când testarea este completă:

1. Actualizați setările de conectare pentru mediul de producție
2. Înlocuiți URL-urile de test cu cele de producție
3. Verificați că toate datele comerciantului sunt corecte

### Probleme Comune

**Plugin-ul nu apare după instalare:**
- Verificați că WooCommerce este instalat și activat
- Verificați versiunea PHP (minimum 7.2)

**Erori la generarea cheilor RSA:**
- Asigurați-vă că `RSAKeyGenerator.exe` rulează pe Windows
- Verificați că aveți drepturi de administrator

**Probleme de conectare la bancă:**
- Verificați datele comerciantului
- Verificați URL-urile de conectare
- Contactați suportul Victoriabank

== Suport ==

Pentru suport tehnic:
- Contactați echipa de suport Victoriabank
- Consultați documentația tehnică furnizată de bancă
- Verificați secțiunea de logging din plugin pentru detalii despre erori

== Changelog ==

= 1.1.0 =
* Versiunea curentă cu toate caracteristicile implementate

== Screenshots ==

1. Payment gateways
2. Payment gateway settings
3. Merchant data
4. Connection settings
5. Notification settings
6. Payment settings
